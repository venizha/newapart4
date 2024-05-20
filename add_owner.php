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


$user_id = $_POST['oid']; 
$owner_name = $_POST['user_name'];
$email = $_POST['email'];
$phone_no = $_POST['phone_no'];
$move_in_date = $_POST['move_in_date'];
$flat_no = $_POST['flat_no'];
$password = $_POST['password'];

$query = "INSERT INTO owners (oid, user_name, email, phone_no, move_in_date, flat_no, password) VALUES ($1, $2, $3, $4, $5, $6, $7)";
$result = pg_query_params($conn, $query, array($user_id, $owner_name, $email, $phone_no, $move_in_date, $flat_no, $password));

if ($result) {
    $_SESSION['message'] = "Owner added successfully.";
} else {
    $_SESSION['message'] = "Error adding tenant: " . pg_last_error($conn);
}


pg_close($conn);


header("Location: admin_owner.php");
exit();
?>
