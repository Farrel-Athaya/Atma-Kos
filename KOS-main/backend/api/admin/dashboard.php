<?php
/**
 * API: Admin - Dashboard
 * GET /backend/api/admin/dashboard.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

requireAdmin();

$conn = getConnection();

$stats = [];

// Total Users
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stats['total_users'] = $result->fetch_assoc()['total'];

// Total Kos
$result = $conn->query("SELECT COUNT(*) as total FROM kos");
$stats['total_kos'] = $result->fetch_assoc()['total'];

// Total Kamar
$result = $conn->query("SELECT COUNT(*) as total FROM kamar");
$stats['total_kamar'] = $result->fetch_assoc()['total'];

// Kamar Tersedia
$result = $conn->query("SELECT COUNT(*) as total FROM kamar WHERE status = 'tersedia'");
$stats['kamar_tersedia'] = $result->fetch_assoc()['total'];

// Total Penyewa aktif
$result = $conn->query("SELECT COUNT(*) as total FROM penyewa WHERE status = 'aktif'");
$stats['total_penyewa'] = $result->fetch_assoc()['total'];

closeConnection($conn);

successResponse('Dashboard data berhasil diambil', $stats);
?>
