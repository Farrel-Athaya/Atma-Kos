<?php
/**
 * API: Admin - Get All Chat Conversations
 * GET /backend/api/chat/conversations.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

requireAdmin();

$conn = getConnection();

// Get all users who have sent messages, with last message and unread count
$sql = "
    SELECT 
        u.id as user_id,
        u.nama,
        u.email,
        u.foto,
        (SELECT message FROM chat_messages WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM chat_messages WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as last_time,
        (SELECT COUNT(*) FROM chat_messages WHERE user_id = u.id AND sender_type = 'user' AND is_read = 0) as unread_count
    FROM users u
    WHERE u.id IN (SELECT DISTINCT user_id FROM chat_messages)
    ORDER BY last_time DESC
";

$result = $conn->query($sql);
$conversations = $result->fetch_all(MYSQLI_ASSOC);

closeConnection($conn);

successResponse('Conversations loaded', $conversations);
?>
