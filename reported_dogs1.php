<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle "Start Rescue"
if (isset($_POST['action']) && $_POST['action'] === 'start_rescue') {
    $report_id = intval($_POST['report_id']);
    $stmt = $conn->prepare("UPDATE reports SET status = 'In process' WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $stmt->close();
    $success = "Rescue started successfully! Move to 'In Process' tab.";
}

// Fetch pending reports
$sql = "SELECT id, picture, location, description, email FROM reports WHERE status='Pending' ORDER BY id DESC";
$result = $conn->query($sql);

$count_sql = "SELECT COUNT(*) as count FROM reports WHERE status='Pending'";
$count_result = $conn->query($count_sql);
$pending_count = $count_result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reported Dogs - RescueTails Admin</title>
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
        .btn-start {
            background: #f39c12;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
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
                <a class="nav-item active" href="reported_dogs1.php">Reported Dogs</a>
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

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-info">
                                <p class="report-location"><?= htmlspecialchars($row['location']) ?></p>
                                <p class="report-description"><?= htmlspecialchars($row['description']) ?></p>
                            </div>
                        </div>
                        <div class="report-actions">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="start_rescue">
                                <input type="hidden" name="report_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-details btn-start">Start Rescue</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pending reports at the moment.</p>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>

<?php $conn->close(); ?>