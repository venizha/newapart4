<?php
// Establish database connection
$dbhost = 'localhost';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpass = 'Keerthi23';

$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Get report month and year from POST data
$report_month = $_POST['report_month'];
$report_year = $_POST['report_year'];

// Write SQL query to calculate total rent payments for the specified month and year
$query = "SELECT source, SUM(amount) AS total_amount 
          FROM monthly_report_view 
          WHERE EXTRACT(MONTH FROM transaction_date) = $report_month 
          AND EXTRACT(YEAR FROM transaction_date) = $report_year 
          GROUP BY source";

// Execute the SQL query
$result = pg_query($conn, $query);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 600px;
    margin: 50px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

p {
    margin: 10px 0;
}

/* Remove border-top from div */
div {
    
    padding-top: 10px;
}

.footer {
    text-align: center;
    margin-top: 20px;
}

.footer a {
    text-decoration: none;
    color: #007bff;
}

.footer a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>

<div class="container">
    <h1>Monthly Report</h1>
    <p>Month: <?php echo $report_month; ?></p>
    <p>Year: <?php echo $report_year; ?></p>

    <div>
        <?php
        while ($row = pg_fetch_assoc($result)) {
            echo "<p>{$row['source']}: {$row['total_amount']}</p>";
        }
        ?>
    </div>

    <div class="footer">
        <a href="monthly_report.php">PREV PAGE</a>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection
pg_close($conn);
?>
