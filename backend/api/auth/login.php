<?php
/**
 * API: Login User
 * POST /backend/api/auth/login.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Ambil input
$input = getJsonInput();

$username = sanitize($input['username'] ?? '');
$password = $input['password'] ?? '';

// Validasi input
if (empty($username) || empty($password)) {
    errorResponse('Username/email dan password harus diisi');
}

$conn = getConnection();

// Cari user berdasarkan username atau email
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Username atau email tidak ditemukan');
}

$user = $result->fetch_assoc();

// Verifikasi password dengan password_verify
if (!password_verify($password, $user['password'])) {
    $stmt->close();
    closeConnection($conn);
    errorResponse('Password salah');
}

// Set session - disesuaikan dengan struktur database
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['role'] = $user['role'];
$_SESSION['foto'] = $user['foto'];

$stmt->close();
closeConnection($conn);

// Response sukses - disesuaikan dengan struktur database
successResponse('Login berhasil', [
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'nama' => $user['nama'],
        'telepon' => $user['telepon'],
        'foto' => $user['foto'],
        'role' => $user['role']
    ]
]);
?>
