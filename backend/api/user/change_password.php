<?php
/**
 * API: Change Password
 * PUT /backend/api/user/change_password.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

$input = getJsonInput();

$current_password = $input['current_password'] ?? '';
$new_password = $input['new_password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';

// Validasi input
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    errorResponse('Semua field harus diisi');
}

if (strlen($new_password) < 6) {
    errorResponse('Password baru minimal 6 karakter');
}

if ($new_password !== $confirm_password) {
    errorResponse('Konfirmasi password tidak cocok');
}

$conn = getConnection();
$user_id = getCurrentUserId();

// Ambil password lama
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verifikasi password lama
if (!password_verify($current_password, $user['password'])) {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Password saat ini salah');
}

// Hash password baru
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password
$stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    closeConnection($conn);
    successResponse('Password berhasil diubah');
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal mengubah password', 500);
}
?>
