<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: ../auth.php");
    exit;
}

if (isset($_POST['listing_id'], $_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['listingLocation'])) {
    $user_id = $_SESSION['user']['user_id'];
    $listing_id = intval($_POST['listing_id']);

    // Sécurité : vérifier que l'annonce appartient à l'utilisateur
    $check = mysqli_query($conn, "SELECT vendeur_id FROM listings WHERE listing_id = $listing_id");
    $row = mysqli_fetch_assoc($check);

    if (!$row || $row['vendeur_id'] != $user_id) {
        header("Location: ../profile.php?error=Accès refusé");
        exit;
    }

    // Nettoyage des champs
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category = intval($_POST['category']);
    $location = intval($_POST['listingLocation']);
    $condition = $_POST['condition'] ?? null;
    $negotiable = isset($_POST['negotiable']) ? 1 : 0;

    $sql = "UPDATE listings SET 
        title = '$title',
        description = '$description',
        price = '$price',
        category_id = '$category',
        location_id = '$location',
        conditions = " . ($condition ? "'$condition'" : "NULL") . ",
        is_negotiable = '$negotiable',
        last_update_date = NOW()
        WHERE listing_id = $listing_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../profile.php?success=Annonce modifiée");
    } else {
        header("Location: ../profile.php?error=Erreur lors de la modification");
    }
}
?>
