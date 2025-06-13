<?php
    session_start();
    if (isset($_SESSION['user']['user_id']) && $_SESSION['user']['grade'] == 'user') {
        header("Location: profile.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion / Inscription - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
    <!-- En-tête -->
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
                        <?php if(isset($_SESSION['user'])){
                            echo "<li><a href='php/logout.php' id='logout-link'> Déconnexion</a></li>";
                        } ?>
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
                <?php if(isset($_SESSION['user'])){
                            echo "<li><a href='php/logout.php' id='logout-link'> Déconnexion</a></li>";
                        } ?>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="auth-container">
                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">Connexion</button>
                    <button class="auth-tab" data-tab="register">Inscription</button>
                </div>

                <div class="auth-content">
                    <!-- Formulaire de connexion -->
                    <div class="auth-form active" id="loginForm">
                        <h2>Connexion</h2>
                        <form action="php/login.php" method="POST" id="idform_con">
                            <div class="form-group">
                                <label for="loginEmail">Email</label>
                                <input type="email" id="email_con" name="email_con" required>
                            </div>
                            <div class="form-group">
                                <label for="loginPassword">Mot de passe</label>
                                <input type="password" id="pass_con" name="pass_con" required>
                            </div>
                            <div class="form-group-inline">
                                <div>
                                    <input type="checkbox" id="rememberMe" name="choixmemory">
                                    <label for="rememberMe">Se souvenir de moi</label>
                                </div>
                                <a href="#" class="forgot-password">Mot de passe oublié?</a>
                            </div>
                            <button type="submit" class="btn-primary" name="btn_con">Se connecter</button>
                        </form>
                    </div>

                    <!-- Formulaire d'inscription -->
                    <div class="auth-form" id="registerForm">
                        <h2>Inscription</h2>
                        <form action="php/register.php" method="POST" id="idform">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">Pseudo</label>
                                    <input type="text" id="pseudo" name="pseudo" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="registerEmail">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Téléphone</label>
                                <div class="phone">
                                    <span class="codes">+237</span>
                                    <input type="tel" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="registerPassword">Mot de passe</label>
                                <input type="password" id="pass" name="pass" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirmer le mot de passe</label>
                                <input type="password" id="confirmPass" name="confirmPass" required>
                            </div>
                            <div class="form-group">
                                <label for="location">Localisation</label>
                                <select id="location" name="location" required>
                                    <option value="">Sélectionnez votre localisation</option>
                                    <option value="1">Bini Carefour Anta-Diop</option>
                                    <option value="2">Bini Guéritte</option>
                                    <option value="3">Bini Cité U</option>
                                    <option value="4">Bini Hotel Pakem</option>
                                    <option value="5">Bini Houro-kessoum</option>
                                    <option value="6">Dang Borongo</option>
                                    <option value="7">Dang Gadabidou</option>
                                    <option value="8">Gada Dang</option>
                                    <option value="9">Marché Dang</option>
                                    <option value="10">Dang Total</option>
                                    <option value="11">Dang Tradex</option>
                                    <option value="12">Dang Sous-prefecture</option>
                                    <option value="13">Autre</option>
                                </select>
                            </div>
                            <div class="form-group-inline">
                                <div>
                                    <input type="checkbox" id="termsAgreement" name="choixpolitic" required>
                                    <label for="termsAgreement">J'accepte les <a href="terms.html">conditions d'utilisation</a></label>
                                </div>
                            </div>
                            <button type="submit" class="btn-primary" name="btn_send">S'inscrire</button>
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
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const logoutLink = document.getElementById('logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', function (e) {
                    const confirmed = confirm("Voulez-vous vraiment vous déconnecter ?");
                    if (!confirmed) {
                        e.preventDefault(); // Empêche la redirection vers logout.php
                    }
                });
            }
        }); 
    </script>
    <script src="js/common.js"></script>
    <script src="js/auth.js"></script>
</body>
</html>