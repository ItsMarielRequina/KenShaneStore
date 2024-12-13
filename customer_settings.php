<?php
session_start();
require 'config.php'; // Ensure you include your config file

// Assuming you have user authentication and user ID stored in the session
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        exit;
    }

    // Save the uploaded image
    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        // Prepare SQL statement to update user settings
        $stmt = $conn->prepare("UPDATE users SET username=?, birthday=?, address=?, contact_number=?, profile_image=? WHERE id=?");
        $stmt->bind_param("sssssi", $username, $birthday, $address, $contact_number, $target_file, $user_id);

        if ($stmt->execute()) {
            echo "Account settings updated successfully.";
        } else {
            echo "Error updating account settings: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Fetch current user settings
$stmt = $conn->prepare("SELECT username, birthday, address, contact_number, profile_image FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_username, $current_birthday, $current_address, $current_contact_number, $current_profile_image);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - KenShane Store</title>
    <style>
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
        .container {
            margin-left: 220px;
            padding: 100px 20px 20px;
            width: 100%;
        }
        h1 {
            color: white;
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .image-container {
    display: flex;
    align-items: center; /* Center vertically within the container */
    justify-content: center; /* Center horizontally within the container */
    margin: 60px 0 40px; /* Adds space above and below */
    height: 100px; /* Set height to ensure centering */
}

.profile-image {
    width: 150px; /* Larger width */
    height: 150px; /* Larger height */
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Optional: Adds a shadow effect */
}

    </style>
</head>
<body>
    <header>
        <h1>KenShane Store - Account Settings</h1>
    </header>

    <div class="sidebar">
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="customer_settings.php">Profile</a>
        <a href="orders.php">Orders</a>      
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">

    <div class="image-container">
        <?php if ($current_profile_image): ?>
            <img src="<?php echo htmlspecialchars($current_profile_image); ?>" alt="Profile Image" class="profile-image">
        <?php endif; ?>
    </div>

        <form action="customer_settings.php" method="post" enctype="multipart/form-data">
            <label for="username">Account Name:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($current_username); ?>" required><br>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($current_birthday); ?>" required><br>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($current_address); ?></textarea><br>

            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($current_contact_number); ?>" required><br>

            <label for="profile_image">Profile Image:</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*"><br>

            <input type="submit" value="Update Settings">
        </form>
    </div>
</body>
</html>
