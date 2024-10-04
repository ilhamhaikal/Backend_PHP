<?php
include '../config/database.php';
session_start();

$message = ""; // Variabel untuk menyimpan pesan

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mencari user berdasarkan username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Memverifikasi password
    if ($user && password_verify($password, $user['password'])) {
        // Simpan user ID dan role di sesi
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Menyimpan role (admin/user)

        // Cek role user dan redirect ke halaman yang sesuai
        if ($user['role'] == 'admin') {
            header("Location: admin/admin_dashboard.php"); // Halaman admin
        } else {
            header("Location: user/user_products.php"); // Halaman user biasa
        }
        exit(); // Menghentikan eksekusi script setelah redirect
    } else {
        $message = "Invalid username or password!"; // Pesan error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    
    <?php if ($message): ?>
        <div style="color: red;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Form HTML untuk login -->
    <form method="POST" action="login.php">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
