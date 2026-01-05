


<?php
session_start();
include 'db.php';

// Only allow admin
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle status update
if (isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $new_status = $_POST['new_status'];
    $stmt = $conn->prepare("UPDATE reports SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $update_id);
    if ($stmt->execute()) {
        error_log("Status updated successfully: ID $update_id to $new_status");
    } else {
        error_log("Status update failed: " . $stmt->error);
    }
    $stmt->close();
    header("Location: reported_dogs1.php");
    exit;
}

// Fetch pending reports
$sql = "SELECT id, picture, location, description, email FROM reports WHERE status='Pending' ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Count pending
$count_sql = "SELECT COUNT(*) as count FROM reports WHERE status='Pending'";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$pending_count = $count_row['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reported Dogs</title>
    <link rel="stylesheet" href="reported_dogs2.css">
</head>
<body>
	<div class="app-shell">
		<header class="header">
			<div class="header-left">
				<div class="logo">🐾</div>
				<h1 class="h-title">Welcome to NGO<br>Dashboard</h1>
			</div>
			<a href="adminlogout.php">Logout</a>
		</header>

		<main class="main">
			<aside class="sidebar">
				<!-- <button class="back-btn"> Back</button> -->

				<nav class="side-nav">
					<a class="nav-item active" href="reported_dogs1.php">Reported Dogs</a>
					<a class="nav-item" href="adoptionlist.php">Adoption List</a>
					<a class="nav-item" href="rescued-dogs.php">Rescued Dogs</a>
					<a class="nav-item" href="add-for-adoption.php">Add for Adoption</a>
					<a class="nav-item" href="adopters.html">Adopters</a>
				</nav>
			</aside>

			<section class="content">
				<div class="status-tabs">
					<button class="status-btn active">
						<a href="reported_dogs1.php"><span class="status-label">Pending</span></a>
						<span class="status-count"><?php echo $pending_count; ?></span>
					</button>
					 <button class="status-btn">
						<a href="reported_dogs2.php"> <span class="status-label">In process</span></a>
						<span class="status-icon">⏱</span>
					</button>

					<!-- <button class="status-btn">
						<a href="reported_dogs3.php"><span class="status-label">Completed</span></a>
						<span class="status-icon">✓</span>
					</button> -->
				</div>

				<?php
				if ($result && $result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						echo '<div class="report-card">';
						echo '<div class="report-header">';
						echo '<div class="report-info">';
						echo '<p class="report-location">' . htmlspecialchars($row['location']) . ' ' . htmlspecialchars($row['email']) . '</p>';
						echo '<p class="report-description">' . htmlspecialchars($row['description']) . '</p>';
						echo '</div>';
						echo '<div class="report-date">';
						echo '<span class="date-icon">📅</span>';
						echo '<span class="date-text">N/A</span>'; // Placeholder for date
						echo '</div>';
						echo '</div>';
						echo '<div class="report-actions">';
						echo '<form method="post" style="display:inline;">';
						echo '<input type="hidden" name="update_id" value="' . $row['id'] . '">';
						echo '<input type="hidden" name="new_status" value="In process">';
						echo '<button type="submit" class="btn-details">Start Rescue</button>';
						echo '</form>';
						echo '</div>';
						echo '</div>';
					}
				} else {
					echo '<p>No pending reports.</p>';
				}
				?>
			</section>
		</main>
	</div>
</body>
</html>

<?php $conn->close(); ?>