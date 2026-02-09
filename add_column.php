<?php
include 'db.php';

$columns_to_add = [
    'name'   => "VARCHAR(100) DEFAULT NULL COMMENT 'Dog name given by admin'",
    'breed'  => "VARCHAR(100) DEFAULT NULL",
    'age'    => "VARCHAR(50)  DEFAULT NULL COMMENT 'e.g. 2 years, Puppy'",
    'gender' => "ENUM('male','female') DEFAULT NULL",
    'size'   => "ENUM('small','medium','large','extra large') DEFAULT NULL"
];

echo "<h2>Adding missing columns to table 'reports'</h2><pre>";

foreach ($columns_to_add as $column => $definition) {
    // Check if column already exists
    $check = $conn->query("SHOW COLUMNS FROM reports LIKE '$column'");
    
    if ($check->num_rows === 0) {
        $sql = "ALTER TABLE reports ADD COLUMN $column $definition";
        if ($conn->query($sql) === TRUE) {
            echo "✓ Added column '$column'\n";
        } else {
            echo "✗ Error adding '$column': " . $conn->error . "\n";
        }
    } else {
        echo "→ Column '$column' already exists - skipped\n";
    }
}

echo "</pre>";
echo "<p><strong>Done.</strong> You can now go back to <a href='rescued-dogs.php'>rescued-dogs.php</a>.</p>";

$conn->close();
?>