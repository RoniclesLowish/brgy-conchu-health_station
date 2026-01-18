<?php
header('Content-Type: application/json');
include('dbConnection.php');

// --- Active number (today only) ---
$active_sql = "SELECT * FROM queue_number 
               WHERE status='active' 
               AND DATE(created_at) = CURDATE() 
               ORDER BY priority DESC, id ASC 
               LIMIT 1";
$active_result = $conn->query($active_sql);
$active = $active_result->fetch_assoc();
$active_id = null;
if ($active) {
    $active['priority_label'] = $active['priority'] == 1 ? 'Priority' : 'Regular';
    $active['queue_number'] = str_pad($active['queue_number'], 4, '0', STR_PAD_LEFT);
    $active_id = $active['id']; // Save active id to exclude from pending
}

// --- Pending & Waiting numbers (today only, exclude active) ---
$pending_sql = "SELECT * FROM queue_number 
                WHERE status IN ('pending','waiting') 
                AND DATE(created_at) = CURDATE()";
if ($active_id) {
    $pending_sql .= " AND id != $active_id"; // exclude active
}
$pending_sql .= " ORDER BY priority DESC, id ASC";

$pending_result = $conn->query($pending_sql);
$pending = [];
while ($row = $pending_result->fetch_assoc()) {
    $row['priority_label'] = $row['priority'] == 1 ? 'Priority' : 'Regular';
    $row['queue_number'] = str_pad($row['queue_number'], 4, '0', STR_PAD_LEFT);
    $pending[] = $row;
}

// --- Past numbers (done today only) ---
$past_sql = "SELECT * FROM queue_number 
             WHERE status='done' 
             AND DATE(updated_at) = CURDATE() 
             ORDER BY updated_at ASC";
$past_result = $conn->query($past_sql);
$past_regular = [];
$past_priority = [];
while ($row = $past_result->fetch_assoc()) {
    $num = str_pad($row['queue_number'], 4, '0', STR_PAD_LEFT);
    if ($row['priority'] == 1) $past_priority[] = $num;
    else $past_regular[] = $num;
}

// --- Return JSON ---
echo json_encode([
    'active'  => $active,
    'pending' => $pending,
    'past'    => [
        'regular_done'  => $past_regular,
        'priority_done' => $past_priority
    ]
]);

$conn->close();
?>
