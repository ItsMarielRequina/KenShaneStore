<?php
session_start();
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$orderId = $_SESSION['order_id']; // Use the existing order ID from the session
$paymentMethod = $_SESSION['payment_method'] ?? 'N/A';
$cartItems = [];

// Fetch user details
$sqlUser = "SELECT username, contact_number, address FROM users WHERE id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userDetails = $userResult->fetch_assoc();
$stmtUser->close();

if (!$userDetails) {
    echo "<p class='error-message'>User details not found. Please update your information in <a href='customer_settings.php'>Settings</a>.</p>";
    exit();
}

// Fetch cart items
$sql = "SELECT products.id, products.name, products.price, cart.quantity
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

// Calculate total amount
$totalAmount = array_reduce($cartItems, function($sum, $item) {
    return $sum + $item['price'] * $item['quantity'];
}, 0);

// Insert order into orders table (only if it's not already inserted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['order_inserted'])) {
    // Start transaction for order placement
    $conn->begin_transaction();

    try {
        // Insert order using existing order ID from the session
        $sqlOrder = "INSERT INTO orders (user_id, order_id, status, payment_method, contact_number, address, total_amount) 
                     VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
        $stmtOrder = $conn->prepare($sqlOrder);
        $stmtOrder->bind_param("issssd", $userId, $orderId, $paymentMethod, $userDetails['contact_number'], $userDetails['address'], $totalAmount);
        $stmtOrder->execute();
        $stmtOrder->close();

        // Deduct stock for each product
        foreach ($cartItems as $item) {
            $productId = $item['id'];
            $quantity = $item['quantity'];

            $sqlUpdateStock = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
            $stmtUpdateStock->bind_param("ii", $quantity, $productId);
            $stmtUpdateStock->execute();
            $stmtUpdateStock->close();
        }

        // Commit the transaction
        $conn->commit();

        // Mark order as inserted in the session
        $_SESSION['order_inserted'] = true;

        // Redirect to success page after confirming
        header("Location: order_success.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "<p class='error-message'>An error occurred while processing your order. Please try again later.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - KenShane Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D22B2B;
            padding: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
            text-align: center;
            flex-direction: column;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.2em;
            margin-top: 0;
        }
        .back-button {
            display: inline-block;
            padding: 10px 25px;
            font-size: 16px;
            background-color: #0056b3;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #003d80;
        }
        .party-popper {
            width: 50px;
            height: 50px;
            background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAzCAYAAABw6u71AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAABl0RVh0U29mdHdhcmUAd3d3Lmljb25maW5kZXIuY29tEf8hTwAAAVxJREFUaN7tmk0OhCAMhTMKZAbMQ3QjFnpI2QAd2gYqAj+DP59oLXSW5bs2n5yp7XNAAAAEAAAAABoQHba9EowIqMWP/EEniAxkA1+lbAa8OpyNjMfNtJqsLg6eAiWTKHQA9hPQYPvrrmLgoBaAzvwA7AB5BuVbUl8HuPVTRxyAPgBtScB8n+MHIB8AHiU/MD1TBc8TyPhtjeAo8DoAq4ju5TqTuB9+hR/TjNuGGgB9BJuU7c8D76xnkBSAHzX/P14XyYXjJdo0YXXkFfAeBgG+AXiQzloTChgB4Un1tdhnV4IZdLccF0JpXil/AB6XLGnvXJmgHkPdjqcdUCjpvDCMAAAAASUVORK5CYII=') no-repeat center center;
            background-size: contain;
            animation: pop 1s ease-out;
            margin: 20px 0;
        }
        @keyframes pop {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="party-popper"></div>
    <h1>Thank you for purchasing at KenShane Store!</h1>
    <p>Order ID: <?php echo $orderId; ?></p>
    <a href="index.php" class="back-button">Back to Homepage</a>
</body>
</html>
