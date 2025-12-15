<?php
/**
 * API: Logout User
 * POST /backend/api/auth/logout.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Hapus semua session
session_unset();
session_destroy();

successResponse('Logout berhasil');
?>
