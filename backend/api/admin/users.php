<?php
/**
 * API: Admin - User Management
 * GET /backend/api/admin/users.php - List all users
 * GET /backend/api/admin/users.php?id=1 - Get single user
 * POST /backend/api/admin/users.php - Create user
 * PUT /backend/api/admin/users.php - Update user
 * DELETE /backend/api/admin/users.php - Delete user
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

requireAdmin();

$conn = getConnection();

// GET - List users atau single user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Single user
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT id, username, email, nama, telepon, foto, role, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        closeConnection($conn);
        
        if ($user) {
            successResponse('Data user berhasil diambil', $user);
        } else {
            errorResponse('User tidak ditemukan', 404);
        }
    }
    
    // List users
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    $keyword = isset($_GET['keyword']) ? sanitize($_GET['keyword']) : '';
    $role = isset($_GET['role']) ? sanitize($_GET['role']) : '';
    
    $sql = "SELECT id, username, email, nama, telepon, foto, role, created_at FROM users WHERE 1=1";
    $params = [];
    $types = "";
    
    if (!empty($keyword)) {
        $sql .= " AND (nama LIKE ? OR username LIKE ? OR email LIKE ?)";
        $searchTerm = "%$keyword%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }
    
    if (!empty($role) && in_array($role, ['user', 'admin'])) {
        $sql .= " AND role = ?";
        $params[] = $role;
        $types .= "s";
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ?";
    $params[] = $limit;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    closeConnection($conn);
    successResponse('Data users berhasil diambil', $users);
}

// POST - Create user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    
    $nama = sanitize($input['nama'] ?? '');
    $username = sanitize($input['username'] ?? '');
    $email = sanitize($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $telepon = sanitize($input['telepon'] ?? '');
    $role = sanitize($input['role'] ?? 'user');
    
    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        errorResponse('Nama, username, email, dan password harus diisi');
    }
    
    if (strlen($password) < 6) {
        errorResponse('Password minimal 6 karakter');
    }
    
    // Cek username/email sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        closeConnection($conn);
        errorResponse('Username atau email sudah digunakan');
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (nama, username, email, password, telepon, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $username, $email, $hashed_password, $telepon, $role);
    
    if ($stmt->execute()) {
        $stmt->close();
        closeConnection($conn);
        successResponse('User berhasil ditambahkan');
    } else {
        $stmt->close();
        closeConnection($conn);
        errorResponse('Gagal menambahkan user', 500);
    }
}

// PUT - Update user
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = getJsonInput();
    
    $id = intval($input['id'] ?? 0);
    $nama = sanitize($input['nama'] ?? '');
    $username = sanitize($input['username'] ?? '');
    $email = sanitize($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $telepon = sanitize($input['telepon'] ?? '');
    $role = sanitize($input['role'] ?? 'user');
    
    if ($id <= 0 || empty($nama) || empty($username) || empty($email)) {
        errorResponse('ID, nama, username, dan email harus diisi');
    }
    
    // Cek username/email sudah ada (kecuali user ini)
    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        closeConnection($conn);
        errorResponse('Username atau email sudah digunakan');
    }
    
    if (!empty($password)) {
        if (strlen($password) < 6) {
            errorResponse('Password minimal 6 karakter');
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nama = ?, username = ?, email = ?, password = ?, telepon = ?, role = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssssssi", $nama, $username, $email, $hashed_password, $telepon, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama = ?, username = ?, email = ?, telepon = ?, role = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssssi", $nama, $username, $email, $telepon, $role, $id);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        closeConnection($conn);
        successResponse('User berhasil diperbarui');
    } else {
        $stmt->close();
        closeConnection($conn);
        errorResponse('Gagal memperbarui user', 500);
    }
}

// DELETE - Delete user
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = getJsonInput();
    $id = intval($input['id'] ?? 0);
    
    if ($id <= 0) {
        errorResponse('ID user tidak valid');
    }
    
    // Jangan hapus admin sendiri
    if ($id == getCurrentUserId()) {
        errorResponse('Tidak dapat menghapus akun sendiri');
    }
    
    // Hapus foto jika ada
    $stmt = $conn->prepare("SELECT foto FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && $user['foto']) {
        deleteFile($user['foto'], 'profiles');
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        closeConnection($conn);
        successResponse('User berhasil dihapus');
    } else {
        $stmt->close();
        closeConnection($conn);
        errorResponse('Gagal menghapus user', 500);
    }
}

errorResponse('Method not allowed', 405);
?>
