<?php
include('visitors.php');
include('db.php'); // Include the database connection

include('secure.php');
// Fetch current business hours from the database
$sql = "SELECT * FROM hours ORDER BY FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Loop through the submitted form data and update the hours
    foreach ($_POST as $day => $times) {
        list($open, $close) = explode('-', $times);
        $open = date('H:i:s', strtotime($open));
        $close = date('H:i:s', strtotime($close));

        // Prepare the SQL query to update the hours
        $stmt = $conn->prepare("UPDATE hours SET open_time = ?, close_time = ? WHERE day = ?");
        $stmt->bind_param("sss", $open, $close, $day);
        $stmt->execute();
    }

    // Redirect back to the hours page after the update
    header("Location: hours.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Business Hours</title>
    <!-- Link to external stylesheet -->
    <link rel="stylesheet" href="styles.css">
    <style>
        input[type="text"] {
            width: 200px;
        }

        input[type="submit"] {
            width: 200px;
            background-color: green;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>
    <h1>Update Business Hours</h1>
    <form action="update_hours.php" method="POST">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <label for="<?php echo $row['day']; ?>"><?php echo ucfirst($row['day']); ?>:</label>
                <input type="text" id="<?php echo $row['day']; ?>" name="<?php echo $row['day']; ?>" 
                    value="<?php echo $row['open_time'] ? date('G:i', strtotime($row['open_time'])) : 'Closed'; ?>-<?php echo $row['close_time'] ? date('G:i', strtotime($row['close_time'])) : 'Closed'; ?>" 
                    placeholder="H:MM-H:MM" required><br><br>
            <?php endwhile; ?>
        <?php endif; ?>
        
        <input type="submit" value="Update Hours">
    </form>

    <p><a href="hours.php">Back to Business Hours</a></p>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
