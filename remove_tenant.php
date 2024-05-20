<?php
session_start();

$dbhost = 'localhost';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpass = 'Keerthi23';

$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
if (!$conn) {
    die("Connection failed. Error: " . pg_last_error());
}

if(isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];

    $query = "DELETE FROM tenants WHERE tid = $1";
    $result = pg_query_params($conn, $query, array($tenant_id));

    if ($result) {
        $_SESSION['message'] = "Tenant removed successfully.";
    } else {
        $_SESSION['message'] = "Error removing tenant: " . pg_last_error($conn);
    }

    pg_close($conn);

    
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Tenant</title>
    <style>
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        label, input, button {
            display: block;
            margin-bottom: 10px;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Remove Tenant</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
            <label for="tenant_id">Enter Tenant ID:</label>
            <input type="text" id="tenant_id" name="tenant_id" placeholder="Tenant ID" required>
            <button type="submit">Remove Tenant</button>
            <a href="admin_dashboard.php"><button type="button" class="back-button"> Prev Page<<</button>

        </form>

    </div>
</body>
</html>