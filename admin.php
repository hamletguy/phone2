<?php 
include 'db.php'; 
session_start();

// 1. Security Check
if(!isset($_SESSION['r']) || $_SESSION['r'] != 'admin') {
    die("Access Denied. You must be an admin.");
}

// 2. Add Product Logic
if(isset($_POST['add'])){
    $name = $_POST['n'];
    $cat  = $_POST['c'];
    $pri  = $_POST['p'];
    $sto  = $_POST['s'];
    $desc = $_POST['d']; 

    // Insert product details (including is_active as 1 by default)
    $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, description, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssdis", $name, $cat, $pri, $sto, $desc);
    
    if ($stmt->execute()) {
        $product_id = $conn->insert_id;
        $target_dir = "uploads/";

        foreach($_FILES['product_images']['tmp_name'] as $key => $tmp_name){
            if(!empty($_FILES['product_images']['name'][$key])) {
                $file_name = time() . "_" . basename($_FILES["product_images"]["name"][$key]);
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $conn->query("INSERT INTO product_images (product_id, image_path) VALUES ('$product_id', '$target_file')");
                    
                    if($key == 0) {
                        $conn->query("UPDATE products SET image_url = '$target_file' WHERE id = '$product_id'");
                    }
                }
            }
        }
        header("Location: admin.php?success=1");
        exit();
    } else {
        echo "<script>alert('Error adding product details.');</script>";
    }
}

// 3. Delete Product Logic (Soft Delete)
if(isset($_GET['delete'])){
    $id = $conn->real_escape_string($_GET['delete']);
    // We update is_active to 0 so it stays in the DB for orders but hides from the shop
    $conn->query("UPDATE products SET is_active = 0 WHERE id = $id");
    header("Location: admin.php"); 
    exit();
}
?> <!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="container">
    <div class="admin-wrapper">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="margin: 0;">Inventory Management</h2>
            <div>
                <a href="admin_orders.php" class="btn" style="background: #eee; color: black; margin-right: 10px;">View Orders</a>
                <a href="index.php" class="btn" style="background: var(--accent); font-size: 0.8rem;">← Back to Store</a>
            </div>
        </header>

        <div class="admin-card">
            <h3>Add New Product</h3>
            <form method="POST" enctype="multipart/form-data" class="admin-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Product Name</label>
                    <input name="n" placeholder="e.g., iPhone 15 Pro" required>
                </div>
                <div>
                    <label>Category</label>
                    <select name="c">
                        <option>Smartphones</option>
                        <option>Headphones</option>
                        <option>Chargers</option>
                        <option>Smartwatches</option>
                    </select>
                </div>
                <div>
                    <label>Price ($)</label>
                    <input name="p" type="number" step="0.01" required>
                </div>
                <div>
                    <label>Stock</label>
                    <input name="s" type="number" required>
                </div>
                <div style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="d" placeholder="Enter product details..." style="width: 100%; height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                </div>
                <div style="grid-column: span 2;">
                    <label>Product Images (Select Multiple)</label>
                    <input type="file" name="product_images[]" accept="image/*" multiple required>
                </div>
                <div style="grid-column: span 2;">
                    <button name="add" class="btn" style="width: 100%;">List Product on Store</button>
                </div>
            </form>
        </div>

        <h3>Active Inventory</h3>
        <table class="inventory-table" style="width: 100%; margin-top: 20px;">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Only select products where is_active is 1
                $res = $conn->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC");
                while($row = $res->fetch_assoc()): ?>
                <tr>
                    <td style="display: flex; align-items: center; gap: 15px; padding: 10px;">
                        <img src="<?php echo $row['image_url']; ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;">
                        <strong><?php echo $row['name']; ?></strong>
                    </td>
                    <td><?php echo $row['category']; ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td>
                        <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Remove this item?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>