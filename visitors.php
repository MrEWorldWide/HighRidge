<?php
// Database credentials
include('db.php');

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the IP address of the visitor
$ipAddress = $_SERVER['REMOTE_ADDR'];

// Get the geo-location (using ipinfo.io API for simplicity)
$geoLocation = file_get_contents("http://ipinfo.io/{$ipAddress}/json");
$geoLocation = json_decode($geoLocation, true);
$city = $geoLocation['city'] ?? 'Unknown';
$region = $geoLocation['region'] ?? 'Unknown';
$country = $geoLocation['country'] ?? 'Unknown';
$location = "{$city}, {$region}, {$country}";

// Get the time and date of the visit
$dateTime = date('Y-m-d H:i:s');

// Get the previous page URL (Referrer)
$previousPage = $_SERVER['HTTP_REFERER'] ?? 'Manual url entry';

// Get the referring domain
$refererDomain = parse_url($previousPage, PHP_URL_HOST) ?? 'No referrer';

// Get the current page URL
$currentPage = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Prepare the SQL query to insert the data
$sql = "INSERT INTO visitors (ip_address, geo_location, visit_time, previous_page, referrer_domain, current_page)
        VALUES ('$ipAddress', '$location', '$dateTime', '$previousPage', '$refererDomain', '$currentPage')";

// Execute the query and check if the data is inserted successfully
if ($conn->query($sql) === TRUE) {
    //echo "Record inserted successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>
