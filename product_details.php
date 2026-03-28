<?php 
include 'db.php'; 
session_start(); 

// 1. Get the Product ID from the URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $conn->real_escape_string($_GET['id']);

// 2. Fetch Main Product Data
$res = $conn->query("SELECT * FROM products WHERE id = $id");
$p = $res->fetch_assoc();

if (!$p) {
    die("Product not found.");
}

// 3. Fetch all gallery images for this product
$img_res = $conn->query("SELECT image_path FROM product_images WHERE product_id = $id");
$all_images = [];
while($img = $img_res->fetch_assoc()) { 
    $all_images[] = $img['image_path']; 
}

// Fallback if no gallery images exist
if (empty($all_images)) {
    $all_images[] = $p['image_url'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title><?php echo htmlspecialchars($p['name']); ?> | TechStore</title>
</head>
<body class="container">
    <header style="margin-bottom: 40px;">
        <a href="index.php" style="text-decoration: none; color: var(--primary); font-weight: 500;">← Back to Shop</a>
    </header>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
        
        <div class="gallery-container">
            <div style="background: #f5f5f7; padding: 40px; border-radius: 24px; text-align: center;">
                <img id="main-featured-img" src="<?php echo $all_images[0]; ?>" 
                     style="max-width: 100%; height: 400px; object-fit: contain; transition: 0.3s;">
            </div>
            
            <?php if (count($all_images) > 1): ?>
                <div style="display: flex; gap: 12px; margin-top: 20px; overflow-x: auto; padding-bottom: 10px;">
                    <?php foreach($all_images as $path): ?>
                        <img src="<?php echo $path; ?>" 
                             onclick="document.getElementById('main-featured-img').src = this.src"
                             style="width: 70px; height: 70px; object-fit: cover; border-radius: 12px; cursor: pointer; border: 2px solid #eee; transition: 0.2s;"
                             onmouseover="this.style.borderColor='var(--primary)'"
                             onmouseout="this.style.borderColor='#eee'">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <span style="color: var(--text-muted); text-transform: uppercase; font-size: 0.85rem; font-weight: 600;">
                <?php echo htmlspecialchars($p['category']); ?>
            </span>
            
            <h1 style="font-size: 3rem; margin: 10px 0 20px 0;"><?php echo htmlspecialchars($p['name']); ?></h1>
            
            <p style="font-size: 2rem; font-weight: 600; color: var(--text-main); margin-bottom: 30px;">
                $<?php echo number_format($p['price'], 2); ?>
            </p>

            <div style="margin-bottom: 40px; border-top: 1px solid #eee; padding-top: 30px;">
                <h3 style="margin-bottom: 15px;">Product Description</h3>
                <p style="color: #555; line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($p['description']); ?></p>
            </div>

            <form action="add_to_cart.php" method="GET">
                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                <button type="submit" class="btn" style="width: 100%; padding: 20px; font-size: 1.1rem;">Add to Bag</button>
            </form>
            
            <p style="margin-top: 20px; color: #34c759; font-weight: 500; font-size: 0.9rem;">
                ● In Stock (<?php echo $p['stock']; ?> units available)
            </p>
        </div>
    </div>

</body>
</html>