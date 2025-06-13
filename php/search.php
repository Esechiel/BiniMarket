<?php
require_once 'db.php';
$q = mysqli_real_escape_string($conn, $_GET['q'] ?? '');
$res = mysqli_query($conn, "SELECT * FROM listings WHERE title LIKE '%$q%' OR description LIKE '%$q%'");
while ($row = mysqli_fetch_assoc($res)) {
    echo "<div><h3>{$row['title']}</h3><p>{$row['description']}</p><strong>{$row['price']} FCFA</strong></div>";
}
?>