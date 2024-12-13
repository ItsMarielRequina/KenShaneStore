    <?php
    // Database configuration
    $host = "localhost";         // Database host
    $dbname = "kenshanestore";    // Database name
    $username = "root";  // Database username
    $password = "";  // Database password

    // Create a database connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check if the connection is successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Enable error reporting (for development only, disable in production)
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    ?>
