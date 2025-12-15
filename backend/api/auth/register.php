<?php
/**
 * API: Register User
 * POST /backend/api/auth/register.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Ambil input
$input = getJsonInput();

$username = sanitize($input['username'] ?? '');
$email = sanitize($input['email'] ?? '');
$password = $input['password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';
$nama = sanitize($input['nama'] ?? '');
$telepon = sanitize($input['telepon'] ?? '');

// Validasi input
if (empty($username) || empty($email) || empty($password) || empty($nama)) {
    errorResponse('Semua field wajib harus diisi');
}

if (strlen($username) < 4) {
    errorResponse('Username minimal 4 karakter');
}

if (!validateEmail($email)) {
    errorResponse('Format email tidak valid');
}

if (strlen($password) < 6) {
    errorResponse('Password minimal 6 karakter');
}

if ($password !== $confirm_password) {
    errorResponse('Konfirmasi password tidak cocok');
}

$conn = getConnection();

// Cek username sudah dipakai
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Username sudah digunakan');
}

// Cek email sudah dipakai
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Email sudah digunakan');
}

// Enkripsi password dengan password_hash (bcrypt)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user baru - sesuai dengan struktur database (nama, telepon)
$stmt = $conn->prepare("INSERT INTO users (username, email, password, nama, telepon) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashed_password, $nama, $telepon);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Registrasi berhasil! Silakan login.', [
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email
    ]);
} else {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Terjadi kesalahan saat registrasi', 500);
}
?>
