<?php
require 'config.php'; // Include your database connection file
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Handle search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch products from the database with search functionality
$sql = "SELECT * FROM products WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%" . $searchTerm . "%";
$stmt->bind_param("s", $searchParam);
$stmt->execute();
$result = $stmt->get_result(); // Execute the query and get the result

// Handle add-to-cart functionality
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];

    // Add product to the cart
    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
    if ($stmt->execute()) {
        $message = "Product added to cart!";
    } else {
        $message = "Failed to add product to cart.";
    }

    $_SESSION['notification'] = "Product added to your cart!";
    
    // Redirect to the same page to avoid re-adding the product if refreshed
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store</title>
    <style>
        /* Basic page styling */
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

        /* Main content styling */
        .container {
            margin-left: 240px; /* Leave space for the sidebar */
            padding: 20px;
            width: calc(100% - 240px); /* Adjust width */
            margin-top: 80px;
        }
        .message { 
            color: #5a5a5a; 
            margin: 15px 0; 
            font-size: 16px; 
        }

        /* Styling for product grid */
        .product-grid { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
            justify-content: center; 
        }
        .product-item { 
            width: 220px; 
            padding: 20px; 
            border: 1px solid #dddddd; 
            text-align: center; 
            background-color: #ffffff; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
            transition: transform 0.2s; 
        }
        .product-item:hover { 
            transform: translateY(-3px); 
        }
        .product-item img { 
            width: 100%; 
            height: 150px; 
            object-fit: cover; 
            border-radius: 8px; 
        }
        .product-item h3 { 
            font-size: 18px; 
            margin: 10px 0; 
            color: #4a4a4a; 
        }
        .product-item p { 
            font-size: 16px; 
            color: #666666; 
            margin-bottom: 15px; 
        }
        .product-item form button { 
            background-color: #4CBB17; 
            color: #ffffff; 
            padding: 10px 15px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 14px; 
            transition: background-color 0.3s; 
        }
        .product-item form button:hover { 
            background-color: #333333; 
        }

        .notification {
            display: none; /* Hidden by default */
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4caf50; /* Success color */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            animation: fadeOut 0.5s ease forwards 3s; /* Fade out after 3s */
        }

        /* Keyframes for fade out */
        @keyframes fadeOut {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }

    </style>
   <script>
    
    // Show notification on adding product to cart
    function showNotification(message) {
        console.log("Notification triggered: ", message); // Debugging log
        const notification = document.getElementById('notification');
        notification.innerText = message;
        notification.style.display = 'block';
        notification.style.opacity = 1; // Make it visible
        setTimeout(() => {
            notification.style.opacity = 0; // Fade out
            setTimeout(() => {
                notification.style.display = 'none'; // Hide after fading out
            }, 500);
        }, 3000); // Hide after 3 seconds
    }

    window.addEventListener('DOMContentLoaded', () => {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000); // Hide after 3 seconds
        }
    });
</script>

</head>
<body>
<!-- HTML -->
<header style="display: flex; justify-content: space-between; align-items: center; padding: 10px;">
    <div class="logo">KenShane Store</div>
    <form action="index.php" method="GET" style="margin-top: 10px; display: flex; align-items: center; margin-right: 30px;">
        <input 
            type="text" 
            name="search" 
            id="searchInput" 
            placeholder="Search for products..." 
            value="<?php echo htmlspecialchars($searchTerm); ?>" 
            required 
            style="padding: 8px; border-radius: 20px; border: 1px solid #ccc; width: 200px; margin-right: 10px;"
        >
        <button type="submit" style="padding: 8px 12px; border-radius: 20px; background-color: #007bff; color: white; border: none; cursor: pointer;">Search</button>
    </form>
</header>

    <?php if (isset($_SESSION['notification'])): ?>
        <div class="notification" id="notification">
            <?php echo $_SESSION['notification']; ?>
        </div>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>

    <div class="sidebar">
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="customer_settings.php">Profile</a>
        <a href="orders.php">Orders</a>      
        <a href="logout.php">Logout</a>
    </div>

    <div class="container"> 
        <?php if ($message): ?>
            <script>showNotification("<?php echo htmlspecialchars($message); ?>");</script> <!-- Display notification -->
        <?php endif; ?>
        
        <div class="product-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>₱<?php echo number_format($row['price'], 2); ?></p> <!-- Display price with currency -->
                    <form action="index.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Notification element -->
    <div id="notification" class="notification"></div>

    <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        if (this.value.trim() === '') {
            window.location.href = 'index.php'; // Reloads the page to clear the search filter
        }
    });
</script>

</body>
</html>

