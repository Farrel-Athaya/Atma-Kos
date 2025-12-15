<?php
/**
 * API: Get Current Session
 * GET /backend/api/auth/session.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

if (!isLoggedIn()) {
    successResponse('Tidak ada session aktif', [
        'logged_in' => false,
        'user' => null
    ]);
}

$user = getCurrentUser();

successResponse('Session aktif', [
    'logged_in' => true,
    'user' => $user
]);
?>
