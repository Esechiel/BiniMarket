<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix - BiniMarket</title>
    <link rel="stylesheet" href="../styles/common.css">
    <link rel="stylesheet" href="../styles/auth.css">
</head>
<body>
    <!-- En-tête -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="../index.php">
                        <i class="fas fa-store"><img src="../images/icons/petit/shop.png"/></i>
                        <span>BiniMarket</span>
                    </a>
                </div>
                <div class="search-bar">
                    <form action="../search.php" method="get">
                        <input type="text" name="q" placeholder="Rechercher un produit, service...">
                        <button type="submit"><i class="fas fa-search"><img src="../images/icons/petit/loupe.png"/></i></button>
                    </form>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="../categories.php">Catégories</a></li>
                        <li><a href="../add-listing.php" class="btn-primary"><i class="fas fa-plus"><img src="../images/icons/petit/icons8-plus-math-24.png"/></i> Publier</a></li>
                        <li><a href="../messages.php"><i class="fas fa-comment"><img src="../images/icons/petit/img-chat1.png"/></i></a></li>
                        <li><a href="../auth.php" class="active"><i class="fas fa-user"><img src="../images/icons/petit/img-user.png"/></i></a></li>
                    </ul>
                </nav>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="../categories.php">Catégories</a></li>
                <li><a href="../add-listing.php">Publier une annonce</a></li>
                <li><a href="../messages.php">Messages</a></li>
                <li><a href="../auth.php" class="active">Connexion / Inscription</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="auth-container">
                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">Connexion</button>
                </div>

                <div class="auth-content">
                    <!-- Formulaire de connexion -->
                    <div class="auth-form active" id="loginForm">
                        <h2>Bienvenue, administrateur</h2>
                        <form action="redirect.php" method="POST" id="idform_con">
                            <div class="form-group">
                                <label for="loginEmail">Choisissez votre mode de connexion :</label>
                                <button type="submit" class="btn-primary" name="mode" value="admin">Administrateur</button>
                                <button type="submit" class="btn-secondary" name="mode" value="user">Utilisateur</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-store"><img src="../images/icons/petit/shop.png"/></i>
                    <span>BiniMarket</span>
                    <p>La plateforme de mini-commerce entre étudiants et résidents de Bini-Dang.</p>
                </div>
                <div class="footer-links">
                    <h3>Liens utiles</h3>
                    <ul>
                        <li><a href="../utiles/about.html">À propos</a></li>
                        <li><a href="../utiles/terms.html">Conditions d'utilisation</a></li>
                        <li><a href="../utiles/privacy.html">Politique de confidentialité</a></li>
                        <li><a href="../utiles/contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact</h3>
                    <p><i class="fas fa-envelope"><img src="../images/icons/petit/icons8-message-24.png"/></i> contact@binimarket.com</p>
                    <p><i class="fas fa-phone"><img src="../images/icons/petit/icons8-phone-24.png"/></i> +237 6XX XXX XXX</p>
                    <p><i class="fas fa-map-marker-alt"><img src="../images/icons/petit/icons8-marqueur-24.png"/></i> Campus de Bini-Dang</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 BiniMarket. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="../js/common.js"></script>
</body>
</html>
