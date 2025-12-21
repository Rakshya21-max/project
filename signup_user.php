<?php
// Database connection
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   

    // Collect form data EXACTLY matching input name=""
    $first = trim($_POST["first"] ?? "");
    $last = trim($_POST["last"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    $role_id = 3; // USER role
    $errors = [];

    // VALIDATIONS
    if (!preg_match("/^[A-Za-z\s'-]+$/", $first)) {
        $errors[] = "Invalid first name.";
    }

    if (!preg_match("/^[A-Za-z\s'-]+$/", $last)) {
        $errors[] = "Invalid last name.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    }

    if (!preg_match("/^\d{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // CHECK EMAIL EXISTS
    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $errors[] = "Email already exists.";
    }

    // IF ERRORS → DISPLAY
    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<p style='color:red;'>$e</p>";
        }
        exit;
    }

    // HASHING PASSWORD
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // INSERT QUERY
    $sql = $conn->prepare(
        "INSERT INTO users (first_name, last_name, email, phone, password, role_id)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $sql->bind_param("sssssi", $first, $last, $email, $phone, $hashedPassword, $role_id);

    if ($sql->execute()) {
        echo "<script>alert('Signup successful!'); window.location.href='login.php';</script>";
    } else {
        echo "Insert Error: " . $sql->error;
    }
   

}
?>
