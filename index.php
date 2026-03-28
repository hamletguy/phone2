<?php include 'db.php'; session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>TechStore | Premium Tech</title>
</head>
<body>
<header>
    <div class="logo">📱 TechStore</div>
    <nav>
        <a href="index.php">Shop</a>
        <a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
        <?php if(isset($_SESSION['u'])): ?>
            <?php if($_SESSION['r'] == 'admin'): ?><a href="admin.php">Admin Panel</a><?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h2>Explore the latest tech.</h2>
    <form method="GET">
        <input type="text" name="s" placeholder="Search for iPhone, AirPods, or Chargers..." value="<?php echo htmlspecialchars($_GET['s'] ?? ''); ?>">
    </form>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
        <?php
        $s = $conn->real_escape_string($_GET['s'] ?? '');
        $res = $conn->query("SELECT * FROM products WHERE is_active = 1 AND name LIKE '%$s%'");
        
        while($r = $res->fetch_assoc()): 
            // Fetch all images for this specific product for the hover effect
            $img_res = $conn->query("SELECT image_path FROM product_images WHERE product_id = " . $r['id']);
            $imgs = [];
            while($img = $img_res->fetch_assoc()) { $imgs[] = $img['image_path']; }
            
            // Fallback to the main image_url if no gallery images exist
            if(empty($imgs)) { $imgs[] = $r['image_url']; }
        ?>
            <a href="product_details.php?id=<?php echo $r['id']; ?>" style="text-decoration: none; color: inherit;">
                <div class="card" onmouseover="startCycle(this)" onmouseout="stopCycle(this)" style="position: relative; background: #f5f5f7; border-radius: 18px; padding: 20px; transition: transform 0.3s;">
                    
                    <div class="img-container" style="height: 250px; position: relative; overflow: hidden; margin-bottom: 15px;">
                        <?php foreach($imgs as $index => $path): ?>
                            <img src="<?php echo $path; ?>" 
                                 class="cycle-img" 
                                 style="width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0; opacity: <?php echo $index == 0 ? '1' : '0'; ?>; transition: opacity 0.5s;">
                        <?php endforeach; ?>
                    </div>
                    
                    <p style="color: #888; font-size: 0.8rem; text-transform: uppercase; margin: 0;"><?php echo $r['category']; ?></p>
                    <h3><?php echo $r['name']; ?></h3>
                    <p class="price" style="font-weight: 700; font-size: 1.2rem;">$<?php echo number_format($r['price'], 2); ?></p>
                    
                    <object><a href="add_to_cart.php?id=<?php echo $r['id']; ?>" class="btn" style="display: block; text-align: center; margin-top: 10px;">Add to Cart</a>
                    </object>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<script>
    let intervals = new Map();

    function startCycle(card) {
        const images = card.querySelectorAll('.cycle-img');
        if (images.length <= 1) return;
        
        let i = 0;
        const interval = setInterval(() => {
            images[i].style.opacity = 0;
            i = (i + 1) % images.length;
            images[i].style.opacity = 1;
        }, 1500); // Cycles every 1 second
        
        intervals.set(card, interval);
    }

    function stopCycle(card) {
        if (intervals.has(card)) {
            clearInterval(intervals.get(card));
            intervals.delete(card);
        }
        // Reset to the first image
        const images = card.querySelectorAll('.cycle-img');
        images.forEach((img, idx) => {
            img.style.opacity = (idx === 0 ? '1' : '0');
        });
    }
</script>

</body>
</html>