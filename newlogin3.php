<?php
// Start the session to access session variables
session_start();

// Check if the form is submitted
if (isset($_POST['login'])) {
    // Retrieve the submitted username and password from the form
    $username = $_POST['user_name'];
    $password = $_POST['password'];

    // Check if the entered credentials match the admin credentials
    if ($username === 'admin' && $password === '1234567890') {
        // Set session variables to indicate successful login
        $_SESSION['username'] = $username;

        // Redirect to the admin dashboard page
        header("Location: admin_dashboard.php");
        exit(); // Terminate script execution after redirect
    } else {
        // Incorrect credentials; handle the error (e.g., display error message)
        echo "Invalid username or password. Please try again.";
    }
} else {
    // Redirect users back to the login page if the form is not submitted
    header("Location: auth_admin.php"); // Adjust the URL as needed
    exit(); // Terminate script execution after redirect
}
?>
