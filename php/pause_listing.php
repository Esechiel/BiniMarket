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

    $sql = "SELECT status FROM listings WHERE listing_id = $listing_id AND vendeur_id = $user_id";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);

    if (!$row) {
        header("Location: ../profile.php?error=Annonce introuvable");
        exit;
    }

    $new_status = ($row['status'] === 'active') ? 'inactive' : 'active';
    $update = "UPDATE listings SET status = '$new_status', last_update_date = NOW() WHERE listing_id = $listing_id";
    mysqli_query($conn, $update);

    header("Location: ../profile.php?success=Statut mis à jour");
}
?>
