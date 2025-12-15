<?php
/**
 * API: Delete Kos
 * DELETE /backend/api/kos/delete.php?id=1
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requirePemilik();

$kos_id = (int)($_GET['id'] ?? 0);

if ($kos_id <= 0) {
    // Coba ambil dari body
    $input = getJsonInput();
    $kos_id = (int)($input['id'] ?? 0);
}

if ($kos_id <= 0) {
    errorResponse('ID kos tidak valid');
}

$conn = getConnection();
$user_id = getCurrentUserId();

// Cek kepemilikan (kecuali admin)
if (!isAdmin()) {
    $stmt = $conn->prepare("SELECT pemilik_id FROM kos WHERE id = ?");
    $stmt->bind_param("i", $kos_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!$result || $result['pemilik_id'] != $user_id) {
        $stmt->close();
        closeConnection($conn);
        errorResponse('Anda tidak memiliki akses untuk menghapus kos ini', 403);
    }
}

// Hapus foto kos jika ada
$stmt = $conn->prepare("SELECT foto FROM kos WHERE id = ?");
$stmt->bind_param("i", $kos_id);
$stmt->execute();
$kos = $stmt->get_result()->fetch_assoc();

if ($kos && !empty($kos['foto'])) {
    $fotoPath = UPLOAD_PATH . 'kos/' . $kos['foto'];
    if (file_exists($fotoPath)) {
        unlink($fotoPath);
    }
}

// Hapus kos (kamar akan terhapus cascade)
$stmt = $conn->prepare("DELETE FROM kos WHERE id = ?");
$stmt->bind_param("i", $kos_id);

if ($stmt->execute()) {
    $stmt->close();
    closeConnection($conn);
    successResponse('Kos berhasil dihapus');
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal menghapus kos', 500);
}
?>
