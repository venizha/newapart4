<?php
session_start();

if (isset($_SESSION['message'])) {
    echo "<script>alert('{$_SESSION['message']}');</script>";
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new Owner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background:url('admin2.jpg');
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        input[type="text"], input[type="password"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"], button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #45a049; 
        }
        h1, h2 {
            text-align: center;
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
        h2{
            color:white;
        }
    </style>
</head>
<body>
    <h2>Add New Owner</h2>
    <form action="add_owner.php" method="post">
        <input type="text" name="user_name" placeholder="Owner Name" required><br>
        <input type="number" name="oid" placeholder="ID" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="phone_no" placeholder="Phone Number" required><br>
        <input type="text" name="move_in_date" placeholder="Move In Date" required><br>
        <input type="text" name="flat_no" placeholder="Flat Number" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Add Owner">
        <a href="admin_dashboard.php"><button type="button" class="back-button"> Prev Page</button></a>
    </form>
</body>
</html>
