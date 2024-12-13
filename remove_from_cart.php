<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];

    // Decrease the quantity of the product in the cart
    $sql = "UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();

    // Check if the quantity becomes zero and remove the item if so
    if ($stmt->affected_rows > 0) {
        // Optionally, remove the product if the quantity is zero
        $sqlCheck = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $userId, $productId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $row = $resultCheck->fetch_assoc();

        if ($row['quantity'] <= 0) {
            // Remove product from cart if quantity is zero
            $sqlDelete = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bind_param("ii", $userId, $productId);
            $stmtDelete->execute();
        }
    }

    header("Location: cart.php");
    exit();
}
