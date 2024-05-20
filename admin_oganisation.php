
<?php
session_start();
$org_id = isset($_SESSION['org_id']) ? $_SESSION['org_id'] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <!-- Your CSS and JS links -->
</head>
<body>
    <header>
        <!-- Your header content -->
    </header>
    <div class="container">
        <div class="events" id="eventsContainer">
            <!-- Your event display code -->
        </div>
        <div class="upload-form">
            <h2>Add New Organisation</h2>
            <form id="orgForm" action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="org_name" placeholder="Organisation Name" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <!-- Add more fields if needed -->
                <input type="submit" value="Add Organisation">
            </form>
            <?php
            // PostgreSQL connection parameters
            $dbhost = 'localhost';
            $dbname = 'postgres';
            $dbuser = 'postgres';
            $dbpass = 'PMkiruthi';

            // Connect to PostgreSQL database
            $conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
            if (!$conn) {
                echo "Failed to connect to PostgreSQL database.";
                exit;
            }

            // Check if the form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Retrieve form data
                $org_name = $_POST['org_name'];
                $password = $_POST['password'];

                // Validate admin credentials
                // You might want to do this before inserting into the organisation table
                $query_admin = "SELECT * FROM admin WHERE a_name = 'admin' AND a_password = '$password'";
                $result_admin = pg_query($conn, $query_admin);
                if (!$result_admin || pg_num_rows($result_admin) == 0) {
                    echo "Error: Invalid admin credentials.";
                    exit;
                }

                // Insert organization details into organisation table
                $query = "INSERT INTO organisation(org_name, password) VALUES ('$org_name', '$password')";
                $result = pg_query($conn, $query);

                if (!$result) {
                    // Display error message if insertion fails
                    echo "Error: Unable to add organization. Please try again.";
                    exit;
                }

                // If insertion is successful, display success message
                echo "<script>alert('Organization added successfully');</script>";
            }

            // Close database connection
            pg_close($conn);
            ?>
        </div>
    </div>
</body>
</html>