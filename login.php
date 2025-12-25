<?php
ob_start();
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header("Location: login.html?error=empty");
    exit;
}

$stmt = $conn->prepare(
    "SELECT user_id, email, password FROM users WHERE email = ? LIMIT 1"
);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: login.html?error=invalid");
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    header("Location: login.html?error=invalid");
    exit;
}

// ✅ SUCCESS
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_email'] = $user['email'];

header("Location: landingafter.php");
exit;
?>
