<?php
include 'db.php';

$columns = [
    'name'   => "VARCHAR(100) DEFAULT NULL COMMENT 'Dog name given by admin'",
    'breed'  => "VARCHAR(100) DEFAULT NULL",
    'age'    => "VARCHAR(50)  DEFAULT NULL COMMENT 'e.g. 2 years, Puppy'",
    'gender' => "ENUM('male','female') DEFAULT NULL",
    'size'   => "ENUM('small','medium','large','extra large') DEFAULT NULL"
];

echo "<pre>";
foreach ($columns as $col_name => $definition) {
    // Check if column already exists
    $check = $conn->query("SHOW COLUMNS FROM reports LIKE '$col_name'");
    
    if ($check->num_rows == 0) {
        $sql = "ALTER TABLE reports ADD COLUMN $col_name $definition";
        if ($conn->query($sql) === TRUE) {
            echo "Success: Added column '$col_name'\n";
        } else {
            echo "Error adding '$col_name': " . $conn->error . "\n";
        }
    } else {
        echo "Column '$col_name' already exists - skipped\n";
    }
}
echo "</pre>";

$conn->close();
?>