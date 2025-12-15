<?php
/**
 * API: Create Kos
 * POST /backend/api/kos/create.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requirePemilik();

// Support both FormData and JSON input
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $input = getJsonInput();
    $nama_kos = sanitize($input['nama_kos'] ?? $input['nama'] ?? '');
    $alamat = sanitize($input['alamat'] ?? '');
    $deskripsi = sanitize($input['deskripsi'] ?? '');
    $latitude = (float)($input['latitude'] ?? 0);
    $longitude = (float)($input['longitude'] ?? 0);
    $tipe_kos = sanitize($input['tipe_kos'] ?? $input['tipe'] ?? 'campur');
    $fasilitas_umum = sanitize($input['fasilitas_umum'] ?? $input['fasilitas'] ?? '');
} else {
    // FormData input
    $nama_kos = sanitize($_POST['nama_kos'] ?? $_POST['nama'] ?? '');
    $alamat = sanitize($_POST['alamat'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $latitude = (float)($_POST['latitude'] ?? 0);
    $longitude = (float)($_POST['longitude'] ?? 0);
    $tipe_kos = sanitize($_POST['tipe_kos'] ?? $_POST['tipe'] ?? 'campur');
    $fasilitas_umum = sanitize($_POST['fasilitas_umum'] ?? $_POST['fasilitas'] ?? '');
}

// Validasi
if (empty($nama_kos) || empty($alamat)) {
    errorResponse('Nama kos dan alamat harus diisi');
}

if (!in_array($tipe_kos, ['putra', 'putri', 'campur'])) {
    errorResponse('Tipe kos tidak valid');
}

$conn = getConnection();
$pemilik_id = getCurrentUserId();

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

$stmt = $conn->prepare("INSERT INTO kos (pemilik_id, nama, alamat, deskripsi, latitude, longitude, tipe, fasilitas, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssddsss", $pemilik_id, $nama_kos, $alamat, $deskripsi, $latitude, $longitude, $tipe_kos, $fasilitas_umum, $foto);

if ($stmt->execute()) {
    $kos_id = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Kos berhasil ditambahkan', [
        'kos_id' => $kos_id,
        'foto' => $foto
    ]);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal menambahkan kos', 500);
}
?>
