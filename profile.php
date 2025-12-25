<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2f5f46; }
        .profile-info { margin-bottom: 20px; }
        .profile-info p { margin: 10px 0; }
        .profile-photo { text-align: center; margin-bottom: 20px; }
        .profile-photo img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input[type="file"] { padding: 8px; }
        button { background: #f68b1e; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #e67e17; }
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #2f5f46; }
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            // Update database with photo path
            $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE user_id = ?");
            if ($stmt) {
                $stmt->bind_param('si', $target_file, $user_id);
                if ($stmt->execute()) {
                    $message = "Profile photo updated successfully.";
                } else {
                    $message = "Error updating database: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Database error: " . $conn->error;
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    } else {
        $message = "File is not an image.";
    }
}

$stmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
    <div class="container">
        <h1>Your Profile</h1>
        <?php if ($message): ?>
            <p style="color: green; text-align: center;"><?php echo $message; ?></p>
        <?php endif; ?>
        <div class="profile-photo">
            <img src="profile.jpg" alt="Profile Photo">
        </div>
        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        </div>

        <a href="landingafter.php" class="back-link">Back to Home</a>
    </div>
</body>
</html>