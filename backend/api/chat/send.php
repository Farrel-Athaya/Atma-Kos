<?php
/**
 * API: Send Chat Message
 * POST /backend/api/chat/send.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

$input = getJsonInput();
$message = trim($input['message'] ?? '');

if (empty($message)) {
    errorResponse('Pesan tidak boleh kosong');
}

if (strlen($message) > 500) {
    errorResponse('Pesan terlalu panjang (maksimal 500 karakter)');
}

$user_id = getCurrentUserId();
$conn = getConnection();

$stmt = $conn->prepare("
    INSERT INTO chat_messages (user_id, sender_type, message, is_read, created_at) 
    VALUES (?, 'user', ?, 0, NOW())
");
$stmt->bind_param("is", $user_id, $message);

if ($stmt->execute()) {
    $msg_id = $conn->insert_id;
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Pesan terkirim', ['id' => $msg_id]);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal mengirim pesan', 500);
}
?>
