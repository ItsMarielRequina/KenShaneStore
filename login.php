<?php
require 'config.php';
session_start();

// Ensure the connection is secure over HTTPS
if ($_SERVER['REQUEST_SCHEME'] != 'https') {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validate inputs
    if (empty($email) || empty($password) || empty($role)) {
        echo "<p class='error'>All fields are required.</p>";
        exit();
    }

    // Use prepared statements to prevent SQL Injection
    $sql = "SELECT * FROM users WHERE email = ? AND user_role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check password validity
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['user_role'];

        // Redirect based on role
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        // Generic error message to prevent detailed info leak
        echo "<p class='error'>Invalid login credentials.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KenShane Store - Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        /* Header styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.4);
            padding: 15px 20px;
            color: white;
        }

        .snowflake {
            position: fixed;
            top: -10px;
            z-index: 1000;
            color: white;
            font-size: 1em;
            pointer-events: none;
            animation: fall 10s linear infinite, sway 3s ease-in-out infinite;
        }

        @keyframes fall {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(100vh);
            }
        }

        @keyframes sway {
            50% {
                transform: translateX(10px);
            }
            100% {
                transform: translateX(-10px);
            }
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .header nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
            font-size: 16px;
        }

        .header nav a:hover {
            text-decoration: underline;
        }

        /* Login container styles */
        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 60px);
            text-align: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            background-color: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        h2 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 0;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button[type="submit"],
        button.toggle-login {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: green;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover,
        button.toggle-login:hover {
            background-color: #005500;
        }

        .hidden {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createSnowflake = () => {
                const snowflake = document.createElement('div');
                snowflake.classList.add('snowflake');
                snowflake.textContent = '❄'; // Snowflake character
                snowflake.style.left = Math.random() * 100 + 'vw';
                snowflake.style.fontSize = Math.random() * 10 + 10 + 'px';
                snowflake.style.opacity = Math.random();

                document.body.appendChild(snowflake);

                setTimeout(() => {
                    snowflake.remove();
                }, 10000); // Remove after 10 seconds
            };

            setInterval(createSnowflake, 200); // Create snowflakes every 200ms
        });

        function toggleLoginForm() {
            const customerForm = document.getElementById('customer-login');
            const adminForm = document.getElementById('admin-login');
            const toggleButton = document.getElementById('toggle-login-btn');

            if (customerForm.classList.contains('hidden')) {
                customerForm.classList.remove('hidden');
                adminForm.classList.add('hidden');
                toggleButton.textContent = 'Login as Admin';
            } else {
                adminForm.classList.remove('hidden');
                customerForm.classList.add('hidden');
                toggleButton.textContent = 'Login as Customer';
            }
        }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">KenShane Store</div>
        <nav>
            <a id="toggle-login-btn" href="javascript:void(0);" onclick="toggleLoginForm()">Login as Admin</a>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Customer Login -->
        <div id="customer-login" class="login-container">
            <h2>Customer Login</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="role" value="customer">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
            </form>
        </div>

        <!-- Admin Login -->
        <div id="admin-login" class="login-container hidden">
            <h2>Admin Login</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="role" value="admin">
                <input type="email" name="email" placeholder="Admin Email" required>
                <input type="password" name="password" placeholder="Admin Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>