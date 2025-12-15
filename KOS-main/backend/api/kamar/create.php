<?php
/**
 * API: Create Kamar
 * POST /backend/api/kamar/create.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAdmin();

// Support both JSON and FormData
$kos_id = (int)($_POST['kos_id'] ?? 0);
$nomor = sanitize($_POST['nomor'] ?? '');
$harga = (float)($_POST['harga'] ?? 0);
$ukuran = sanitize($_POST['ukuran'] ?? '');
$fasilitas = sanitize($_POST['fasilitas'] ?? '');
$status = sanitize($_POST['status'] ?? 'tersedia');

// Validasi
if ($kos_id <= 0 || empty($nomor) || $harga <= 0) {
    errorResponse('Kos ID, nomor kamar, dan harga harus diisi');
}

if (!in_array($status, ['tersedia', 'terisi', 'maintenance'])) {
    $status = 'tersedia';
}

$conn = getConnection();

// Handle foto upload
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $uploadResult = uploadFile($_FILES['foto'], 'kamar');
    if ($uploadResult['success']) {
        $foto = $uploadResult['filename'];
    }
}

$stmt = $conn->prepare("INSERT INTO kamar (kos_id, nomor, harga, ukuran, fasilitas, status, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isdssss", $kos_id, $nomor, $harga, $ukuran, $fasilitas, $status, $foto);

if ($stmt->execute()) {
    $kamar_id = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Kamar berhasil ditambahkan', [
        'kamar_id' => $kamar_id
    ]);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal menambahkan kamar', 500);
}
?>
