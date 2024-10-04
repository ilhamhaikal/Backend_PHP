<?php
$host = 'localhost';
$dbname = 'phpfullstack';  // Sesuaikan dengan nama database di pgAdmin
$username = 'postgres';  // Gunakan user postgres atau user lain yang kamu buat
$password = 'CoRei7%4dKontol123!@#*?*?*?321';  // Password yang digunakan untuk login ke PostgreSQL

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to PostgreSQL successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
