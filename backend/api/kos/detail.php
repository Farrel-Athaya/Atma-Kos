<?php
/**
 * API: Get Kos Detail
 * GET /backend/api/kos/detail.php?id=1
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$kos_id = (int)($_GET['id'] ?? 0);

if ($kos_id <= 0) {
    errorResponse('ID kos tidak valid');
}

$conn = getConnection();

// Ambil data kos dengan pemilik - sesuai struktur database (nama, telepon)
$stmt = $conn->prepare("SELECT k.*, u.nama as pemilik_nama, u.telepon as pemilik_telepon, u.email as pemilik_email
                        FROM kos k
                        LEFT JOIN users u ON k.pemilik_id = u.id
                        WHERE k.id = ?");
$stmt->bind_param("i", $kos_id);
$stmt->execute();
$kos = $stmt->get_result()->fetch_assoc();

if (!$kos) {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Kos tidak ditemukan', 404);
}

// Ambil data kamar - sesuai struktur database (nomor, bukan nomor_kamar)
$stmt = $conn->prepare("SELECT * FROM kamar WHERE kos_id = ? ORDER BY nomor");
$stmt->bind_param("i", $kos_id);
$stmt->execute();
$kamar_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Hitung statistik
$total_kamar = count($kamar_list);
$kamar_tersedia = 0;
$harga_terendah = null;

foreach ($kamar_list as &$kamar) {
    if ($kamar['status'] === 'tersedia') {
        $kamar_tersedia++;
    }
    if ($harga_terendah === null || $kamar['harga'] < $harga_terendah) {
        $harga_terendah = $kamar['harga'];
    }
    // Format fasilitas kamar sebagai array jika ada
    if (!empty($kamar['fasilitas'])) {
        $kamar['fasilitas_list'] = array_map('trim', explode(',', $kamar['fasilitas']));
    }
}

$stmt->close();
closeConnection($conn);

// Format fasilitas kos sebagai array jika ada
$fasilitas_list = [];
if (!empty($kos['fasilitas'])) {
    $fasilitas_list = array_map('trim', explode(',', $kos['fasilitas']));
}

// Build response
$response = [
    'id' => $kos['id'],
    'nama' => $kos['nama'],
    'alamat' => $kos['alamat'],
    'deskripsi' => $kos['deskripsi'],
    'tipe' => $kos['tipe'],
    'fasilitas' => $kos['fasilitas'],
    'fasilitas_list' => $fasilitas_list,
    'foto' => $kos['foto'],
    'latitude' => $kos['latitude'],
    'longitude' => $kos['longitude'],
    'total_kamar' => $total_kamar,
    'kamar_tersedia' => $kamar_tersedia,
    'harga_terendah' => $harga_terendah,
    'pemilik' => [
        'nama' => $kos['pemilik_nama'],
        'telepon' => $kos['pemilik_telepon'],
        'email' => $kos['pemilik_email']
    ],
    'kamar' => $kamar_list
];

successResponse('Data kos berhasil diambil', $response);
?>
