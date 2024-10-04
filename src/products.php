<?php
include '../config/database.php';
session_start();

// Menangani permintaan untuk menghapus produk
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect jika user belum login
    exit();


    // Hapus produk berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    // Reset urutan ID agar teratur setelah penghapusan
    $stmt = $conn->prepare("ALTER SEQUENCE products_id_seq RESTART WITH 1");
    $stmt->execute();
    
    $stmt = $conn->prepare("UPDATE products SET id = DEFAULT");
    $stmt->execute();

    header("Location: products.php");
    exit();
 }
}

// Menangani permintaan untuk mengedit produk
$productToEdit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $productToEdit = $stmt->fetch();
}

// Menangani permintaan untuk menambahkan atau mengupdate produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Cek apakah ini update atau tambah produk baru
    if (isset($_POST['id']) && $_POST['id']) {
        // Update produk
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $stock, $id]);
        echo "Product updated successfully!";
    } else {
        // Tambah produk baru
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $description, $price, $stock])) {
            echo "Product added successfully!";
        } else {
            echo "Failed to add product.";
        }
    }

    header("Location: products.php");
    exit();
}
?>

<!-- Form untuk menambahkan atau mengedit produk -->
<form method="POST" action="add_to_cart.php">
    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
    <input type="hidden" name="price" value="<?= $product['price']; ?>">
    Quantity: <input type="number" name="quantity" value="1" min="1">
    <button type="submit" name="add_to_cart">Add to Cart</button>
</form>

<!-- Menampilkan daftar produk -->
<h2>Product List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php
    // Menampilkan produk yang ada di database
    $stmt = $conn->query("SELECT * FROM products ORDER BY id ASC");
    $products = $stmt->fetchAll();

    if (count($products) > 0) {
        foreach ($products as $product) {
            echo "<tr>
                    <td>{$product['id']}</td>
                    <td>{$product['name']}</td>
                    <td>{$product['description']}</td>
                    <td>{$product['price']}</td>
                    <td>{$product['stock']}</td>
                    <td>
                        <a href='?edit={$product['id']}'>Edit</a>
                        <a href='?delete={$product['id']}'>Delete</a>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No products found.</td></tr>";
    }
    ?>
</table>
