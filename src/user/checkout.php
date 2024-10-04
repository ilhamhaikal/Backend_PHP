<?php
include '../../config/database.php';
session_start();

// Proses checkout
if (isset($_POST['checkout'])) {
    $cart_id = $_POST['cart_id'];

    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Buat pesanan baru
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price) SELECT user_id, total_price FROM carts WHERE id = ?");
    $stmt->execute([$cart_id]);
    $order_id = $conn->lastInsertId();

    // Pindahkan item keranjang ke item pesanan
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
                            SELECT ?, product_id, quantity, (SELECT price FROM products WHERE id = cart_items.product_id), subtotal
                            FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$order_id, $cart_id]);

    // Hapus keranjang setelah checkout
    $stmt = $conn->prepare("DELETE FROM carts WHERE id = ?");
    $stmt->execute([$cart_id]);

    header("Location: orders.php"); // Redirect ke halaman pesanan
    exit();
}
?>
