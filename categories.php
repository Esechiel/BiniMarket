<?php
    require_once "php/db.php";
    session_start();
    $sql1 = "SELECT * FROM categories ";
    $res1 = mysqli_query($conn, $sql1);
    $category = mysqli_fetch_all($res1, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/search.css">
    
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
                        <li><a href="categories.php" class="active">Catégories</a></li>
                        <li><a href="add-listing.php" class="btn-primary"><i class="fas fa-plus"><img src="images/icons/petit/icons8-plus-math-24.png"/></i> Publier</a></li>
                        <li><a href="messages.php"><i class="fas fa-comment"><img src="images/icons/petit/img-chat1.png"/></i></a></li>
                        <li><a href="auth.php"><i class="fas fa-user"><img src="images/icons/petit/img-user1.png"/></i></a></li>
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
                <li><a href="categories.html" class="active">Catégories</a></li>
                <li><a href="add-listing.html">Publier une annonce</a></li>
                <li><a href="messages.html">Messages</a></li>
                <li><a href="auth.php">Connexion / Inscription</a></li>
                <?php if(isset($_SESSION['user'])){
                            echo "<li><a href='php/logout.php' id='logout-link'> Déconnexion</a></li>";
                        } ?>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="categories-container">
                <h1>Explorez les catégories</h1>
                <p class="categories-subtitle">Trouvez exactement ce que vous cherchez parmi nos catégories</p>
                
                <div class="categories-grid">
                    <!-- Catégorie Électronique -->
                    <?php 
                        $style=[
                            'Électronique' => 'background: linear-gradient(to right, #4facfe, #00f2fe);',
                            'Livres'  => 'background: linear-gradient(to right, #6a11cb, #2575fc);',
                            'Vêtements' => 'background: linear-gradient(to right, #ff0844, #ffb199);',
                            'Nourriture' => 'background: linear-gradient(to right, #f6d365, #fda085);',
                            'Services' => 'background: linear-gradient(to right, #667eea, #764ba2);',
                            'Mobilier' => 'background: linear-gradient(to right, #ff9a9e, #fad0c4);',
                            'Transport' => 'background: linear-gradient(to right, #48c6ef, #6f86d6);',
                            'Divers' => 'background: linear-gradient(to right, #a1c4fd, #c2e9fb);'
                        ];
                        foreach($category as $cat): 
                            $name = $cat['name'];
                            if(!isset($style[$name])){
                                $st = 'background: linear-gradient(to right, #ff0844, #ffb199);';
                            }else {
                                $st = $style[$name];
                            }
                            if($cat['name']=='Divers'){
                                $dernier = $cat;
                                $st = $style[$name];
                                continue;
                            }
                    ?>
                    <a href="search.php?category[]=<?= $cat['name'] ?>" class="category-card">
                        <div class="category-header" style="<?= $st ?>">
                            <i class="fas fa-laptop"><img src="images/icons/petit/<?= $cat['icon2'] ?>"/></i>
                        </div>
                        <div class="category-content">
                            <h2><?= $cat['name'] ?></h2>
                            <p><?= $cat['description'] ?></p>
                            <div class="category-meta">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; 
                    if($dernier){
                        echo "<a href='search.php?category[]={$dernier['name']}' class='category-card'>
                            <div class='category-header' style='{$st}''>
                                <i class='fas fa-laptop'><img src='images/icons/petit/{$dernier['icon2']}'/></i>
                            </div>
                            <div class='category-content'>
                                <h2>{$dernier['name']}</h2>
                                <p>{$dernier['description']}</p>
                                <div class='category-meta'>
                                    <i class='fas fa-chevron-right'></i>
                                </div>
                            </div>
                        </a>";
                    }
                    ?>
                </div>
                
                <!-- Section populaire -->
                <div class="popular-section">
                    <h2>Recherches populaires</h2>
                    <div class="popular-tags">
                        <a href="search.php?q=laptop" class="popular-tag">Laptop</a>
                        <a href="search.php?q=livre+maths" class="popular-tag">Livres de maths</a>
                        <a href="search.php?q=chaussures" class="popular-tag">Chaussures</a>
                        <a href="search.php?q=cours+particuliers" class="popular-tag">Cours particuliers</a>
                        <a href="search.php?q=samsung" class="popular-tag">Samsung</a>
                        <a href="search.php?q=iphone" class="popular-tag">iPhone</a>
                        <a href="search.php?q=imprimante" class="popular-tag">Imprimante</a>
                        <a href="search.php?q=covoiturage" class="popular-tag">Covoiturage</a>
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
</body>
</html>