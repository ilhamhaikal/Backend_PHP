<?php
include '../config/database.php';
session_start();

// Pastikan user telah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Ambil data dari form
$productId = $_POST['product_id'];
$quantity = $_POST['quantity'];

// Cek apakah user sudah memiliki keranjang
if (!isset($_SESSION['cart_id'])) {
    // Buat keranjang baru untuk user jika belum ada
    $stmt = $conn->prepare("INSERT INTO carts (user_id, total_price) VALUES (?, 0)");
    $stmt->execute([$_SESSION['user_id']]);

    // Set session cart_id dengan id terakhir yang diinsert
    $_SESSION['cart_id'] = $conn->lastInsertId();
}

// Cek apakah produk sudah ada di keranjang
$stmt = $conn->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
$stmt->execute([$_SESSION['cart_id'], $productId]);
$cartItem = $stmt->fetch();

if ($cartItem) {
    // Update kuantitas produk jika sudah ada di keranjang
    $newQuantity = $cartItem['quantity'] + $quantity;
    $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->execute([$newQuantity, $cartItem['id']]);
} else {
    // Tambahkan produk baru ke keranjang
    $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['cart_id'], $productId, $quantity]);
}

// Redirect kembali ke halaman produk
header("Location: ../user/cart.php");
exit();
?>
