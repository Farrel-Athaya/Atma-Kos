<?php
/**
 * API: Check Username/Email Availability
 * GET /backend/api/auth/check.php?field=username&value=xxx
 */

require_once __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$field = sanitize($_GET['field'] ?? '');
$value = sanitize($_GET['value'] ?? '');

if (empty($field) || empty($value)) {
    errorResponse('Parameter tidak lengkap');
}

if (!in_array($field, ['username', 'email'])) {
    errorResponse('Field tidak valid');
}

$conn = getConnection();

$sql = "SELECT id FROM users WHERE $field = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $value);
$stmt->execute();
$result = $stmt->get_result();

$available = $result->num_rows === 0;

$stmt->close();
closeConnection($conn);

successResponse($available ? 'Tersedia' : ucfirst($field) . ' sudah digunakan', [
    'available' => $available,
    'field' => $field
]);
?>
