<?php
require 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize search variable
$searchQuery = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $searchQuery = $_GET['search'] ?? '';
}

// Fetch total sales amount
$sqlTotalSales = "SELECT SUM(total_amount) AS total_sales 
                  FROM orders 
                  WHERE status = 'Completed' AND payment_method != '0'";
$resultTotal = $conn->query($sqlTotalSales);
$totalSales = $resultTotal->fetch_assoc()['total_sales'] ?? 0;

// Fetch sales data from the orders table
$sqlSales = "SELECT id, user_id, order_id, total_amount, payment_method, contact_number, address, status, order_date 
             FROM orders 
             WHERE status = 'Completed' AND payment_method != '0'";

// Append search criteria if it exists
if ($searchQuery) {
    $sqlSales .= " AND (order_id LIKE ? OR contact_number LIKE ?)";
}

// Add ORDER BY clause to sort by order date in descending order
$sqlSales .= " ORDER BY order_date DESC";

$stmt = $conn->prepare($sqlSales);

// Bind parameters if search query exists
if ($searchQuery) {
    $searchWildcard = "%$searchQuery%";
    $stmt->bind_param("ss", $searchWildcard, $searchWildcard);
}

$stmt->execute();
$resultSales = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Sales</title>
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
            margin-left: 350px; 
            padding: 20px;
            width: calc(80% - 240px); 
            margin-top: 100px;
            margin-bottom: 100px;
            background-color: #ffffff; 
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: black;
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
            background-color: black;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }

        .search-box {
            margin-bottom: 20px;
        }
        .search-box input[type="text"] {
            padding: 8px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            width: 200px;
        }
        .search-box button {
            padding: 8px 12px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-box button:hover {
            background-color: #575757;
        }

        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8); 
            color: white;
            text-align: center;
            padding: 5px 0;
            font-size: 14px;
        }
   </style>
</head>
<body>
<header>
    <h1>All Sales</h1>
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
    <h2>List of All Sales</h2>
    <p><strong>Total Sales:</strong> ₱<?php echo number_format($totalSales, 2); ?></p>
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by Order ID or Contact Number" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <table>
        <thead>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total Amount</th>
            <th>Payment Method</th>
            <th>Contact Number</th>
            <th>Address</th>
            <th>Status</th>
            <th>Order Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($resultSales->num_rows > 0): ?>
            <?php while ($row = $resultSales->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No sales found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> KenShane Store. All rights reserved.</p>
</div>
</body>
</html>
