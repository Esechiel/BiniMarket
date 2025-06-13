
document.addEventListener('DOMContentLoaded', function () {
    const serviceSection = document.getElementById('popular-services');
    if (serviceSection) {
        const iframe = document.createElement('iframe');
        iframe.src = 'php/get_popular_services.php';
        iframe.style.width = '100%';
        iframe.style.border = 'none';
        serviceSection.appendChild(iframe);
    }
});
