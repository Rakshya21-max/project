<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// ====================== HANDLE "RESCUE COMPLETE" ======================
if (isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $new_status = $_POST['new_status'];   // This will be 'Rescued'

    $stmt = $conn->prepare("UPDATE reports SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $update_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        
        // === SEND NOTIFICATION TO THE USER WHO REPORTED ===
        $get_email = $conn->prepare("SELECT email FROM reports WHERE id = ?");
        $get_email->bind_param("i", $update_id);
        $get_email->execute();
        $get_email->bind_result($reporter_email);
        $get_email->fetch();
        $get_email->close();

        if (!empty($reporter_email)) {
            $message = "Great news! The dog you reported has been successfully rescued and is now safe with us. 🐾";

            $insert_notif = $conn->prepare(
                "INSERT INTO notifications (email, message, is_read, created_at) 
                 VALUES (?, ?, 0, NOW())"
            );
            $insert_notif->bind_param("ss", $reporter_email, $message);
            $insert_notif->execute();
            $insert_notif->close();
        }

        $success = "Rescue completed successfully! Notification sent to the reporter.";
    } else {
        $error = "Failed to update status.";
    }
    $stmt->close();
}

// Fetch "In Process" reports
$sql = "SELECT id, picture, location, description, email FROM reports WHERE status='In process' ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Count in process
$count_sql = "SELECT COUNT(*) as count FROM reports WHERE status='In process'";
$count_result = $conn->query($count_sql);
$in_process_count = $count_result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Process Reports - RescueTails Admin</title>
    <link rel="stylesheet" href="reported_dogs2.css">
    <style>
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
        }
        .logout {
            background: #ff7700;
            color: white;
            padding: 10px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
        }
        .logout:hover {
            background: #e66a00;
        }
        .btn-completed {
            background: #27ae60;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="app-shell">
    <header class="header">
        <div class="header-left">
            <div class="logo">🐾</div>
            <h1 class="h-title">Welcome to NGO<br>Dashboard</h1>
        </div>
        <a href="adminlogout.php" class="logout">Logout</a>
    </header>

    <main class="main">
        <aside class="sidebar">
            <nav class="side-nav">
                <a class="nav-item" href="reported_dogs1.php">Reported Dogs</a>
                <a class="nav-item" href="adoptionlist.php">Adoption List</a>
                <a class="nav-item" href="rescued-dogs.php">Rescued Dogs</a>
                <a class="nav-item" href="add-for-adoption.php">Post for Adoption</a>
                <a class="nav-item" href="adopters.php">Adopters</a>
            </nav>
        </aside>

        <section class="content">
            <?php if (isset($success)): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="status-tabs">
                <button class="status-btn">
                    <a href="reported_dogs1.php"><span class="status-label">Pending</span></a>
                </button>
                <button class="status-btn active">
                    <a href="reported_dogs2.php"><span class="status-label">In process</span></a>
                    <span class="status-icon">⏱</span>
                </button>
            </div>

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="report-card">';
                    echo '<div class="report-header">';
                    echo '<div class="report-info">';
                    echo '<p class="report-location">' . htmlspecialchars($row['location']) . '</p>';
                    echo '<p class="report-description">' . htmlspecialchars($row['description']) . '</p>';
                    echo '</div>';
                    echo '<div class="report-date">';
                    echo '<span class="date-icon">📅</span>';
                    echo '<span class="date-text">N/A</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="report-actions">';
                    
                    echo '<form method="post" style="display:inline;">';
                    echo '<input type="hidden" name="update_id" value="' . $row['id'] . '">';
                    echo '<input type="hidden" name="new_status" value="Rescued">';
                    echo '<button type="submit" class="btn-completed">Rescue Complete</button>';
                    echo '</form>';
                    
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No reports in process.</p>';
            }
            ?>
        </section>
    </main>
</div>
</body>
</html>

<?php $conn->close(); ?>