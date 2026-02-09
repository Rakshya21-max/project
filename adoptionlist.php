<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle accept / decline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $app_id = intval($_POST['application_id']);
    $action = $_POST['action']; // 'approved' or 'rejected'

    // Update application status
    $stmt = $conn->prepare("UPDATE adoption_applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $app_id);
    $stmt->execute();
    $stmt->close();

    // If approved → mark dog as adopted
    if ($action === 'approved') {
        $update_dog = $conn->prepare("
            UPDATE reports 
            SET status = 'Adopted' 
            WHERE id = (SELECT dog_id FROM adoption_applications WHERE id = ?)
        ");
        $update_dog->bind_param("i", $app_id);
        $update_dog->execute();
        $update_dog->close();
    }

    header("Location: adoptionlist.php?updated=1");
    exit;
}

// Fetch all applications with user & dog details
$sql = "
    SELECT 
        aa.id, aa.user_id, aa.report_id, aa.application_date, aa.status,
        u.first_name, u.last_name, u.phone,
        r.name AS dog_name
    FROM adoption_applications aa
    LEFT JOIN users u ON aa.user_id = u.user_id
    LEFT JOIN reports r ON aa.report_id = r.id
    ORDER BY aa.application_date DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>Adoption List - RescueTails Admin</title>
	<link rel="stylesheet" href="adopters.css" />
	<style>
		/* ── Only table visual improvements ── */
		.table-card {
			background: white;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 4px 20px rgba(0,0,0,0.08);
			margin-top: 20px;
		}

		.dogs-table {
			width: 100%;
			border-collapse: separate;
			border-spacing: 0;
		}

		.dogs-table th {
			background: #2f6b4f;
			color: white;
			padding: 14px 16px;
			text-align: left;
			font-weight: 600;
			text-transform: uppercase;
			font-size: 0.9rem;
			letter-spacing: 0.5px;
		}

		.dogs-table td {
			padding: 16px;
			border-bottom: 1px solid #eee;
			vertical-align: middle;
			color: #444;
		}

		.dogs-table tr {
			transition: all 0.2s ease;
		}

		.dogs-table tr:hover {
			background: #f8fff8;
			transform: translateY(-1px);
			box-shadow: 0 2px 8px rgba(0,0,0,0.05);
		}

		/* Status badges - pill style */
		.status-badge {
			padding: 6px 12px;
			border-radius: 20px;
			font-size: 0.85rem;
		
			display: inline-block;
		}

		.status-pending { background: #fff3cd; color: #856404; }
		.status-approved { background: #d4edda; color: #155724; }
		.status-rejected { background: #f8d7da; color: #721c24; }

		/* Buttons with icons */
		.btn-view, .btn-approve, .btn-reject {
			padding: 8px ;
			border-radius: 6px;
			font-size: 0.9rem;
			transition: all 0.2s;
			display: inline-flex;
			align-items: center;
			gap: 6px;
		}

		.btn-view {
			background: #3498db;
			color: white;
		}
		.btn-view:hover { background: #2980b9; }

		.btn-approve {
			background: #27ae60;
			color: white;
		}
		.btn-approve:hover { background: #219653; }

		.btn-reject {
			background: #e74c3c;
			color: white;
		}
		.btn-reject:hover { background: #c0392b; }

		/* No data message */
		.no-data {
			text-align: center;
			padding: 60px 20px;
			color: #777;
			font-size: 1.1rem;
			background: #f8f9fa;
			border-radius: 12px;
			margin: 20px;
		}
	</style>
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
						<a class="nav-item active" href="adoptionlist.php">Adoption List</a>
						<a class="nav-item" href="rescued-dogs.php">Rescued Dogs</a>
						<a class="nav-item" href="add-for-adoption.php">Post for Adoption</a>
						<a class="nav-item" href="adopters.php">Adopters</a>
					</nav>
				</aside>

				<section class="content">
					<div class="table-card">
						<h2>Adoption Requests</h2>

						<?php if (isset($_GET['updated'])): ?>
							<div class="success-msg">Application updated successfully.</div>
						<?php endif; ?>

						<table class="dogs-table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Applicant</th>
									<th>Phone</th>
									<th>Dog</th>
									<th>Status</th>
									<th>Date</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($result && $result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										$statusText = ucfirst($row['status']);
										$statusClass = 'status-' . strtolower($row['status']);
										
										$applicant = trim($row['first_name'] . ' ' . $row['last_name']);
										$applicant_display = $applicant ? htmlspecialchars($applicant) : 'User #' . $row['user_id'];
										
										$dog_display = $row['dog_name'] ? htmlspecialchars($row['dog_name']) : 'Dog #' . $row['report_id'];
								?>
										<tr>
											<td>#<?php echo $row['id']; ?></td>
											<td><?php echo $applicant_display; ?></td>
											<td><?php echo htmlspecialchars($row['phone'] ?: 'N/A'); ?></td>
											<td><?php echo $dog_display; ?></td>
											<td>
												<span class="status-badge <?php echo $statusClass; ?>">
													<?php echo $statusText; ?>
												</span>
											</td>
											<td><?php echo date('d M Y H:i', strtotime($row['application_date'])); ?></td>
											<td>
												<a href="view_application.php?id=<?php echo $row['id']; ?>" class="btn-view">View Details</a>
												
												<?php if ($row['status'] === 'pending'): ?>
													<form method="post" style="display:inline;">
														<input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
														<button type="submit" name="action" value="approved" class="btn-approve">Approve</button>
														<button type="submit" name="action" value="rejected" class="btn-reject">Reject</button>
													</form>
												<?php endif; ?>
											</td>
										</tr>
								<?php
									}
								} else {
								?>
									<tr>
										<td colspan="7" class="no-data">
											No adoption requests at the moment.<br>
											When someone applies, they will appear here.
										</td>
									</tr>
								<?php
								}
								?>
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