<?php
/**
 * Konfigurasi Database
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kos_management');

// Konfigurasi Aplikasi
define('APP_NAME', 'Kos Atma');
define('APP_URL', 'http://localhost/KOS');
define('UPLOAD_PATH', __DIR__ . '/../../uploads/');

// Koneksi Database
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Koneksi database gagal']));
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Fungsi untuk menutup koneksi
function closeConnection($conn) {
    $conn->close();
}

// Set header JSON untuk API
function setJsonHeader() {
    header('Content-Type: application/json');
    
    // Handle CORS for credentials
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    $allowedOrigins = ['http://localhost', 'http://127.0.0.1'];
    
    // Check if origin is allowed or starts with localhost
    if (in_array($origin, $allowedOrigins) || strpos($origin, 'http://localhost') === 0) {
        header('Access-Control-Allow-Origin: ' . $origin);
    } else {
        header('Access-Control-Allow-Origin: http://localhost');
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
}

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    setJsonHeader();
    exit(0);
}
?>
