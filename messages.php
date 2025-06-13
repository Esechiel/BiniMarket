<?php
    session_start();
    require_once "php/db.php";

    if(!isset($_SESSION['user'])){
        header("Location: auth.php");
    }
    if (!empty($_GET['conversation_id'])) {
        $conversation_id = (int)$_GET['conversation_id'];
    }
    $user_id = $_SESSION['user']['user_id'];

    $sql = "
    SELECT c.*, u.user_id AS participant_id, u.username AS participant_name, u.phone_number, u.profile_picture, u.last_login, u.is_active,
        m.content AS last_message_content, m.sent_date AS last_message_date, m.sender_id,
        
        -- Compte des messages non lus par l'utilisateur dans chaque conversation
        (
            SELECT COUNT(*) 
            FROM messages 
            WHERE conversation_id = c.conversation_id 
                AND is_read = 0 
                AND sender_id != $user_id
        ) AS unread_count

    FROM conversations c
    JOIN conversation_participants cp ON c.conversation_id = cp.conversation_id
    JOIN users u ON u.user_id = cp.user_id
    LEFT JOIN (
        SELECT m1.*
        FROM messages m1
        INNER JOIN (
            SELECT conversation_id, MAX(sent_date) AS last_date
            FROM messages
            GROUP BY conversation_id
        ) m2 ON m1.conversation_id = m2.conversation_id AND m1.sent_date = m2.last_date
    ) m ON c.conversation_id = m.conversation_id

    WHERE c.conversation_id IN (
        SELECT conversation_id FROM conversation_participants WHERE user_id = $user_id
    )
    AND u.user_id != $user_id

    ORDER BY m.sent_date DESC
    ";
    $res = mysqli_query($conn, $sql);
    if (!$res || (mysqli_num_rows($res) == 0)) {
        echo "
            <script>
                alert('Conversation introuvable. Commencer une discussion pour acceder au messagerie.');
                window.location.href = 'index.php';
            </script>";
    exit;
    }

    $conversations = [];
    while($row = mysqli_fetch_assoc($res)) {
        $conversations[] = $row;
    }
    $active_conversation = null;
    foreach ($conversations as $conv) {
        if (isset($conversation_id) && $conv['conversation_id'] == $conversation_id) {
            $active_conversation = $conv;
            break;
        }
    }
    if (isset($conversation_id)) {
        $sql1 = "SELECT l.* FROM listings l 
                 JOIN conversations c ON l.listing_id = c.listing_id
                 WHERE c.conversation_id = $conversation_id";
        $res1 = mysqli_query($conn, $sql1);
        $listing = mysqli_fetch_assoc($res1);
    
        $listing_image = '../icons/petit/img-image.png'; // par défaut
    
        if (!empty($listing)) {
            $listing_id = $listing['listing_id'];
            $img_sql = "SELECT image_path FROM listing_images WHERE is_primary = 1 AND listing_id = $listing_id LIMIT 1";
            $img_res = mysqli_query($conn, $img_sql);
            if ($img_row = mysqli_fetch_assoc($img_res)) {
                $listing_image = $img_row['image_path'];
            }
        }
    }
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
    <title>Messages - BiniMarket</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/messages.css">
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
                        <li><a href="messages.php" class="active"><i class="fas fa-comment"><img src="images/icons/petit/img-chat.png"/></i></a></li>
                        <li><a href="auth.php"><i class="fas fa-user"><img src="images/icons/petit/img-user1.png"/></i></a></li>
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
                <li><a href="messages.php" class="active">Messages</a></li>
                <li><a href="auth.php">Connexion / Inscription</a></li>
                <li><a href="php/logout.php" id="logout-link"> Déconnexion</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="messages-container">
                <!-- Liste des conversations -->
                <div class="conversations-list">
                    <div class="conversations-header">
                        <h2>Messages</h2>
                        <div class="message-search">
                            <input type="text" placeholder="Rechercher une conversation..." id="conversationSearch">
                            <i class="fas fa-search"><img src="images/icons/petit/loupe.png"/></i>
                        </div>
                    </div>
                    
                    <div class="conversations">
                        <?php foreach($conversations as $conver): 
                            $unread = (int)$conver['unread_count'];
                        ?>
                        <a href="messages.php?conversation_id=<?= $conver['conversation_id'] ?>" class="conversation <?php 
                                if(isset($conversation_id)) {
                                    if($conversation_id == $conver['conversation_id']){
                                        echo "active";
                                    }
                                }
                            ?>">
                            <!-- <div class="back-to-conversations" onclick="showConversations()">
                                <i class="fas fa-arrow-left"></i>
                                <span>Retour</span>
                            </div> -->
                        
                            <div class="conversation-avatar">
                                <div class="avatar">
                                    <img src="images/profils/<?= htmlspecialchars($conver['profile_picture']) ?>" alt="Avatar">
                                        <?php if ($conver['is_active'] == 1): ?>
                                            <div class="status online"></div>
                                        <?php else: ?>
                                            <div class="status"></div> <!-- point gris = offline -->
                                        <?php endif; ?>
                                </div>
                            </div>

                            <div class="conversation-info">
                                <div class="conversation-header">
                                    <h3><?= htmlspecialchars($conver['participant_name']) ?></h3>
                                    <h3>
                                    <?php if ($unread > 0): ?>
                                        <span class="unread-count">(<?= $unread ?> non lu<?= $unread > 1 ? 's' : '' ?>)</span>
                                    <?php endif; ?>
                                    </h3>
                                    <span class="timestamp"><?= date('H:i:s', strtotime($conver['last_message_date'])) ?></span>
                                </div>
                                <?php
                                    $preview = strlen($conver['last_message_content']) > 40 ? substr($conver['last_message_content'], 0, 40) . '...' : $conver['last_message_content'];
                                ?>
                                <p class="last-message"><?= htmlspecialchars($preview) ?></p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Zone de conversation -->
                <?php if (isset($conversation_id) && $active_conversation): ?>
                <!-- Affichage de la conversation -->
                <div class="conversation-area">
                    <div class="conversation-header">
                        <div class="contact-info">
                            <div class="contact-avatar">
                                <div class="avatar">
                                    <i class="fas fa-user"><img src="images/profils/<?= htmlspecialchars($active_conversation['profile_picture']) ?>" alt="Avatar"></i>
                                    <?php if ($active_conversation['is_active'] == 1): ?>
                                        <div class="status online"></div>
                                    <?php else: ?>
                                        <div class="status"></div> <!-- point gris = offline -->
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <h3><?= htmlspecialchars($active_conversation['participant_name']) ?></h3>
                                <?php if ($active_conversation['is_active'] == 1): ?>
                                        <span class='status-text'>En ligne</span>
                                    <?php else: ?>
                                        <?php
                                            $last_login = new DateTime($active_conversation['last_login']);
                                            $now = new DateTime();
                                            $interval = $now->diff($last_login);

                                            if ($interval->days == 0) {
                                                if ($interval->h > 0) {
                                                    echo "<span class='status-text'>En ligne il y a {$interval->h}h</span>";
                                                } elseif ($interval->i > 0) {
                                                    echo "<span class='status-text'>En ligne il y a {$interval->i}min</span>";
                                                } else {
                                                    echo "<span class='status-text'>En ligne il y a quelques secondes</span>";
                                                }
                                            } else {
                                                echo "<span class='status-text'>En ligne il y a {$interval->days} jour(s)</span>";
                                            }
                                            ?>
                                    <?php endif; ?>    
                            </div>
                        </div>
                        <div class="conversation-actions">
                            <a class="action-btn" href="tel:<?= $active_conversation['phone_number'] ?>"><i class="fas fa-phone"><img src="images/icons/petit/icons8-phone-24.png"/></i></a>
                            <button class="action-btn"><i class="fas fa-ellipsis-v"><img src="images/icons/petit/icons8-menu-vertical-24.png"/></i></button>
                        </div>
                    </div>
                    
                    <div class="messages-area" id="messagesContainer">
                        <!-- Journée -->
                        <!-- Messages -->
                        <?php
                        if (isset($conversation_id)) {
                            $msg_query = "SELECT * FROM messages WHERE conversation_id = $conversation_id ORDER BY sent_date ASC";
                            $msg_result = mysqli_query($conn, $msg_query);

                            $last_date = '';
                            while ($msg = mysqli_fetch_assoc($msg_result)) {
                                $is_sent = $msg['sender_id'] == $user_id;
                                $msg_date = date('Y-m-d', strtotime($msg['sent_date']));
                                $msg_time = date('H:i', strtotime($msg['sent_date']));

                                if ($msg_date != $last_date) {
                                    echo "<div class='message-date'><span>" . date('d M Y', strtotime($msg_date)) . "</span></div>";
                                    $last_date = $msg_date;
                                }

                                // Déterminer l’état de lecture ou d’envoi
                                $status_icon = '';
                                if ($is_sent) {
                                    if ($msg['is_read']) {
                                        $status_icon = "<i class='fas coche'><img src='images/icons/petit/double-tick-indicator (1).png' alt='Lu'></i>";
                                    } elseif ($msg['is_read']==0) {
                                        $status_icon = "<i class='fas coche'><img src='images/icons/petit/check (1).png' alt='Envoyé'></i>";
                                    } else {
                                        $status_icon = "<i class='fas coche'><img src='images/icons/petit/wall-clock.png' alt='En cours'></i>";
                                    }
                                }

                                echo '<div class="message ' . ($is_sent ? 'sent' : 'received') . '">';
                                echo '<div class="message-content">';
                                echo '<p>' . htmlspecialchars($msg['content']) . '</p>';
                                echo '<span class="message-time">' . $msg_time . '</span>';
                                echo $status_icon;
                                echo '</div></div>';
                            }

                            // Marquer les messages reçus comme lus
                            mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE conversation_id = $conversation_id AND sender_id != $user_id AND is_read = 0");
                        }
                        ?>

                    <form action="php/send-message.php" method="post" class="message-input" id="message-input">
                        <button class="attachment-btn"><i class="fas fa-plus"><img src="images/icons/petit/icons8-plus-math-24 (2).png"/></i></button>
                        <button type="file" class="photo-btn"><i class="fas fa-image"><img src="images/icons/petit/icons8-image-24.png"/></i></button>
                        <input type="hidden" name="conversation_id" value="<?= $conversation_id ?>">
                        <input type="hidden" name="receiver" value="<?= $active_conversation['participant_id'] ?>" >
                        <input type="text" name="content" placeholder="Écrivez un message..." id="messageInput" required>
                        <button type="submit" class="send-btn" id="sendMessage"><i class="fas fa-paper-plane"><img src="images/icons/petit/img-telegram.png"/></i></button>
                    </form>
                </div>
                <?php else: ?>
                    <div class="conversation-area empty">
                        <p class="conver_empty">Sélectionne une conversation pour commencer à discuter.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php if(isset($conversation_id)): ?>
                <div class="product-details">
                    <div class="product-header">
                        <h3>Détails de l'annonce</h3>
                    </div>
                    <div class="product-card">
                        <div class="product-image">
                            <div class="image-placeholder">
                                <img class="listing-thumbnail" src="images/annonces/<?= htmlspecialchars($listing_image) ?>" alt="Annonce"/>
                            </div>
                        </div>
                        <div class="product-info">
                            <h4><?= htmlspecialchars($listing['title']) ?></h4>
                            <p class="product-price"><?= number_format($listing['price'], 0, ',', ' ') ?> F</p>
                            <p class="product-status">
                                <?php 
                                    if(htmlspecialchars($listing['status']) != 'active'){
                                        echo 'Disponible';
                                    }else {
                                        echo 'Indisponible';
                                    }    
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="product-details.php?id=<?= $listing['listing_id'] ?>" class="btn-secondary">Voir l'annonce</a>
                        <button class="btn-tertiary" id="reportUser">
                            <img src="images/icons/petit/icons8-drapeau-24.png" alt="Signaler"/> Signaler
                        </button>
                    </div>
                </div>
                <?php endif; ?>
        </div>
    </main>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.has('conversation_id') || params.has('success')) {
                const target = document.getElementById('message-input');
                if (target) {
                    const yOffset = -600; // ← Décalage pour compenser le header (ajuste selon sa hauteur)
                    const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
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
        function showConversationArea() {
            document.querySelector('.conversations-list').style.display = 'none';
            document.querySelector('.conversation-area').classList.add('active');
        }

        function showConversations() {
            document.querySelector('.conversations-list').style.display = 'block';
            document.querySelector('.conversation-area').classList.remove('active');
        }

        // Active sur tous les éléments .conversation
        document.querySelectorAll('.conversation').forEach(conv => {
            conv.addEventListener('click', function () {
                // Active uniquement sur mobile
                if (window.innerWidth <= 1024) {
                    showConversationArea();
                }
            });
        });
    </script>
    <script src="js/common.js"></script>
    <script src="js/messages.js"></script>
</body>
</html>