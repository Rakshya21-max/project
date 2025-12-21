<?php
session_start();
include 'db.php'; // Your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first = trim($_POST['first'] ?? '');
    $last = trim($_POST['last'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $staff_id = trim($_POST['staff_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $role_id = 2; // NGO role
    $errors = [];

    // Validation
    if (!preg_match("/^[A-Za-z\s'-]+$/", $first)) $errors[] = "Invalid first name.";
    if (!preg_match("/^[A-Za-z\s'-]+$/", $last)) $errors[] = "Invalid last name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if (!preg_match("/^\d{10}$/", $phone)) $errors[] = "Phone must be 10 digits.";
    if (!preg_match("/^[A-Za-z0-9-]{4,}$/", $staff_id)) $errors[] = "Invalid Staff ID.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";

    // Check duplicate email/staff_id
    $check = $conn->prepare("SELECT email FROM ngo WHERE email = ? OR staff_id = ?");
    $check->bind_param("ss", $email, $staff_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) $errors[] = "Email or Staff ID already exists.";

    if (!empty($errors)) {
        foreach ($errors as $e) echo "<p style='color:red;'>$e</p>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = $conn->prepare("INSERT INTO ngo (first_name,last_name,email,phone_no,staff_id,password,role_id) VALUES (?,?,?,?,?,?,?)");
    $sql->bind_param("ssssssi", $first, $last, $email, $phone, $staff_id, $hashedPassword, $role_id);

    if ($sql->execute()) {
        echo "<script>alert('NGO Signup Successful!'); window.location.href='loginNGO.php';</script>";
    } else {
        echo "Insert Error: ".$sql->error;
    }
}
?>
