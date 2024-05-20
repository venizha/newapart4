<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Bill Management for Owners</title>
    <style>
      .center-box {
        width: 400px;
        margin: 0 auto;
        padding: 60px;
        background-color: #f0f0f0;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 5%;
    }

    .center-box h2 {
        text-align: center;
        color: #333;
    }

    .center-box form label {
        display: block;
        margin-bottom: 10px;
        color: #555;
    }

    .center-box form input[type="text"],
    .center-box form input[type="number"],
    .center-box form select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .center-box form input[type="submit"] {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .center-box form input[type="submit"]:hover {
        background-color: #0056b3;
    }

    .back-button {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        margin-bottom: 10px;
        cursor: pointer;
        float: right;
    }

    .back-button:hover {
        background-color: #0056b3;
    }

    </style>
</head>
<body>
<div class="center-box">
    <h2>Supervisor Bill Management for Owners</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="ownerId">Owner ID:</label>
        <input type="text" id="ownerId" name="ownerId" required><br><br>

        <label for="billType">Select Bill Type:</label>
        <select id="billType" name="billType" required>
            <option value="water">Water Bill</option>
            <option value="electricity">Electricity Bill</option>
            <option value="maintenance">Maintenance fees</option>
        </select><br><br>

        <label for="newAmount">New Amount (in Rs.):</label>
        <input type="number" id="newAmount" name="newAmount" min="0" required><br><br>

        <input type="submit" name="submit" value="Update Bill"><br><br>
        <a href="supervisor_dashboard.php"><button type="button" class="back-button">Go to Previous</button></a>
    </form>
</div>

<?php
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ownerId = isset($_POST['ownerId']) ? htmlspecialchars($_POST['ownerId']) : null;
    $billType = isset($_POST['billType']) ? htmlspecialchars($_POST['billType']) : null;
    $newAmount = isset($_POST['newAmount']) ? intval($_POST['newAmount']) : 0;

    if (!$ownerId || !$billType || !$newAmount) {
        die("Invalid input. Please fill all required fields.");
    }

    // Database connection parameters
    $dbhost = 'localhost';
    $dbname = 'postgres';
    $dbuser = 'postgres';
    $dbpass = 'Keerthi23';

    // Create a database connection
    $conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
    if (!$conn) {
        die("Connection failed. Error: " . pg_last_error());
    }

    // Determine the table name based on the selected bill type
    switch ($billType) {
        case 'water':
        case 'electricity':
            $tableName = ($billType === 'water') ? 'water_bills' : 'electricity_bills';
            break;
        case 'maintenance':
            $tableName = 'maintenance_fees';
            break;
        default:
            die("Invalid bill type.");
    }

    // Update the bill amount for the specified owner and bill type
    $query = "UPDATE $tableName SET amount = $1 WHERE owner_id = $2";
    $result = pg_query_params($conn, $query, array($newAmount, $ownerId));

    if ($newAmount == 0) {
        $paymentStatus = 'Paid';
    } else {
        $paymentStatus = 'Not Paid';
    }
    
    // Execute SQL update for amount and payment status
    $query = "UPDATE $tableName SET amount = $1, payment_status = $2 WHERE owner_id = $3";
    $result = pg_query_params($conn, $query, array($newAmount, $paymentStatus, $ownerId));
    
    if ($result) {
        echo "<script>alert('Bill updated successfully.');</script>";
    } else {
        echo "<script>alert('Failed to update bill: " . pg_last_error($conn) . "');</script>";
    }
    

    // Close database connection
    pg_close($conn);
}
?>
</body>
</html>
