<?php
require 'config.php';
session_start();

// Handle restock form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restock'])) {
    $product_id = $_POST['product_id'];
    $add_quantity = $_POST['add_quantity'];

    if ($add_quantity < 0) {
        $add_quantity = 0;
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Update the inventory table to reflect the new quantity
        $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity + ? WHERE product_id = ?");
        $stmt->bind_param("ii", $add_quantity, $product_id);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        $_SESSION['message'] = "Stock updated successfully!";
        header("Location: inventory.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollback();
        $_SESSION['message'] = "Error updating stock!";
        header("Location: inventory.php");
        exit();
    }
}

// Fetch products along with their inventory levels from the inventory table
$sql = "SELECT products.id, products.name, products.description, products.price, products.image_url, inventory.quantity
        FROM products
        JOIN inventory ON products.id = inventory.product_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <style>
        body {
            font-family: 'Arial', serif;
            background-color: #D22B2B;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
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
            background-color: rgba(0, 0, 0, 0);
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
        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 12px;
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
            margin-top: 20px;
            text-align: center;
            color: #666;
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

        .restock-form {
            display: flex;
            align-items: center;
        }

        .restock-form input {
            width: 60px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Inventory Management</h1>
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
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Current Stock Levels</h2>
            <a href="add_product.php" style="text-decoration: none;">
                <button style="padding: 10px 20px; background-color: #D22B2B; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Add Product
                </button>
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Restock</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <form class="restock-form" method="POST" action="inventory.php">
                                <input type="number" name="add_quantity" min="1" max="100" required>
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <button type="submit" name="restock">Restock</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
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

<?php
$conn->close();
?>
