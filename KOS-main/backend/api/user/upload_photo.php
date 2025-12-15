<?php
/**
 * API: Upload Profile Photo
 * POST /backend/api/user/upload_photo.php
 */

require_once __DIR__ . '/../../helpers/functions.php';

setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

requireAuth();

// Cek apakah ada file yang diupload (support both 'foto' and 'foto_profil')
$fileKey = isset($_FILES['foto']) ? 'foto' : (isset($_FILES['foto_profil']) ? 'foto_profil' : null);

if (!$fileKey || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
    errorResponse('Tidak ada file yang diupload atau terjadi error');
}

$conn = getConnection();
$user_id = getCurrentUserId();

// Ambil foto lama
$stmt = $conn->prepare("SELECT foto FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$old_photo = $user['foto'];

// Upload foto baru
$uploadResult = uploadFile($_FILES[$fileKey], 'profiles');

if (!$uploadResult['success']) {
    $stmt->close();
    closeConnection($conn);
    errorResponse($uploadResult['message']);
}

$new_photo = $uploadResult['filename'];

// Update database
$stmt = $conn->prepare("UPDATE users SET foto = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $new_photo, $user_id);

if ($stmt->execute()) {
    // Hapus foto lama jika ada
    if ($old_photo && $old_photo !== 'default.jpg') {
        deleteFile($old_photo, 'profiles');
    }
    
    // Update session
    $_SESSION['foto'] = $new_photo;
    
    $stmt->close();
    closeConnection($conn);
    
    successResponse('Foto profil berhasil diupload', [
        'foto' => $new_photo
    ]);
} else {
    // Hapus foto yang baru diupload karena gagal update database
    deleteFile($new_photo, 'profiles');
    
    $stmt->close();
    closeConnection($conn);
    errorResponse('Gagal mengupdate foto profil', 500);
}
?>
