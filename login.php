<?php
session_start();

// Database connection
$servername = $_ENV['DB_URL'];;
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Debugging: Output the username and password received from the form
    error_log("Username: $inputUsername, Password: $inputPassword");

    // SQL query to fetch the user by username
    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("Error preparing statement: " . $conn->error);
        die("Error preparing SQL query.");
    }

    // Bind the parameter
    $stmt->bind_param("s", $inputUsername);

    // Execute the statement
    $stmt->execute();

    // Check for errors in executing the query
    if ($stmt->error) {
        error_log("Error executing query: " . $stmt->error);
        die("Error executing query.");
    }

    // Store the result
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // User found, fetch the result
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();

        // Debugging: Output fetched user details
        error_log("Fetched User ID: $userId, Hashed Password: $hashedPassword");

        // Verify the password
        if (password_verify($inputPassword, $hashedPassword)) {
            $_SESSION['authenticated'] = true;
            $_SESSION['user_id'] = $userId; // Store the user ID in session
            header('Location: add_ambassador.php'); // Redirect to ambassador addition page
            exit();
        } else {
            // Invalid credentials
            error_log("Invalid password for username: $inputUsername");
            echo "Invalid credentials!";
        }
    } else {
        // User not found
        error_log("User not found: $inputUsername");
        echo "User not found!";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>