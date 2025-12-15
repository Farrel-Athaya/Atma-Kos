<?php
/**
 * Admin Penyewa API
 * GET - List penyewa / Get single penyewa
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method tidak didukung', 405);
}

// Check admin
requireAdmin();

$conn = getConnection();

if (isset($_GET['id'])) {
    // Get single penyewa
    $id = intval($_GET['id']);
    
    $query = "SELECT p.*, u.nama, u.email, u.telepon, 
              k.nomor as kamar_nomor, k.harga,
              kos.nama as kos_nama
              FROM penyewa p
              LEFT JOIN users u ON p.user_id = u.id
              LEFT JOIN kamar k ON p.kamar_id = k.id
              LEFT JOIN kos ON k.kos_id = kos.id
              WHERE p.id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($penyewa = $result->fetch_assoc()) {
        $stmt->close();
        closeConnection($conn);
        successResponse('Data penyewa berhasil diambil', $penyewa);
    } else {
        $stmt->close();
        closeConnection($conn);
        errorResponse('Penyewa tidak ditemukan', 404);
    }
} else {
    // List penyewa
    $keyword = isset($_GET['keyword']) ? sanitize($_GET['keyword']) : '';
    $kosId = isset($_GET['kos_id']) ? intval($_GET['kos_id']) : 0;
    $status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
    
    $query = "SELECT p.*, u.nama, u.email, u.telepon,
              k.nomor as kamar_nomor,
              kos.nama as kos_nama
              FROM penyewa p
              LEFT JOIN users u ON p.user_id = u.id
              LEFT JOIN kamar k ON p.kamar_id = k.id
              LEFT JOIN kos ON k.kos_id = kos.id
              WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if ($keyword) {
        $query .= " AND u.nama LIKE ?";
        $params[] = "%$keyword%";
        $types .= 's';
    }
    
    if ($kosId) {
        $query .= " AND kos.id = ?";
        $params[] = $kosId;
        $types .= 'i';
    }
    
    if ($status) {
        $query .= " AND p.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $query .= " ORDER BY p.tanggal_masuk DESC";
    
    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $penyewa = [];
    while ($row = $result->fetch_assoc()) {
        $penyewa[] = $row;
    }
    
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Data penyewa berhasil diambil', $penyewa);
}
?>
