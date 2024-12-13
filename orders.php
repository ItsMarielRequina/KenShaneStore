<?php
session_start();
require 'config.php'; // Database configuration

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's unique orders, excluding those with payment method equal to 0, grouped by order_id
$stmt = $conn->prepare("
    SELECT order_id, MAX(order_date) AS order_date, status, SUM(total_amount) AS total_amount, payment_method 
    FROM orders 
    WHERE user_id = ? 
    AND payment_method IS NOT NULL AND payment_method != '' AND payment_method != '0'
    GROUP BY order_id
    ORDER BY order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <style>
        /* Styles */
        body {
            font-family: 'Arial', serif;
            background-color: #D22B2B;
            margin: 0;
            padding: 0;
            display: flex;
            height: auto;
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
        .container {
            margin-left: 350px;
            padding: 20px;
            width: calc(80% - 240px);
            margin-top: 100px;
            background-color: #ffffff;
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
            background-color: black;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header>
        <h1>Order History</h1>
    </header>

    <div class="sidebar">
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="customer_settings.php">Profile</a>
        <a href="orders.php">Orders</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($order['order_date']))); ?></td>
                            <td class="<?php echo ($order['status'] === 'completed') ? 'status-completed' : 'status-pending'; ?>">
                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                            </td>
                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php $stmt->close(); ?>
    </div>
</body>
</html>
