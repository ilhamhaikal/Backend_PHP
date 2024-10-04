<?php
include '../../config/database.php';
session_start();

// Pastikan user telah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Jika user menambahkan produk ke keranjang
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];
    $subtotal = $price * $quantity;

    // Cek apakah user sudah punya cart_id, jika tidak, buatkan cart baru
    if (!isset($_SESSION['cart_id'])) {
        $_SESSION['cart_id'] = uniqid(); // Buat ID unik untuk cart
    }
    $cart_id = $_SESSION['cart_id'];

    // Simpan item ke dalam cart_items
    $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, subtotal, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$cart_id, $product_id, $quantity, $subtotal, $user_id]);

    // Redirect ke halaman cart setelah ditambahkan
    header("Location: cart.php");
    exit();
}

// Menampilkan item keranjang user
$stmt = $conn->prepare("SELECT cart_items.*, products.name, products.price 
                        FROM cart_items 
                        JOIN products ON cart_items.product_id = products.id 
                        WHERE cart_items.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

// Menghitung total harga
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['subtotal'];
}
?>

<h2>Your Cart</h2>

<!-- Cek jika keranjang kosong -->
<?php if (count($cartItems) == 0): ?>
    <p>Keranjang belanja kosong. Silakan tambahkan produk terlebih dahulu.</p>
<?php else: ?>
    <table border="1">
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($cartItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td><?= htmlspecialchars($item['price']); ?></td>
                <td><?= htmlspecialchars($item['quantity']); ?></td>
                <td><?= htmlspecialchars($item['subtotal']); ?></td>
                <td>
                    <!-- Tindakan untuk memperbarui kuantitas atau menghapus -->
                    <form method="POST" action="update_cart.php">
                        <input type="hidden" name="cart_item_id" value="<?= $item['id']; ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" required>
                        <button type="submit" name="update_cart">Update</button>
                        <button type="submit" name="remove_from_cart">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Total Price: <?= $totalPrice; ?></h3>

    <!-- Tombol untuk checkout -->
    <form method="POST" action="checkout.php">
        <input type="hidden" name="cart_id" value="<?= $_SESSION['cart_id']; ?>">
        <button type="submit" name="checkout">Checkout</button>
    </form>
<?php endif; ?>
