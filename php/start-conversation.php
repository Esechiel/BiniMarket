<?php
session_start();
require 'db.php'; // doit définir $conn (objet mysqli)

$currentUserId = $_SESSION['user']['user_id'];
if (!isset($_GET['to']) || !is_numeric($_GET['to']) || !isset($_GET['ida']) || !is_numeric($_GET['ida'])) {
    echo "<p>Identifiant invalide.</p>";
    exit;
}
$receiverId = (int) $_GET['to'];
$listing_id = (int) $_GET['ida'];

// Vérifie si une conversation existe déjà entre les deux
$sql = "SELECT cp1.conversation_id
        FROM conversation_participants cp1
        JOIN conversation_participants cp2 ON cp1.conversation_id = cp2.conversation_id
        WHERE cp1.user_id = ? AND cp2.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $currentUserId, $receiverId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $conversationId = $row['conversation_id'];
} else {
    // Nouvelle conversation
    $conn->begin_transaction();

    $conn->query("INSERT INTO conversations (listing_id) VALUES ('$listing_id')");
    $conversationId = $conn->insert_id;

    $stmt = $conn->prepare("INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?)");
    $stmt->bind_param("iiii", $conversationId, $currentUserId, $conversationId, $receiverId);
    $stmt->execute();

    $conn->commit();
}

header("Location: ../messages.php?conversation_id=$conversationId");
exit;
?>
