<?php

// Database connection variables
include ('21check.php');
include ('db.php');
include ('visitors.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tavern of Reflection - Interested and Curious</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body style="background-color: #050607; color: white; text-align: center;">  <script>
    function getDirections() {
      // Check if geolocation is available in the browser
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          // Get user's current location (latitude and longitude)
          const latitude = position.coords.latitude;
          const longitude = position.coords.longitude;
          
          // Define the destination address
          const destination = 'Tavern of Reflection, 123 Mondo Burger Drive, Earth';

          // Create the Google Maps URL with current location and destination
          const googleMapsLink = `https://www.google.com/maps/dir/?api=1&origin=${latitude},${longitude}&destination=${encodeURIComponent(destination)}`;
          
          // Open the link in a new tab
          window.open(googleMapsLink, '_blank');
        }, function(error) {
          // If the user denies location access or there's an error
          alert('Unable to retrieve your location. Please enable location services.');
        });
      } else {
        // If geolocation is not supported by the browser
        alert('Geolocation is not supported by this browser.');
      }
    }
  </script>


<center><img src="Logo.png" style="width:60%;" />  </center>

<h2  >Serving both Interested and Curious  </h2>

<h3><?php
include('hours.php');
?>
</h3>
<br><br><br><br><br><br><br>
<h2><center><a href="/Wholesale.html">WholeSale Menu</a></center></h2>
</body>

<footer>
Tavern of Reflection, 123 Mondo Burger Drive<br> 
Earth<br>
<a href="https://www.google.com/maps/dir/?api=1&origin=Current+Location&destination=Tavern+Of+Reflection,+123+Mondo+Burger+Drive,+Earth" target="_blank" onclick="getDirections(event)">
Get Directions</a>

<br><a href="tel:+1555-555-5555"><br> 555-555-5555</h3></a>


<br>


<a href="https://www.instagram.com/TavernOfReflectionz/" target="_blank">Find us on Instagram!</a>

      <p>&copy; 2023 Tavern Of Reflection. All Rights Reserved.</p>

<a href="https://www.instagram.com/TavernOfReflectionz/" target="_blank">Admin Panel</a>
</footer>

</html>