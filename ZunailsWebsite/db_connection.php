<?php
$servername = "localhost";
$username = "root";
$password = "@jdb6642";
$database = "zunails";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>