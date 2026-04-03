<?php
include 'db.php';
session_start();

// Security: Only admins allowed
if ($_SESSION['r'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// 1. FETCH CURRENT DATA
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Fetch existing extra images
$img_res = $conn->query("SELECT * FROM product_images WHERE product_id = $id");

// 2. HANDLE UPDATE LOGIC
if (isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    // Update basic info
    $update_stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, stock=? WHERE id=?");
    $update_stmt->bind_param("sdssi", $name, $price, $desc, $stock, $id);
    $update_stmt->execute();

    // Handle Main Thumbnail Update (Optional)
    if (!empty($_FILES['main_image']['name'])) {
        $main_path = "uploads/" . time() . "_" . $_FILES['main_image']['name'];
        move_uploaded_file($_FILES['main_image']['tmp_name'], $main_path);
        $conn->query("UPDATE products SET image_url='$main_path' WHERE id=$id");
    }

    // Handle Adding New Extra Images
    if (!empty($_FILES['extra_images']['name'][0])) {
        foreach ($_FILES['extra_images']['tmp_name'] as $key => $tmp_name) {
            $extra_path = "uploads/" . time() . "_" . $_FILES['extra_images']['name'][$key];
            if (move_uploaded_file($tmp_name, $extra_path)) {
                $conn->query("INSERT INTO product_images (product_id, image_path) VALUES ($id, '$extra_path')");
            }
        }
    }

    header("Location: admin.php?msg=Product Updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product | Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container { max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        textarea { width: 100%; height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; }
        .current-images { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
        .img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .btn-update { background: #2563eb; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 20px; }
    </style>
</head>
<body style="background: #f8fafc; font-family: sans-serif;">

<div class="edit-container">
    <h2>Modify Product: <?php echo $product['name']; ?></h2>
    <hr><br>

    <form method="POST" enctype="multipart/form-data" class="form-grid">
        <div class="full-width">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required style="width:100%; padding:10px;">
        </div>

        <div>
            <label>Price ($)</label>
            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required style="width:100%; padding:10px;">
        </div>

        <div>
            <label>Stock Quantity</label>
            <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required style="width:100%; padding:10px;">
        </div>

        <div class="full-width">
            <label>Description</label>
            <textarea name="description"><?php echo $product['description']; ?></textarea>
        </div>

        <div>
            <label>Change Main Thumbnail</label><br>
            <input type="file" name="main_image">
            <div class="current-images">
                <img src="<?php echo $product['image_url']; ?>" class="img-preview" title="Current Main">
            </div>
        </div>

        <div>
            <label>Add More Gallery Pictures</label><br>
            <input type="file" name="extra_images[]" multiple>
            <div class="current-images">
                <?php while($img = $img_res->fetch_assoc()): ?>
                    <img src="<?php echo $img['image_path']; ?>" class="img-preview">
                <?php endwhile; ?>
            </div>
        </div>

        <div class="full-width">
            <button name="update_product" class="btn-update">Save Changes</button>
            <a href="admin.php" style="margin-left: 15px; color: #64748b;">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>