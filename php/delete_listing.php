<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: ../auth.php");
    exit;
}

if (isset($_POST['listing_id'])) {
    $listing_id = intval($_POST['listing_id']);
    $user_id = $_SESSION['user']['user_id'];

    // On ne supprime pas physiquement mais on change le statut
    $sql = "DELETE FROM listings 
            WHERE listing_id = $listing_id AND vendeur_id = $user_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../profile.php?success=Annonce supprimée");
    } else {
        header("Location: ../profile.php?error=Erreur de suppression");
    }
}
?>
