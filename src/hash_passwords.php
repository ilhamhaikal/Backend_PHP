<?php
include '../config/database.php';

// Ambil semua user dengan password plaintext (teks biasa)
$stmt = $conn->query("SELECT id, username, password FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    // Cek apakah password sudah di-hash atau belum
    if (password_get_info($user['password'])['algo'] == 0) {  // Jika password belum di-hash
        // Hash password
        $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT);
        
        // Update password di database
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->execute([$hashedPassword, $user['id']]);
        
        echo "Password for user {$user['username']} has been updated to hash.<br>";
    }
}
?>
