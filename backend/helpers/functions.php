<?php
/**
 * Helper Functions
 */

// Suppress warnings and start output buffering
error_reporting(E_ERROR | E_PARSE);
ob_start();

// Prevent session errors
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// ============================================
// RESPONSE FUNCTIONS
// ============================================

function jsonResponse($success, $message, $data = null, $statusCode = 200) {
    // Clean any previous output
    if (ob_get_length()) ob_clean();
    
    http_response_code($statusCode);
    setJsonHeader();
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}

function successResponse($message, $data = null) {
    jsonResponse(true, $message, $data, 200);
}

function errorResponse($message, $statusCode = 400) {
    jsonResponse(false, $message, null, $statusCode);
}

// ============================================
// INPUT FUNCTIONS
// ============================================

function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getInput($key, $default = '') {
    $input = getJsonInput();
    return isset($input[$key]) ? sanitize($input[$key]) : $default;
}

// ============================================
// AUTH FUNCTIONS
// ============================================

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return getUserRole() === 'admin';
}

function isPemilik() {
    return getUserRole() === 'pemilik';
}

function requireAuth() {
    if (!isLoggedIn()) {
        errorResponse('Anda harus login terlebih dahulu', 401);
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        errorResponse('Akses ditolak. Hanya admin yang bisa mengakses', 403);
    }
}

function requirePemilik() {
    requireAuth();
    if (!isPemilik() && !isAdmin()) {
        errorResponse('Akses ditolak', 403);
    }
}

// ============================================
// USER FUNCTIONS
// ============================================

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    $conn = getConnection();
    $id = getCurrentUserId();
    
    $stmt = $conn->prepare("SELECT id, username, email, nama, telepon, alamat, foto, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $stmt->close();
    closeConnection($conn);
    
    return $user;
}

// ============================================
// FILE UPLOAD FUNCTIONS
// ============================================

function uploadFile($file, $folder = 'profiles') {
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $fileName = basename($file['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $fileSize = $file['size'];
    
    // Validasi tipe file
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Gunakan: ' . implode(', ', $allowedTypes)];
    }
    
    // Validasi ukuran file
    if ($fileSize > $maxSize) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB'];
    }
    
    // Generate nama file unik
    $newFileName = uniqid() . '_' . time() . '.' . $fileType;
    $uploadDir = UPLOAD_PATH . $folder . '/';
    
    // Buat folder jika belum ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $targetPath = $uploadDir . $newFileName;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFileName];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }
}

function deleteFile($filename, $folder = 'profiles') {
    $filePath = UPLOAD_PATH . $folder . '/' . $filename;
    if (file_exists($filePath) && $filename !== 'default.jpg') {
        unlink($filePath);
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateToken() {
    return bin2hex(random_bytes(32));
}
?>
