<?php
header('Content-Type: application/json');
include('dbConnection.php');

$name = $_POST['name'] ?? '';
$priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 0;

// Get the last queue number generated today
$last_queue_sql = "SELECT queue_number FROM queue_number WHERE DATE(created_at) = CURDATE() ORDER BY id DESC LIMIT 1";
$last_queue_result = $conn->query($last_queue_sql);
$last_queue = $last_queue_result->fetch_assoc();

// Determine the new queue number
$new_queue_number = $last_queue
    ? str_pad((int)$last_queue['queue_number'] + 1, 4, '0', STR_PAD_LEFT)
    : '0001';

// Insert new queue record
$sql = "INSERT INTO queue_number (queue_number, name, priority, status, created_at)
        VALUES ('$new_queue_number', '$name', '$priority', 'waiting', NOW())";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'queue_number' => $new_queue_number]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>
