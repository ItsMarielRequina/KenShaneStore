<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        echo "<p class='error'>All fields are required.</p>";
        exit();
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, redirect to duplicate-account.php
        header("Location: duplicate-account.php");
        exit();
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Automatically assign role as 'customer'
        $role = 'customer';

        // Insert the user into the database
        $sql = "INSERT INTO users (email, password, user_role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $hashed_password, $role);

        if ($stmt->execute()) {
            echo "<p class='success'>Account created successfully!</p>";
            // Optionally, redirect to login page
            header("Location: login.php");
            exit();
        } else {
            echo "<p class='error'>Something went wrong. Please try again later.</p>";
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KenShane Store - Customer Signup</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
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
            to {
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.4);
            padding: 15px 20px;
            color: white;
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

        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 60px);
            text-align: center;
        }

        .signup-container {
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

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 0;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button[type="submit"] {
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

        button[type="submit"]:hover {
            background-color: #005500;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .success {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="logo">KenShane Store</div>
    <nav>
        <a href="login.php">Login</a>
    </nav>
</header>

<!-- Main Content -->
<div class="main-content">
    <div class="signup-container">
        <h2>Customer Signup</h2>
        <form action="signup.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</div>
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
    </script>
</body>
</html>
