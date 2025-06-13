<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth.php");
    exit;
}

require_once 'db.php'; // Fichier contenant la connexion $conn (MySQLi)
require_once 'add_notification.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_send'])) {
    $user_id = $_SESSION['user']['user_id'];
    $listing_id = intval($_POST['listing_id'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if ($listing_id <= 0 || $price <= 0 || empty($payment_method) || empty($phone)) {
        die("Données de paiement invalides.");
    }

    // Vérifier que l'annonce existe et récupérer le vendeur
    $stmt = mysqli_prepare($conn, "SELECT listing_id, vendeur_id FROM listings WHERE listing_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $listing_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $listing_id_check, $vendeur_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!$listing_id_check) {
        die("Annonce introuvable.");
    }

    // Vérifier si la méthode de paiement existe déjà pour ce user
    $stmt = mysqli_prepare($conn, "SELECT payment_method_id FROM payment_methods WHERE user_id = ? AND method_name = ?");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $payment_method);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $payment_method_id);
    $has_method = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Si la méthode n'existe pas, on l'ajoute
    if (!$has_method) {
        $stmt = mysqli_prepare($conn, "INSERT INTO payment_methods (user_id, method_name, is_default) VALUES (?, ?, 0)");
        mysqli_stmt_bind_param($stmt, "is", $user_id, $payment_method);
        mysqli_stmt_execute($stmt);
        $payment_method_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    }

    // Simulation de l'appel API fictif
    $transaction_ref = uniqid('TX_');
    $status = 'en attente';
    $success = true ;  //rand(0, 1) ? true : false;
    $status = $success ? 'completé' : 'refusé';

    // Enregistrement de la transaction
    $stmt = mysqli_prepare($conn, "INSERT INTO transactions 
        (listing_id, acheteur_id, vendeur_id, payment_method_id, amount, status, phone, transaction_ref)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiiidsss", $listing_id, $user_id, $vendeur_id, $payment_method_id, $price, $status, $phone, $transaction_ref);

    if (mysqli_stmt_execute($stmt)) {
        $transaction_id = mysqli_insert_id($conn);
        $ref = "TX_" . str_pad($transaction_id, 6, "0", STR_PAD_LEFT);
        $msg_type = $success ? "success=1" : "error=1";
        //notifications
        addNotification($conn, $user_id, 'transaction', "Votre transaction pour l’annonce \"$title\" a été $status.", $transaction_id);

        header("Location: ../payement.php?listing_id=$listing_id&price=$price&$msg_type&ref=$ref");
        exit;
    } else {
        die("Erreur lors de l'enregistrement de la transaction.");
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>