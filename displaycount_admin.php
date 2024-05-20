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

$occupied_tenants_query = "SELECT COUNT(*) AS occupied_count FROM tenants WHERE tid IS NOT NULL";
$occupied_tenants_result = pg_query($conn, $occupied_tenants_query);
if (!$occupied_tenants_result) {
  die("Error counting occupied tenants: " . pg_last_error($conn));
}
$occupied_tenants_count = pg_fetch_assoc($occupied_tenants_result)['occupied_count'];

$occupied_owners_query = "SELECT COUNT(*) AS occupied_count FROM owners WHERE oid IS NOT NULL";
$occupied_owners_result = pg_query($conn, $occupied_owners_query);
if (!$occupied_owners_result) {
  die("Error counting occupied owners: " . pg_last_error($conn));
}
$occupied_owners_count = pg_fetch_assoc($occupied_owners_result)['occupied_count'];

$total_occupancy = $occupied_tenants_count + $occupied_owners_count;

pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Occupancy</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      padding-left:30%;
    }

    h1 {
      text-align: center;
    }

    table {
      border-collapse: collapse;
      width: 50%;
      
    }

    th, td {
      padding: 20px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
      font-weight: bold;
    }
    h1{
        text-align:left;
        padding:20px;
        margin-left:10%;
    }
    .back-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
  </style>
</head>
<body>

<h1>OCCUPANCY</h1>

<table>
  <tr>
    <th>Occupancy</th>
    <th>Count</th>
  </tr>
  <tr>
    <td>Tenants</td>
    <td><?= $occupied_tenants_count ?></td>
  </tr>
  <tr>
    <td>Owners</td>
    <td><?= $occupied_owners_count ?></td>
  </tr>
  <tr>
    <th>Total</th>
    <th><?= $total_occupancy ?></th>
  </tr>
</table>
<a href="admin_dashboard.php"><button type="button" class="back-button"> Prev Page</button></a>
       
</body>
</html>
