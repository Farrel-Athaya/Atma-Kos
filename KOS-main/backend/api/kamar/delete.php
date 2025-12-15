<?php
/**
 * API: Delete Kamar
 * DELETE /backend/api/kamar/delete.php?id=1
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAdmin();

$kamar_id = (int)($_GET['id'] ?? 0);

if ($kamar_id <= 0) {
    $input = getJsonInput();
    $kamar_id = (int)($input['id'] ?? 0);
}

if ($kamar_id <= 0) {
    errorResponse('ID kamar tidak valid');
}

$conn = getConnection();

// Hapus foto jika ada
$stmt = $conn->prepare("SELECT foto FROM kamar WHERE id = ?");
$stmt->bind_param("i", $kamar_id);
$stmt->execute();
$kamar = $stmt->get_result()->fetch_assoc();
if ($kamar && $kamar['foto']) {
    deleteFile($kamar['foto'], 'kamar');
}

$stmt = $conn->prepare("DELETE FROM kamar WHERE id = ?");
$stmt->bind_param("i", $kamar_id);

if ($stmt->execute()) {
    $stmt->close();
    closeConnection($conn);
    successResponse('Kamar berhasil dihapus');
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal menghapus kamar', 500);
}
?>
