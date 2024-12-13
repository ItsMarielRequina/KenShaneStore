<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    // Update the order status in the database
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the admin order management page
    header("Location: admin_order_management.php");
    exit();
}

// Fetch orders based on search query, and only include those with a payment method that is not null, empty, or '0'
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT orders.id, orders.order_id, orders.status, orders.total_amount, orders.payment_method, users.username, users.contact_number, users.address 
        FROM orders
        JOIN users ON orders.user_id = users.id
        WHERE (orders.order_id LIKE ? OR users.username LIKE ? OR users.contact_number LIKE ?) 
        AND orders.payment_method IS NOT NULL 
        AND orders.payment_method != '' 
        AND orders.payment_method != '0'  /* Exclude payment method equal to '0' */
        ORDER BY orders.order_id DESC";
$stmt = $conn->prepare($sql);
$search_param = "%" . $search_query . "%";
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Management - KenShane Store</title>
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
            margin-left: 350px; /* Leave space for the sidebar */
            padding: 20px;
            width: calc(80% - 240px); /* Adjust width */
            margin-top: 100px;
            margin-bottom: 100px;
            background-color: #ffffff; /* White background for the content */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .search-bar {
            margin-bottom: 20px;
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

        .footer {
            position: fixed;
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
        <h1>Order Management</h1>
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
        <h2>Manage Orders</h2>

        <!-- Search bar -->
        <form class="search-bar" method="GET" action="admin_order_management.php">
            <input type="text" name="search" placeholder="Search orders..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>₱<?php echo htmlspecialchars($row['total_amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td>
                                <form class="status-form" method="POST" action="admin_order_management.php">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <select name="new_status">
                                        <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Completed" <?php if ($row['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                        <option value="Incomplete" <?php if ($row['status'] == 'Incomplete') echo 'selected'; ?>>Incomplete</option>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No orders found with a valid payment method.</td>
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
            }, 10000); //
        };

        setInterval(createSnowflake, 200); // Create snowflakes at regular intervals
    });
    </script>
</body>
</html>
