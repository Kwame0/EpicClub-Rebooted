<?php
$servername = "localhost";
$username = "richggby_epicclubowner";
$password = "tFQHNOi51H!H";
$database = "richggby_epicclub";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>