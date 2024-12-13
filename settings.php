<?php
require 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch only customer accounts from the database
$sqlUsers = "SELECT id, username, email, created_at, status FROM users WHERE user_role='customer'";
$resultUsers = $conn->query($sqlUsers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <style>
        /* Basic page styling */
        body { 
            font-family: 'Arial', serif;
            background-color: #D22B2B;
            margin: 0;
            padding: 0;
            display: flex;
            height: auto; /* Change to auto to accommodate content */
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
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

        header { 
            background-color: black;
            color: white;
            padding: 15px;
            text-align: center;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        header h1 { 
            margin: 0; 
            font-size: 28px; 
            font-weight: normal; 
        }

        /* Sidebar styling */
        .sidebar {
            width: 220px;
            color: white;
            padding: 20px;
            height: 100vh;
            margin-top: 50px;
            position: fixed;
        }
        .sidebar h2 {
            color: white;
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: normal;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-size: 16px;
        }
        .sidebar a:hover {
            background-color: rgba(0, 0, 0, 0.4);
        }

        .container {
            margin-left: 350px; /* Leave space for the sidebar */
            padding: 20px;
            width: calc(80% - 240px); /* Adjust width */
            margin-top: 100px;
            margin-bottom: 100px;
            background-color: #ffffff; /* White background for the content */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #dddddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: white;
        }   

        .footer {
            position: fixed; /* Fixes the footer at the bottom */
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8); /* Background color */
            color: white;
            text-align: center;
            padding: 5px 0;
            font-size: 14px;
        }

    </style>
</head>
<body>
    <header>
        <h1>User Management</h1>
    </header>

    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="inventory.php">Inventory Management</a>
        <a href="sales.php">Sales Management</a>
        <a href="settings.php">User Management</a>
        <a href="admin_order_management.php">Order Management</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="container">
        <h2>List of Customers</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultUsers->num_rows > 0): ?>
                    <?php while ($row = $resultUsers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> KenShane Store. All rights reserved.</p>
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
