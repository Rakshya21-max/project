<?php
include 'db.php';

// Add columns to reports table for dog details
$columns = [
    'name' => "VARCHAR(255) DEFAULT NULL",
    'breed' => "VARCHAR(255) DEFAULT NULL",
    'age' => "VARCHAR(50) DEFAULT NULL",
    'size' => "ENUM('small', 'medium', 'large', 'extra large') DEFAULT NULL",
    'gender' => "ENUM('male', 'female') DEFAULT NULL"
];

foreach ($columns as $column => $definition) {
    $result = $conn->query("SHOW COLUMNS FROM reports LIKE '$column'");
    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE reports ADD COLUMN $column $definition";
        if ($conn->query($sql) === TRUE) {
            echo "Column $column added successfully<br>";
        } else {
            echo "Error adding column $column: " . $conn->error . "<br>";
        }
    } else {
        echo "Column $column already exists<br>";
    }
}
?>