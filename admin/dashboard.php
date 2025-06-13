<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['grade'] !== 'admin') {
    // Redirection si non connecté ou mauvais rôle
    header("Location: ../auth.php?error=Accès refusé");
    exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../php/db.php';

include 'includes/sidebar.php';

// Récupération des statistiques
$totalUsers = $conn->query("SELECT COUNT(*) FROM users WHERE grade = 'user'")->fetch_row()[0];
$activeListings = $conn->query("SELECT COUNT(*) FROM listings WHERE status = 'active'")->fetch_row()[0];
$blockedListings = $conn->query("SELECT COUNT(*) FROM listings WHERE status = 'inactive' OR status = 'rejeté'")->fetch_row()[0];
?>

<div class="content">
    <h1 style="margin-bottom: 20px;">Tableau de bord Administrateur</h1>
    
    <div class="dashboard-grid">
        <div class="card bg-primary">
            <i class="fas fa-users card-icon"></i>
            <div class="card-details">
                <h2><?= $totalUsers ?></h2>
                <p>Utilisateurs</p>
            </div>
        </div>
        <div class="card bg-success">
            <i class="fas fa-bullhorn card-icon"></i>
            <div class="card-details">
                <h2><?= $activeListings ?></h2>
                <p>Annonces actives</p>
            </div>
        </div>
        <div class="card bg-warning">
            <i class="fas fa-ban card-icon"></i>
            <div class="card-details">
                <h2><?= $blockedListings ?></h2>
                <p>Annonces bloquées</p>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f9;
    }

    .content {
        margin-left: 230px; /* pour éviter le chevauchement */
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

    .bg-primary {
        background-color: #1a237e;
    }

    .bg-success {
        background-color: #2e7d32;
    }

    .bg-warning {
        background-color: #e67e22;
    }

    @media (max-width: 768px) {
        .content {
            margin-left: 0;
            padding: 15px;
        }
    }
</style>
