<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'config.php'; // Include your database connection file

// Fetch data for inventory management
$inventoryQuery = "SELECT COUNT(*) AS total FROM products"; // Example query, adjust as needed
$inventoryResult = $conn->query($inventoryQuery);
$inventoryCount = $inventoryResult->fetch_assoc()['total'];

// Fetch data for sales management
$salesQuery = "SELECT COUNT(*) AS total FROM orders"; // Example query, adjust as needed
$salesResult = $conn->query($salesQuery);
$salesCount = $salesResult->fetch_assoc()['total'];

// Fetch data for user management
$userQuery = "SELECT COUNT(*) AS total FROM users"; // Example query, adjust as needed
$userResult = $conn->query($userQuery);
$userCount = $userResult->fetch_assoc()['total'];

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-left: 500px; /* Leave space for the sidebar */
            padding: 20px;
            width: calc(50% - 240px); /* Adjust width */
            margin-top: 100px; /* Offset for header */
            margin-bottom: 50px;
            background-color: rgba(0, 0, 0, 0.4); /* White background for the content */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: white;
            margin-bottom: 20px;
            font-size: 2.5em;
            text-transform: uppercase;
        }

        h2 {
            text-align: center;
            color: white;
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
        <h1>Admin Dashboard</h1>
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
        <div class="chart-container">
            <h2>Inventory Management</h2>
            <canvas id="inventoryChart" width="400" height="400"></canvas>
        </div>
        <div class="chart-container">
            <h2>Sales Management</h2>
            <canvas id="salesChart" width="400" height="400"></canvas>
        </div>
        <div class="chart-container">
            <h2>User Management</h2>
            <canvas id="userChart" width="400" height="400"></canvas>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> KenShane Store. All rights reserved.</p>
    </div>

    <script>
        // Inventory Chart
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        const inventoryChart = new Chart(inventoryCtx, {
            type: 'pie',
            data: {
                labels: ['Inventory'],
                datasets: [{
                    label: 'Inventory Overview',
                    data: [<?php echo $inventoryCount; ?>, 100 - <?php echo $inventoryCount; ?>], // Example data
                    backgroundColor: ['red', 'white'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(201, 203, 207, 1)'],
                    borderWidth: 1      
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                    }
                }
            }
        });

        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'pie',
            data: {
                labels: ['Sales'],
                datasets: [{
                    label: 'Sales Overview',
                    data: [<?php echo $salesCount; ?>, 100 - <?php echo $salesCount; ?>], // Example data
                    backgroundColor: ['blue', 'white'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(201, 203, 207, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                    }
                }
            }
        });

        // User Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(userCtx, {
            type: 'pie',
            data: { 
                labels: ['Users'],
                datasets: [{
                    label: 'User Overview',
                    data: [<?php echo $userCount; ?>, 100 - <?php echo $userCount; ?>], // Example data
                    backgroundColor: ['yellow', 'white'],
                    borderColor: ['rgba(255, 206, 86, 1)', 'rgba(201, 203, 207, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                    }
                }
            }
        });
    </script>
</body>
</html>
