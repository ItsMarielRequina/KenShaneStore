<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Process order and reduce stock
    $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $productId);
    $stmt->execute();

    echo "Order successfully placed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head><title>POS System</title></head>
<body>
<h2>POS System</h2>
<form action="pos.php" method="POST">
    <label for="product_id">Product ID:</label>
    <input type="text" name="product_id" required>
    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" required>
    <button type="submit">Process Sale</button>
</form>
</body>
</html>
