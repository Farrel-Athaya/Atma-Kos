// Konfigurasi API - Otomatis detect URL
const API_BASE_URL = (function() {
    const currentUrl = window.location.href;
    const pathArray = currentUrl.split('/');
    const protocol = pathArray[0];
    const host = pathArray[2];
    
    // Cari posisi 'frontend' di URL dan ganti dengan 'backend'
    const frontendIndex = currentUrl.indexOf('/frontend');
    if (frontendIndex !== -1) {
        return currentUrl.substring(0, frontendIndex) + '/backend';
    }
    
    // Fallback jika struktur berbeda
    return protocol + '//' + host + '/KOS/backend';
})();

// Frontend base URL untuk navigasi
const FRONTEND_BASE_URL = (function() {
    const currentUrl = window.location.href;
    const frontendIndex = currentUrl.indexOf('/frontend');
    if (frontendIndex !== -1) {
        return currentUrl.substring(0, frontendIndex) + '/frontend';
    }
    return window.location.origin + '/KOS/frontend';
})();

// Upload base URL untuk akses file uploads
const UPLOAD_BASE_URL = (function() {
    const currentUrl = window.location.href;
    const frontendIndex = currentUrl.indexOf('/frontend');
    if (frontendIndex !== -1) {
        return currentUrl.substring(0, frontendIndex) + '/uploads';
    }
    return window.location.origin + '/KOS/uploads';
})();
