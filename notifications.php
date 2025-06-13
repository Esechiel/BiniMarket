<?php
// notifications.php
session_start();
require_once 'php/db.php';
require_once 'php/add_notification.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

//compte le nombre de notif non lu
$sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user']['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$unread_count = $res->fetch_assoc()['unread_count'];

$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY creation_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$notifications = [];
while ($row = $res->fetch_assoc()) {
    $notifications[] = $row;
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/auth.css">
    <style>
        .notification-item {
        border: 1px solid #ccc;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        background-color: #f9f9f9;
        transition: background 0.3s;
        }
        .notification-item.unread {
            background-color: #e6f7ff;
            border-left: 5px solid #1850AA;
        }
        .notification-item.read {
            opacity: 0.8;
        }
        .notification-item:hover {
            background-color: #f0f8ff;
        }
        .notification-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
    </style>
</head>
<body>
<header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <i class="fas fa-store"><img src="images/icons/petit/shop.png"/></i>
                        <span>BiniMarket</span>
                    </a>
                </div>
                <div class="search-bar">
                    <form action="search.php" method="get">
                        <input type="text" name="q" placeholder="Rechercher un produit, service...">
                        <button type="submit"><i class="fas fa-search"><img src="images/icons/petit/loupe.png"/></i></button>
                    </form>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="categories.php">Catégories</a></li>
                        <li><a href="add-listing.php" class="btn-primary"><i class="fas fa-plus"><img src="images/icons/petit/icons8-plus-math-24.png"/></i> Publier</a></li>
                        <li><a href="messages.php"><i class="fas fa-comment"><img src="images/icons/petit/img-chat1.png"/></i></a></li>
                        <li><a href="auth.php" class="active"><i class="fas fa-user"><img src="images/icons/petit/img-user.png"/></i></a></li>
                        <?php if ($unread_count > 0): ?>
                            <li style="position: relative;">
                                <a href="notifications.php" title="Notifications">
                                    <img src="images/icons/petit/bell (2).png"="Notifications" style="width:24px; vertical-align: middle;" />
                                    <span style="
                                        position: absolute;
                                        top: -5px;
                                        right: -5px;
                                        background: red;
                                        color: white;
                                        border-radius: 50%;
                                        padding: 2px 6px;
                                        font-size: 12px;
                                        font-weight: bold;
                                    "><?= $unread_count ?></span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li><a href="php/logout.php" id="logout-link"> Déconnexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="categories.php">Catégories</a></li>
                <li><a href="add-listing.php">Publier une annonce</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="auth.php" class="active">Connexion / Inscription</a></li>
                <?php if ($unread_count > 0): ?>
                    <li style="position: relative;">
                        <a href="php/notifications.php" title="Notifications">
                            <img src="images/icons/petit/bell (2).png"="Notifications" style="width:24px; vertical-align: middle;" />
                            <span style="
                                position: absolute;
                                top: -5px;
                                right: -5px;
                                background: red;
                                color: white;
                                border-radius: 50%;
                                padding: 2px 6px;
                                font-size: 12px;
                                font-weight: bold;
                            "><?= $unread_count ?></span>
                        </a>
                    </li>
                <?php else: ?>
                    <li><a href="php/logout.php" id="logout-link"> Déconnexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <h1>Vos notifications</h1>
    <?php if (count($notifications) === 0): ?>
        <p>Aucune notification.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-item <?php echo $notif['is_read'] ? 'read' : 'unread'; ?>">
                    <a href="php/notification-redirect.php?id=<?php echo $notif['notification_id']; ?>" class="notification-link">
                        <div class="notif-content">
                            <p><?php echo htmlspecialchars($notif['content']); ?></p>
                            <small><?php echo date('d/m/Y H:i', strtotime($notif['creation_date'])); ?></small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-store"><img src="images/icons/petit/shop.png"/></i>
                    <span>BiniMarket</span>
                    <p>La plateforme de mini-commerce entre étudiants et résidents de Bini-Dang.</p>
                </div>
                <div class="footer-links">
                    <h3>Liens utiles</h3>
                    <ul>
                        <li><a href="utiles/about.html">À propos</a></li>
                        <li><a href="utiles/terms.html">Conditions d'utilisation</a></li>
                        <li><a href="utiles/privacy.html">Politique de confidentialité</a></li>
                        <li><a href="utiles/contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact</h3>
                    <p><i class="fas fa-envelope"><img src="images/icons/petit/icons8-message-24.png"/></i> contact@binimarket.com</p>
                    <p><i class="fas fa-phone"><img src="images/icons/petit/icons8-phone-24.png"/></i> +237 6XX XXX XXX</p>
                    <p><i class="fas fa-map-marker-alt"><img src="images/icons/petit/icons8-marqueur-24.png"/></i> Campus de Bini-Dang</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 BiniMarket. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
