<?php
session_start();

// L'utilisateur est-il connecté ?
if (!isset($_SESSION['user'])) {
    header("Location: ../auth.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'];

    if ($mode === 'admin') {
        // Vérifie le rôle
        if ($_SESSION['user']['grade'] === 'super_admin') {
            header("Location: ../super_admin/dashboard.php");
        } elseif ($_SESSION['user']['grade'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            // Un utilisateur normal ne peut pas accéder à l'espace admin
            header("Location: ../profile.php");
        }
    } else {
        // Mode utilisateur
        header("Location: ../profile.php");
    }
    exit;
}
?>
