/**
 * User Inbox Badge - Load unread count for user pages
 */

let userInboxInterval = null;
let userInboxInitialized = false;

document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for auth check to complete
    setTimeout(initUserInboxBadge, 800);
});

async function initUserInboxBadge() {
    // Prevent multiple initializations
    if (userInboxInitialized) return;
    
    // Check if user is logged in by checking if user-menu is visible
    const userMenu = document.querySelector('.user-menu');
    if (!userMenu || userMenu.style.display === 'none') {
        return;
    }
    
    userInboxInitialized = true;
    
    // Load unread count once
    await loadUserUnreadCount();
    
    // Clear any existing interval
    if (userInboxInterval) {
        clearInterval(userInboxInterval);
    }
    
    // Refresh every 30 seconds (less frequent to reduce load)
    userInboxInterval = setInterval(loadUserUnreadCount, 30000);
}

async function loadUserUnreadCount() {
    try {
        const response = await fetch(API_BASE_URL + '/api/chat/unread.php', {
            credentials: 'include'
        });
        const result = await response.json();
        
        if (result.success && result.data) {
            updateUserInboxBadge(result.data.count || 0);
        }
    } catch (error) {
        // Silently fail
    }
}

function updateUserInboxBadge(count) {
    const badge = document.getElementById('inboxBadge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = 'inline-flex';
        } else {
            badge.style.display = 'none';
        }
    }
}
