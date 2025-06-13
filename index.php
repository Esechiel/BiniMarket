<?php
require_once "php/db.php";

// Récupération des catégories
$sql1 = "SELECT * FROM categories";
$res1 = mysqli_query($conn, $sql1);
$category = mysqli_fetch_all($res1, MYSQLI_ASSOC);

// Récupération des 4 annonces actives les plus récentes avec localisation
$sql2 = "
    SELECT l.*, loc.name AS localisation 
    FROM listings l
    JOIN users u ON l.vendeur_id = u.user_id
    JOIN locations loc ON u.location_id = loc.location_id
    WHERE l.status = 'active'
    ORDER BY l.creation_date DESC
    LIMIT 4
";
$res2 = mysqli_query($conn, $sql2);
$listings = mysqli_fetch_all($res2, MYSQLI_ASSOC);

// Récupération des services populaires avec localisation
$sql3 = "
    SELECT l.*, loc.name AS localisation 
    FROM listings l
    JOIN users u ON l.vendeur_id = u.user_id
    JOIN locations loc ON u.location_id = loc.location_id
    WHERE l.status = 'active' AND l.type = 'service'
    ORDER BY l.views_count DESC
    LIMIT 4
";
$res3 = mysqli_query($conn, $sql3);
$listings1 = mysqli_fetch_all($res3, MYSQLI_ASSOC);

// Images principales pour produits récents
$listing_images = [];
if (!empty($listings)) {
    $ids = array_column($listings, 'listing_id');
    $id_list = implode(",", array_map('intval', $ids));
    $img_sql = "SELECT listing_id, image_path FROM listing_images WHERE is_primary = 1 AND listing_id IN ($id_list)";
    $img_res = mysqli_query($conn, $img_sql);
    while ($row = mysqli_fetch_assoc($img_res)) {
        $listing_images[$row['listing_id']] = $row['image_path'];
    }
}

// Images principales pour services populaires
$listing_images1 = [];
if (!empty($listings1)) {
    $ids = array_column($listings1, 'listing_id');
    $id_list = implode(",", array_map('intval', $ids));
    $img_sql = "SELECT listing_id, image_path FROM listing_images WHERE is_primary = 1 AND listing_id IN ($id_list)";
    $img_res = mysqli_query($conn, $img_sql);
    while ($row = mysqli_fetch_assoc($img_res)) {
        $listing_images1[$row['listing_id']] = $row['image_path'];
    }
}
?>
    
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiniMarket - Le marché de Bini-Dang</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/home.css">
    
</head>
<body>
    <!-- En-tête -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <i class="fas fa-store"><img src="images/icons/petit/shop.png"/> </i>
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
                <li><a href="categories.php">Catégories</a></li>
                <li><a href="add-listing.php">Publier une annonce</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="auth.php">Connexion / Inscription</a></li>
                <?php if(isset($_SESSION['user'])){
                            echo "<li><a href='php/logout.php' id='logout-link'> Déconnexion</a></li>";
                        } ?>
            </ul>
        </div>
    </header>

    <main>
        <!-- Bannière -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1>Bienvenue sur BiniMarket</h1>
                        <p>Le marché en ligne pour les étudiants et résidents de Bini-Dang</p>
                        <a href="search.php" class="btn-primary">Explorer les annonces</a>
                    </div>
                    <div class="hero-image">
                        <i class="fas fa-shopping-basket"><img src="images/icons/grand/basket.png"/></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Catégories -->
        <section class="categories">
            <div class="container">
                <h2>Catégories populaires</h2>
                <div class="categories-grid">
                    <?php 
                    $count=0;
                    foreach ($category as $cat): 
                        if($count >= 6){
                            break;
                        }
                    ?>
                    <a href="search.php?category[]=<?= $cat['name'] ?>" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-laptop"><img src="images/icons/petit/<?= $cat['icon1'] ?>"/></i>
                        </div>
                        <span><?= $cat['name'] ?></span>
                    </a>
                    <?php 
                        $count++;
                        endforeach; 
                        echo "
                            <a href='search.php?category=Divers' class='category-card'>
                                <div class='category-icon'>
                                    <i class='fas fa-laptop'><img src='images/icons/petit/img-others.png'/></i>
                                </div>
                                <span>Divers</span>
                            </a>
                        ";
                    ?>
                </div>
            </div>
        </section>

        <!-- Nouveautés -->
        <section class="featured-products">
            <div class="container">
                <div class="section-header">
                    <h2>Annonces récentes</h2>
                    <a href="search.php" class="view-all">Voir tout</a>
                </div>
                <div class="products-grid" id="recentProducts">
                    <?php foreach ($listings as $listing): ?>
                        <?php
                            $id = $listing['listing_id'];
                            $title = htmlspecialchars($listing['title']);
                            $price = number_format($listing['price'], 0, '', ' ') . ' F' . ($listing['type'] === 'service' ? '/h' : '');
                            $description = htmlspecialchars($listing['description']);
                            $location = htmlspecialchars($listing['localisation']);
                            $created_at = date('d/m/Y', strtotime($listing['creation_date']));
                            $badge = strtoupper($listing['type']) === 'SERVICE' ? 'SERVICE' : (strtoupper($listing['type']) ?? 'PRODUIT');
                            $badgeClass = $badge === 'SERVICE' ? 'service' : 'PRODUIT';
                            $thumbnail = isset($listing_images[$id]) ? 'images/annonces/' . $listing_images[$id] : '../icons/petit/img-image.png';
                        ?>
                        <div class="product-card">
                            <a href="product-details.php?id=<?= $id ?>">
                                <div class="product-image">
                                    <img src="<?= $thumbnail ?>" alt="<?= $title ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <span class="product-badge <?= strtolower($badgeClass) ?>"><?= $badge ?></span>
                                </div>
                                <div class="content">
                                    <h3><?= $title ?></h3>
                                    <div class="price"><?= $price ?></div>
                                    <div class="description"><?= $description ?></div>
                                    <div class="meta">
                                        <div class="location"><i class="fas fa-map-marker-alt"></i> <?= $location ?></div>
                                        <div class="date">Il y a <?= date_diff(date_create($listing['creation_date']), date_create())->days ?> jour(s)</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Services populaires -->
        <section class="featured-services">
            <div class="container">
                <div class="section-header">
                    <h2>Services populaires</h2>
                    <a href="search.php?type[]=service" class="view-all">Voir tout</a>
                </div>
                <div class="services-grid">
                    <?php 
                    $count = 0;
                    foreach ($listings1 as $listing): 
                        if ($count >= 4) break;
                        $listing_id = $listing['listing_id'];
                        $image = isset($listing_images1[$listing_id]) ? 'images/annonces/' . $listing_images1[$listing_id] : '../icons/petit/img-image.png';
                        $badge = strtoupper($listing['type']) === 'SERVICE' ? 'SERVICE' : 'ACTIF';
                    ?>
                    <div class="product-card">
                        <a href="product-details.php?id=<?= $listing_id ?>">
                            <div class="product-image">
                                <img src="<?= $image ?>" alt="Image" style="width: 100%; height: 100%; object-fit: cover;">
                                <div class="product-badge <?= $badge === 'SERVICE' ? 'bg-primary' : 'bg-success' ?>">
                                    <?= $badge ?>
                                </div>
                            </div>
                            <div class="content">
                                <h3><?= htmlspecialchars($listing['title']) ?></h3>
                                <div class="price">
                                    <?= number_format($listing['price'], 0, ',', ' ') ?> F<?= $listing['type'] === 'service' ? '/h' : '' ?>
                                </div>
                                <p class="description"><?= htmlspecialchars($listing['description']) ?></p>
                                <div class="meta">
                                    <div class="location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($listing['localisation']) ?>
                                    </div>
                                    <div class="date">
                                        Il y a <?= date_diff(date_create($listing['creation_date']), date_create())->days ?> jour(s)
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php 
                    $count++; 
                    endforeach; 
                    ?>
                </div>
            </div>
        </section>


        <!-- Bannière promotion -->
        <section class="promo-banner">
            <div class="container">
                <div class="promo-content">
                    <h2>Vous avez quelque chose à vendre?</h2>
                    <p>Publiez votre annonce gratuitement et trouvez des acheteurs rapidement!</p>
                    <a href="add-listing.php" class="btn-secondary">Publier une annonce</a>
                </div>
            </div>
        </section>
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
    <script src="js/products.js"></script>
</body>
</html>