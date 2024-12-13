<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Retrieve cart items and product details
$sql = "SELECT products.name, products.price, cart.quantity, products.image_url, products.id, products.stock
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total amount
$totalAmount = 0;
while ($row = $result->fetch_assoc()) {
    $totalAmount += $row['price'] * $row['quantity'];
}
$result->data_seek(0); // Reset result pointer for the table display

// Handle quantity addition and stock deduction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_quantity'], $_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $addQuantity = intval($_POST['add_quantity']);

    // Check current stock
    $checkStockSql = "SELECT stock FROM products WHERE id = ?";
    $checkStockStmt = $conn->prepare($checkStockSql);
    $checkStockStmt->bind_param("i", $productId);
    $checkStockStmt->execute();
    $checkStockResult = $checkStockStmt->get_result();
    $product = $checkStockResult->fetch_assoc();

    if ($product['stock'] >= $addQuantity) {
        // Update cart with new quantity
        $updateCartSql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $updateCartStmt = $conn->prepare($updateCartSql);
        $updateCartStmt->bind_param("iii", $addQuantity, $userId, $productId);
        $updateCartStmt->execute();

        // Deduct stock in products table
        $updateStockSql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $updateStockStmt = $conn->prepare($updateStockSql);
        $updateStockStmt->bind_param("ii", $addQuantity, $productId);
        $updateStockStmt->execute();

        header("Location: cart.php"); // Refresh the page to show updated cart
        exit();
    } else {
        echo "Not enough stock available for this item.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
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
            margin-left: 240px; 
            padding: 20px;
            width: calc(100% - 240px); 
            margin-top: 80px;
        }

        h2 {
            color: white;
            text-align: center;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .checkout-btn, .back-btn {
            padding: 10px 15px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 16px;
        }

        .checkout-btn {
            background-color: #28a745; /* Green */
        }

        .back-btn {
            background-color: #007bff; /* Blue */
        }

        .checkout-btn:hover {
            background-color: #218838; /* Darker green */
        }

        .back-btn:hover {
            background-color: #0056b3; /* Darker blue */
        }

        .increase-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .increase-btn:hover {
            background-color: #0056b3;
        }

        .remove-btn {
            background-color: #FF4136;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background-color: #C70039;
        }
    </style>
</head>
<body>
    <header>
        <h1>KenShane Store</h1>
    </header>

    <div class="sidebar">
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="customer_settings.php">Profile</a>
        <a href="orders.php">Orders</a>      
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Your Cart</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <form action="increase_quantity.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="increase-btn">Add</button>
                            </form>
                            <form action="remove_from_cart.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
         <h3 style="color: white; margin: 0;">Total Amount: ₱<?php echo number_format($totalAmount, 2); ?></h3>
         </div>
        <div style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
        <button class="checkout-btn" onclick="window.location.href='checkout.php'">Proceed Checkout</button>
        <button class="back-btn" onclick="window.location.href='index.php'">Back to Homepage</button>
        </div>

    <div id="notification" class="notification"></div>
</body>
</html>
