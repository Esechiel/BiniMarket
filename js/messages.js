
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const activeConvId = params.get('conversation_id');

    if (activeConvId) {
        const activeConv = document.querySelector(`.conversation[data-id="${activeConvId}"]`);
        if (activeConv) {
            activeConv.click(); // Simule le clic
        }
    }
    const container = document.getElementById("messagesContainer");
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
    

});
