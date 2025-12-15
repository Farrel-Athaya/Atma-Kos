<?php
/**
 * API: Update Kamar
 * POST /backend/api/kamar/update.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAdmin();

// Support FormData
$kamar_id = (int)($_POST['id'] ?? 0);
$nomor = sanitize($_POST['nomor'] ?? '');
$harga = (float)($_POST['harga'] ?? 0);
$ukuran = sanitize($_POST['ukuran'] ?? '');
$fasilitas = sanitize($_POST['fasilitas'] ?? '');
$status = sanitize($_POST['status'] ?? 'tersedia');

// Validasi
if ($kamar_id <= 0 || empty($nomor) || $harga <= 0) {
    errorResponse('ID kamar, nomor kamar, dan harga harus diisi');
}

$conn = getConnection();

// Handle foto upload
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    // Hapus foto lama
    $stmt = $conn->prepare("SELECT foto FROM kamar WHERE id = ?");
    $stmt->bind_param("i", $kamar_id);
    $stmt->execute();
    $old = $stmt->get_result()->fetch_assoc();
    if ($old && $old['foto']) {
        deleteFile($old['foto'], 'kamar');
    }
    
    $uploadResult = uploadFile($_FILES['foto'], 'kamar');
    if ($uploadResult['success']) {
        $foto = $uploadResult['filename'];
    }
}

if ($foto) {
    $stmt = $conn->prepare("UPDATE kamar SET nomor = ?, harga = ?, ukuran = ?, fasilitas = ?, status = ?, foto = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sdssssi", $nomor, $harga, $ukuran, $fasilitas, $status, $foto, $kamar_id);
} else {
    $stmt = $conn->prepare("UPDATE kamar SET nomor = ?, harga = ?, ukuran = ?, fasilitas = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sdsssi", $nomor, $harga, $ukuran, $fasilitas, $status, $kamar_id);
}

if ($stmt->execute()) {
    $stmt->close();
    closeConnection($conn);
    successResponse('Kamar berhasil diupdate');
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal mengupdate kamar', 500);
}
?>
