<?php
session_start();
include 'db.php';

// Only allow admin
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Updated SQL – include the new dog detail columns
$sql = "
    SELECT id, picture, location, email, name, breed, age, gender, size, status 
    FROM reports 
    WHERE status IN ('Ready for adoption', 'Adopted') 
    ORDER BY id DESC
";
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
	<link rel="stylesheet" href="adopters.css" />
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
						<a class="nav-item active" href="add-for-adoption.php">Post for Adoption</a>
						<a class="nav-item" href="adopters.php">Adopters</a>
					</nav>
				</aside>

				<section class="content">
					<div class="table-card">
						<table class="dogs-table">
							<!-- Inside your table <thead> -->
<thead>
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Breed</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Size</th>
        <th>Location</th>
        <th>Status</th>
        <th>Actions</th> <!-- ← Add this column -->
    </tr>
</thead>

<!-- Inside <tbody> loop -->
<tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="uploads/<?php echo htmlspecialchars($row['picture']); ?>" width="60" style="border-radius:6px;"></td>
            <td><?php echo htmlspecialchars($row['name'] ?: 'Unnamed'); ?></td>
            <td><?php echo htmlspecialchars($row['breed'] ?: '—'); ?></td>
            <td><?php echo htmlspecialchars($row['age'] ?: '—'); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($row['gender'] ?? '—')); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($row['size'] ?? '—')); ?></td>
            <td><?php echo htmlspecialchars($row['location'] ?: '—'); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td>
                <a href="admin-dog-detail.php?id=<?php echo $row['id']; ?>" 
                   style="color:#3498db; text-decoration:none; font-weight:500;">
                    View Details
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
						</table>
					</div>
				</section>
			</main>
		</div>
	</div>

<?php $conn->close(); ?>
</body>
</html>