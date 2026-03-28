<?php
include 'db.php';
session_start();

if (isset($_POST['reg'])) {
    $u = $_POST['u'];
    // Securely hash the password
    $p = password_hash($_POST['p'], PASSWORD_DEFAULT);
    
    // Everyone registering here is a 'customer'
    $sql = "INSERT INTO users (username, password, role) VALUES ('$u', '$p', 'customer')";
    
    if ($conn->query($sql)) {
        // After successful registration, send them to login
        header("Location: login.php?registered=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Create Account | TechStore</title>
</head>
<body class="auth-page"> <div class="auth-card"> <h2>Create Account</h2>
        <p>Join the TechStore community.</p>

        <form method="POST">
            <input name="u" placeholder="Choose a Username" required>
            <input name="p" type="password" placeholder="Choose a Password" required>
            <button name="reg" class="btn">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>

</body>
</html>