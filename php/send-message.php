<?php
session_start();
require 'db.php';
require_once 'add_notification.php';

$conversationId = (int) $_POST['conversation_id'];
$senderId = $_SESSION['user']['user_id'];
$content = trim($_POST['content']);
$receiver_id = $_POST['receiver'];

if (!empty($content)) {
    addNotification($conn, $receiver_id, 'message', "Vous avez reçu un message de " . $_SESSION['user']['username'], $conversationId);
    $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $conversationId, $senderId, $content);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE conversations SET last_activity = NOW() WHERE conversation_id = ?");
    $stmt->bind_param("i", $conversationId);
    $stmt->execute();
}

header("Location: ../messages.php?conversation_id=$conversationId");
exit;
?>
