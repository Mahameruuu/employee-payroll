<?php
header('Content-Type: application/json');
include '../../config/database.php';

$result = mysqli_query($conn, "SELECT * FROM agenda");
$events = [];

while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start_date'],
        'end' => $row['end_date'],
        'description' => $row['description']
    ];
}

echo json_encode($events);
?>
