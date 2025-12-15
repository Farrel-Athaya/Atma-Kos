<?php
/**
 * API: Admin - Get Messages for a User
 * GET /backend/api/chat/admin_messages.php?user_id=1
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

requireAdmin();

$user_id = (int)($_GET['user_id'] ?? 0);

if (!$user_id) {
    errorResponse('User ID required');
}

$conn = getConnection();

// Get user info
$stmt = $conn->prepare("SELECT id, nama, foto FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    errorResponse('User not found', 404);
}

// Get messages
$stmt = $conn->prepare("
    SELECT id, user_id, sender_type, message, is_read, created_at 
    FROM chat_messages 
    WHERE user_id = ? 
    ORDER BY created_at ASC
    LIMIT 100
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Mark user messages as read
$stmt = $conn->prepare("
    UPDATE chat_messages 
    SET is_read = 1 
    WHERE user_id = ? AND sender_type = 'user' AND is_read = 0
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$stmt->close();
closeConnection($conn);

successResponse('Messages loaded', [
    'user' => $user,
    'messages' => $messages
]);
?>
