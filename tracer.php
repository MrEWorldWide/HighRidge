<?php 
include('secure.php');

// Include necessary files
include('visitors.php');
include('cookie.php');
$cookie_name = 'Admin'; // Name of the cookie you want to retrieve
$cookie_value = getCookieByName($cookie_name);
// Check if the user has a valid cookie
if ($cookie_value == false) {
    include('secure.php');
    die();
} else {
    createCookie('Admin', 'Admin');
}

// Include Database connection file 
include('db.php');

// Get the IP address from the URL parameter
$ipAddress = isset($_GET['IPaddress']) ? $_GET['IPaddress'] : '';

// Initialize variables
$visitStats = [];
$totalVisits = 0;
$webpageViews = 0;
$dailyVisits = [];
$weeklyVisits = [];
$monthlyVisits = [];
$quarterlyVisits = [];
$yearlyVisits = [];
$visitorRows = [];  // Array to store visitor rows

// If an IP address is provided, fetch and process the visitor data
if (!empty($ipAddress)) {
    // Fetch all data for the given IP address, including the previous page
    $dataQuery = "SELECT * FROM visitors WHERE ip_address = '$ipAddress' ORDER BY visit_time DESC";
    $dataResult = $conn->query($dataQuery);

    if ($dataResult->num_rows > 0) {
        // Fetch the rows and process them
        while ($row = $dataResult->fetch_assoc()) {
            $visitorRows[] = $row;  // Store rows in the array

            // Increment total visits and webpage views
            $totalVisits++;
            $webpageViews++; // Assuming each record represents a webpage view

            // Get the visit time for statistics
            $visitTime = strtotime($row['visit_time']);
            $visitDate = date('Y-m-d', $visitTime);  // Daily stats
            $visitWeek = date('W', $visitTime);  // Week number
            $visitMonth = date('m', $visitTime); // Month number
            $visitQuarter = ceil(date('n', $visitTime) / 3); // Quarter number (1-4)
            $visitYear = date('Y', $visitTime); // Year

            // Store visits by day
            if (!isset($dailyVisits[$visitDate])) {
                $dailyVisits[$visitDate] = 0;
            }
            $dailyVisits[$visitDate]++;

            // Store visits by week
            if (!isset($weeklyVisits[$visitWeek])) {
                $weeklyVisits[$visitWeek] = 0;
            }
            $weeklyVisits[$visitWeek]++;

            // Store visits by month
            if (!isset($monthlyVisits[$visitMonth])) {
                $monthlyVisits[$visitMonth] = 0;
            }
            $monthlyVisits[$visitMonth]++;

            // Store visits by quarter
            if (!isset($quarterlyVisits[$visitQuarter])) {
                $quarterlyVisits[$visitQuarter] = 0;
            }
            $quarterlyVisits[$visitQuarter]++;

            // Store visits by year
            if (!isset($yearlyVisits[$visitYear])) {
                $yearlyVisits[$visitYear] = 0;
            }
            $yearlyVisits[$visitYear]++;
        }
    }

    // Sort all visits by time period
    ksort($dailyVisits);
    ksort($weeklyVisits);
    ksort($monthlyVisits);
    ksort($quarterlyVisits);
    ksort($yearlyVisits);
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Details for IP: <?php echo htmlspecialchars($ipAddress); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        input, textarea, select { padding: 10px; margin: 5px 0; font-size: 16px; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; font-size: 16px; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .message { margin-top: 20px; padding: 10px; background-color: #f4f4f4; border: 1px solid #ddd; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        select {
            max-width: 300px;
            width: auto;
            display: inline-block;
        }
        textarea {
            width: 288px;
            height: 384px;
            resize: both;
        }
    </style>
</head>
<body>
  <h2>Visitor Details for IP: <?php echo htmlspecialchars($ipAddress); ?></h2>
<!-- IP Whois Data -->
        <h4>IP Whois Data</h4>
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include the get_ipwhois.php file
                include('whois.php');
                // Call the function to get the Whois data
                $ipData = get_ipwhois_data($ipAddress);

                // Check if the result is an array (successful data retrieval)
                if (is_array($ipData)) {
                    // Loop through the result array and print the data
                    foreach ($ipData as $key => $value) {
                        echo "<tr><td>$key</td><td>$value</td></tr>";
                    }
                } else {
                    // If the result is a string, it indicates an error
                    echo "<tr><td colspan='2'>$ipData</td></tr>";
                }
                ?>
            </tbody>
        </table>


  

    <!-- Visitor Statistics -->
    <h3>Visitor Statistics</h3>
    <table>
        <thead>
            <tr>
                <th>Statistic</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Visits</td>
                <td><?php echo $totalVisits; ?></td>
            </tr>
            <tr>
                <td>Total Webpage Views</td>
                <td><?php echo $webpageViews; ?></td>
            </tr>
            <tr>
                <td>Visits by Day</td>
                <td>
                    <ul>
                        <?php foreach ($dailyVisits as $date => $visits): ?>
                            <li>Daily Visits on <?php echo $date; ?>: <?php echo $visits; ?> visit(s)</li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Visits by Week</td>
                <td>
                    <ul>
                        <?php foreach ($weeklyVisits as $week => $visits): ?>
                            <li>Weekly Visits (Week <?php echo $week; ?>): <?php echo $visits; ?> visit(s)</li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Visits by Month</td>
                <td>
                    <ul>
                        <?php foreach ($monthlyVisits as $month => $visits): ?>
                            <li>Monthly Visits (Month <?php echo $month; ?>): <?php echo $visits; ?> visit(s)</li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Visits by Quarter</td>
                <td>
                    <ul>
                        <?php foreach ($quarterlyVisits as $quarter => $visits): ?>
                            <li>Quarter <?php echo $quarter; ?> Visits: <?php echo $visits; ?> visit(s)</li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Visits by Year</td>
                <td>
                    <ul>
                        <?php foreach ($yearlyVisits as $year => $visits): ?>
                            <li>Yearly Visits (Year <?php echo $year; ?>): <?php echo $visits; ?> visit(s)</li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>



    <h4>Visit Details</h4>
    <table>
        <thead>
            <tr>
                <th>Visit Time</th>
                <th>Visit Date</th>
                <th>Previous Page</th> <!-- Changed column name to "Previous Page" -->
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($visitorRows)): ?>
                <?php foreach ($visitorRows as $row): ?>
                    <tr>
                        <td><?php echo date('h:i:s A', strtotime($row['visit_time'])); ?></td>
                        <td><?php echo date('m/d/Y', strtotime($row['visit_time'])); ?></td>
                        <td><?php echo htmlspecialchars($row['previous_page']); ?></td> <!-- Display the previous_page field -->
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No visit data available for this IP address.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

 




</body>
</html>
