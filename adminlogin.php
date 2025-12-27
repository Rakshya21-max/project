<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') {
        die("Please fill in both fields.");
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
    $_SESSION['admin'] = $username;
    header("Location: reported_dogs1.html");
    exit;
    }else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
}
$conn->close();
?>

<?php
session_start();

// If admin is not logged in, redirect to login page
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

$adminUsername = $_SESSION['admin'];
?>