<?php
/**
 * Get Kamar Detail API
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

if (!isset($_GET['id'])) {
    errorResponse('ID kamar diperlukan');
}

$conn = getConnection();
$id = intval($_GET['id']);

$query = "SELECT k.*, kos.nama as kos_nama 
          FROM kamar k 
          LEFT JOIN kos ON k.kos_id = kos.id 
          WHERE k.id = ?";
          
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($kamar = $result->fetch_assoc()) {
    $stmt->close();
    closeConnection($conn);
    successResponse('Data kamar berhasil diambil', $kamar);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Kamar tidak ditemukan', 404);
}
?>
