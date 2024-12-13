<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empty Cart - KenShane Store</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #D22B2B;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }
        .empty-cart-container {
            text-align: center;
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent background */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
        }
        .empty-cart-container h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #f8d7da;
        }
        .empty-cart-container p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .shop-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .shop-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="empty-cart-container">
        <h2>Your cart is empty!</h2>
        <p>It seems like there are no items in your cart. Start shopping now and fill it with great deals!</p>
        <a href="index.php" class="shop-button">Go to Shop</a>
    </div>
</body>
</html>
