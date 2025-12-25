<?php
include 'db.php';

// Check if column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_photo'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "Column profile_photo added successfully";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column profile_photo already exists";
}
?>