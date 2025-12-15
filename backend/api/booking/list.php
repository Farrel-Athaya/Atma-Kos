<?php
/**
 * Booking List API - Get user's bookings
 */

require_once __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method tidak didukung', 405);
}

// Check login
requireAuth();

$conn = getConnection();
$userId = getCurrentUserId();

$query = "SELECT p.*, 
          k.nomor as kamar_nomor, k.harga,
          kos.nama as kos_nama,
          GREATEST(1, TIMESTAMPDIFF(MONTH, p.tanggal_masuk, IFNULL(p.tanggal_keluar, NOW()))) as durasi,
          (k.harga * GREATEST(1, TIMESTAMPDIFF(MONTH, p.tanggal_masuk, IFNULL(p.tanggal_keluar, NOW())))) as total_harga
          FROM penyewa p
          LEFT JOIN kamar k ON p.kamar_id = k.id
          LEFT JOIN kos ON k.kos_id = kos.id
          WHERE p.user_id = ?
          ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();
closeConnection($conn);

successResponse('Data booking berhasil diambil', $bookings);
?>
