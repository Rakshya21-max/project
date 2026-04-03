<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit;
}

$email = $_SESSION['user_email'] ?? '';

// Mark all as read when page is opened
$conn->query("UPDATE notifications SET is_read = 1 WHERE email = '$email'");

$result = $conn->query("SELECT * FROM notifications WHERE email = '$email' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Notifications - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; margin:0; padding:20px; }
        .notif-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .notif-header {
            background: linear-gradient(135deg, #2f6b4f, #3d8b66);
            color: white;
            padding: 25px 30px;
            font-size: 24px;
            font-weight: 600;
        }
        .notif-item {
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            transition: 0.2s;
        }
        .notif-item:hover { background: #f8fff8; }
        .notif-item:last-child { border-bottom: none; }
        .notif-time { font-size: 13px; color: #888; margin-top: 8px; }
        .back-btn {
            padding: 12px 20px;
            background: #2f6b4f;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            margin: 20px 30px;
        }
    </style>
</head>
<body>

<div class="notif-container">
    <div class="notif-header">🛎️ My Notifications</div>
    <br><br>
    <a href="javascript:history.back()" class="back-btn">Back</a>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="notif-item">
                <strong><?= htmlspecialchars($row['message']) ?></strong>
                <div class="notif-time"><?= date('d M Y • h:i A', strtotime($row['created_at'])) ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="padding:60px; text-align:center; color:#666;">
            <h3>No notifications yet</h3>
            <p>When a dog you reported gets rescued, you'll see it here.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>