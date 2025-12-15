/**
 * API Wrapper untuk KosKu
 * Menyediakan fungsi-fungsi untuk komunikasi dengan backend
 */

const API = {
    /**
     * Authentication API
     */
    auth: {
        // Login
        login: async (username, password) => {
            const response = await fetch(API_BASE_URL + '/api/auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ username, password })
            });
            return response.json();
        },

        // Register
        register: async (data) => {
            const response = await fetch(API_BASE_URL + '/api/auth/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });
            return response.json();
        },

        // Logout
        logout: async () => {
            const response = await fetch(API_BASE_URL + '/api/auth/logout.php', {
                method: 'POST',
                credentials: 'include'
            });
            return response.json();
        },

        // Check session
        checkSession: async () => {
            const response = await fetch(API_BASE_URL + '/api/auth/session.php', {
                credentials: 'include'
            });
            return response.json();
        },

        // Check username/email availability
        checkAvailability: async (field, value) => {
            const response = await fetch(API_BASE_URL + `/api/auth/check.php?field=${field}&value=${encodeURIComponent(value)}`, {
                credentials: 'include'
            });
            return response.json();
        }
    },

    /**
     * User Profile API
     */
    user: {
        // Get profile
        getProfile: async () => {
            const response = await fetch(API_BASE_URL + '/api/user/profile.php', {
                credentials: 'include'
            });
            return response.json();
        },

        // Update profile
        update: async (data) => {
            const response = await fetch(API_BASE_URL + '/api/user/update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });
            return response.json();
        },

        // Upload photo
        uploadPhoto: async (formData) => {
            const response = await fetch(API_BASE_URL + '/api/user/upload_photo.php', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });
            return response.json();
        },

        // Change password
        changePassword: async (data) => {
            const response = await fetch(API_BASE_URL + '/api/user/change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });
            return response.json();
        }
    },

    /**
     * Kos API
     */
    kos: {
        // Get list
        getList: async (params = {}) => {
            const queryString = new URLSearchParams(params).toString();
            const response = await fetch(API_BASE_URL + '/api/kos/list.php?' + queryString, {
                credentials: 'include'
            });
            return response.json();
        },

        // Get detail
        getDetail: async (id) => {
            const response = await fetch(API_BASE_URL + '/api/kos/detail.php?id=' + id, {
                credentials: 'include'
            });
            return response.json();
        },

        // Create
        create: async (formData) => {
            const response = await fetch(API_BASE_URL + '/api/kos/create.php', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });
            return response.json();
        },

        // Update
        update: async (id, formData) => {
            formData.append('id', id);
            const response = await fetch(API_BASE_URL + '/api/kos/update.php', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });
            return response.json();
        },

        // Delete
        delete: async (id) => {
            const response = await fetch(API_BASE_URL + '/api/kos/delete.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ id })
            });
            return response.json();
        }
    },

    /**
     * Kamar API
     */
    kamar: {
        // Get list
        getList: async (params = {}) => {
            const queryString = new URLSearchParams(params).toString();
            const response = await fetch(API_BASE_URL + '/api/kamar/list.php?' + queryString, {
                credentials: 'include'
            });
            return response.json();
        },

        // Create
        create: async (formData) => {
            const response = await fetch(API_BASE_URL + '/api/kamar/create.php', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });
            return response.json();
        },

        // Update
        update: async (id, formData) => {
            formData.append('id', id);
            const response = await fetch(API_BASE_URL + '/api/kamar/update.php', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });
            return response.json();
        },

        // Delete
        delete: async (id) => {
            const response = await fetch(API_BASE_URL + '/api/kamar/delete.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ id })
            });
            return response.json();
        }
    }
};
