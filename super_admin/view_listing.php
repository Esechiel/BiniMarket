<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'super_admin') {
    // Redirection si non connecté ou mauvais rôle
    header("Location: ../auth.php?error=Accès réservé au super administrateur");
    exit;
}



$host = 'localhost';
$db   = 'binimarket';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}



if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Annonce invalide.";
    exit;
}

$listing_id = intval($_GET['id']);

// Récupération des détails de l’annonce
$stmt = $conn->prepare("SELECT l.*, u.username, c.name AS category_name, loc.name AS location_name 
                        FROM listings l
                        JOIN users u ON l.vendeur_id = u.user_id
                        JOIN categories c ON l.category_id = c.category_id
                        JOIN locations loc ON l.location_id = loc.location_id
                        WHERE l.listing_id = ?");
$stmt->bind_param("i", $listing_id);
$stmt->execute();
$result = $stmt->get_result();
$listing = $result->fetch_assoc();
$stmt->close();

if (!$listing) {
    echo "Annonce introuvable.";
    exit;
}

// Récupération des images
$stmt = $conn->prepare("SELECT * FROM listing_images WHERE listing_id = ?");
$stmt->bind_param("i", $listing_id);
$stmt->execute();
$images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php include 'includes/sidebar_sa.php'; ?>

<style>
    
.container {
    margin-left: 250px;
    padding: 30px;
    background-color:rgb(245, 248, 250);
    color: #fff;
    min-height: 100vh;
}

h1 {
    margin-bottom: 20px;
    font-size: 28px;
    color:rgb(3, 6, 8);
}

.info-box {
    background-color:rgb(236, 238, 243);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-box strong {
    display: inline-block;
    width: 150px;
    color:rgb(3, 6, 8);
}

.images {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.images img {
    max-width: 250px;
    border-radius: 10px;
    border: 4px solid #2f4c77;
}
.image-gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.image-gallery img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 10px;
  border: 2px solid #fff;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.image-gallery img:hover {
  transform: scale(1.05);
}

/* Lightbox backdrop */
#lightbox {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

#lightbox img {
  max-width: 90%;
  max-height: 90%;
  border-radius: 12px;
  box-shadow: 0 0 15px #000;
}
p {
  color:rgb(3, 6, 8);
}

</style>

<script>
function openLightbox(src) {
  document.getElementById("lightbox-img").src = src;
  document.getElementById("lightbox").style.display = "flex";
}

function closeLightbox() {
  document.getElementById("lightbox").style.display = "none";
}
</script>


<div class="container">
    <h1>Détails de l'annonce</h1>

    <div class="info-box">
        <p><strong>Titre :</strong> <?= htmlspecialchars($listing['title']) ?></p>
        <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($listing['description'])) ?></p>
        <p><strong>Prix :</strong> <?= number_format($listing['price'], 0, ',', ' ') ?> FCFA</p>
        <p><strong>Négociable :</strong> <?= $listing['is_negotiable'] ? 'Oui' : 'Non' ?></p>
        <p><strong>Type :</strong> <?= ucfirst($listing['type']) ?></p>
        <p><strong>Condition :</strong> <?= $listing['conditions'] ?></p>
        <p><strong>Statut :</strong> <?= ucfirst($listing['status']) ?></p>
        <p><strong>Vendeur :</strong> <?= htmlspecialchars($listing['username']) ?></p>
        <p><strong>Catégorie :</strong> <?= htmlspecialchars($listing['category_name']) ?></p>
        <p><strong>Localisation :</strong> <?= htmlspecialchars($listing['location_name']) ?></p>
        <p><strong>Date de création :</strong> <?= $listing['creation_date'] ?></p>
    </div>
    <div class="image-gallery">
        <?php
        $stmt = $conn->prepare("SELECT image_path FROM listing_images WHERE listing_id = ?");
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($img = $result->fetch_assoc()) {
            echo '<img src="../images/annonces/' . htmlspecialchars($img['image_path']) . '" alt="Image annonce" onclick="openLightbox(this.src)">';
        }
        ?>
        </div>

        <!-- Lightbox -->
        <div id="lightbox" onclick="closeLightbox()">
        <img id="lightbox-img" src="" alt="Aperçu annonce">
    </div>


    
</div>
