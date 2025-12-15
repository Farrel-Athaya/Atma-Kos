<?php
/**
 * API: Get Kamar List
 * GET /backend/api/kamar/list.php?kos_id=1
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$kos_id = (int)($_GET['kos_id'] ?? 0);
$status = sanitize($_GET['status'] ?? '');

$conn = getConnection();

$sql = "SELECT km.*, k.nama as kos_nama 
        FROM kamar km 
        JOIN kos k ON km.kos_id = k.id
        WHERE 1=1";

$params = [];
$types = "";

if ($kos_id > 0) {
    $sql .= " AND km.kos_id = ?";
    $params[] = $kos_id;
    $types .= "i";
}

if (!empty($status) && in_array($status, ['tersedia', 'terisi', 'maintenance'])) {
    $sql .= " AND km.status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY k.nama, km.nomor";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$kamar_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Format fasilitas
foreach ($kamar_list as &$kamar) {
    $kamar['fasilitas'] = $kamar['fasilitas'] ? explode(',', $kamar['fasilitas']) : [];
    $kamar['fasilitas'] = array_map('trim', $kamar['fasilitas']);
}

$stmt->close();
closeConnection($conn);

successResponse('Data kamar berhasil diambil', $kamar_list);
?>
