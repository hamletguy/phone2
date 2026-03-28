<?php
include 'db.php';
session_start();

if (empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;

// Calculate Total
foreach ($_SESSION['cart'] as $id => $qty) {
    $p = $conn->query("SELECT price FROM products WHERE id = $id")->fetch_assoc();
    $total += ($p['price'] * $qty);
}

// 1. Insert into 'orders' table using user_id
$conn->query("INSERT INTO orders (user_id, total_price, status) VALUES ('$user_id', '$total', 'Processing')");
$order_id = $conn->insert_id;

// 2. Insert into 'order_items' table
foreach ($_SESSION['cart'] as $id => $qty) {
    $p = $conn->query("SELECT price FROM products WHERE id = $id")->fetch_assoc();
    $price = $p['price'];
    $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                  VALUES ('$order_id', '$id', '$qty', '$price')");
    
    // Update Stock
    $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $id");
}

unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body class="auth-page">
    <div class="auth-card">
        <h2 style="color: #34c759;">Success!</h2>
        <p>Order #<?php echo $order_id; ?> has been placed.</p>
        <a href="index.php" class="btn">Back to Store</a>
    </div>
</body>
</html>