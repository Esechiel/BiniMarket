<?php
require_once 'db.php';
$res = mysqli_query($conn, "SELECT * FROM listings WHERE category='services' ORDER BY id DESC LIMIT 10");
while ($row = mysqli_fetch_assoc($res)) {
    echo "<div class='service-card'><h3>{$row['title']}</h3><p>{$row['description']}</p></div>";
}
?>