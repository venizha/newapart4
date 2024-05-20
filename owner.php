<?php
session_start();

if (!isset($_SESSION['user_name'])) {
  header("Location: resident.php");
  exit();
}

$userName = $_SESSION['user_name'];
$dbhost = 'localhost';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpass = 'Keerthi23';

$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
if (!$conn) {
  die("Connection failed. Error: " . pg_last_error());
}

$query = "SELECT oid, user_name, email, phone_no, move_in_date, flat_no FROM owners WHERE user_name = $1";
$result = pg_query_params($conn, $query, array($userName));

if (!$result) {
  die("Query failed. Error: " . pg_last_error($conn));
}

$ownerDetails = pg_fetch_assoc($result); 
$queryWaterBill = "SELECT amount, payment_status FROM water_bills WHERE owner_id = $1";
$resultWaterBill = pg_query_params($conn, $queryWaterBill, array($ownerDetails['oid']));
$waterBillDetails = pg_fetch_assoc($resultWaterBill);

// Set default values if water bill details not found
$waterBillAmount = 0;
$waterBillPaymentStatus = 0;
if ($waterBillDetails) {
  $waterBillAmount = $waterBillDetails['amount'];
  $waterBillPaymentStatus = $waterBillDetails['payment_status'];
}

// Query to fetch electricity bill details
$queryElectricityBill = "SELECT amount, payment_status FROM electricity_bills WHERE owner_id = $1";
$resultElectricityBill = pg_query_params($conn, $queryElectricityBill, array($ownerDetails['oid']));
$electricityBillDetails = pg_fetch_assoc($resultElectricityBill);

// Set default values if electricity bill details not found
$electricityBillAmount = 0;
$electricityBillPaymentStatus = 0;
if ($electricityBillDetails) {
  $electricityBillAmount = $electricityBillDetails['amount'];
  $electricityBillPaymentStatus = $electricityBillDetails['payment_status'];
}

// Query to fetch maintenance fees details
$queryMaintenanceFees = "SELECT amount, payment_status FROM maintenance_fees WHERE owner_id = $1";
$resultMaintenanceFees = pg_query_params($conn, $queryMaintenanceFees, array($ownerDetails['oid']));
$maintenanceFeesDetails = pg_fetch_assoc($resultMaintenanceFees);

// Set default values if maintenance fees details not found
$maintenanceFeesAmount = 0;
$maintenanceFeesPaymentStatus = 0;
if ($maintenanceFeesDetails) {
  $maintenanceFeesAmount = $maintenanceFeesDetails['amount'];
  $maintenanceFeesPaymentStatus = $maintenanceFeesDetails['payment_status'];
}

// Calculate total bill amount including rent
$totalBillAmount = $waterBillAmount + $electricityBillAmount + $maintenanceFeesAmount;
// Calculate total amount of paid bills
$totalPaidAmount = ($waterBillPaymentStatus == 1 ? $waterBillAmount : 0) +
                   ($electricityBillPaymentStatus == 1 ? $electricityBillAmount : 0) +
                   ($maintenanceFeesPaymentStatus == 1 ? $maintenanceFeesAmount : 0);

// Calculate balance of unpaid bills
$balance = $totalBillAmount - $totalPaidAmount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Owner Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #f0f0f0;
    }
    .owner-details {
      max-width: 800px;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    .total {
      margin-top: 20px;
      font-weight: bold;
    }
    .balance {
      color: <?php echo ($balance >= 0) ? 'green' : 'red'; ?>;
    }
    .pay-button {
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .pay-button:hover {
      background-color: #0056b3;
    }

    .back-button {
      display: inline-block;
      background-color: #007bff;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      margin-bottom:10px;
      cursor: pointer;
      float: right; /* Aligns button to the right */
    }

    .back-button:hover {
      background-color: #0056b3;
    }

  </style>
</head>
<body>
  <div class="owner-details">
    <h2>Owner Details</h2>
    <h1>Welcome <?php echo $ownerDetails['user_name']; ?>!!</h1>
    <table>
      <tr>
        <th>Name</th>
        <td><?php echo $ownerDetails['user_name']; ?></td>
      </tr>
      <tr>
        <th>ID</th>
        <td><?php echo $ownerDetails['oid']; ?></td>
      </tr>
      <tr>
        <th>Flat Number</th>
        <td><?php echo $ownerDetails['flat_no']; ?></td>
      </tr>
      <tr>
        <th>Phone Number</th>
        <td><?php echo $ownerDetails['phone_no']; ?></td>
      </tr>
      <tr>
        <th>Move-in Date</th>
        <td><?php echo $ownerDetails['move_in_date']; ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?php echo $ownerDetails['email']; ?></td>
      </tr>
    </table>
    <br><br>
    <div>
      <h2>Bills Information</h2>
      <table>
        <tr>
          <th>Bill Type</th>
          <th>Amount</th>
          <th>Payment Status</th>
        </tr>
        <tr>
          <td>Water Bill</td>
          <td><?php echo $waterBillAmount; ?></td>
          <td style="color: <?php echo ($waterBillPaymentStatus == 1) ? 'green' : 'red'; ?>">
            <?php echo ($waterBillPaymentStatus == 1) ? 'Paid' : 'Unpaid'; ?>
          </td>
        </tr>
        <tr>
          <td>Electricity Bill</td>
          <td><?php echo $electricityBillAmount; ?></td>
          <td style="color: <?php echo ($electricityBillPaymentStatus == 1) ? 'green' : 'red'; ?>">
            <?php echo ($electricityBillPaymentStatus == 1) ? 'Paid' : 'Unpaid'; ?>
          </td>
        </tr>
        <tr>
          <td>Maintenance Fees</td>
          <td><?php echo $maintenanceFeesAmount; ?></td>
          <td style="color: <?php echo ($maintenanceFeesPaymentStatus == 1) ? 'green' : 'red'; ?>">
            <?php echo ($maintenanceFeesPaymentStatus == 1) ? 'Paid' : 'Unpaid'; ?>
          </td>
        </tr>
      </table>
      <div class="total">
        <p>Total Bill Amount: <?php echo $totalBillAmount; ?></p>
        <p>Balance of Unpaid Bills: <span class="balance"><?php echo $balance; ?></span></p>
        <form action="payment.php" method="post">
          <input type="hidden" name="owner_id" value="<?php echo $tenantDetails['oid']; ?>">
          <input type="submit" value="Pay" class="pay-button">
          <a href="resident.php"><button type="button" class="back-button">Go to Previous</button></a>
        </form>
  </div>
    </div>
  </div>
</body>
</html>