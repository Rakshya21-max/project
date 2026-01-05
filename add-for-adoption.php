<?php
session_start();
include 'db.php';

// Only allow admin
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch ready for adoption and adopted reports
$sql = "SELECT id, picture, location, description, email, status FROM reports WHERE status IN ('Ready for adoption', 'Adopted') ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>Add for Adoption</title>
	<link rel="stylesheet" href="rescued-dogs.css" />
</head>
<body>
	<div class="frame">
		<div class="app-shell">
			<header class="header">
				<div class="header-left">
					<div class="logo">🐾</div>
					<div class="titles">
						<h1 class="h-title">Welcome to NGO<br>Dashboard</h1>
					</div>
				</div>

				<button class="logout"><a href="adminlogout.php">Logout</a></button>
			</header>

			<main class="main">
				<aside class="sidebar">
					
                

					<nav class="side-nav">
						<a class="nav-item" href="reported_dogs1.php">Reported Dogs</a>
						<a class="nav-item" href="adoptionlist.php">Adoption List</a>
						<a class="nav-item" href="rescued-dogs.php">Rescued Dogs</a>
						<a class="nav-item active" href="add-for-adoption.php">Add for Adoption</a>
						<a class="nav-item" href="adopters.html">Adopters</a>
					</nav>
				</aside>

				<section class="content">
					<div class="table-card">
						<table class="dogs-table">
							<thead>
								<tr>
									<th>Location</th>
									<th>Description</th>
									<th>Email</th>
									<th>Image</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($result && $result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										$statusText = ($row['status'] == 'Ready for adoption') ? 'Available' : 'Adopted';
										$statusClass = ($row['status'] == 'Ready for adoption') ? 'status-available' : 'status-adopted';
										
										echo '<tr>';
										echo '<td>' . htmlspecialchars($row['location']) . '</td>';
										echo '<td>' . htmlspecialchars($row['description']) . '</td>';
										echo '<td>' . htmlspecialchars($row['email']) . '</td>';
										echo '<td><img src="uploads/' . htmlspecialchars($row['picture']) . '" alt="Dog" style="width:100px;"></td>';
										echo '<td><span class="' . $statusClass . '">' . $statusText . '</span></td>';
										echo '</tr>';
									}
								} else {
									echo '<tr><td colspan="5">No dogs available for adoption yet.</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
				</section>
			</main>
		</div>
	</div>
</body>
</html>

<?php $conn->close(); ?>