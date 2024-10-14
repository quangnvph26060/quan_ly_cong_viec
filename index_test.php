<?php
$servername = "localhost";
$username = "nxajrdtqhosting_kpi";
$password = "Binhan2801!@#";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>