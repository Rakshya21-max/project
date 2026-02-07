<?php
session_start();
include 'db.php';

// Verify admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch adopters from database
$sql = "
SELECT 
	u.user_id,
	u.first_name,
	u.last_name,
	u.email,
	u.phone
FROM users u
WHERE u.role_id = 2
ORDER BY u.first_name ASC
";

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopters</title>
    <link rel="stylesheet" href="adopters.css">
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

				<nav class="side-nav">
					<a class="nav-item" href="reported_dogs1.php">Reported Dogs</a>
					<a class="nav-item" href="adoptionlist.php">Adoption List</a>
					<a class="nav-item" href="rescued-dogs.php">Rescued Dogs</a>
					<a class="nav-item" href="add-for-adoption.php">Post for Adoption</a>
					<a class="nav-item active" href="adopters.php">Adopters</a>
				</nav>
			</aside>

			<section class="content">
				<div class="table-card">
					<h2>Adopters</h2>

					<table class="adopters-table">
						<thead>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Phone</th>
							</tr>
						</thead>
						<tbody>
													<?php if ($result && $result->num_rows > 0): ?>
														<?php while ($row = $result->fetch_assoc()): ?>
															<tr>
																<td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
																<td><?= htmlspecialchars($row['email']) ?></td>
																<td><?= htmlspecialchars($row['phone'] ?? 'N/A') ?></td>
															</tr>
														<?php endwhile; ?>
													<?php else: ?>
														<tr>
															<td colspan="3" style="text-align: center; padding: 20px;">No adopters found.</td>
														</tr>
													<?php endif; ?>
						</tbody>
					</table>
				</div>
			</section>
		</main>
	</div>
</body>
</html>
<?php $conn->close(); ?>