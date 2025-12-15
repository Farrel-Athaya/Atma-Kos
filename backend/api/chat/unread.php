<?php
/**
 * API: Get Unread Count
 * GET /backend/api/chat/unread.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

$user_id = getCurrentUserId();
$conn = getConnection();

// Count unread messages from admin
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM chat_messages 
    WHERE user_id = ? AND sender_type = 'admin' AND is_read = 0
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$stmt->close();
closeConnection($conn);

successResponse('Unread count', ['count' => (int)$result['count']]);
?>
