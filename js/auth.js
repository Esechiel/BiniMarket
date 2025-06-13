// js/auth.js

document.addEventListener('DOMContentLoaded', function() {
    // Variables pour les onglets et formulaires
    const loginTab = document.querySelector('[data-tab="login"]');
    const registerTab = document.querySelector('[data-tab="register"]');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    // Switch entre les onglets de connexion et d'inscription
    if (loginTab && registerTab) {
        loginTab.addEventListener('click', function() {
            switchTab('login');
        });
        
        registerTab.addEventListener('click', function() {
            switchTab('register');
        });
        
        // Vérifier si un onglet spécifique est demandé dans l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        if (tab === 'register') {
            switchTab('register');
        } else {
            switchTab('login');
        }
    }
    
    // Soumission du formulaire de connexion
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email_con').value;
            const password = document.getElementById('pass_con').value;
            
            if (!email || !password) {
                alert('Veuillez remplir tous les champs');
                return;
            }
            
            
            document.getElementById('idform_con').submit();
        });
    }
    
    // Soumission du formulaire d'inscription
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer les valeurs du formulaire
            const pseudo = document.getElementById('pseudo').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const pass = document.getElementById('pass').value;
            const confirmPass = document.getElementById('confirmPass').value;
            const location = document.getElementById('location').value;
            const termsAgreement = document.getElementById('termsAgreement').checked;
            
            // Validation basique
            if (!pseudo || !email || !phone || !pass || !confirmPass || !location) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            if (pass !== confirmPass) {
                alert('Les mots de passe ne correspondent pas');
                return;
            }
            
            if (!termsAgreement) {
                alert('Vous devez accepter les conditions d\'utilisation');
                return;
            }
            
            if (pass.length < 8) {
                alert('Le mot de passe doit contenir au moins 8 caractères');
                return;
            }
            
            // Validation de l'email
            if (!isValidEmail(email)) {
                alert('Veuillez entrer une adresse email valide');
                return;
            }
            
            // Validation du téléphone (format camerounais)
            if (!isValidPhone(phone)) {
                alert('Veuillez entrer un numéro de téléphone valide (format:  6XXXXXXXX)');
                return;
            }
            
            document.getElementById('idform').submit();
        });
    }
    
    // Mot de passe oublié
    const forgotPasswordLink = document.querySelector('.forgot-password');
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            const email = prompt('Veuillez entrer votre adresse email pour réinitialiser votre mot de passe:');
            
            if (email && isValidEmail(email)) {
                alert('Un email de réinitialisation a été envoyé à ' + email);
            } else if (email) {
                alert('Veuillez entrer une adresse email valide');
            }
        });
    }
});

// Fonction pour changer d'onglet
function switchTab(tabName) {
    const loginTab = document.querySelector('[data-tab="login"]');
    const registerTab = document.querySelector('[data-tab="register"]');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (tabName === 'login') {
        // Activer l'onglet de connexion
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        
        // Afficher le formulaire de connexion
        loginForm.classList.add('active');
        registerForm.classList.remove('active');
    } else {
        // Activer l'onglet d'inscription
        loginTab.classList.remove('active');
        registerTab.classList.add('active');
        
        // Afficher le formulaire d'inscription
        loginForm.classList.remove('active');
        registerForm.classList.add('active');
    }
}

// Fonction pour afficher un message
function showMessage(message, type) {
    // Supprimer tout message existant
    const existingMessage = document.querySelector('.auth-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Créer un nouvel élément de message
    const messageElement = document.createElement('div');
    messageElement.className = `auth-message ${type}`;
    messageElement.textContent = message;
    
    // Ajouter le message au formulaire actif
    const activeForm = document.querySelector('.auth-form.active');
    if (activeForm) {
        activeForm.insertBefore(messageElement, activeForm.firstChild);
        
        // Faire disparaître le message après quelques secondes
        setTimeout(() => {
            messageElement.classList.add('fade-out');
            setTimeout(() => {
                messageElement.remove();
            }, 500);
        }, 3000);
    }
}

// Validation d'email
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// Validation de numéro de téléphone camerounais
function isValidPhone(phone) {
    // Format: 6XXXXXXXX (9 chiffres, commençant par 6)
    const cleaned = phone.replace(/\s/g, '');
    const re = /^6\d{8}$/;
    return re.test(cleaned);
}