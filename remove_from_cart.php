<?php
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Remove the specific ID from the cart array
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
}

// Send them back to the cart page
header("Location: cart.php");
exit();
?>