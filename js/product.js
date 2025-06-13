

document.addEventListener('DOMContentLoaded', function () {
    // Affiche les produits récents dans la page d'accueil
    const productSection = document.getElementById('recent-products');
    if (productSection) {
        const iframe = document.createElement('iframe');
        iframe.src = 'php/get_recent_products.php';
        iframe.style.width = '100%';
        iframe.style.border = 'none';
        productSection.appendChild(iframe);
    }

    // Gestion du clic sur une carte produit
    document.addEventListener('click', function (e) {
        const productCard = e.target.closest('.product-card');
        if (productCard && productCard.dataset.id) {
            window.location.href = 'product-details.php?id=' + productCard.dataset.id;
        }
    });

    // Formulaire d'ajout d'une annonce
    const addListingForm = document.getElementById('addListingForm');
    if (addListingForm) {
        addListingForm.addEventListener('submit', function () {
            addListingForm.action = 'php/add_listing.php';
        });
    }
});