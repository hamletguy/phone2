<?php
include 'db.php';
session_start();

if(isset($_POST['register'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $user = mysqli_real_escape_string($conn, $_POST['username']);

    // 1. Validate Email Format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } 
    // 2. Check if passwords match
    elseif ($pass !== $confirm_pass) {
        $error = "Passwords do not match.";
    }
    // 3. Password Strength (8+ chars, 1 Uppercase, 1 Special)
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+|~-]).{8,}$/', $pass)) {
        $error = "Password must be at least 8 characters, include an uppercase letter and a special character.";
    } 
    else {
        // 4. CHECK FOR DUPLICATES (Prevents Database Crash)
        // We check both email AND username in one go
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $user);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){
            $error = "That username or email is already taken.";
        } else {
            // 5. Secure Hashing & Insert
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $user, $email, $hashed_pass);
            
            if($stmt->execute()){
                header("Location: login.php?success=1");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | TechStore</title>
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* Isolated Auth Styles - Synced with Login */
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
            color: #2563eb; /* Professional Blue accent */
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
        <h2>Create Account</h2>
        <p class="subtitle">Join our community today</p>

        <?php if(isset($error)): ?>
            <div class="error-banner">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label>Full Name</label>
            <input type="text" name="username" placeholder="e.g. John Doe" required>
            
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@gmail.com" required>
            
            <label>Create Password</label>
            <input type="password" name="password" placeholder="8+ characters" required>
            
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Repeat your password" required>
            
            <button name="register" class="btn-primary">Register</button>
        </form>

        <footer>
            Already have an account? <a href="login.php">Log in</a>
        </footer>
    </div>

</body>
</html>