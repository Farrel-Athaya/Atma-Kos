<?php
/**
 * API: Update Kos
 * PUT/POST /backend/api/kos/update.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requirePemilik();

// Support both FormData and JSON input
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $input = getJsonInput();
    $kos_id = (int)($input['id'] ?? 0);
    $nama_kos = sanitize($input['nama_kos'] ?? $input['nama'] ?? '');
    $alamat = sanitize($input['alamat'] ?? '');
    $deskripsi = sanitize($input['deskripsi'] ?? '');
    $latitude = (float)($input['latitude'] ?? 0);
    $longitude = (float)($input['longitude'] ?? 0);
    $tipe_kos = sanitize($input['tipe_kos'] ?? $input['tipe'] ?? 'campur');
    $fasilitas_umum = sanitize($input['fasilitas_umum'] ?? $input['fasilitas'] ?? '');
} else {
    // FormData input
    $kos_id = (int)($_POST['id'] ?? 0);
    $nama_kos = sanitize($_POST['nama_kos'] ?? $_POST['nama'] ?? '');
    $alamat = sanitize($_POST['alamat'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $latitude = (float)($_POST['latitude'] ?? 0);
    $longitude = (float)($_POST['longitude'] ?? 0);
    $tipe_kos = sanitize($_POST['tipe_kos'] ?? $_POST['tipe'] ?? 'campur');
    $fasilitas_umum = sanitize($_POST['fasilitas_umum'] ?? $_POST['fasilitas'] ?? '');
}

// Validasi
if ($kos_id <= 0) {
    errorResponse('ID kos tidak valid');
}

if (empty($nama_kos) || empty($alamat)) {
    errorResponse('Nama kos dan alamat harus diisi');
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
        errorResponse('Anda tidak memiliki akses untuk mengedit kos ini', 403);
    }
}

// Handle foto upload
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = UPLOAD_PATH . 'kos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = $_FILES['foto']['type'];
    
    if (in_array($fileType, $allowedTypes)) {
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = 'kos_' . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
            $foto = $filename;
        }
    }
}

// Build query based on whether foto is being updated
if ($foto) {
    $stmt = $conn->prepare("UPDATE kos SET nama = ?, alamat = ?, deskripsi = ?, latitude = ?, longitude = ?, tipe = ?, fasilitas = ?, foto = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sssddsssi", $nama_kos, $alamat, $deskripsi, $latitude, $longitude, $tipe_kos, $fasilitas_umum, $foto, $kos_id);
} else {
    $stmt = $conn->prepare("UPDATE kos SET nama = ?, alamat = ?, deskripsi = ?, latitude = ?, longitude = ?, tipe = ?, fasilitas = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sssddssi", $nama_kos, $alamat, $deskripsi, $latitude, $longitude, $tipe_kos, $fasilitas_umum, $kos_id);
}

if ($stmt->execute()) {
    $stmt->close();
    closeConnection($conn);
    successResponse('Kos berhasil diupdate', [
        'foto' => $foto
    ]);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal mengupdate kos', 500);
}
?>
