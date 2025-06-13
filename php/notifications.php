<?php
// notifications.php
session_start();
require_once 'db.php';
require_once 'NotificationManager.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth.html");
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$notifManager = new NotificationManager($conn);

$notifications = $notifManager->getNotificationsByUser($user_id);
$notifManager->markAllRead($user_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Notifications</title>
<style>
    body { font-family: Arial, sans-serif; }
    .notification-unread { font-weight: bold; }
    ul { list-style-type: none; padding: 0; }
    li { padding: 8px; border-bottom: 1px solid #ddd; }
    small { color: #666; font-size: 0.85em; }
</style>
</head>
<body>
<h1>Vos notifications</h1>
<?php if (count($notifications) === 0): ?>
    <p>Aucune notification.</p>
<?php else: ?>
    <ul>
        <?php foreach ($notifications as $notif): ?>
            <li class="<?= $notif['is_read'] ? '' : 'notification-unread' ?>">
                [<?= htmlspecialchars($notif['type']) ?>]
                <?= htmlspecialchars($notif['message']) ?>
                <br /><small><?= $notif['created_at'] ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<a href="index.php">← Retour à l'accueil</a>
</body>
</html>
