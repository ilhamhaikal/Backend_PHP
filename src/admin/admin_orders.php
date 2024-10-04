<?php
// Mendapatkan semua pesanan
$stmt = $conn->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id");
$orders = $stmt->fetchAll();

foreach ($orders as $order) {
    echo "<h3>Order #{$order['id']} by {$order['username']}</h3>";
    echo "<p>Total Price: {$order['total_price']}</p>";

    // Menampilkan item dalam pesanan
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order['id']]);
    $items = $stmt->fetchAll();

    echo "<ul>";
    foreach ($items as $item) {
        echo "<li>Product ID: {$item['product_id']}, Quantity: {$item['quantity']}, Subtotal: {$item['subtotal']}</li>";
    }
    echo "</ul>";
}
?>
