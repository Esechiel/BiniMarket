<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'admin') {
    // Redirection si non connecté ou mauvais rôle
    header("Location: ../auth.php?error=Accès refusé");
    exit;
}
require_once '../php/db.php'; // connexion à la base

// Suppression d'une annonce
if (isset($_POST['delete_listing_id'])) {
    $listing_id = intval($_POST['delete_listing_id']);

    // Supprimer d'abord les images associées
    $stmt_images = $conn->prepare("DELETE FROM listing_images WHERE listing_id = ?");
    $stmt_images->bind_param("i", $listing_id);
    $stmt_images->execute();
    $stmt_images->close();

    // Ensuite supprimer l'annonce
    $stmt_listing = $conn->prepare("DELETE FROM listings WHERE listing_id = ?");
    $stmt_listing->bind_param("i", $listing_id);
    $stmt_listing->execute();
    $stmt_listing->close();
}

// Blocage/déblocage d'une annonce
if (isset($_POST['toggle_listing_id'])) {
    $listing_id = intval($_POST['toggle_listing_id']);

    // Récupérer le statut actuel
    $stmt_get = $conn->prepare("SELECT status FROM listings WHERE listing_id = ?");
    $stmt_get->bind_param("i", $listing_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $listing = $result->fetch_assoc();
    $stmt_get->close();

    if ($listing) {
        $new_status = $listing['status'] === 'active' ? 'inactive' : 'active';

        $stmt_toggle = $conn->prepare("UPDATE listings SET status = ? WHERE listing_id = ?");
        $stmt_toggle->bind_param("si", $new_status, $listing_id);
        $stmt_toggle->execute();
        $stmt_toggle->close();
    }
}
?>

<?php
session_start();
require_once '../php/db.php';

// Initialisation des filtres
$typeFilter = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

// Construction dynamique de la requête SQL
$query = "SELECT l.*, u.username, (SELECT image_path FROM listing_images WHERE listing_id = l.listing_id AND is_primary = 1 LIMIT 1) AS image_path FROM listings l JOIN users u ON l.vendeur_id = u.user_id WHERE 1";
$params = [];

if (!empty($typeFilter)) {
    $query .= " AND l.type = ?";
    $params[] = $typeFilter;
}
if (!empty($statusFilter)) {
    $query .= " AND l.status = ?";
    $params[] = $statusFilter;
}
if (!empty($search)) {
    $query .= " AND l.title LIKE ?";
    $params[] = "%$search%";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$listings = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Annonces</title>
    <link rel="stylesheet" href="../styles/admin.css">
    <style>
          
        
        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form input,
        .filter-form select {
            padding: 8px;
            margin-right: 10px;
            border-radius: 5px;
            border: none;
        }
        h1{
            color: #3498db;
        }


        .main-content {
            margin-left: 250px;
            padding: 2rem;
            background-color: #f9f9f9;
            min-height: 100vh;
        }

        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }
        .btn-add {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            float: right;
            margin-bottom: 10px;
        }

        .btn-add:hover {
            background-color: #293c5d;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        .listing-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
        }
        .btn {
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
            color: white;
            font-size: 14px;
            display: inline-block;
        }

        .btn-block { background-color: #f39c12; }
        .btn-unblock { background-color: #2ecc71; }
        .btn-delete { background-color: #e74c3c; }
        .btn-view { background-color: #3498db; }

        .btn:hover {
            opacity: 0.85;
        }
    </style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
    <h1>Gestion des annonces</h1>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Rechercher un titre..." value="<?= htmlspecialchars($search) ?>">
        <select name="type">
            <option value="">Tous les types</option>
            <option value="product" <?= $typeFilter === 'product' ? 'selected' : '' ?>>Produit</option>
            <option value="service" <?= $typeFilter === 'service' ? 'selected' : '' ?>>Service</option>
        </select>
        <select name="status">
            <option value="">Tous les statuts</option>
            <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="vendu" <?= $statusFilter === 'vendu' ? 'selected' : '' ?>>Vendu</option>
            <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactif</option>
            <option value="rejeté" <?= $statusFilter === 'rejeté' ? 'selected' : '' ?>>Rejeté</option>
            <option value="supprimé" <?= $statusFilter === 'supprimé' ? 'selected' : '' ?>>Supprimé</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <table class="listing-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Prix</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Vendeur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($listings as $listing): ?>
            <tr>
                <td><img src="../images/annonces/<?= $listing['image_path'] ?? 'images/no-image.png' ?>" class="listing-image"></td>
                <td><?= htmlspecialchars($listing['title']) ?></td>
                <td><?= number_format($listing['price'], 2) ?> FCFA</td>
                <td><?= $listing['type'] ?></td>
                <td><?= $listing['status'] ?></td>
                <td><?= htmlspecialchars($listing['username']) ?></td>
                <td class="actions">
                    <form method="post" action="">
                        <input type="hidden" name="toggle_listing_id" value="<?= $listing['listing_id'] ?>">
                        <button type="submit" class="btn btn-block">
                            <?= $listing['status'] === 'active' ? 'Bloquer' : 'Activer' ?>
                        </button>
                    </form>
                    <form method="post" action="" onsubmit="return confirm('Supprimer cette annonce ?')">
                        <input type="hidden" name="delete_listing_id" value="<?= $listing['listing_id'] ?>">
                        <button type="submit" class="btn btn-delete">Supprimer</button>
                    </form>
                    <a href="view_listing.php?id=<?= $listing['listing_id'] ?>" class="btn btn-view">Voir</a>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
