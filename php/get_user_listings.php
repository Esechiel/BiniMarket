<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    exit("Non connecté");
}

$user_id = $_SESSION['user']['user_id'];
$status = $_GET['status'] ?? 'all';

$sql = "SELECT l.*, 
        (SELECT image_path FROM listing_images WHERE listing_id = l.listing_id LIMIT 1) as main_image 
        FROM listings l WHERE vendeur_id = $user_id";

if ($status !== 'all') {
    $sql .= " AND status = '$status'";
}

$sql .= " ORDER BY creation_date DESC";
$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {
    $img = $row['main_image'] ? "images/annonces/" . $row['main_image'] : "images/icons/petit/icons8-image-48.png";
    $price = number_format($row['price'], 0, ',', ' ') . " F";
    $badge = strtoupper($row['status']);
    echo "
    <div class='listing-card'>
        <div class='listing-image'>
            <img src='$img' class='image-placeholder' style='width:100%; height:140px; object-fit:cover;'/>
            <span class='listing-badge $badge'>$badge</span>
            <div class='listing-stats'>
                <span><img src='images/icons/petit/img-view.png'/> 0</span>
                <span><img src='images/icons/petit/img-heart.png'/> 0</span>
            </div>
        </div>
        <div class='listing-details'>
            <div class='listing-title-row'>
                <h3>{$row['title']}</h3>
                <span class='listing-price'>$price</span>
            </div>
            <p class='listing-description'>{$row['description']}</p>
            <div class='listing-footer'>
                <span class='listing-date'>" . date("d/m/Y", strtotime($row['creation_date'])) . "</span>
                <div class='listing-actions'>
                    <button class='action-btn edit' data-id='{$row['listing_id']}'><img src='images/icons/petit/editer (1).png'/></button>
                    <button class='action-btn pause' data-id='{$row['listing_id']}'><img src='images/icons/petit/img-pause.png'/></button>
                    <button class='action-btn delete' data-id='{$row['listing_id']}'><img src='images/icons/petit/img-delete.png'/></button>
                </div>
            </div>
        </div>
    </div>
    ";
}
?>
