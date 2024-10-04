<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Enkripsi password dengan BCRYPT
    $role = $_POST['role'];  // Menyimpan role admin atau user

    // Menyimpan data user ke dalam tabel 'users'
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $role])) {
        echo "Account created successfully!";
    } else {
        echo "Error: " . $stmt->errorInfo();
    }
}
?>

<!-- Form HTML untuk registrasi user -->
<form method="POST" action="register.php">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    Role: 
    <select name="role">
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select><br>
    <button type="submit">Register</button>
</form>
