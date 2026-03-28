<?php 
include 'db.php'; 
session_start();

// 1. Security Check
if(!isset($_SESSION['r']) || $_SESSION['r'] != 'admin') {
    die("Access Denied.");
}

// 2. Update Order Status Logic
if(isset($_POST['update_status'])){
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    header("Location: admin_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Orders | TechStore</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="container">
    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; padding-top: 20px;">
        <h1>Customer Orders</h1>
        <a href="admin.php" style="text-decoration: none; color: var(--accent);">← Back to Inventory</a>
    </header>

    <?php
    // Fetch orders and join with users table to see who bought what
    $order_query = $conn->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY order_date DESC");
    
    if($order_query->num_rows > 0): ?>
        <table class="inventory-table" style="width: 100%;">
            <thead>
                <tr style="background: #111; color: #fff;">
                    <th style="padding: 15px;">Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items Purchased</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $order_query->fetch_assoc()): 
                    $order_id = $order['id'];
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 20px; font-weight: bold;">#<?php echo $order_id; ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td style="color: #666; font-size: 0.9rem;"><?php echo $order['order_date']; ?></td>
                    <td>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem;">
                        <?php
                        // Fetch the specific items for this order
                        $items_res = $conn->query("SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_id = $order_id");
                        while($item = $items_res->fetch_assoc()){
                            echo "<li>{$item['quantity']}x {$item['name']}</li>";
                        }
                        ?>
                        </ul>
                    </td>
                    <td style="font-weight: 600;">$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td>
                        <span style="padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; 
                            background: <?php echo ($order['status'] == 'Delivered' ? '#e1f7e7' : ($order['status'] == 'Shipped' ? '#e1f0ff' : '#fff4e5')); ?>;
                            color: <?php echo ($order['status'] == 'Delivered' ? '#148020' : ($order['status'] == 'Shipped' ? '#0066cc' : '#d97706')); ?>;">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display: flex; gap: 5px;">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <select name="status" style="padding: 5px; border-radius: 5px; border: 1px solid #ddd;">
                                <option value="Processing" <?php if($order['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                <option value="Shipped" <?php if($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                <option value="Delivered" <?php if($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                            </select>
                            <button name="update_status" class="btn" style="padding: 5px 10px; font-size: 0.7rem;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 100px;">
            <p style="color: #888; font-size: 1.2rem;">No orders have been placed yet.</p>
        </div>
    <?php endif; ?>
</body>
</html>