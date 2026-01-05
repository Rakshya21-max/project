<?php
session_start();
include 'db.php';

// Only allow admin
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch completed reports
$sql = "SELECT id, picture, location, description, email FROM reports WHERE status='Completed' ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Count completed
$count_sql = "SELECT COUNT(*) as count FROM reports WHERE status='Completed'";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$completed_count = $count_row['count'];
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
				<button class="back-btn">⟲ Back</button>

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
					<a href="reported_dogs1.php">
						<button class="status-btn">
						<span class="status-label">Pending</span>
						<span class="status-count">1</span>
					</button></a>
					<a href="reported_dogs2.php">
					<button class="status-btn">
						<span class="status-label">In process</span>
						<span class="status-icon">⏱</span>
					</button></a>
					<a href="reported_dogs3.php">
					<button class="status-btn active">
						<span class="status-label">Completed</span>
						<span class="status-icon">✓</span>
					</button></a>
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
						echo '<button class="btn-details">View details</button>';
						echo '<button class="btn-completed">Completed</button>';
						echo '</div>';
						echo '</div>';
					}
				} else {
					echo '<p>No completed reports.</p>';
				}
				?>
			</section>
		</main>
	</div>
</body>
</html>

<?php $conn->close(); ?>