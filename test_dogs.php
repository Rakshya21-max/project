<?php
include 'db.php';

// Check for dogs ready for adoption
$sql = "SELECT id, picture, location, description, status FROM reports WHERE status = 'Ready for adoption'";
$result = $conn->query($sql);

echo "Dogs ready for adoption:\n";
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Location: " . $row['location'] . ", Picture: " . $row['picture'] . ", Status: " . $row['status'] . "\n";
    }
} else {
    echo "No dogs found with 'Ready for adoption' status\n";
}

// Check all statuses
$sql2 = "SELECT DISTINCT status FROM reports";
$result2 = $conn->query($sql2);

echo "\nAll statuses in database:\n";
if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        echo "Status: " . $row['status'] . "\n";
    }
} else {
    echo "No statuses found\n";
}

$conn->close();
?>