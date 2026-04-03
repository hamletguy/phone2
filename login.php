<?php
include 'db.php';
session_start();

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    // 1. Secret Admin Code Check (Gmail-style email used here now)
    if ($email === 'admin@techstore.com' && $pass === '14720680') {
        $_SESSION['user_id'] = 0; 
        $_SESSION['username'] = "Master Admin";
        $_SESSION['r'] = 'admin';
        header("Location: admin.php");
        exit();
    }

    // 2. Professional Database Check
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify the hashed password
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username'];
            $_SESSION['r'] = $user['role'];
            
            // Redirect based on role
            header("Location: " . ($user['role'] == 'admin' ? "admin.php" : "index.php"));
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TechStore</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Synced with Register Page */
        body.auth-page {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .auth-card h2 {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-size: 1.8rem;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: left;
        }

        .auth-form label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: -10px;
        }

        .auth-form input {
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .auth-form input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .error-banner {
            background: #fef2f2;
            color: #ef4444;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            border: 1px solid #fee2e2;
            margin-bottom: 20px;
            text-align: left;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        footer {
            margin-top: 25px;
            font-size: 0.9rem;
            color: #64748b;
        }

        footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body class="auth-page"> 

    <div class="auth-card"> 
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to manage your orders.</p>
        
        <?php if(isset($error)): ?>
            <div class="error-banner">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@gmail.com" required>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
            
            <button name="login" class="btn-primary">Sign In</button>
        </form>

        <footer>
            Don't have an account? <a href="register.php">Create one</a>
        </footer>
    </div>

</body>
</html>