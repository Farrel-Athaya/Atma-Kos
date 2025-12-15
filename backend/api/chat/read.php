<?php
/**
 * API: Mark Messages as Read
 * POST /backend/api/chat/read.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

$current_user_id = getCurrentUserId();
$current_role = getUserRole();
$conn = getConnection();

$input = getJsonInput();

if ($current_role === 'admin' && isset($input['user_id'])) {
    // Admin marking user messages as read
    $target_user_id = (int)$input['user_id'];
    $stmt = $conn->prepare("
        UPDATE chat_messages 
        SET is_read = 1 
        WHERE user_id = ? AND sender_type = 'user' AND is_read = 0
    ");
    $stmt->bind_param("i", $target_user_id);
} else {
    // User marking admin messages as read
    $stmt = $conn->prepare("
        UPDATE chat_messages 
        SET is_read = 1 
        WHERE user_id = ? AND sender_type = 'admin' AND is_read = 0
    ");
    $stmt->bind_param("i", $current_user_id);
}

$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
closeConnection($conn);

successResponse('Marked as read', ['affected' => $affected, 'user_id' => $current_user_id]);
?>
