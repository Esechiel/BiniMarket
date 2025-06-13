<?php
require_once 'db.php';
function addNotification($conn, $user_id, $type, $content, $related_id = null) {
    $sql = "INSERT INTO notifications (user_id, type, content, related_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $user_id, $type, $content, $related_id);
    return $stmt->execute();
}
?>