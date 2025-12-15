<?php
/**
 * API: Get User Profile
 * GET /backend/api/user/profile.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

$user = getCurrentUser();

if (!$user) {
    errorResponse('User tidak ditemukan', 404);
}

successResponse('Data profil berhasil diambil', [
    'user' => $user
]);
?>
