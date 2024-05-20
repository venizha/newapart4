<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Bill Management for Tenants</title>
</head>
<style>
    /* Styles for the centered box and form elements */
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
<body>
<div class="center-box">
    <h2>Supervisor Bill Management For Tenants</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="tenantId">Tenant ID :</label>
        <input type="text" id="tenantId" name="tenantId" required><br><br>

        <label for="billType">Select Bill Type:</label>
        <select id="billType" name="billType" required>
            <option value="water">Water Bill</option>
            <option value="electricity">Electricity Bill</option>
            <option value="rent">Rent Amount</option>
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
    $tenantId = isset($_POST['tenantId']) ? $_POST['tenantId'] : null;
    $billType = $_POST['billType'];
    $newAmount = $_POST['newAmount'];

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

    // Determine the table name and column based on the selected bill type
    switch ($billType) {
        case 'water':
        case 'electricity':
            $tableName = ($billType === 'water') ? 'water_bills' : 'electricity_bills';
            $updateColumn = 'amount'; // Update the 'amount' column
            $idColumnName = 'tenant_id'; // ID column name for the related record
            break;
        case 'rent':
            $tableName = 'tenants';
            $updateColumn = 'rent_amt'; // Update the 'rent_amt' column
            $idColumnName = 'tid'; // ID column name for the related record
            break;
        case 'maintenance':
            $tableName = 'maintenance_fees';
            $updateColumn = 'amount'; // Update the 'amount' column
            $idColumnName = 'tenant_id'; // ID column name for the related record
            break;
        default:
            echo "Invalid bill type.";
            exit;
    }

    // Check if the tenant exists in the tenants table
    $tenantExistsQuery = "SELECT * FROM tenants WHERE tid = $1";
    $tenantExistsResult = pg_query_params($conn, $tenantExistsQuery, array($tenantId));
    $tenantExistsRowCount = pg_num_rows($tenantExistsResult);

    if ($tenantExistsRowCount > 0) {
        // Check if the bill amount exists, if not, insert it
        $checkBillQuery = "SELECT * FROM $tableName WHERE $idColumnName = $1";
        $checkBillResult = pg_query_params($conn, $checkBillQuery, array($tenantId));
        $checkBillRowCount = pg_num_rows($checkBillResult);

        if ($checkBillRowCount > 0) {
            // Update the bill amount
            $query = "UPDATE $tableName SET $updateColumn = $1 WHERE $idColumnName = $2";
            $result = pg_query_params($conn, $query, array($newAmount, $tenantId));
        } else {
            // Insert the new bill record
            $query = "INSERT INTO $tableName ($idColumnName, $updateColumn) VALUES ($1, $2)";
            $result = pg_query_params($conn, $query, array($tenantId, $newAmount));
        }

        if ($result) {
            if ($newAmount == 0) {
                $paymentStatus = 'Paid';
            } else {
                $paymentStatus = 'Not Paid';
            }

            // Update payment status in the table
            $updatePaymentStatusQuery = "UPDATE $tableName SET payment_status = $1 WHERE $idColumnName = $2";
            $resultPaymentStatus = pg_query_params($conn, $updatePaymentStatusQuery, array($paymentStatus, $tenantId));

            if ($resultPaymentStatus) {
                echo "<script>alert('Bill updated successfully.');</script>";
            } else {
                echo "<script>alert('Failed to update payment status: " . pg_last_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Failed to update or insert bill: " . pg_last_error($conn) . "');</script>";
        }
    } else {
        // Tenant ID not found
        echo "<script>alert('Tenant ID not found.');</script>";
    }

    // Close database connection
    pg_close($conn);
}
?>

</body>
</html>