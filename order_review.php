<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$paymentMethod = $_SESSION['payment_method'] ?? null;
$cartItems = [];

// Redirect to payment method selection if not set
if (!$paymentMethod) {
    echo "<p class='error-message'>Please select a payment method in <a href='checkout.php'>Checkout</a>.</p>";
    exit();
}

// Fetch the user's details
$sqlUser = "SELECT username, contact_number, address FROM users WHERE id = ?";
$stmtUser = $conn->prepare($sqlUser);
if (!$stmtUser) {
    die("Failed to prepare user query.");
}
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userDetails = $userResult->fetch_assoc();
$stmtUser->close();

if (!$userDetails) {
    // Redirect to the incomplete-profile page
    header("Location: incomplete-profile.php");
    exit();
}

// Fetch the cart items
$sql = "SELECT p.id, p.name, p.price, i.quantity AS stock, c.quantity AS cart_quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        JOIN inventory i ON p.id = i.product_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare cart query.");
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmt->close();

// Check if cart is empty
if (empty($cartItems)) {
    header("Location: empty-cart.php");
    exit();
}

// Calculate the total amount
$totalAmount = array_reduce($cartItems, function ($sum, $item) {
    return $sum + $item['price'] * $item['cart_quantity'];
}, 0);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = uniqid('order_'); // Using unique ID for order

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert order into orders table
        $sqlOrder = "INSERT INTO orders (user_id, order_id, status, payment_method, contact_number, address, total_amount) 
                     VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
        $stmtOrder = $conn->prepare($sqlOrder);
        $stmtOrder->bind_param("issssd", $userId, $orderId, $paymentMethod, $userDetails['contact_number'], $userDetails['address'], $totalAmount);
        $stmtOrder->execute();
        $stmtOrder->close();

        // Check and deduct stock for each product in the cart
        foreach ($cartItems as $item) {
            $deductQuantity = $item['cart_quantity'];

            if ($item['stock'] < $deductQuantity) {
                throw new Exception("Not enough stock for product: " . htmlspecialchars($item['name']));
            }

            // Deduct the quantity from the stock in the inventory
            $sqlUpdateStock = "UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?";
            $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
            $stmtUpdateStock->bind_param("ii", $deductQuantity, $item['id']);
            $stmtUpdateStock->execute();
            $stmtUpdateStock->close();
        }

        // Clear the cart after successful order
        $sqlClearCart = "DELETE FROM cart WHERE user_id = ?";
        $stmtClearCart = $conn->prepare($sqlClearCart);
        $stmtClearCart->bind_param("i", $userId);
        $stmtClearCart->execute();
        $stmtClearCart->close();

        // Commit the transaction
        $conn->commit();

        // Redirect to order processing page with status update
        header("Location: confirmation.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        echo "<div class='error-message'>Failed to place order: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Review - KenShane Store</title>
    <style>
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

        .sidebar a:hover {
            background-color: rgba(0, 0, 0, 0.4);
        }

        .container {
            margin: auto;
            padding: 80px 20px 20px;
            width: 500px;

        }

        .review-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 20px 0;
        }

        h2 {
            color: white;
        } 
        
        h3 {
            color: #343a40;
        }

        .total, .success-message, .error-message {
            font-weight: bold;
            margin-top: 10px;
        }

        .info, .cart-item {
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-top: 20px;
        }

        .back-button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px 5px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .button-container {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
<header>
    <h1>KenShane Store</h1>
</header>
<div class="sidebar">
    <a href="index.php">Homepage</a>
    <a href="cart.php">View Cart</a>
    <a href="orders.php">Your Orders</a>
    <a href="customer_settings.php">Settings</a>
    <a href="logout.php">Logout</a>
</div>
<div class="container">
    <h2>Order Review</h2>
    <div class="review-container">
        <h3>Payment Method: <?php echo htmlspecialchars($paymentMethod); ?></h3>
        <h3>Items:</h3>
        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <p><?php echo htmlspecialchars($item['name']); ?> - <?php echo $item['cart_quantity']; ?> x ₱<?php echo number_format($item['price'], 2); ?></p>
            </div>
        <?php endforeach; ?>
        <div class="total">Total Amount: ₱<?php echo number_format($totalAmount, 2); ?></div>
        <h3>Delivery Information</h3>
        <div class="info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($userDetails['username']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($userDetails['contact_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($userDetails['address']); ?></p>
        </div>
        <form method="POST">
            <div class="button-container">
                <button type="submit" class="back-button">Confirm Order</button>
            </div>
        </form>
        <form action="checkout.php" method="get">
            <div class="button-container">
                <button type="submit" class="back-button">Back to Checkout</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
    