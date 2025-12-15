<?php
/**
 * API: Update User Profile
 * PUT /backend/api/user/update.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

$input = getJsonInput();

$nama = sanitize($input['nama'] ?? '');
$telepon = sanitize($input['telepon'] ?? '');
$alamat = sanitize($input['alamat'] ?? '');

if (empty($nama)) {
    errorResponse('Nama harus diisi');
}

$conn = getConnection();
$user_id = getCurrentUserId();

$stmt = $conn->prepare("UPDATE users SET nama = ?, telepon = ?, alamat = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("sssi", $nama, $telepon, $alamat, $user_id);

if ($stmt->execute()) {
    // Update session
    $_SESSION['nama'] = $nama;
    
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Profil berhasil diupdate', [
        'nama' => $nama,
        'telepon' => $telepon,
        'alamat' => $alamat
    ]);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal mengupdate profil', 500);
}
?>
