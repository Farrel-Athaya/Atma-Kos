/**
 * Authentication Helper Functions
 */

// Check if user is logged in
async function checkAuth() {
    try {
        const response = await API.auth.checkSession();
        if (response.success && response.data.logged_in) {
            return response.data.user;
        }
        return null;
    } catch (error) {
        console.error('Auth check error:', error);
        return null;
    }
}

// Require authentication - redirect to login if not authenticated
async function requireAuth() {
    const user = await checkAuth();
    if (!user) {
        window.location.href = FRONTEND_BASE_URL + '/auth/login.html';
        return null;
    }
    return user;
}

// Require admin role
async function requireAdmin() {
    const user = await checkAuth();
    if (!user) {
        window.location.href = FRONTEND_BASE_URL + '/admin/login.html';
        return null;
    }
    if (user.role !== 'admin') {
        alert('Akses ditolak. Halaman ini hanya untuk administrator.');
        window.location.href = FRONTEND_BASE_URL + '/index.html';
        return null;
    }
    return user;
}

// Admin logout function
async function adminLogout() {
    try {
        await API.auth.logout();
        localStorage.removeItem('user');
        window.location.href = FRONTEND_BASE_URL + '/admin/login.html';
    } catch (error) {
        console.error('Logout error:', error);
        localStorage.removeItem('user');
        window.location.href = FRONTEND_BASE_URL + '/admin/login.html';
    }
}

// Logout function
async function logout() {
    try {
        await API.auth.logout();
        localStorage.removeItem('user');
        window.location.href = FRONTEND_BASE_URL + '/index.html';
    } catch (error) {
        console.error('Logout error:', error);
        localStorage.removeItem('user');
        window.location.href = FRONTEND_BASE_URL + '/index.html';
    }
}

// Get current user from localStorage (quick access)
function getCurrentUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}
