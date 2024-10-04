<?php
include '../../config/database.php';
session_start();

// Pastikan user telah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php"); // Redirect jika user belum login
    exit();
}

// Menampilkan daftar produk
$stmt = $conn->query("SELECT * FROM products ORDER BY id ASC");
$products = $stmt->fetchAll();
?>

<h2>Available Products</h2>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['name']); ?></td>
            <td><?= htmlspecialchars($product['description']); ?></td>
            <td><?= htmlspecialchars($product['price']); ?></td>
            <td><?= htmlspecialchars($product['stock']); ?></td>
            <td>
                <!-- Form untuk menambahkan ke keranjang -->
                <form method="POST" action="cart.php">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock']; ?>" required>
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
