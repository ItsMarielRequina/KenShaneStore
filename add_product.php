<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock']; // Capture the stock quantity

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $image_name = basename($image['name']);
        $image_tmp = $image['tmp_name'];
        $image_path = 'image/' . $image_name; // Set the path to save the image

        // Move the uploaded file to the 'uploads' directory
        move_uploaded_file($image_tmp, $image_path);
    } else {
        $image_path = NULL; // Set to NULL if no image is uploaded
    }

    // Insert the new product into the products table
    $stmt = $conn->prepare("INSERT INTO products (name, price, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $name, $price, $image_path); // Bind 3 parameters: string, double, string

    if ($stmt->execute()) {
        // Retrieve the ID of the newly inserted product
        $product_id = $stmt->insert_id;

        // Insert into the inventory table
        $stmt_inventory = $conn->prepare("INSERT INTO inventory (product_id, quantity) VALUES (?, ?)");
        $stmt_inventory->bind_param("ii", $product_id, $stock_quantity); // Bind 2 parameters: integer, integer

        if ($stmt_inventory->execute()) {
            echo "Product and inventory added successfully with stock quantity of " . $stock_quantity . ".";
        } else {
            echo "Failed to add inventory. Error: " . $stmt_inventory->error;
        }

        $stmt_inventory->close();
    } else {
        echo "Failed to add product. Error: " . $stmt->error;
    }

    $stmt->close();

    // After adding, redirect back to inventory
    header("Location: inventory.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product</title>
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
            width: calc(65% - 240px); /* Adjust width */
            margin-top: 200px;
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

        form {
            max-width: 600px;
            margin: auto;
        }

        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        input, button {
            padding: 10px;
            font-size: 16px;
        }

        button {
            background-color: #D22B2B;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #C12B2B;
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
    </style>
</head>
<body>

    <header>
        <h1>Add New Product</h1>
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
        <form method="POST" action="add_product.php" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required><br>

            <label for="stock">Stock Quantity:</label>
            <input type="number" id="stock" name="stock" required><br>

            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image" accept="image/*"><br>

            <button type="submit">Add Product</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> KenShane Store. All rights reserved.</p>
    </div>

</body>
</html>

<?php
$conn->close();
?>
