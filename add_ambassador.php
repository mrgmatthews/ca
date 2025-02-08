<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection credentials
$servername = "sql206.infinityfree.com:3306";
$username = "if0_38269952";
$password = "5KdwMdmbu9xzK";
$dbname = "if0_38269952_ca"; // Replace with your database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$ambassador_name = '';
$city = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $ambassador_name = $conn->real_escape_string(trim($_POST['name']));
    $city = $conn->real_escape_string(trim($_POST['city']));

    // Validate required fields
    if (empty($ambassador_name) || empty($city)) {
        echo "All fields are required!";
    } else {
        // Insert ambassador details into the database
        $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

        // SQL query to insert ambassador
        $sql = "INSERT INTO ambassadors (name, city, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $ambassador_name, $city, $user_id);

        if ($stmt->execute()) {
            echo "Ambassador added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Ambassador</title>
</head>
<body>
    <h2>Add a New Ambassador</h2>
    <form method="POST" action="add_ambassador.php">
        <label for="name">Ambassador Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($ambassador_name) ?>" required><br><br>

        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($city) ?>" required><br><br>

        <button type="submit">Add Ambassador</button>
    </form>
</body>
</html>
