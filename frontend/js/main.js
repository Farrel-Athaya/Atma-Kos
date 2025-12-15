/**
 * Main JavaScript untuk KosKu
 * Fungsi-fungsi umum yang digunakan di semua halaman
 */

// Document ready
document.addEventListener('DOMContentLoaded', async function() {
    // Update navbar based on auth status
    await updateNavbar();
    
    // Setup logout button
    setupLogout();
    
    // Setup mobile navigation toggle
    setupMobileNav();
});

/**
 * Update navbar berdasarkan status login
 */
async function updateNavbar() {
    try {
        const user = await checkAuth();
        
        const guestMenus = document.querySelectorAll('.guest-menu');
        const userMenus = document.querySelectorAll('.user-menu');
        const adminMenus = document.querySelectorAll('.admin-menu');
        const adminLinks = document.querySelectorAll('.admin-link');
        
        if (user) {
            // User sudah login - show user menu, hide guest menu
            guestMenus.forEach(menu => {
                menu.classList.remove('show');
                menu.style.display = 'none';
            });
            userMenus.forEach(menu => {
                menu.classList.add('show');
                menu.style.display = 'block';
            });
            
            // Update nama user di navbar (nama depan saja)
            const navUserName = document.getElementById('navUserName');
            if (navUserName) {
                const fullName = user.nama || user.username;
                const firstName = fullName.split(' ')[0];
                navUserName.textContent = firstName;
            }
            
            // Update foto profil
            const navProfileImg = document.getElementById('navProfileImg');
            if (navProfileImg) {
                if (user.foto) {
                    navProfileImg.src = UPLOAD_BASE_URL + '/profiles/' + user.foto;
                    navProfileImg.onerror = function() {
                        this.src = 'assets/images/default-avatar.svg';
                    };
                } else {
                    navProfileImg.src = 'assets/images/default-avatar.svg';
                }
            }
            
            // Tampilkan menu admin jika role admin
            if (user.role === 'admin') {
                adminMenus.forEach(menu => {
                    menu.classList.add('show');
                    menu.style.display = 'block';
                });
                adminLinks.forEach(link => {
                    link.classList.add('show');
                    link.style.display = 'block';
                });
            }
            
            // Simpan user ke localStorage
            localStorage.setItem('user', JSON.stringify(user));
        } else {
            // User belum login - show guest menu, hide user menu
            guestMenus.forEach(menu => {
                menu.classList.add('show');
                menu.style.display = 'block';
            });
            userMenus.forEach(menu => {
                menu.classList.remove('show');
                menu.style.display = 'none';
            });
            adminMenus.forEach(menu => {
                menu.classList.remove('show');
                menu.style.display = 'none';
            });
            adminLinks.forEach(link => {
                link.classList.remove('show');
                link.style.display = 'none';
            });
            
            localStorage.removeItem('user');
        }
    } catch (error) {
        console.error('Error updating navbar:', error);
    }
}

/**
 * Setup logout button
 */
function setupLogout() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            await logout();
        });
    }
}

/**
 * Setup mobile navigation toggle
 */
function setupMobileNav() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
}

/**
 * Format harga ke format Rupiah
 */
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(angka);
}

/**
 * Format tanggal
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

/**
 * Tampilkan loading spinner
 */
function showLoading(container) {
    if (typeof container === 'string') {
        container = document.querySelector(container);
    }
    if (container) {
        container.innerHTML = `
            <div class="loading">
                <div class="spinner"></div>
                <p>Memuat data...</p>
            </div>
        `;
    }
}

/**
 * Tampilkan pesan error
 */
function showError(container, message) {
    if (typeof container === 'string') {
        container = document.querySelector(container);
    }
    if (container) {
        container.innerHTML = `
            <div class="error-state">
                <p>❌ ${message}</p>
            </div>
        `;
    }
}

/**
 * Tampilkan empty state
 */
function showEmpty(container, message) {
    if (typeof container === 'string') {
        container = document.querySelector(container);
    }
    if (container) {
        container.innerHTML = `
            <div class="empty-state">
                <p>📭 ${message}</p>
            </div>
        `;
    }
}

/**
 * Tampilkan alert/notifikasi
 */
function showAlert(message, type = 'info') {
    // Hapus alert sebelumnya
    const existingAlerts = document.querySelectorAll('.alert-notification');
    existingAlerts.forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-notification`;
    alert.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px; animation: slideIn 0.3s ease;';
    alert.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; margin-left: 10px; font-size: 18px;">&times;</button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}

/**
 * Get badge class untuk tipe kos
 */
function getTipeBadgeClass(tipe) {
    switch (tipe.toLowerCase()) {
        case 'putra':
            return 'badge-putra';
        case 'putri':
            return 'badge-putri';
        case 'campur':
            return 'badge-campur';
        default:
            return '';
    }
}

/**
 * Get badge class untuk status kamar
 */
function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'tersedia':
            return 'status-badge tersedia';
        case 'terisi':
            return 'status-badge terisi';
        case 'maintenance':
            return 'status-badge maintenance';
        default:
            return 'status-badge';
    }
}
