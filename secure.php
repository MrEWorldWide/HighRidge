<?php
// Define the correct password
$correct_password = 'BessFren'; 

// Variable to store the error message if the password is incorrect
$error_message = 'Wrong credentials.';

// Check if the form has been submitted
if (isset($_POST['password'])) {
    $entered_password = $_POST['password'];
    
    // Check if the entered password matches the correct one
    if ($entered_password === $correct_password) {
        // Password is correct, set the cookie and reload the page
        setcookie('Admin', 'Admin', time() + 86400, '/'); // 86400 = 1 day
        header("Location: " . $_SERVER['PHP_SELF']); // Reload the page to check cookie
        exit; 
    } else {
        //  Incorrect password LOG via pwfail.php 

        if (!isset($_POST['username'])) {
            $_POST['username'] = ''; // optional field for pwfail.php
        }
        @include __DIR__ . '/pwfail.php'; // silently log the failure



        $error_message = 'Incorrect password. Please try again.';
    }
}

// Check if the cookie is set, and if so, show the protected content
if (isset($_COOKIE['Admin'])) {
    // Content for authenticated users (keep your existing page content below this include)
    // (Do not echo anything here if this file is included at the top of a page)
} else {
    // Content for users who haven't entered the correct password
    echo '<h1>Password Prompt</h1>';
    echo '<form action="" method="POST">';
    echo '<label for="password">Enter Password:</label>';
    echo '<input type="password" name="password" id="password" required>';
    echo '<button type="submit">Submit</button>';
    echo '</form>';
    if (isset($error_message)) {
        echo '<p style="color: red;">' . $error_message . '</p>';
        die();
    }
}
?>
