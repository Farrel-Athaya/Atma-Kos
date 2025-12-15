<?php
/**
 * API: Get All Kos / Search Kos
 * GET /backend/api/kos/list.php
 * GET /backend/api/kos/list.php?q=keyword&tipe=putra&harga_min=500000&harga_max=1000000
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$conn = getConnection();

// Filter parameters - support both 'q' and 'keyword'
$search = sanitize($_GET['q'] ?? $_GET['keyword'] ?? '');
$tipe = sanitize($_GET['tipe'] ?? '');
$harga_min = (int)($_GET['harga_min'] ?? 0);
$harga_max = (int)($_GET['harga_max'] ?? 0);
$limit = (int)($_GET['limit'] ?? 20);
$offset = (int)($_GET['offset'] ?? 0);

// Build query - disesuaikan dengan struktur database
$sql = "SELECT k.id, k.nama, k.alamat, k.deskripsi, k.latitude, k.longitude, 
        k.foto, k.tipe, k.fasilitas, k.created_at,
        u.nama as pemilik_nama, u.telepon as pemilik_telepon,
        (SELECT MIN(harga) FROM kamar WHERE kos_id = k.id) as harga_min,
        (SELECT MAX(harga) FROM kamar WHERE kos_id = k.id) as harga_max,
        (SELECT COUNT(*) FROM kamar WHERE kos_id = k.id) as total_kamar,
        (SELECT COUNT(*) FROM kamar WHERE kos_id = k.id AND status = 'tersedia') as kamar_tersedia
        FROM kos k
        LEFT JOIN users u ON k.pemilik_id = u.id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (k.nama LIKE ? OR k.alamat LIKE ? OR k.deskripsi LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

if (!empty($tipe) && in_array($tipe, ['putra', 'putri', 'campur'])) {
    $sql .= " AND k.tipe = ?";
    $params[] = $tipe;
    $types .= "s";
}

$sql .= " ORDER BY k.created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$kos_list = $result->fetch_all(MYSQLI_ASSOC);

// Filter by harga (post-query)
if ($harga_min > 0 || $harga_max > 0) {
    $kos_list = array_filter($kos_list, function($kos) use ($harga_min, $harga_max) {
        $harga = $kos['harga_min'] ?? 0;
        if ($harga_min > 0 && $harga < $harga_min) return false;
        if ($harga_max > 0 && $harga > $harga_max) return false;
        return true;
    });
    $kos_list = array_values($kos_list);
}

$stmt->close();
closeConnection($conn);

successResponse('Data kos berhasil diambil', $kos_list);
?>
