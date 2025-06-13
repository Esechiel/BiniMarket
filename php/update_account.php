<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];
$username = trim($_POST['username'] ?? '');
$phone1 = trim($_POST['phone1'] ?? '');
$phone2 = trim($_POST['phone2'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$location_id = intval($_POST['location'] ?? 0);

$errors = [];
if (empty($username)) $errors[] = "Le pseudo est obligatoire.";
if (empty($phone)) $errors[] = "Le numéro de téléphone est obligatoire.";
if ($location_id === 0) $errors[] = "La localisation est obligatoire.";

$profile_image_path = null;

// 📁 Gestion du fichier uploadé
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['profile_image']['tmp_name'];
    $original_name = basename($_FILES['profile_image']['name']);
    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($extension, $allowed_ext)) {
        $new_filename = "user_" . $user_id . "_" . time() . "." . $extension;
        $destination = "../images/profils/" . $new_filename;

        if (move_uploaded_file($tmp_name, $destination)) {
            $profile_image_path = "images/profils/" . $new_filename;
        } else {
            $errors[] = "Échec du téléchargement de l'image.";
        }
    } else {
        $errors[] = "Format de fichier non supporté (jpg, jpeg, png, webp uniquement).";
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header("Location: ../profile.php?tab=settings");
    exit();
}

// 🔁 Construire la requête selon s’il y a une image
if ($profile_image_path) {
    $sql = "UPDATE users 
            SET username = ?, phone_number = ?, bio = ?, location_id = ?, profile_picture = ?
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $username, $phone, $bio, $location_id, $profile_image_path, $user_id);
} else {
    $sql = "UPDATE users 
            SET username = ?, phone_number = ?, bio = ?, location_id = ?
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $username, $phone, $bio, $location_id, $user_id);
}

if ($stmt->execute()) {
    // ✅ Mise à jour de la session
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['phone_number'] = $phone;
    $_SESSION['user']['bio'] = $bio;
    $_SESSION['user']['location_id'] = $location_id;
    if ($profile_image_path) {
        $_SESSION['user']['profile_picture'] = $profile_image_path;
    }

    $_SESSION['success'] = "Compte mis à jour avec succès.";
} else {
    $_SESSION['error'] = "Erreur lors de la mise à jour.";
}

header("Location: ../profile.php?tab=settings");
exit();
?>