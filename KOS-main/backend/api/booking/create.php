<?php
/**
 * Booking Create API
 */

require_once __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method tidak didukung', 405);
}

// Check login
requireAuth();

$conn = getConnection();
$data = getJsonInput();

$kamarId = intval($data['kamar_id'] ?? 0);
$tanggalMasuk = sanitize($data['tanggal_masuk'] ?? '');
$durasi = intval($data['durasi'] ?? 1);
$catatan = sanitize($data['catatan'] ?? '');

if (!$kamarId || !$tanggalMasuk) {
    errorResponse('Kamar dan tanggal masuk harus diisi');
}

// Check kamar availability
$stmt = $conn->prepare("SELECT k.*, kos.nama as kos_nama FROM kamar k 
                        LEFT JOIN kos ON k.kos_id = kos.id 
                        WHERE k.id = ? AND k.status = 'tersedia'");
$stmt->bind_param("i", $kamarId);
$stmt->execute();
$kamar = $stmt->get_result()->fetch_assoc();

if (!$kamar) {
    errorResponse('Kamar tidak tersedia');
}

$userId = getCurrentUserId();
$totalHarga = $kamar['harga'] * $durasi;
$tanggalKeluar = date('Y-m-d', strtotime($tanggalMasuk . " + $durasi months"));

// Create booking (penyewa)
$stmt = $conn->prepare("INSERT INTO penyewa (user_id, kamar_id, tanggal_masuk, tanggal_keluar, catatan, status) 
                        VALUES (?, ?, ?, ?, ?, 'aktif')");
$stmt->bind_param("iisss", $userId, $kamarId, $tanggalMasuk, $tanggalKeluar, $catatan);

if ($stmt->execute()) {
    $penyewaId = $conn->insert_id;
    
    // Update kamar status
    $updateStmt = $conn->prepare("UPDATE kamar SET status = 'terisi' WHERE id = ?");
    $updateStmt->bind_param("i", $kamarId);
    $updateStmt->execute();
    
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Booking berhasil', [
        'booking_id' => $penyewaId,
        'kos_nama' => $kamar['kos_nama'],
        'kamar_nomor' => $kamar['nomor'],
        'total_harga' => $totalHarga
    ]);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal membuat booking', 500);
}
?>
