<?php
session_start();
require_once 'php/db.php';
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

// Récupération des données GET (nécessaire pour afficher le prix et ID dans les champs cachés)
$listing_id = intval($_GET['listing_id'] ?? 0);
$price = floatval($_GET['price'] ?? 0);

//compte le nombre de notif non lu
$sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user']['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$unread_count = $res->fetch_assoc()['unread_count'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/auth.css">
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

    <main>
        <div class="container">
            <div class="auth-container">
                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">Orange Money</button>
                    <button class="auth-tab" data-tab="register">Mobile Money</button>
                </div>

                <div class="auth-content">
                    <!-- Orange Money -->
                    <div class="auth-form active" id="loginForm">
                        <h2>Orange Money</h2>
                        <form action="php/payement-handler.php" method="POST">
                            <input type="hidden" name="listing_id" value="<?= $listing_id ?>">
                            <input type="hidden" name="payment_method" value="Orange Money">
                            <div class="form-group">
                                <label for="price1">Prix (FCFA)</label>
                                <div class="price-input">
                                    <div class="input-addon">
                                        <input type="number" id="price1" value="<?= $price ?>" disabled>
                                        <span class="currency">F</span>
                                        <input type="hidden" name="price" value="<?= $price ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone1">Téléphone</label>
                                <div style="display: flex; align-items: center;">
                                    <span class="codes">+237</span>
                                    <input type="tel" id="phone1" name="phone" required style="flex: 1;">
                                </div>
                            </div>
                            <button type="submit" class="btn-primary" name="btn_send">Confirmer</button>
                        </form>
                        
                    </div>

                    <!-- Mobile Money -->
                    <div class="auth-form" id="registerForm">
                        <h2>Mobile Money</h2>
                        <form action="php/payement-handler.php" method="POST">
                            <input type="hidden" name="listing_id" value="<?= $listing_id ?>">
                            <input type="hidden" name="payment_method" value="Mobile Money">
                            <div class="form-group">
                                <label for="price2">Prix (FCFA)</label>
                                <div class="price-input">
                                    <div class="input-addon">
                                        <input type="number" id="price2" value="<?= $price ?>" disabled>
                                        <span class="currency">F</span>
                                        <input type="hidden" name="price" value="<?= $price ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone2">Téléphone</label>
                                <div style="display: flex; align-items: center;">
                                    <span class="codes">+237</span>
                                    <input type="tel" id="phone2" name="phone" required style="flex: 1;">
                                </div>
                            </div>
                            <button type="submit" class="btn-primary" name="btn_send">Confirmer</button>
                        </form>
                    </div>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert success">
                            ✅ Paiement réussi ! Référence : <?= htmlspecialchars($_GET['ref']) ?>
                        </div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="alert error">
                            ❌ Échec du paiement. Référence : <?= htmlspecialchars($_GET['ref']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>


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
    
<script src="js/common.js"></script>
<script src="js/payement.js"></script>
</body>
</html>
