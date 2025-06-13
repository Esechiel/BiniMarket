<?php
require_once 'php/db.php';
session_start();
if(!isset($_SESSION['user'])){
    header("Location: auth.php");
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Identifiant invalide.</p>";
    exit;
}

$listing_id = (int) $_GET['id']; // Cast en entier pour éviter les injections


// Requête améliorée avec jointures pour récupérer toutes les infos utiles
$sql = "SELECT l.*, c.name AS category_name, u.username, u.user_id, u.phone_number, loc.name AS location_name
        FROM listings l
        JOIN categories c ON l.category_id = c.category_id
        JOIN users u ON l.vendeur_id = u.user_id
        JOIN locations loc ON l.location_id = loc.location_id
        WHERE l.listing_id = $listing_id";

$res = mysqli_query($conn, $sql);
if (!$res || mysqli_num_rows($res) == 0) {
    echo "<p>Annonce introuvable.</p>";
    exit;
}

$listing = mysqli_fetch_assoc($res);

// Récupération des images
$listing_images = [];
$img_sql = "SELECT image_path FROM listing_images WHERE listing_id = $listing_id";
$img_res = mysqli_query($conn, $img_sql);
while ($row = mysqli_fetch_assoc($img_res)) {
    $listing_images[] = $row['image_path'];
}

//Compte le nombre de vue

if ($listing_id) {
    // Empêche le comptage multiple dans la même session
    $viewed_key = 'viewed_listing_' . $listing_id;

    if (!isset($_SESSION[$viewed_key])) {
        // Mise à jour du nombre de vues
        $update = $conn->prepare("UPDATE listings SET views_count = views_count + 1 WHERE listing_id = ?");
        $update->bind_param("i", $listing_id);
        $update->execute();
        $update->close();

        // Marquer comme vu pour cette session
        $_SESSION[$viewed_key] = true;
    }
}

//compte le nombre de notif non lu
$sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user']['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$unread_count = $res->fetch_assoc()['unread_count'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du produit - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/product.css">
</head>
<body>
    <!-- En-tête -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <i class="fas fa-store"><img src="images/icons/petit/shop.png"/></i>
                        <span>BiniMarket</span>
                    </a>
                </div>
                <div class="search-bar">
                    <form action="search.php" method="get">
                        <input type="text" name="q" placeholder="Rechercher un produit, service...">
                        <button type="submit"><i class="fas fa-search"><img src="images/icons/petit/loupe.png"/></i></button>
                    </form>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="categories.php">Catégories</a></li>
                        <li><a href="add-listing.php" class="btn-primary"><i class="fas fa-plus"><img src="images/icons/petit/icons8-plus-math-24.png"/></i> Publier</a></li>
                        <li><a href="messages.php"><i class="fas fa-comment"><img src="images/icons/petit/img-chat1.png"/></i></a></li>
                        <li><a href="auth.php"><i class="fas fa-user"><img src="images/icons/petit/img-user1.png"/></i></a></li>
                        <?php if(isset($_SESSION['user'])){
                            echo "<li><a href='php/logout.php' id='logout-link'> Déconnexion</a></li>";
                        } ?>
                    </ul>
                </nav>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="categories.php">Catégories</a></li>
                <li><a href="add-listing.php">Publier une annonce</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="auth.php">Connexion / Inscription</a></li>
                <?php if(isset($_SESSION['user'])){
                            echo "<li><a href='php/logout.php' id='logout-link'> Déconnexion</a></li>";
                        } ?>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <!-- Fil d'Ariane -->
            <div class="breadcrumb">
                <a href="index.php">Accueil</a> /
                <a href="categories.php">Catégories</a> /
                <a href="search.php?category[]=<?= urlencode($listing['category_name']) ?>"><?= htmlspecialchars($listing['category_name']) ?></a> /
                <span class="current"><?= htmlspecialchars($listing['title']) ?></span>
            </div>

            <div class="product-details loaded">
                <div class="product-images">
                    <div class="product-main-image">
                    <img src="<?= isset($listing_images[0]) ? 'images/annonces/' . $listing_images[0] : 'images/icons/petit/img-image.png' ?>" alt="Image principale">
                    </div>
                    <div class="product-thumbs">
                        <?php foreach ($listing_images as $img): ?>
                            <div class="thumb">
                                <img src="images/annonces/<?= htmlspecialchars($img) ?>" alt="Image secondaire">
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($listing_images)): ?>
                            <div class="thumb">
                                <img src="images/icons/petit/img-image.png" alt="Image par défaut">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-info">
                    <div class="product-header">
                        <span class="product-category"><?= htmlspecialchars($listing['category_name']) ?></span>
                        <span class="product-date">Publié le <?= date('d/m/Y', strtotime($listing['creation_date'])) ?></span>
                    </div>
                    <h1 class="product-title"><?= htmlspecialchars($listing['title']) ?></h1>
                    <div class="product-price"><?= number_format($listing['price'], 0, ',', ' ') ?> FCFA</div>

                    <div class="product-description">
                        <p><?= nl2br(htmlspecialchars($listing['description'])) ?></p>
                    </div>

                    <div class="product-location">
                        <strong><img src="images/icons/petit/icons8-marqueur-24.png"/></strong> <?= htmlspecialchars($listing['location_name']) ?>
                    </div>

                    <div class="seller-info">
                        <h3>Vendeur : <?= htmlspecialchars($listing['username']) ?></h3>
                        <a href="profile.php?id=<?= $listing['user_id'] ?>" class="btn-secondary">Voir le profil</a>
                    </div>

                    <div class="product-actions">
                        <a href="php/start-conversation.php?to=<?= $listing['user_id'] ?>&ida=<?= $listing_id ?>" class="btn-primary">Message</a>
                        <a href="tel:<?= $listing['phone_number'] ?>" class="btn-secondary">Contacter</a>
                    </div><br>
                    <?php if($_SESSION['user']['user_id'] != $listing['user_id'] ): ?>
                        <a href="payement.php?listing_id=<?= $listing['listing_id'] ?>&price=<?= $listing['price'] ?>" class="btn-primary"><img src="images/icons/petit/bell (2).png"/>Payer</a>
                    <?php endif ?>
                </div>
            </div>
            <!-- Produits similaires -->
            <section class="similar-products">
                <h2>Produits similaires</h2>
                <div class="products-grid" id="similarProducts">
                    <!-- Produits similaires chargés via JavaScript -->
                </div>
            </section>
        </div>
    </main>
    <div id="loading-state" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Chargement des détails du produit...</p>
    </div> 
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-store"><img src="images/icons/petit/shop.png"/></i>
                    <span>BiniMarket</span>
                    <p>La plateforme de mini-commerce entre étudiants et résidents de Bini-Dang.</p>
                </div>
                <div class="footer-links">
                    <h3>Liens utiles</h3>
                    <ul>
                        <li><a href="about.html">À propos</a></li>
                        <li><a href="terms.html">Conditions d'utilisation</a></li>
                        <li><a href="privacy.html">Politique de confidentialité</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact</h3>
                    <p><i class="fas fa-envelope"><img src="images/icons/petit/icons8-message-24.png"/></i> contact@binimarket.com</p>
                    <p><i class="fas fa-phone"><img src="images/icons/petit/icons8-phone-24.png"/></i> +237 6XX XXX XXX</p>
                    <p><i class="fas fa-map-marker-alt"><img src="images/icons/petit/icons8-marqueur-24.png"/></i> Campus de Bini-Dang</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 BiniMarket. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Modal de contact -->
    <div class="modal" id="contactModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Contacter le vendeur</h2>
            <form id="contactForm">
                <div class="form-group">
                    <label for="messageContent">Message</label>
                    <textarea id="messageContent" rows="5" placeholder="Bonjour, je suis intéressé par votre MacBook Pro. Est-il toujours disponible ?"></textarea>
                </div>
                <button type="submit" class="btn-primary">Envoyer</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainImage = document.querySelector('.product-main-image img');
            const thumbs = document.querySelectorAll('.product-thumbs .thumb img');

            thumbs.forEach(thumb => {
                thumb.addEventListener('click', function () {
                    if (mainImage && this.src) {
                        mainImage.src = this.src;

                        // Optionnel : activer l'effet visuel sur la miniature
                        thumbs.forEach(t => t.parentElement.classList.remove('active'));
                        this.parentElement.classList.add('active');
                    }
                });
            });
            const logoutLink = document.getElementById('logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', function (e) {
                    const confirmed = confirm("Voulez-vous vraiment vous déconnecter ?");
                    if (!confirmed) {
                        e.preventDefault(); // Empêche la redirection vers logout.php
                    }
                });
            }
        });
    </script>

    <script src="js/common.js"></script>
    <script src="js/products.js"></script>
</body>
</html>