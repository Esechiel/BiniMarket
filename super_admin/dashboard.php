<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'super_admin') {
    // Redirection si non connecté ou mauvais rôle
    header("Location: ../auth.php?error=Accès réservé au super administrateur");
    exit;
}


require_once '../php/db.php';

// Total utilisateurs (hors admin et super admin)
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE grade = 'user'");
$stmt->execute();
$stmt->bind_result($totalUsers);
$stmt->fetch();
$stmt->close();

// Total administrateurs
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE grade = 'admin'");
$stmt->execute();
$stmt->bind_result($totalAdmins);
$stmt->fetch();
$stmt->close();

// Total annonces
$stmt = $conn->prepare("SELECT COUNT(*) FROM listings");
$stmt->execute();
$stmt->bind_result($totalListings);
$stmt->fetch();
$stmt->close();

// Total annonces actives
$stmt = $conn->prepare("SELECT COUNT(*) FROM listings WHERE status = 'active'");
$stmt->execute();
$stmt->bind_result($totalActiveListings);
$stmt->fetch();
$stmt->close();

// Total catégories
$stmt = $conn->prepare("SELECT COUNT(*) FROM categories");
$stmt->execute();
$stmt->bind_result($totalCategories);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Super Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/sidebar_sa.php'; ?>

<div class="content">
    <h1 style="margin-bottom: 20px;">Tableau de bord Super Administrateur 👑</h1>
    
    <div class="dashboard-grid">
        <a href="users.php">
        <div class="card bg-primary">
            <i class="fas fa-users card-icon"></i>
            <div class="card-details">
                <h2><?= $totalUsers ?></h2>
                <p>Utilisateurs</p>
            </div>
        </div>
        </a>
        <a href="admins.php">
            <div class="card bg-dark">
            <i class="fas fa-user-shield card-icon"></i>
            <div class="card-details">
                <h2><?= $totalAdmins ?></h2>
                <p>Administrateurs</p>
            </div>
            </div>
        </a>
        <a href="annonces.php">
            <div class="card bg-info">
                <i class="fas fa-bullhorn card-icon"></i>
                <div class="card-details">
                    <h2><?= $totalListings ?></h2>
                    <p>Annonces</p>
                </div>
            </div>
        </a>
        <a href="annonces.php">
            <div class="card bg-success">
                <i class="fas fa-check-circle card-icon"></i>
                <div class="card-details">
                    <h2><?= $totalActiveListings ?></h2>
                    <p>Annonces actives</p>
                </div>
            </div>
        </a>
        <a href="categories.php">
            <div class="card bg-warning">
                <i class="fas fa-list card-icon"></i>
                <div class="card-details">
                    <h2><?= $totalCategories ?></h2>
                    <p>Catégories</p>
                </div>
            </div>
        </a>
        
    </div>
</div>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f9;
    }

    .content {
        margin-left: 230px; /* même marge que la sidebar */
        padding: 30px;
        min-height: 100vh;
    }

    h1 {
        color: #1a237e;
        margin-bottom: 30px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .card {
        background-color: #34495e;
        color: #fff;
        padding: 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-icon {
        font-size: 2.8rem;
        margin-right: 15px;
    }

    .card-details h2 {
        margin: 0;
        font-size: 2rem;
    }

    .card-details p {
        margin: 5px 0 0;
        font-size: 1rem;
    }

    .bg-primary { background-color: #1a237e; }
    .bg-dark { background-color: #2c3e50; }
    .bg-info { background-color: #2980b9; }
    .bg-success { background-color: #2e7d32; }
    .bg-warning { background-color: #e67e22; }

    @media (max-width: 768px) {
        .content {
            margin-left: 0;
            padding: 15px;
        }
    }
</style>

</body>
</html>
