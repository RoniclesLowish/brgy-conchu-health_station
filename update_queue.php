<?php
header('Content-Type: application/json');

include('dbConnection.php');
// Mark current active as completed
$complete_sql = "UPDATE queue_table SET status='completed' WHERE status='active'";
$conn->query($complete_sql);

// Move next waiting user to active
$next_sql = "UPDATE queue_table SET status='active' WHERE status='waiting' ORDER BY id ASC LIMIT 1";
$conn->query($next_sql);

echo json_encode(['success' => true]);

$conn->close();
?>
