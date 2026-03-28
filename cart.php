<?php include 'db.php'; session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Your Cart | TechStore</title>
</head>
<body class="container">
    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 60px; padding-top: 20px;">
        <h1 style="font-size: 3rem; margin: 0;">Shopping Cart</h1>
        <a href="index.php" style="text-decoration: none; color: #0066cc; font-weight: 500;">← Continue Shopping</a>
    </header>

    <?php if(!empty($_SESSION['cart'])): ?>
        <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
            <thead>
                <tr style="background: #111; color: #fff; text-align: left;">
                    <th style="padding: 20px 0 20px 40px; border-radius: 15px 0 0 15px; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px;">Product</th>
                    <th style="padding: 20px 0;">Price</th>
                    <th style="padding: 20px 0; text-align: center;">Quantity</th>
                    <th style="padding: 20px 0;">Subtotal</th>
                    <th style="padding: 20px 40px 20px 0; border-radius: 0 15px 15px 0;"></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach($_SESSION['cart'] as $id => $qty): 
                    $res = $conn->query("SELECT * FROM products WHERE id=$id");
                    $p = $res->fetch_assoc();
                    $subtotal = $p['price'] * $qty;
                    $total += $subtotal;
                ?>
                <tr style="border-bottom: 1px solid #f5f5f7;">
                    <td style="display: flex; align-items: center; gap: 25px; padding: 30px 0 30px 40px;">
                        <div style="background: #f5f5f7; padding: 10px; border-radius: 14px; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?php echo $p['image_url']; ?>" 
                                 style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <strong style="display: block; font-size: 1.2rem; margin-bottom: 4px;"><?php echo $p['name']; ?></strong>
                            <span style="font-size: 0.85rem; color: #888;"><?php echo $p['category']; ?></span>
                        </div>
                    </td>
                    
                    <td style="font-weight: 500; font-size: 1.1rem;">$<?php echo number_format($p['price'], 2); ?></td>
                    
                    <td style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; background: #f5f5f7; border-radius: 30px; padding: 6px 15px; gap: 15px;">
                            <a href="update_cart.php?id=<?php echo $id; ?>&action=minus" 
                               style="text-decoration: none; color: #1d1d1f; font-weight: bold; font-size: 1.2rem; user-select: none;">−</a>
                            
                            <span style="font-weight: 600; font-size: 1.1rem; min-width: 20px; text-align: center; user-select: none;">
                                <?php echo $qty; ?>
                            </span>
                            
                            <a href="update_cart.php?id=<?php echo $id; ?>&action=plus" 
                               style="text-decoration: none; color: #1d1d1f; font-weight: bold; font-size: 1.2rem; user-select: none;">+</a>
                        </div>
                    </td>

                    <td style="font-weight: 700; font-size: 1.1rem;">$<?php echo number_format($subtotal, 2); ?></td>
                    
                    <td style="text-align: right; padding-right: 40px;">
                        <a href="remove_from_cart.php?id=<?php echo $id; ?>" 
                           style="color: #ff3b30; text-decoration: none; font-size: 1.8rem; font-weight: 300; opacity: 0.6;"
                           onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                           &times;
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 60px; text-align: right; padding-top: 40px; border-top: 1px solid #eee;">
            <p style="font-size: 1.2rem; color: #888; margin-bottom: 8px;">Total Amount</p>
            <h2 style="font-size: 4rem; margin: 0 0 30px 0; font-weight: 600; letter-spacing: -2px;">
                $<?php echo number_format($total, 2); ?>
            </h2>
            <a href="checkout.php" class="btn" style="padding: 22px 60px; font-size: 1.2rem; border-radius: 16px; background: #000; color: #fff; text-decoration: none; display: inline-block; font-weight: 500;">
                Complete Purchase
            </a>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 120px 0;">
            <div style="font-size: 4rem; margin-bottom: 20px;">🛍️</div>
            <p style="font-size: 1.6rem; color: #1d1d1f; margin-bottom: 30px; font-weight: 500;">Your cart is feeling a bit light.</p>
            <a href="index.php" class="btn" style="padding: 15px 40px; border-radius: 12px; background: #0071e3; color: white; text-decoration: none;">Start Shopping</a>
        </div>
    <?php endif; ?>
</body>
</html>