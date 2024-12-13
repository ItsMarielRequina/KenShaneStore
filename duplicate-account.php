<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duplicate Account - KenShane Store</title>
    <style>
        /* General body styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #D22B2B;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Error container styling */
        .error-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }

        /* Heading styles */
        .error-container h2 {
            font-size: 26px;
            color: #721c24;
            margin-bottom: 15px;
        }

        /* Paragraph styles */
        .error-container p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        /* Button styles */
        .error-container .action-button {
            display: inline-block;
            padding: 10px 25px;
            font-size: 16px;
            background-color: #0056b3;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .error-container .action-button:hover {
            background-color: #003d80;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h2>Duplicate Account Found!</h2>
        <p>The email address you provided is already associated with an existing account. Please use a different email address or log in to your existing account.</p>
        <a href="login.php" class="action-button">Log In</a>
    </div>
</body>
</html>
