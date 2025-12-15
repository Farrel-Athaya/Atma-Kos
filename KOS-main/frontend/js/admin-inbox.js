/**
 * Admin Inbox Badge - Load unread count for all admin pages
 */

let adminInboxInterval = null;
let adminInboxInitialized = false;

document.addEventListener("DOMContentLoaded", function () {
  // Prevent multiple initializations
  if (adminInboxInitialized) return;
  adminInboxInitialized = true;

  // Load unread count on page load
  loadAdminUnreadCount();

  // Clear any existing interval
  if (adminInboxInterval) {
    clearInterval(adminInboxInterval);
  }

  // Refresh every 30 seconds
  adminInboxInterval = setInterval(loadAdminUnreadCount, 30000);
});

async function loadAdminUnreadCount() {
  try {
    const response = await fetch(API_BASE_URL + "/api/chat/conversations.php", {
      credentials: "include",
    });
    const result = await response.json();

    if (result.success && result.data) {
      const totalUnread = result.data.reduce(
        (sum, c) => sum + (parseInt(c.unread_count) || 0),
        0
      );
      updateAdminInboxBadge(totalUnread);
    }
  } catch (error) {
    console.error("Error loading unread count:", error);
  }
}

function updateAdminInboxBadge(count) {
  const numericCount = parseInt(count);
  const badge = document.getElementById("inboxBadge");
  if (badge) {
    if (numericCount > 0) {
      badge.textContent = numericCount > 99 ? "99+" : numericCount;
      badge.style.display = "inline";
    } else {
      badge.style.display = "none";
    }
  }
}
