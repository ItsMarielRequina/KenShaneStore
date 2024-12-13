<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    // Update order status
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $orderId);

    if ($stmt->execute()) {
        // Redirect back to admin order management page
        header("Location: admin_order_management.php");
        exit();
    } else {
        echo "Error updating order status: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
