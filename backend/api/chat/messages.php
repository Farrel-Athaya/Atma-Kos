<?php
/**
 * API: Get Chat Messages
 * GET /backend/api/chat/messages.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

$user_id = getCurrentUserId();
$conn = getConnection();

// Get messages for this user
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

$stmt->close();
closeConnection($conn);

successResponse('Messages loaded', $messages);
?>
