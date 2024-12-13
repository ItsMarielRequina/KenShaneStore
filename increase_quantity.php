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

    // Increase the quantity of the product in the cart
    $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    
    header("Location: cart.php");
    exit();
}
