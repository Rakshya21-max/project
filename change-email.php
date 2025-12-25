<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Email - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2f5f46; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; background: #f68b1e; color: #fff; border: none; padding: 10px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #e67e17; }
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #2f5f46; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = trim($_POST['new_email']);
    $password = $_POST['password'];

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Verify password
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Check if email already exists
            $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            $check->bind_param('si', $new_email, $user_id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $message = "Email already in use.";
            } else {
                // Update email
                $stmt = $conn->prepare("UPDATE users SET email = ? WHERE user_id = ?");
                $stmt->bind_param('si', $new_email, $user_id);
                if ($stmt->execute()) {
                    $_SESSION['user_email'] = $new_email;
                    $message = "Email updated successfully.";
                } else {
                    $message = "Error updating email.";
                }
            }
        } else {
            $message = "Incorrect password.";
        }
    }
}
?>
    <div class="container">
        <h1>Change Email</h1>
        <?php if ($message): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="new_email">New Email:</label>
                <input type="email" name="new_email" id="new_email" required>
            </div>
            <div class="form-group">
                <label for="password">Current Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Update Email</button>
        </form>
        <a href="landingafter.php" class="back-link">Back to Home</a>
    </div>
</body>
</html>