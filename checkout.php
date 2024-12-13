<?php
require 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$cartItems = []; // Array to hold cart items for processing

// Fetch the cart items to display to the user before checkout
$sql = "SELECT products.id, products.name, products.price, cart.quantity
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row; // Store cart items for later processing
}

// Check if there are items in the cart
if (empty($cartItems)) {
    header("Location: empty-cart.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle payment method selection
    $paymentMethod = $_POST['payment_method'] ?? null;

    // Validate payment method
    if ($paymentMethod && $paymentMethod != '0') {
        $_SESSION['payment_method'] = $paymentMethod; // Save payment method to session

        // Fetch the user's contact number and address
        $userQuery = "SELECT contact_number, address FROM users WHERE id = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $userData = $userResult->fetch_assoc();

        // Check if the profile is complete
        if (!$userData['contact_number'] || !$userData['address']) {
            // Redirect to the incomplete-profile page if any required data is missing
            header("Location: incomplete-profile.php");
            exit();
        } else {
            $contactNumber = $userData['contact_number'];
            $address = $userData['address'];

            // Generate unique order ID
            $orderId = uniqid('order_'); // Prefix 'order_' for clarity
            $totalAmount = 0;

            // Insert order into the orders table
            $insertOrderSql = "INSERT INTO orders (user_id, total_amount, status, order_date, order_id, payment_method, contact_number, address) 
                               VALUES (?, ?, 'Pending', NOW(), ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertOrderSql);
            $stmt->bind_param("idsiss", $userId, $totalAmount, $orderId, $paymentMethod, $contactNumber, $address);

            // Execute the insert query
            if ($stmt->execute()) {
                $orderIdInserted = $stmt->insert_id; // Get the inserted order ID

                // Insert each cart item into the order_items table
                $insertOrderDetailsSql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $detailStmt = $conn->prepare($insertOrderDetailsSql);

                foreach ($cartItems as $item) {
                    $quantity = $item['quantity'];
                    $price = $item['price'];
                    $detailStmt->bind_param("iiid", $orderIdInserted, $item['id'], $quantity, $price);
                    $detailStmt->execute();

                    // Calculate total amount
                    $totalAmount += $price * $quantity;
                }

                // Update the total amount in the orders table
                $updateTotalSql = "UPDATE orders SET total_amount = ? WHERE order_id = ?";
                $updateStmt = $conn->prepare($updateTotalSql);
                $updateStmt->bind_param("ds", $totalAmount, $orderId);
                $updateStmt->execute();

                // Redirect to order review page with the order ID
                header("Location: order_review.php?order_id=" . urlencode($orderId));
                exit();
            } else {
                echo "Error creating order: " . $stmt->error;
            }
        }
    } else {
        echo "<div class='error-message'>Invalid payment method. Please choose a valid payment method.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - KenShane Store</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #D22B2B;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: black;
            color: white;
            padding: 20px;
            text-align: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .sidebar {
            background-color: white;
            color: black;
            width: 200px;
            height: 100vh;
            padding-top: 80px;
            position: fixed;
            margin-top: 50px;
            left: 0;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            color: black;
            padding: 15px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .container {
            width: 80%;
            margin-left: 240px;
            padding: 20px;
            margin-top: 150px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }

        .back-btn {
            padding: 10px 15px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 16px;
        }

        .back-btn {
            background-color: #007bff; /* Blue */
        }

        .invoice {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .total {
            font-weight: bold;
            margin-top: 10px;
            font-size: 1.2em;
            text-align: right;
        }
        .payment-button, .back-button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .payment-button:hover, .back-button:hover {
            background-color: #28a745;
        }
        .payment-options {
            margin-top: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .payment-options label {
            display: block;
            margin-bottom: 10px;
        }
        .empty-cart-container {
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 600px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .empty-cart-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .empty-cart-container p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .shop-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .shop-button:hover {
            background-color: #0056b3;
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
        <div class="invoice">
            <?php $totalAmount = 0; ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span><?php echo number_format($item['price'] * $item['quantity'], 2); ?> PHP</span>
                </div>
                <?php $totalAmount += $item['price'] * $item['quantity']; ?>
            <?php endforeach; ?>
            <div class="total">
                <span>Total: <?php echo number_format($totalAmount, 2); ?> PHP</span>
            </div>

            <form method="POST" action="">
                <div class="payment-options">
                    <label>
                        <input type="radio" name="payment_method" value="COD" required> Cash on Delivery
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="GCash" required> GCash
                    </label>
                </div>
                <button type="submit" class="payment-button">Proceed to Order Review</button>
                <button class="back-btn" onclick="window.location.href='cart.php'">Back to Cart</button>
            </form>
        </div>
    </div>
</body>
</html>
