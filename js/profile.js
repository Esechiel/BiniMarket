// js/profile.js

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets du profil
    const profileTabs = document.querySelectorAll('.profile-tab');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    if (profileTabs.length > 0 && tabPanes.length > 0) {
        profileTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Enlever la classe active de tous les onglets
                profileTabs.forEach(t => t.classList.remove('active'));
                
                // Ajouter la classe active à l'onglet cliqué
                this.classList.add('active');
                
                // Récupérer l'ID de l'onglet à afficher
                const tabId = this.getAttribute('data-tab');
                
                // Cacher tous les contenus d'onglets
                tabPanes.forEach(pane => pane.classList.remove('active'));
                
                // Afficher le contenu de l'onglet sélectionné
                document.getElementById(tabId + 'Tab').classList.add('active');
            });
        });
        
        // Vérifier si un onglet spécifique est demandé dans l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        if (tab) {
            const targetTab = document.querySelector(`.profile-tab[data-tab="${tab}"]`);
            if (targetTab) {
                targetTab.click();
            }
        }
    }

    // Gestion des onglets de paramètres
    const settingsTabs = document.querySelectorAll('.settings-tab');
    const settingsPanes = document.querySelectorAll('.settings-pane');
    
    if (settingsTabs.length > 0 && settingsPanes.length > 0) {
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Enlever la classe active de tous les onglets
                settingsTabs.forEach(t => t.classList.remove('active'));
                
                // Ajouter la classe active à l'onglet cliqué
                this.classList.add('active');
                
                // Récupérer l'ID de l'onglet à afficher
                const tabId = this.getAttribute('data-settings-tab');
                
                // Cacher tous les contenus d'onglets
                settingsPanes.forEach(pane => pane.classList.remove('active'));
                
                // Afficher le contenu de l'onglet sélectionné
                document.getElementById(tabId + 'Settings').classList.add('active');
            });
        });
    }
    
    // Gestion des filtres d'annonces dynamiques via PHP
    
    
    // Gestion des boutons d'action sur les annonces
    const editButtons = document.querySelectorAll('.action-btn.edit');
    const pauseButtons = document.querySelectorAll('.action-btn.pause');
    const deleteButtons = document.querySelectorAll('.action-btn.delete');
    
    if (editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const listingCard = this.closest('.listing-card');
                const listingId = listingCard ? listingCard.getAttribute('data-id') : null;
                
                if (listingId) {
                    // Rediriger vers la page d'édition de l'annonce
                    window.location.href = `add-listing.html?edit=${listingId}`;
                }
            });
        });
    }
    
    if (pauseButtons.length > 0) {
        pauseButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const listingCard = this.closest('.listing-card');
                
                if (listingCard) {
                    const badge = listingCard.querySelector('.listing-badge');
                    if (badge && badge.classList.contains('active')) {
                        badge.textContent = 'INACTIVE';
                        badge.classList.remove('active');
                        badge.classList.add('paused');
                        badge.style.backgroundColor = '#f39c12';
                        
                        this.innerHTML = '<i class="fas fa-play"><img src="images/icons/img-play.png"/></i>';
                        
                        showToast('Annonce mise en pause');
                    } else {
                        badge.textContent = 'ACTIVE';
                        badge.classList.remove('paused');
                        badge.classList.add('active');
                        badge.style.backgroundColor = '#339c12';
                        
                        this.innerHTML = '<i class="fas fa-pause"><img src="images/icons/img-pause.png"/></i>';
                        
                        showToast('Annonce réactivée');
                    }
                    if (badge && badge.classList.contains('completé')) {
                        badge.textContent = 'REFUSE';
                        badge.classList.remove('completé');
                        badge.classList.add('refusé');
                        badge.style.backgroundColor = '#f39c12';
                        this.innerHTML = '<i class="fas fa-play"><img src="images/icons/img-play.png"/></i>';
                        
                    } else {
                        badge.textContent = 'COMPLETE';
                        badge.classList.remove('refusé');
                        badge.classList.add('completé');
                        badge.style.backgroundColor = '#339c12'; 
                        this.innerHTML = '<i class="fas fa-pause"><img src="images/icons/img-pause.png"/></i>';
                    }
                }
            });
        });
    }
    
    if (deleteButtons.length > 0) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const listingCard = this.closest('.listing-card');
                
                if (listingCard && confirm('Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.')) {
                    // Animation de suppression
                    listingCard.style.transition = 'all 0.3s';
                    listingCard.style.opacity = '0';
                    listingCard.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        listingCard.remove();
                        showToast('Annonce supprimée');
                    }, 300);
                }
            });
        });
    }
    
    // Gestion du formulaire de modification du profil
    const editProfileBtn = document.getElementById('editProfileBtn');
    
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            const settingsTab = document.querySelector('.profile-tab[data-tab="settings"]');
            if (settingsTab) {
                settingsTab.click(); // Ouvre l'onglet des paramètres
            }
        });
    }
    
    // Gestion du formulaire de compte
    const accountForm = document.getElementById('accountForm');
    
    if (accountForm) {
        accountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Dans une implémentation réelle, envoyer les données au serveur
            showToast('Vos informations ont été mises à jour');
        });
    }
});

// Fonction pour afficher un toast de notification
function showToast(message, type = 'success') {
    // Vérifier si la fonction globale existe
    if (window.showToast) {
        window.showToast(message, type);
        return;
    }
    
    // Implémentation locale si la fonction globale n'est pas disponible
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Styles inline pour le toast
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.backgroundColor = type === 'success' ? '#4CAF50' : '#F44336';
    toast.style.color = 'white';
    toast.style.padding = '12px 20px';
    toast.style.borderRadius = '4px';
    toast.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.2)';
    toast.style.zIndex = '9999';
    toast.style.transition = 'all 0.3s';
    toast.style.transform = 'translateY(100px)';
    toast.style.opacity = '0';
    
    document.body.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 10);
    
    // Disparition automatique
    setTimeout(() => {
        toast.style.transform = 'translateY(100px)';
        toast.style.opacity = '0';
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
