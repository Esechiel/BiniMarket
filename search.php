<?php
    require_once 'php/db.php';
    session_start();

    function build_query_url($overrides = []) {
        $params = $_GET;
        foreach ($overrides as $key => $value) {
            $params[$key] = $value;
        }
        return htmlspecialchars($_SERVER['PHP_SELF']) . '?' . http_build_query($params);
    }
    
    

    // Pagination
    $per_page = 6;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
    $start = ($page - 1) * $per_page;
    

    // Tri
    $sort = $_GET['sort'] ?? 'recent';
    switch ($sort) {
        case 'price-asc':     $order_by = 'ORDER BY price ASC'; break;
        case 'price-desc':    $order_by = 'ORDER BY price DESC'; break;
        case 'popularity':    $order_by = 'ORDER BY views_count DESC'; break;
        default:              $order_by = 'ORDER BY creation_date DESC'; break;
    }

    // Recherche
    $mot = mysqli_real_escape_string($conn, trim($_GET['q'] ?? ''));
    $mot_sql = $mot ? "AND (l.title LIKE '%$mot%' OR l.description LIKE '%$mot%' OR l.type LIKE '%$mot%')" : '';

    // Filtres
    $category_filter = '';
    if (!empty($_GET['category']) && is_array($_GET['category'])) {
        $categories = array_map(fn($c) => "'" . mysqli_real_escape_string($conn, $c) . "'", $_GET['category']);
        $category_filter = "AND c.name IN (" . implode(",", $categories) . ")";
    }   

    $condition_filter = '';
    if (!empty($_GET['conditions']) && is_array($_GET['conditions'])) {
        $conds = array_map(fn($c) => "'" . mysqli_real_escape_string($conn, $c) . "'", $_GET['conditions']);
        $condition_filter = "AND l.conditions IN (" . implode(",", $conds) . ")";
    }

    $type_filter = '';
    if (!empty($_GET['type']) && is_array($_GET['type'])) {
        $types = array_map(fn($t) => "'" . mysqli_real_escape_string($conn, $t) . "'", $_GET['type']);
        $type_filter = "AND l.type IN (" . implode(",", $types) . ")";
    }

    $location_filter = '';
    if (!empty($_GET['location'])) {
        $loc = mysqli_real_escape_string($conn, $_GET['location']);
        $location_filter = "AND l.location_id = '$loc'";
    }

    $price_filter = '';
    $min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? intval($_GET['min_price']) : null;
    $max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? intval($_GET['max_price']) : null;

    if ($min_price !== null) {
        $price_filter .= "AND l.price >= $min_price ";
    }
    if ($max_price !== null) {
        $price_filter .= "AND l.price <= $max_price ";
    }

    // Construction finale
    $base_query = "
        FROM listings l
        LEFT JOIN categories c ON l.category_id = c.category_id
        WHERE 1=1
        $mot_sql
        $category_filter
        $condition_filter
        $type_filter
        $location_filter
        $price_filter
    ";

    // Compte total
    $count_sql = "SELECT COUNT(*) $base_query";
    $count_res = mysqli_query($conn, $count_sql);
    $total_results = ($count_res && mysqli_num_rows($count_res) > 0) ? (int)mysqli_fetch_row($count_res)[0] : 0;
    $total_pages = max(1, ceil($total_results / $per_page));

    // Résultats paginés
    $listings = [];
    $listing_images = [];
    $data_sql = "SELECT l.* $base_query $order_by LIMIT $start, $per_page";
    $res = mysqli_query($conn, $data_sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $listings = mysqli_fetch_all($res, MYSQLI_ASSOC);

        $ids = array_column($listings, 'listing_id');
        $id_list = implode(",", array_map('intval', $ids));
        $img_sql = "SELECT listing_id, image_path FROM listing_images WHERE is_primary = 1 AND listing_id IN ($id_list)";
        $img_res = mysqli_query($conn, $img_sql);
        while ($row = mysqli_fetch_assoc($img_res)) {
            $listing_images[$row['listing_id']] = $row['image_path'];
        }
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - BiniMarket</title>
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
                    <form action="search.php" method="get" id="searchForm">
                        <input type="text" name="q" placeholder="Rechercher un produit, service..." id="searchInput">
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
        <div class="container">
            <div class="search-container">
                <!-- Filtres (côté gauche) -->
                <form method="get" id="filterForm">
                    <div class="filters">
                        <div class="filter-header">
                            <h2>Filtres</h2>
                            <button type="reset" id="resetFilters">Réinitialiser</button>
                        </div>

                        <!-- Catégories -->
                        <div class="filter-group">
                            <h3>Catégories</h3>
                            <div class="filter-options">
                                <?php
                                $categories_list = ['Électronique' => 'Électronique', 'Livres' => 'Livres', 'Vêtements' => 'Vêtements', 'Nourriture' => 'Nourriture', 'Services' => 'Services', 'Mobilier' => 'Mobilier', 'Transport' => 'Transport', 'Divers' => 'Divers'];
                                foreach ($categories_list as $value => $label):
                                ?>
                                <label class="filter-option">
                                    <input type="checkbox" name="category[]" value="<?= $value ?>" <?= in_array($value, $_GET['category'] ?? []) ? 'checked' : '' ?>>
                                    <span><?= $label ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="filter-group">
                            <h3>Prix</h3>
                            <div class="price-inputs">
                                <input type="number" name="min_price" placeholder="Min" min="0" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                                <span>à</span>
                                <input type="number" name="max_price" placeholder="Max" min="0" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Localisation -->
                        <div class="filter-group">
                            <h3>Localisation</h3>
                            <select name="location" id="locationFilter">
                                <option value="">Tous les blocs</option>
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

                        <!-- État -->
                        <div class="filter-group">
                            <h3>État</h3>
                            <div class="filter-options">
                                <?php
                                $conditions_list = ['new' => 'Neuf', 'very-good' => 'Très bon état', 'good' => 'Bon état', 'average' => 'État moyen'];
                                foreach ($conditions_list as $value => $label):
                                ?>
                                <label class="filter-option">
                                    <input type="checkbox" name="condition[]" value="<?= $value ?>" <?= in_array($value, $_GET['conditions'] ?? []) ? 'checked' : '' ?>>
                                    <span><?= $label ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="filter-group">
                            <h3>Type d'annonce</h3>
                            <div class="filter-options">
                                <?php
                                $types_list = ['product' => 'product', 'service' => 'service'];
                                foreach ($types_list as $value => $label):
                                ?>
                                <label class="filter-option">
                                    <input type="checkbox" name="type[]" value="<?= $value ?>" <?= in_array($value, $_GET['type'] ?? ['product', 'service']) ? 'checked' : '' ?>>
                                    <span><?= $label ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Tri caché si présent -->
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? 'recent') ?>">

                        <!-- Bouton appliquer -->
                        <button type="submit" class="btn-primary apply-filters">Appliquer les filtres</button>
                    </div>
                </form>

                <!-- Résultats (côté droit) -->
                <div class="search-results">
                    <div class="results-header">
                        <div class="results-info">
                            <h1>Résultats de recherche</h1>
                            <span id="resultsCount"><?php 
                                if($total_results == 0){
                                    echo "<h2>Aucune annonce trouvée </2>";
                                }elseif($total_results == 1){
                                    echo $total_results." résultat trouvé";
                                }else{
                                    echo $total_results." résultats trouvés";
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="results-sorting">
                        <form method="get" id="sortForm">
                            <!-- Recherche -->
                            <input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

                            <!-- Catégories multiples -->
                            <?php if (!empty($_GET['category'])): ?>
                                <?php foreach ((array)$_GET['category'] as $cat): ?>
                                    <input type="hidden" name="category[]" value="<?= htmlspecialchars($cat) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Conditions multiples -->
                            <?php if (!empty($_GET['conditions'])): ?>
                                <?php foreach ((array)$_GET['conditions'] as $cond): ?>
                                    <input type="hidden" name="conditions[]" value="<?= htmlspecialchars($cond) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Types multiples -->
                            <?php if (!empty($_GET['type'])): ?>
                                <?php foreach ((array)$_GET['type'] as $type): ?>
                                    <input type="hidden" name="type[]" value="<?= htmlspecialchars($type) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Localisation -->
                            <input type="hidden" name="location" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">

                            <!-- Prix -->
                            <input type="hidden" name="min_price" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                            <input type="hidden" name="max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">

                            <!-- Pagination (retour page 1 au changement de tri) -->
                            <input type="hidden" name="page" value="1">

                            <!-- Tri -->
                            <label for="sort">Trier par:</label>
                            <select id="sort" name="sort" onchange="document.getElementById('sortForm').submit()">
                                <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Plus récent</option>
                                <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Prix croissant</option>
                                <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Prix décroissant</option>
                                <option value="popularity" <?= $sort === 'popularity' ? 'selected' : '' ?>>Popularité</option>
                            </select>
                        </form>

                    </div>
                    </div>
                    
                    <div class="search-terms">
                        <span>Recherche: "<span id="searchTerms"><?= htmlspecialchars($mot) ?></span>"</span>
                    </div>
                    
                    <!-- Conteneur des résultats de recherche -->
                    <div class="search-results-grid" id="searchResultsGrid">
                        <!-- Les résultats seront insérés ici via JavaScript -->
                        <?php foreach($listings as $listing): ?>
                        <a href="product-details.php?id=<?= $listing['listing_id'] ?>" class="result-card product-card" data-id="<?= $listing['listing_id'] ?>">
                            <div class="product-image">
                                <i class="fas fa-image"><img src="images/annonces/<?= $listing_images[$listing['listing_id']] ?? '../icons/petit/img-image.png' ?>" alt="Annonce"></i>
                                <?php
                                    $is_favorited = false;

                                    // Vérifie si connecté
                                    if (isset($_SESSION['user']['user_id'])) {
                                        $user_id = $_SESSION['user']['user_id'];

                                        // Préparation de la requête
                                        $stmt = $conn->prepare("SELECT favorite_id FROM favorites WHERE user_id = ? AND listing_id = ?");
                                        $stmt->bind_param("ii", $user_id, $listing['listing_id']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        // Vérifie si l'utilisateur a déjà ajouté cette annonce aux favoris
                                        $is_favorited = $result->num_rows > 0;

                                        $stmt->close();
                                    }
                                    ?>
                                <form action="php/add_favorite.php" method="POST">
                                    <input type="hidden" name="listing_id" value="<?= $listing['listing_id'] ?>">
                                    <button type="submit" class="favorite-btn" name="add_favorite">
                                        <img src="<?= $is_favorited ? 'images/icons/petit/heart-filled.png' : 'images/icons/petit/heart-empty.png' ?>" alt="Heart Icon">
                                    </button>
                                </form>
                            </div>
                            <div class="product-content">
                                <div class="product-title-row">
                                    <h3><?= htmlspecialchars($listing['title']) ?></h3>
                                    <span class="product-price"><?= number_format($listing['price'], 0, ',', ' ') ?> F</span>
                                </div>
                                <p class="product-description"><?= htmlspecialchars($listing['description']) ?></p>
                                <div class="product-meta">
                                    <span class="product-location"><i class="fas fa-map-marker-alt"></i></span>
                                    <span class="product-time"><?= date('d/m/Y', strtotime($listing['creation_date'])) ?></span>
                                </div>
                            </div>
                        </a>

                        <?php endforeach;?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination" id="pagination">

                            <!-- Bouton précédent -->
                            <?php if ($page > 1): ?>
                                <a href="<?= build_query_url(['page' => $page - 1]) ?>">
                                    <button class="pagination-prev" id="prevPage">
                                        <i class="fas fa-chevron-left"><img src="images/icons/petit/chevron.png" alt="←"></i>
                                    </button>
                                </a>
                            <?php else: ?>
                                <button class="pagination-prev disabled" id="prevPage" disabled>
                                    <i class="fas fa-chevron-left"><img src="images/icons/petit/chevron.png" alt="←"></i>
                                </button>
                            <?php endif; ?>

                            <!-- Numéros de pages -->
                            <?php
                            for ($i = 1; $i <= $total_pages; $i++):
                                // Limite d'affichage si trop de pages
                                if ($total_pages > 6 && $i > 3 && $i < $total_pages - 2) {
                                    if ($i == 4) {
                                        echo '<button class="pagination-more" disabled>...</button>';
                                    }
                                    continue;
                                }
                            ?>
                                <?php if ($i == $page): ?>
                                    <button class="pagination-number active"><?= $i ?></button>
                                <?php else: ?>
                                    <a href="<?= build_query_url(['page' => $i]) ?>">
                                        <button class="pagination-number"><?= $i ?></button>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Bouton suivant -->
                            <?php if ($page < $total_pages): ?>
                                <a href="<?= build_query_url(['page' => $page + 1]) ?>">
                                    <button class="pagination-next" id="nextPage">
                                        <i class="fas fa-chevron-right"><img src="images/icons/petit/chevron-right.png" alt="→"></i>
                                    </button>
                                </a>
                            <?php else: ?>
                                <button class="pagination-next disabled" id="nextPage" disabled>
                                    <i class="fas fa-chevron-right"><img src="images/icons/petit/chevron-right.png" alt="→"></i>
                                </button>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>


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

    <button id="filterToggle" class="filter-toggle">
        <i class="fas fa-filter"></i> Filtres
    </button>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const resetButton = document.getElementById('resetFilters');
        const filterForm = document.getElementById('filterForm');

            if (resetButton && filterForm) {
                resetButton.addEventListener('click', function (e) {
                    // Laisse le navigateur réinitialiser les champs du formulaire
                    setTimeout(() => {
                        // Supprime tous les paramètres GET de l’URL en rechargeant la page sans filtres
                        window.location.href = window.location.pathname;
                    }, 50);
                });
            }
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
    <script src="js/search.js"></script>
</body>
</html>