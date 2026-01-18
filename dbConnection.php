<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bis_conchu');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>