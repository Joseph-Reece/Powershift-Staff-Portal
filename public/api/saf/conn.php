<?php
$servername = "localhost";
$username = "cryptohe_web";
$password = "Q8A[qoc=KqR7AdpP!D";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>