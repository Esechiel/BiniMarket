// js/payement.js

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');

    // Gestion des onglets Orange Money / Mobile Money
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = tab.getAttribute('data-tab');

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            forms.forEach(form => form.classList.remove('active'));
            if (target === 'login') {
                document.getElementById('loginForm').classList.add('active');
            } else if (target === 'register') {
                document.getElementById('registerForm').classList.add('active');
            }
        });
    });

    // Validation du numéro de téléphone avant soumission
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const phoneInput = form.querySelector('input[name="phone"]');
            const phone = phoneInput.value.trim();

            if (!/^(6[5-9][0-9]{7})$/.test(phone)) {
                e.preventDefault();
                alert("Veuillez entrer un numéro de téléphone valide (ex: 6XXXXXXXX)");
                phoneInput.focus();
            }
        });
    });
});
