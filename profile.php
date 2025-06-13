<?php
session_start();
require_once "php/db.php";
require_once "php/add_notification.php";
if (!isset($_SESSION['user']['user_id'])) {
    header("Location: auth.php");
    exit;
}
$user_id = (int)$_SESSION['user']['user_id'];
$user = null;
$reviewed_user_id = null;
$total = null;
if(isset($_GET['id'])){
    $reviewed_user_id = (int)$_GET['id'] ?? null;
    $user_id = (int)$_GET['id'];
    $sql = "SELECT * FROM users WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $resul = $stmt->get_result();
    if ($resul && mysqli_num_rows($resul) > 0) {
        $user = mysqli_fetch_assoc($resul);
    }
    if (isset($_GET['id']) && $_GET['id'] != $_SESSION['user']['user_id']) {
        $visited_id = (int)$_GET['id'];
        addNotification($conn, $visited_id, 'visite_profil', $_SESSION['user']['username'] . " a consulté votre profil.", $_SESSION['user']['user_id']);
    }
    
}
$_SESSION['reviewed_user_id'] = $reviewed_user_id;
$filter = trim($_GET['filter'] ?? 'all');
$tfilter = mysqli_real_escape_string($conn, $_GET['tfilter'] ?? 'completé');
$activeTab = isset($_GET['tfilter']) ? 'transactionsTab' : 'mesAnnoncesTab'; // tab à activer


// Construction de la requête SQL en fonction du filtre
if(empty($user)){
    $where = "vendeur_id = $user_id";
    switch ($filter) {
        case 'active':
            $where .= " AND status = 'active'";
            break;
        case 'vendu':
            $where .= " AND status = 'vendu'";
            break;
        case 'rejeté':
            $where .= " AND status = 'rejeté'";
            break;
        case 'brouillon':
            $where .= " AND status = 'inactive'";
            break;
        case 'supprimé':
            $where .= " AND status = 'supprimé'";
            break;
        case 'all':
        default:
            // Pas de filtre supplémentaire
            break;
    }
}else{
    $where = "vendeur_id = $user_id AND status = 'active'";
}


$sql1 = "SELECT * FROM listings WHERE $where ORDER BY creation_date DESC";
$res1 = mysqli_query($conn, $sql1);
if(!$res1){
    die("Erreur SQL : ".mysqli_error($conn));
}
$listings = mysqli_fetch_all($res1, MYSQLI_ASSOC);

// On récupère les images principales associées
$listing_images = [];
if (!empty($listings)) {
    $ids = array_column($listings, 'listing_id');
    $id_list = implode(",", $ids);
    $img_sql = "SELECT listing_id, image_path FROM listing_images WHERE is_primary = 1 AND listing_id IN ($id_list)";
    $img_res = mysqli_query($conn, $img_sql);
    while ($row = mysqli_fetch_assoc($img_res)) {
        $listing_images[$row['listing_id']] = $row['image_path'];
    }
}

// Échapper manuellement la variable (puisqu’on n’utilise pas prepare)
$user_id_safe = mysqli_real_escape_string($conn, $user_id);
$twhere = "t.acheteur_id = $user_id_safe OR t.vendeur_id = $user_id_safe";
switch ($tfilter) {
    case 'completé':
        $twhere .= " AND t.status = 'completé'";
        break;
    case 'refusé':
        $twhere .= " AND t.status = 'refusé'";
        break;
    case 'en_cours':
        $twhere .= " AND t.status = 'en cours'";
        break;
    default:
        // Pas de filtre supplémentaire
        break;
}

// Requête SQL sans préparation
$sql2 = "SELECT t.*, l.title, pm.method_name 
        FROM transactions t
        JOIN listings l ON t.listing_id = l.listing_id
        JOIN payment_methods pm ON t.payment_method_id = pm.payment_method_id
        WHERE $twhere
        ORDER BY t.transaction_date DESC";

$result = mysqli_query($conn, $sql2);

if ($result && mysqli_num_rows($result) > 0) {
    $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
$listing_images2 = [];
if (!empty($transactions)) {
    $ids = array_column($transactions, 'listing_id');
    $id_list = implode(",", $ids);
    $img_sql = "SELECT listing_id, image_path FROM listing_images WHERE is_primary = 1 AND listing_id IN ($id_list)";
    $img_res = mysqli_query($conn, $img_sql);
    while ($rows = mysqli_fetch_assoc($img_res)) {
        $listing_images2[$rows['listing_id']] = $rows['image_path'];
    }
}

//les annonces mise en favoris
$sql = "SELECT l.* 
        FROM listings l
        INNER JOIN favorites f ON l.listing_id = f.listing_id
        WHERE f.user_id = ?
        ORDER BY f.date_added DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resul = $stmt->get_result();
if ($resul && mysqli_num_rows($resul) > 0) {
    $list = mysqli_fetch_all($resul, MYSQLI_ASSOC);
}
$listing_images3 = [];
if (!empty($list)) {
    $ids = array_column($list, 'listing_id');
    $id_list = implode(",", $ids);
    $img_sql = "SELECT listing_id, image_path FROM listing_images WHERE is_primary = 1 AND listing_id IN ($id_list)";
    $img_res = mysqli_query($conn, $img_sql);
    while ($rows = mysqli_fetch_assoc($img_res)) {
        $listing_images3[$rows['listing_id']] = $rows['image_path'];
    }
}
//compte le nombre de notif non lu
$sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user']['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$unread_count = $res->fetch_assoc()['unread_count'];

//avis
$average = 0;
if ($reviewed_user_id) {
    // 1. Statistiques générales
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) AS total_reviews,
            ROUND(AVG(rating), 1) AS average_rating,
            SUM(rating = 5) AS r5,
            SUM(rating = 4) AS r4,
            SUM(rating = 3) AS r3,
            SUM(rating = 2) AS r2,
            SUM(rating = 1) AS r1
        FROM reviews
        WHERE reviewed_user_id = ?
    ");
    $stmt->bind_param("i", $reviewed_user_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    

    $average = $stats['average_rating'] ?? 0;
    $total = $stats['total_reviews'] ?? 0;
    function percent($count, $total) {
        return $total > 0 ? round(($count / $total) * 100) : 0;
    }

    // 2. Liste des avis
    $stmt = $conn->prepare("
        SELECT r.*, u.username, l.title AS listing_title
        FROM reviews r
        JOIN users u ON u.user_id = r.reviewer_id
        LEFT JOIN listings l ON l.listing_id = r.listing_id
        WHERE r.reviewed_user_id = ?
        ORDER BY r.review_date DESC
    ");
    $stmt->bind_param("i", $reviewed_user_id);
    $stmt->execute();
    $reviews = $stmt->get_result();
    $stmt->close();

}

// Vérifier si l'utilisateur est connecté et si l'utilisateur n'est pas celui qui a été évalué
$current_user_id = $_SESSION['user']['user_id'] ?? null;
$can_review = false;

if ($current_user_id && $current_user_id != $reviewed_user_id) {
    // Vérifie s’il a acheté chez ce vendeur
    $check = $conn->prepare("
        SELECT 1 FROM transactions t
        JOIN listings l ON t.listing_id = l.listing_id
        WHERE t.acheteur_id = ? AND l.vendeur_id = ?
    ");
    $check->bind_param("ii", $current_user_id, $reviewed_user_id);
    $check->execute();
    $check->store_result();
    $has_bought = $check->num_rows > 0;
    $check->close();

    // Vérifie s’il a déjà laissé un avis
    $check = $conn->prepare("SELECT 1 FROM reviews WHERE reviewer_id = ? AND reviewed_user_id = ?");
    $check->bind_param("ii", $current_user_id, $reviewed_user_id);
    $check->execute();
    $check->store_result();
    $has_reviewed = $check->num_rows > 0;
    $check->close();

    if ($has_bought && !$has_reviewed) {
        $can_review = true;
    }
}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/profile.css">
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
                        <li style="position: relative;">
                            <a href="notifications.php" title="Notifications">
                                <img src="images/icons/petit/bell (2).png"="Notifications" style="width:24px; vertical-align: middle;" />
                                    <?php if ($unread_count > 0): ?>
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
                                <?php endif; ?>
                                </a>
                            </li>
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
                <li><a href="php/logout.php" id="logout-link"> Déconnexion</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="profile-container">
                <!-- En-tête du profil -->
                <div class="profile-header">
                    <div class="cover-photo"></div>
                    <div class="profile-info">
                        <div class="profile-avatar">
                            <i class="fas fa-user"><img src="images/profils/<?php
                                if(!empty($user)){
                                    $img_user = $user['profile_picture'];
                                }else{
                                    $img_user = $_SESSION['user']['profile_picture'];
                                }
                                echo $img_user; 
                            ?>"/></i>
                        </div>
                        <div class="profile-details">
                            <h1><?php
                                if(!empty($user)){
                                    $pseudo = $user['username'];
                                }else{
                                    $pseudo = $_SESSION['user']['username'];
                                }
                                echo $pseudo; 
                            ?>
                            </h1><br>
                            <p>
                                <img src="images/icons/petit/icons8-marqueur-24 (2).png"/>
                                <?php 
                                    if(!empty($user)){
                                        $location_id = $user['location_id'];
                                    }else{
                                        $location_id = $_SESSION['user']['location_id'];
                                    }
                                    $sql = "SELECT * FROM locations WHERE location_id = '$location_id'";
                                    $res = mysqli_query($conn, $sql);
                                    $location = mysqli_fetch_assoc($res);
                                    $nom = htmlspecialchars($location['name']); 
                                    echo $nom; 
                                ?>
                            </p>
                            <div class="rating">
                            <div class="stars">
                                <?php
                                    $filled = floor($average); // Nombre d'étoiles pleines
                                    $half = ($average - $filled) >= 0.5; // Étoile à moitié pleine ?
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $filled) {
                                            echo '<i class="fas fa-star"><img src="images/icons/petit/icons8-étoile-24.png"/></i>';
                                        } elseif ($i == $filled + 1 && $half) {
                                            echo '<i class="fas fa-star-half-alt"><img src="images/icons/petit/icons8-étoile-à-moitié-vide-24.png"/></i>';
                                        } else {
                                            echo '<i class="far fa-star"><img src="images/icons/petit/icons8-étoile-vide-24.png"/></i>';
                                        }
                                    }
                                ?>
                            </div>
                            <span><?= number_format($average, 1) ?> (<?= $total ?> avis)</span>
                            </div>

                            <div class="form-group">
                                <p class="accountBio" ><?php 
                                    if(!empty($user)){
                                        $bio = htmlspecialchars($user['bio']);
                                    }else{
                                        $bio = htmlspecialchars($_SESSION['user']['bio']); ;
                                    }
                                    echo $bio; 
                                ?></p>
                             </div>
                        </div>
                        <?php 
                            if(empty($user)){
                                echo "<button class='btn-primary' id='editProfileBtn'>
                                        <i class='fas fa-edit'></i> Modifier le profil
                                     </button>"; 
                            }
                        ?>
                    </div>
                    
                    <!-- Statistiques utilisateur -->
                    <?php if(empty($user)): ?>
                    <div class="profile-stats">
                        <div class="stat-box">
                            <div class="stat-number">
                                <p>
                                <?php 
                                    $vendeur_id = $_SESSION['user']['user_id'];
                                    $sql = "SELECT * FROM listings WHERE vendeur_id = '$vendeur_id'";
                                    $res = mysqli_query($conn, $sql);
                                    $nbr = mysqli_num_rows($res);
                                    echo $nbr; 
                                ?>
                                </p>
                            </div>
                            <div class="stat-label">Annonces</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">
                                <p>
                                <?php 
                                    $vendeur_id = $_SESSION['user']['user_id'];
                                    $sql = "SELECT * FROM listings WHERE vendeur_id = '$vendeur_id' AND status = 'active'";
                                    $res = mysqli_query($conn, $sql);
                                    $nbr = mysqli_num_rows($res);
                                    echo $nbr; 
                                ?>
                                </p>
                            </div>
                            <div class="stat-label">Ventes</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">
                                <p>
                                <?php 
                                    $user_id = $_SESSION['user']['user_id'];
                                    $sql = "SELECT * FROM transactions WHERE acheteur_id = '$user_id' AND status = 'completé'";
                                    $res = mysqli_query($conn, $sql);
                                    $nbr = mysqli_num_rows($res);
                                    echo $nbr; 
                                ?>
                                </p>
                            </div>
                            <div class="stat-label">Achats</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Onglets profil -->
                <?php if(empty($user)): ?>
                    <div class="profile-tabs">
                        <button class="profile-tab <?= $activeTab == 'mesAnnoncesTab' ? 'active' : '' ?>" data-tab="listings">Mes annonces</button>
                        <button class="profile-tab" data-tab="favorites">Favoris</button>
                        <button class="profile-tab" data-tab="reviews">Avis</button>
                        <button class="profile-tab <?= $activeTab == 'transactionsTab' ? 'active' : '' ?>" data-tab="transactions">Transactions</button>
                        <button class="profile-tab" data-tab="settings">Paramètres</button>
                    </div>
                <?php else: ?>
                    <div class="profile-tabs">
                        <button class="profile-tab <?= $activeTab == 'mesAnnoncesTab' ? 'active' : '' ?>" data-tab="listings">Ses annonces</button>
                        <button class="profile-tab" data-tab="reviews">Avis</button>
                    </div>
                <?php endif; ?>

                <!-- Contenu des onglets -->
                <div class="tab-content">
                    <!-- Mes annonces -->
                    <div class="tab-pane <?= $activeTab == 'mesAnnoncesTab' ? 'active' : '' ?>" id="listingsTab">
                        
                        <?php if(empty($user)): ?>
                            <!-- Filtres d'annonces -->
                            <div class="listing-filters">
                                <a href="profile.php?filter=all" class="filter-btn <?= $filter == 'all' ? 'active' : '' ?>">Toutes</a>
                                <a href="profile.php?filter=active" class="filter-btn <?= $filter == 'active' ? 'active' : '' ?>">Actives</a>
                                <a href="profile.php?filter=vendu" class="filter-btn <?= $filter == 'vendu' ? 'active' : '' ?>">Vendues</a>
                                <a href="profile.php?filter=brouillon" class="filter-btn <?= $filter == 'brouillon' ? 'active' : '' ?>">Brouillons</a>
                                <a href="profile.php?filter=rejeté" class="filter-btn <?= $filter == 'rejeté' ? 'active' : '' ?>">Rejetées</a>
                            </div>
                        <?php endif; ?>
                        <!-- Liste des annonces -->
                        <div class="listings-grid" id="listings-grid">
                            <?php foreach ($listings as $listing): ?>
                            <a class="listing-card" href="product-details.php?id=<?= $listing['listing_id'] ?>" data-id="<?= $listing['listing_id'] ?>">
                                <div class="listing-image">
                                    <img class="listing-thumbnail" src="images/annonces/<?= $listing_images[$listing['listing_id']] ?? '../icons/petit/img-image.png' ?>" alt="Annonce"/>
                                    <span class="listing-badge <?= $listing['status']; ?>"><?= strtoupper($listing['status']) ?></span>
                                    <div class="listing-stats">
                                        <span><img src="images/icons/petit/img-view.png"/> <?= $listing['views_count'] ?></span>
                                        <?php if(empty($user)): ?>
                                            <span><img src="images/icons/petit/img-heart.png"/> 0</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="listing-details">
                                    <div class="listing-title-row">
                                        <h3><?= htmlspecialchars($listing['title']);  ?></h3>
                                        <span class="listing-price"><?= number_format($listing['price'], 0, ',', ' ') ?> F</span>
                                    </div>
                                    <p class="listing-description"><?= htmlspecialchars($listing['description']) ?></p>
                                    <div class="listing-footer">
                                        <span class="listing-date"><?= date('d/m/Y', strtotime($listing['creation_date'])) ?></span>
                                        <?php if(empty($user)): ?>
                                            <div class="listing-actions">
                                                <form action="add-listing.php" method="POST">
                                                    <input type="hidden" name="listing_id" value="<?= $listing['listing_id'] ?>">
                                                    <button type="submit" class="action-btn edit">
                                                        <img src="images/icons/petit/editer (1).png"/>
                                                    </button>
                                                </form>
                                                <form action="php/pause_listing.php" method="POST">
                                                    <input type="hidden" name="listing_id" value="<?= $listing['listing_id'] ?>">
                                                    <input type="hidden" name="current_status" value="<?= $listing['status'] ?>">
                                                    <button type="submit" class="action-btn pause" title="<?= $listing['status'] == 'active' ? 'Mettre en pause' : 'Réactiver' ?>">
                                                        <?php if ($listing['status'] == 'active'): ?>
                                                            <i class="fas fa-pause"><img src="images/icons/petit/img-pause.png"/></i> <!-- Icône pause -->
                                                        <?php else: ?>
                                                            <i class="fas fa-play"><img src="images/icons/petit/img-play.png"/></i> <!-- Icône play -->
                                                        <?php endif; ?>
                                                    </button>
                                                </form>
                                                <form action="php/delete_listing.php" method="POST">
                                                    <input type="hidden" name="listing_id" value="<?= $listing['listing_id'] ?>">
                                                    <button type="submit" class="action-btn delete">
                                                        <img src="images/icons/petit/img-delete.png"/>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>

                    </div>
                    
                    <!-- Favoris -->
                    <div class="tab-pane" id="favoritesTab">
                        <div class="listings-grid" id="listings-grid">
                            <?php if(!empty($list)) : foreach ($list as $listing): ?>
                            <a class="listing-card" href="product-details.php?id=<?= $listing['listing_id'] ?>" data-id="<?= $listing['listing_id'] ?>">
                                <div class="listing-image">
                                    <img class="listing-thumbnail" src="images/annonces/<?= $listing_images3[$listing['listing_id']] ?? '../icons/petit/img-image.png' ?>" alt="Annonce"/>
                                    <span class="listing-badge <?= $listing['status']; ?>"><?= strtoupper($listing['status']) ?></span>
                                </div>
                                <div class="listing-details">
                                    <div class="listing-title-row">
                                        <h3><?= htmlspecialchars($listing['title']);  ?></h3>
                                        <span class="listing-price"><?= number_format($listing['price'], 0, ',', ' ') ?> F</span>
                                    </div>
                                    <p class="listing-description"><?= htmlspecialchars($listing['description']) ?></p>
                                    <div class="listing-footer">
                                        <span class="listing-date"><?= date('d/m/Y', strtotime($listing['creation_date'])) ?></span>
                                        <div class="listing-actions">
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
                                            <form action="php/add_favorite.php" method="POST" class="favorite-badge">
                                                <input type="hidden" name="listing_id" value="<?= $listing['listing_id'] ?>">
                                                <button type="submit"  name="add_favorite">
                                                    <img src="<?= $is_favorited ? 'images/icons/petit/heart-filled.png' : 'images/icons/petit/heart-empty.png' ?>" alt="Heart Icon">
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                    
                    <!-- Avis -->
                    <div class="tab-pane" id="reviewsTab">
                        <div class="reviews-container">
                            <?php if ($total > 0): ?>
                            <div class="reviews-summary">
                                <div class="overall-rating">
                                    <h3>Note moyenne</h3>
                                    <div class="rating-number"><?= $average ?></div>
                                    <div class="stars">
                                        <?php
                                        $filled = floor($average);
                                        $half = ($average - $filled) >= 0.5;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $filled) {
                                                echo '<img src="images/icons/petit/icons8-étoile-24.png"/>';
                                            } elseif ($i == $filled + 1 && $half) {
                                                echo '<img src="images/icons/petit/icons8-étoile-à-moitié-vide-24.png"/>';
                                            } else {
                                                echo '<img src="images/icons/petit/icons8-étoile-vide-24.png"/>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span class="reviews-count">Basé sur <?= $total ?> avis</span>
                                </div>

                                <div class="rating-breakdown">
                                    <?php foreach ([5, 4, 3, 2, 1] as $r): ?>
                                    <div class="rating-bar">
                                        <span><?= $r ?></span>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: <?= percent($stats["r$r"], $total) ?>%"></div>
                                        </div>
                                        <span><?= $stats["r$r"] ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($can_review): ?>
                                <div class="leave-review-form">
                                    <h4>Laisser un avis</h4>
                                    <form method="post" action="php/submit_review.php">
                                        <label for="rating">Note :</label>
                                        <select name="rating" required>
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="5">★★★★★</option>
                                            <option value="4">★★★★☆</option>
                                            <option value="3">★★★☆☆</option>
                                            <option value="2">★★☆☆☆</option>
                                            <option value="1">★☆☆☆☆</option>
                                        </select>
                                        <label for="comment">Commentaire :</label>
                                        <textarea name="comment" rows="4" required placeholder="Votre expérience..."></textarea>
                                        <input type="hidden" name="submit_review" value="1">
                                        <button type="submit">Envoyer</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <p>Vous ne pouvez pas laisser un avis pour ce vendeur.</p>
                            <?php endif; ?>


                            <?php if ($total > 0): ?>
                            <div class="reviews-list">
                                <?php while ($rev = $reviews->fetch_assoc()): ?>
                                    <div class="review">
                                        <div class="review-header">
                                            <div class="reviewer">
                                                <div class="avatar">
                                                    <img src="images/icons/petit/img-user2.png" />
                                                </div>
                                                <div>
                                                    <h4><?= htmlspecialchars($rev['username']) ?></h4>
                                                    <div class="stars">
                                                        <?php
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            echo '<img src="images/icons/petit/' . ($i <= $rev['rating'] ? 'icons8-étoile-24.png' : 'icons8-étoile-vide-24.png') . '"/>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="review-info">
                                                <span class="review-date"><?= date("d/m/Y", strtotime($rev['review_date'])) ?></span>
                                                <?php if ($rev['listing_title']): ?>
                                                    <span class="review-product">Achat: <?= htmlspecialchars($rev['listing_title']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <p class="review-content"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <!-- Transactions -->
                    <div class="tab-pane <?= $activeTab == 'transactionsTab' ? 'active' : '' ?>" id="transactionsTab">
                        <h3>Historique des Transactions</h3>
                        <!-- Filtres des transactions -->
                        <div class="listing-filters">
                            <a href="profile.php?tfilter=completé" class="filter-btn <?= $tfilter == 'completé' ? 'active' : '' ?>">Completée</a>
                            <a href="profile.php?tfilter=refusé" class="filter-btn <?= $tfilter == 'refusé' ? 'active' : '' ?>">Refusée</a>
                            <a href="profile.php?tfilter=en_cours" class="filter-btn <?= $tfilter == 'en_cours' ? 'active' : '' ?>">En cours</a>
                        </div>
                        <div class="listings-grid" id="transactions-grid">
                            <?php if (!empty($transactions)): ?>
                                <?php foreach ($transactions as $row): ?>
                                    <a class="listing-card" href="product-details.php?id=<?= $row['listing_id'] ?>" data-id="<?= $row['listing_id'] ?>">
                                        <div class="listing-image">
                                            <img class="listing-thumbnail" src="images/annonces/<?= $listing_images2[$row['listing_id']] ?? '../icons/petit/img-image.png' ?>" alt="Annonce"/>
                                            <span class="listing-badge <?= htmlspecialchars($row['status']) ?>">
                                                <?= strtoupper(htmlspecialchars($row['status'])) ?>
                                            </span>
                                            <div class="listing-stats">
                                                <span> <?= number_format($row['amount'], 0, ',', ' ') ?> FCFA</span>
                                                <span> <?= date('d/m/Y', strtotime($row['transaction_date'])) ?></span>
                                            </div>
                                        </div>
                                        <div class="listing-details">
                                            <div class="listing-title-row">
                                                <h3><?= htmlspecialchars($row['title']); ?></h3>
                                                <span class="listing-price"><?= htmlspecialchars($row['method_name']); ?></span>
                                            </div>
                                            <p class="listing-description">
                                                Téléphone :? <?= htmlspecialchars($row['phone']) ?><br>
                                                <strong>Transaction <?= $row['acheteur_id'] == $user_id ? 'effectuée' : 'reçue' ?></strong>
                                            </p>
                                            <div class="listing-footer">
                                                <span class="listing-date"><?= date('H:i', strtotime($row['transaction_date'])) ?></span>
                                                <div class="listing-actions">
                                                    <form action="php/impression_recu.php" method="POST" target="_blank">
                                                        <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                                        <button type="submit" class="action-btn view" title="Imprimer le reçu">
                                                            <i> </i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucune transaction trouvée.</p>
                            <?php endif; ?>
                        </div>
                    </div>


                    <!-- Paramètres -->
                    <div class="tab-pane" id="settingsTab">
                        <div class="settings-container">
                            <!-- Onglets paramètres -->
                            <div class="settings-tabs">
                                <button class="settings-tab active" data-settings-tab="account">Compte</button>
                                <button class="settings-tab" data-settings-tab="security">Sécurité</button>
                                <button class="settings-tab" data-settings-tab="notifications">Notifications</button>
                                <button class="settings-tab" data-settings-tab="privacy">Confidentialité</button>
                            </div>
                            
                            <!-- Contenu onglets paramètres -->
                            <div class="settings-content">
                                <!-- Paramètres de compte -->
                                <div class="settings-pane active" id="accountSettings">
                                    <h3>Informations du compte</h3>
                                    
                                    <form id="accountForm" action="php/update_account.php" method="post" enctype="multipart/form-data">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="accountFirstName">Pseudo</label>
                                                <input type="text" id="accountFirstName" value="<?= $_SESSION['user']['username'] ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="accountEmail">Email</label>
                                            <div class="input-addon">
                                                <input type="email" id="accountEmail" value="<?= $_SESSION['user']['email'] ?>" disabled>
                                                <button type="button" class="btn-secondary">Modifier</button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Tél Orange Money</label>
                                            <div class="phone">
                                                <span class="codes">+237</span>
                                                <input type="tel" id="phone" name="phone1" value="<?= $_SESSION['user']['phone_number'] ?>" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Tél Mobile Money</label>
                                            <div class="phone">
                                                <span class="codes">+237</span>
                                                <input type="tel" id="phone" name="phone2" value="<?= $_SESSION['user']['phone_momo'] ?>" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="accountBio">Bio</label>
                                            <textarea id="accountBio" rows="4">Qu'est-ce que vous proposez ?</textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="accountLocation">Localisation</label>
                                            <select id="accountLocation">
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
                                        <div class="form-group">
                                            <label for="profileImage">Photo de profil</label>
                                            
                                            <!-- Aperçu de l'image -->
                                            <div style="margin-bottom: 10px;">
                                                <img id="previewProfile" 
                                                    src="<?= $_SESSION['user']['profile_picture'] ?? 'images/icons/grand/user_profile.png' ?>" 
                                                    alt="Aperçu" 
                                                    style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 1px solid #ccc;" />
                                            </div>

                                            <!-- Champ input -->
                                            <input 
                                                type="file" 
                                                id="profileImage" 
                                                name="profile_image" 
                                                accept="image/*" 
                                                style="padding: 10px; font-size: 16px;" />
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                                            <a href="php/logout.php" class="btn-secondary">Deconnexion </a>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Autres onglets de paramètres seront chargés via JavaScript -->
                            </div>
                        </div>
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
            const params = new URLSearchParams(window.location.search);
            if (params.has('filter') || params.has('success')) {
                const target = document.getElementById('listings-grid');
                if (target) {
                    const yOffset = -170; // ← Décalage pour compenser le header (ajuste selon sa hauteur)
                    const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            }
            if (params.has('tfilter')) {
                const target = document.getElementById('listings-grid');
                if (target) {
                    const yOffset = -170; // ← Décalage pour compenser le header (ajuste selon sa hauteur)
                    const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            }
            if (params.has('setting')) {
                const target = document.getElementById('listings-grid');
                if (target) {
                    const yOffset = -170; // ← Décalage pour compenser le header (ajuste selon sa hauteur)
                    const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            }
            
        });
    </script>
    <script>
    document.getElementById('profileImage').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('previewProfile');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = "images/icons/default-avatar.png"; // image par défaut
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
    <script src="js/profile.js"></script>
    
</body>
</html>