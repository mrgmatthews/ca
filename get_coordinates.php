<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Example MySQL database connection (update with your own credentials)
$host = $_ENV['DB_URL'];;
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$database = $_ENV['DB_NAME'];

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if city parameter is provided
if (isset($_GET['city']) && !empty($_GET['city'])) {
    $city = urlencode($_GET['city']); // URL encode the city name

    // Check if coordinates are already in the database
    $sql = "SELECT latitude, longitude FROM city_coordinates WHERE city = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $city);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Coordinates found in database
        $stmt->bind_result($latitude, $longitude);
        $stmt->fetch();
        echo json_encode([
            'lat' => $latitude,
            'lon' => $longitude
        ]);
    } else {
        // Coordinates not found, call the Nominatim API
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$city},UK";
        $response = @file_get_contents($url);

        if ($response === FALSE) {
            die("Error fetching coordinates from Nominatim API.");
        }

        $data = json_decode($response, true);
        
        if (!$data) {
            die("Invalid response from Nominatim API.");
        }

        if (isset($data[0])) {
            // Extract latitude and longitude from API response
            $latitude = $data[0]['lat'];
            $longitude = $data[0]['lon'];

            // Save the coordinates to the database
            $insert_sql = "INSERT INTO city_coordinates (city, latitude, longitude) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sss", $city, $latitude, $longitude);
            $insert_stmt->execute();

            // Return the coordinates
            echo json_encode([
                'lat' => $latitude,
                'lon' => $longitude
            ]);
        } else {
            // No coordinates found, return an error
            echo json_encode(['error' => 'Coordinates not found']);
        }
    }

    $stmt->close();
} else {
    // City parameter is missing
    echo json_encode(['error' => 'No city provided']);
}

// Close the database connection
$conn->close();
?>