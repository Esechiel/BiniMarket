<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    die("Accès refusé");
}

$user_id = $_SESSION['user']['user_id'];

// Sécuriser et récupérer les données du formulaire
$category = intval($_POST['category']);
$location = intval($_POST['listingLocation']);
$condition = mysqli_real_escape_string($conn, $_POST['condition']);
$status = mysqli_real_escape_string($conn, $_POST['status']);
$type = mysqli_real_escape_string($conn, $_POST['listingType']);
$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$price = floatval($_POST['price']);
$negotiable = isset($_POST['negotiable']) ? 1 : 0;

if(isset($_SESSION['listing_id'])){
    $listing_id = intval($_SESSION['listing_id']);
     //modifier l'annonce
    $sql = "UPDATE listings SET 
        title = '$title',
        description = '$description',
        price = '$price',
        category_id = '$category',
        location_id = '$location',
        type = '$type',
        conditions = " . ($condition ? "'$condition'" : "NULL") . ",
        is_negotiable = '$negotiable',
        last_update_date = NOW()
        WHERE listing_id = $listing_id";
    
}else{
    // Insertion dans listings
    $sql = "INSERT INTO listings (vendeur_id, category_id, location_id, title, description, price, is_negotiable, type, conditions, status)
        VALUES ('$user_id', '$category', '$location', '$title', '$description', '$price', '$negotiable', '$type', '$condition', '$status')";
}

if (mysqli_query($conn, $sql)) {
    $listing_id = mysqli_insert_id($conn);

    // Gestion des images
    $images = ['img2', 'img3'];
    $upload_dir = "../images/annonces/";

    $input_name1 ='img1';
    if (isset($_FILES[$input_name1]) && $_FILES[$input_name1]['error'] === 0) {
        $ext = pathinfo($_FILES[$input_name1]['name'], PATHINFO_EXTENSION);
        $filename = uniqid("img_") . "." . $ext;
        $target = $upload_dir . $filename;
        $is_primary = 1;
        if (move_uploaded_file($_FILES[$input_name1]['tmp_name'], $target)) {
            $safe_path = mysqli_real_escape_string($conn, $filename);
            if(isset($_SESSION['listing_id'])){
                $listing_id = intval($_SESSION['listing_id']);
                mysqli_query($conn, "UPDATE listing_images SET image_path = '$safe_path', upload_date = NOW() WHERE listing_id = '$listing_id'AND is_primary = '$is_primary'");
            }else{
                mysqli_query($conn, "INSERT INTO listing_images (listing_id, image_path, is_primary) VALUES ('$listing_id', '$safe_path', '$is_primary')");
            }
        }
    }

    foreach ($images as $input_name) {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === 0) {
            $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
            $filename = uniqid("img_") . "." . $ext;
            $target = $upload_dir . $filename;

            if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target)) {
                $safe_path = mysqli_real_escape_string($conn, $filename);
                if(isset($_SESSION['listing_id'])){
                    $listing_id = intval($_SESSION['listing_id']);
                    mysqli_query($conn, "UPDATE listing_images SET image_path = '$safe_path', upload_date = NOW() WHERE listing_id = '$listing_id'");
                }else{
                    mysqli_query($conn, "INSERT INTO listing_images (listing_id, image_path) VALUES ('$listing_id', '$safe_path')");
                }
            }
        }
    }
    if (isset($_SESSION['listing_id'])) {
        header("Location: ../profile.php?success=Annonce modifiée");
        $_SESSION['listing_id'] = '';
    } else {
        header("Location: ../profile.php?success=Annonce ajoutée");
    }
    exit;
    
} else {
    echo "Erreur SQL : " . mysqli_error($conn);
}
?>
