<?php
    session_start();
    if(!isset($_SESSION['user'])){
        header("Location: auth.php?il faut se connecter");
    }
    
    if(!empty($_GET['id'])){
        $_SESSION['listing_id'] = (int)$_GET['id'];
        // Requête améliorée avec jointures pour récupérer toutes les infos utiles
        $sql = "SELECT l.*, c.name AS category_id, u.username, u.user_id, u.phone_number, loc.name AS location_name
        FROM listings l
        JOIN categories c ON l.category_id = c.category_id
        JOIN users u ON l.vendeur_id = u.user_id
        JOIN locations loc ON l.location_id = loc.location_id
        WHERE l.listing_id = $listing_id";

        $res = mysqli_query($conn, $sql);
        if (!$res || mysqli_num_rows($res) == 0) {
        echo "<p>Annonce introuvable.</p>";
        exit;
        }

        $listing = mysqli_fetch_assoc($res);

        // Récupération des images
        $listing_images = [];
        $img_sql = "SELECT image_path FROM listing_images WHERE listing_id = $listing_id";
        $img_res = mysqli_query($conn, $img_sql);
        while ($row = mysqli_fetch_assoc($img_res)) {
        $listing_images[] = $row['image_path'];
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une annonce - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/product.css">
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
                        <li><a href="add-listing.php" class="btn-primary active"><i class="fas fa-plus"><img src="images/icons/petit/icons8-plus-math-24.png"/></i> Publier</a></li>
                        <li><a href="messages.html"><i class="fas fa-comment"><img src="images/icons/petit/img-chat1.png"/></i></a></li>
                        <li><a href="auth.php"><i class="fas fa-user"><img src="images/icons/petit/img-user1.png"/></i></a></li>
                        <li><a href="php/logout.php" id="logout-link"> Déconnexion</a></li>
                    </ul>
                </nav>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="categories.html">Catégories</a></li>
                <li><a href="add-listing.html" class="active">Publier une annonce</a></li>
                <li><a href="messages.html">Messages</a></li>
                <li><a href="auth.php">Connexion / Inscription</a></li>
                <li><a href="php/logout.php" id="logout-link"> Déconnexion</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="add-listing">
                <div class="page-header">
                    <h1><?php 
                        if(isset($_SESSION['listing_id'])){
                            echo "Modifier une annonce";
                        }else{
                            echo "Publier une annonce";
                        }
                    ?></h1>
                    <p>Remplissez les informations pour mettre en ligne votre annonce</p>
                </div>
                
                <form id="listingForm" action="php/add_listing.php" method="POST" enctype="multipart/form-data">
                    <!-- Type d'annonce -->
                    <div class="form-group">
                        <label>Type d'annonce</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="listingType" value="product" checked>
                                <span>Produit</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="listingType" value="service">
                                <span>Service</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Catégorie -->
                    <div class="form-group">
                        <label for="category">Catégorie</label>
                        <select id="category" name="category" required>
                            <?php 
                                if(isset($_SESSION['listing_id'])):
                                    $cat = $listing['category_id']; ?>
                                    <option value="" <?= empty($cat) ? "selected" : "" ?>>Sélectionnez une catégorie</option>
                                    <option value="1" <?= ($cat == 1) ? "selected" : "" ?>>Électronique</option>
                                    <option value="2" <?= ($cat == 2) ? "selected" : "" ?>>Livres</option>
                                    <option value="3" <?= ($cat == 3) ? "selected" : "" ?>>Vêtements</option>
                                    <option value="4" <?= ($cat == 4) ? "selected" : "" ?>>Nourriture</option>
                                    <option value="5" <?= ($cat == 5) ? "selected" : "" ?>>Services</option>
                                    <option value="6" <?= ($cat == 6) ? "selected" : "" ?>>Mobilier</option>
                                    <option value="7" <?= ($cat == 7) ? "selected" : "" ?>>Transport</option>
                                    <option value="8" <?= ($cat == 8) ? "selected" : "" ?>>Divers</option>

                                <?php else: ?>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <option value="1">Électronique</option>
                                    <option value="2">Livres</option>
                                    <option value="3">Vêtements</option>
                                    <option value="4">Nourriture</option>
                                    <option value="5">Services</option>
                                    <option value="6">Mobilier</option>
                                    <option value="7">Transport</option>
                                    <option value="8">Divers</option>
                            <?php  endif; ?>
                        </select>
                    </div>
                    
                    <!-- Titre -->
                    <div class="form-group">
                        <label for="title">Titre de l'annonce</label>
                        <input type="text" id="title" name="title" 
                        <?php if(isset($_SESSION['listing_id'])){
                            $title = $listing['title'];
                            echo "value='$title'";
                        }else{
                            echo "placeholder='Ex: Laptop Pro, Cours de maths...'";
                        } ?>
                        required>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5" <?php if(isset($_SESSION['listing_id'])){
                            $description = $listing['description'];
                            echo "value='$description'";
                        }else{
                            echo "placeholder='Décrivez votre produit ou service en détail...'";
                        } ?> required></textarea>
                        <div class="text-count">
                            <span id="charCount">0</span>/2000 caractères
                        </div>
                    </div>
                    
                    <!-- État (pour les produits) -->
                    <div class="form-group" id="productConditionField">
                        <label>État</label>
                        <div class="condition-options">
                            <label class="condition-label">
                                <input type="radio" name="condition" value="Neuf">
                                <span>Neuf</span>
                            </label>
                            <label class="condition-label">
                                <input type="radio" name="condition" value="Presque neuf">
                                <span>Presque neuf</span>
                            </label>
                            <label class="condition-label">
                                <input type="radio" name="condition" value="Bon état">
                                <span>Bon état</span>
                            </label>
                            <label class="condition-label">
                                <input type="radio" name="condition" value="Etat moyen">
                                <span>État moyen</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Prix -->
                    <div class="form-group">
                        <label for="price">Prix (FCFA)</label>
                        <div class="price-input">
                            <input type="number" id="price" name="price" <?php if(isset($_SESSION['listing_id'])){
                            $price = (int)$listing['price'];
                            echo "value='$price'";
                        }else{
                            echo "placeholder='Ex: 5000'";
                        } ?> required>
                            <span class="currency">F</span>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="negotiable" name ="negotiable">
                            <label for="negotiable">Prix négociable</label>
                        </div>
                    </div>
                    
                    <!-- Localisation -->
                    <div class="form-group">
                        <label for="listingLocation">Localisation</label>
                        <select id="listingLocation" name="listingLocation" required>
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
                    
                    <!-- Photos -->
                    <div class="form-group">
                        <label>Photos</label>
                        <div class="photo-upload">
                            <div class="upload-box" id="photoUpload1">
                                <i class="fas fa-plus"></i>
                                <span>Ajouter</span>
                                <input type="file" accept="image/*" name="img1" class="file-input">
                            </div>
                            <div class="upload-box" id="photoUpload2">
                                <i class="fas fa-plus"></i>
                                <span>Ajouter</span>
                                <input type="file" accept="image/*" name="img2" class="file-input">
                            </div>
                            <div class="upload-box" id="photoUpload3">
                                <i class="fas fa-plus"></i>
                                <span>Ajouter</span>
                                <input type="file" accept="image/*" name="img3" class="file-input">
                            </div>
                        </div>
                        <p class="input-hint">Vous pouvez ajouter jusqu'à 3 photos. Première photo = image principale.</p>
                    </div>
                    
                    <!-- Méthodes de paiement -->
                    <div class="form-group">
                        <label>Méthodes de paiement acceptées</label>
                        <div class="checkbox-group">
                            <div>
                                <input type="checkbox" id="mobilePayment" name="method1">
                                <label for="mobilePayment">Mobile Money</label>
                            </div>
                            <div>
                                <input type="checkbox" id="bankTransfer" name="method2">
                                <label for="bankTransfer">Orange Money</label>
                            </div>
                            <input type="hidden" name="status" id="statusInput" value="active">
                        </div>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="form-actions">
                        <button type="submit" class="btn-primary" name ="" ><?php if(isset($_SESSION['listing_id'])){
                            echo "Modifier l'annonce";
                        }else{
                            echo "Publier l'annonce";
                        }?></button>
                        <button type="button" class="btn-secondary" id="saveDraft">Enregistrer comme brouillon</button>
                        <button type="button" class="btn-tertiary" id="cancelListing">Annuler</button>
                    </div>
                </form>
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
        document.getElementById("saveDraft").addEventListener("click", function () {
            document.getElementById("statusInput").value = "inactive";
            document.getElementById("listingForm").submit();
        });
        
        document.getElementById("cancelListing").addEventListener("click", function () {
            if (confirm("Êtes-vous sûr de vouloir annuler ? Votre annonce ne sera pas enregistrée.")) {
                window.location.href = "profile.php"; // ou la page de ton choix
            }
        });
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