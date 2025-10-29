</main> <!-- container-fluid kapanışı -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script> <!-- Özel script dosyası -->

<!-- Toast Bildirim Sistemi -->
<script>
function showToast(message, type = 'success', duration = 5000) {
    const toastContainer = document.querySelector('.toast-container');
    const toastId = 'toast-' + Date.now();
    
    const iconClass = {
        'success': 'bi-check-circle-fill text-success',
        'error': 'bi-exclamation-triangle-fill text-danger',
        'warning': 'bi-exclamation-circle-fill text-warning',
        'info': 'bi-info-circle-fill text-info'
    }[type] || 'bi-info-circle-fill text-info';
    
    const bgClass = {
        'success': 'bg-success',
        'error': 'bg-danger', 
        'warning': 'bg-warning',
        'info': 'bg-info'
    }[type] || 'bg-info';
    
    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="${duration}">
            <div class="toast-header ${bgClass} text-white">
                <i class="bi ${iconClass} me-2"></i>
                <strong class="me-auto">Bildirim</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Kapat"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Toast kapandıktan sonra DOM'dan kaldır
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// URL parametrelerinden toast mesajını kontrol et
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const toastMessage = urlParams.get('toast');
    const toastType = urlParams.get('toast_type') || 'success';
    
    if (toastMessage) {
        showToast(decodeURIComponent(toastMessage), toastType);
        // URL'den parametreleri temizle
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('toast');
        newUrl.searchParams.delete('toast_type');
        window.history.replaceState({}, '', newUrl);
    }
});
</script>