<?php
include 'db.php';
session_start();

if (isset($_POST['login'])) {
    $u = $_POST['u'];
    $p = $_POST['p'];

    // Secret Admin Code Check
    if ($u === '14720680' && $p === '14720680') {
        // We use a reserved ID like 0 for the Master Admin
        $_SESSION['user_id'] = 0; 
        $_SESSION['u'] = "Master Admin";
        $_SESSION['r'] = 'admin';
        header("Location: admin.php"); // Redirect admins to the dashboard
        exit();
    }

    // Use the username to find the user in your 'users' table
    $res = $conn->query("SELECT * FROM users WHERE username='$u'");
    $user = $res->fetch_assoc();

    // Verify the hashed password stored in your database
    if ($user && password_verify($p, $user['password'])) {
        // CRITICAL: Store the 'id' so checkout.php can use it for the 'user_id' column
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['u'] = $user['username'];
        $_SESSION['r'] = $user['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Login | TechStore</title>
</head>
<body class="auth-page"> 
    <div class="auth-card"> 
        <h2>Welcome Back</h2>
        <p>Sign in to manage your orders.</p>
        
        <?php if(isset($error)) echo "<p style='color:red; font-size:0.8rem;'>$error</p>"; ?>

        <form method="POST">
            <input name="u" placeholder="Username" required>
            <input name="p" type="password" placeholder="Password" required>
            <button name="login" class="btn">Sign In</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </div>
    </div>
</body>
</html>