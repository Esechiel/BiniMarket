<?php
session_start();
require_once 'db.php'; // connexion à la base
require_once 'add_notification.php'; // contient addNotification si besoin

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../profile.php");
    exit;
}

$notif_id = (int)$_GET['id'];
$user_id = $_SESSION['user']['user_id'];

// Marquer comme lue
$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
$stmt->bind_param("ii", $notif_id, $user_id);
$stmt->execute();

// Récupérer la notification
$stmt = $conn->prepare("SELECT * FROM notifications WHERE notification_id = ? AND user_id = ?");
$stmt->bind_param("ii", $notif_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: profile.php");
    exit;
}

$notif = $res->fetch_assoc();
$type = $notif['type'];
$related_id = $notif['related_id'];

// Redirection selon type
switch ($type) {
    case 'message':
        header("Location: ../messages.php?conversation_id=" . $related_id);
        break;

    case 'favori':
    case 'annonce_bloquee':
    case 'transaction':
        header("Location: ../profile.php?idtr=" . $related_id);
        break;

    case 'visite_profil':
        header("Location: ../profile.php?id=" . $related_id);
        break;

    case 'systeme':
    default:
        header("Location: ../profile.php");
        break;
}
exit;
?>
