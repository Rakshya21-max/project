<?php
session_start();
include 'db.php';

$email = $_GET['email'] ?? $_SESSION['user_email'] ?? '';

if (empty($email)) {
    echo $_GET['count_only'] ? '0' : json_encode([]);
    exit;
}

if (isset($_GET['count_only'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE email = ? AND is_read = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    echo $count;
    exit;
}

// Get notifications
$stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE email = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
?>