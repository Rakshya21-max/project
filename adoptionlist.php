<?php
session_start();
include 'db.php';

/* =========================
   HANDLE ACCEPT / DECLINE
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $app_id = intval($_POST['application_id']);
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $conn->prepare(
            "UPDATE adoption_applications SET status = ? WHERE id = ?"
        );
        $stmt->bind_param("si", $action, $app_id);
        $stmt->execute();
        $stmt->close();
    }
}

/* =========================
   FETCH APPLICATIONS
   ========================= */
$sql = "
SELECT 
    aa.id AS application_id,
    aa.status,
    aa.application_date,
    u.first_name,
    u.last_name,
    u.email,
    r.dog_name
FROM adoption_applications aa
JOIN users u ON aa.user_id = u.id
JOIN reports r ON aa.dog_id = r.id
ORDER BY aa.application_date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adoption Requests</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="back-section">
            <a href="admin_dashboard.php">
                <button class="back-btn">← Back</button>
            </a>
        </div>

        <nav class="side-nav">
            <a class="nav-item active">Adoption Requests</a>
            <a class="nav-item" href="reports.php">Dog Reports</a>
            <a class="nav-item" href="logout.php">Logout</a>
        </nav>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        <div class="table-container">
            <h2>Adoption Applications</h2>

            <table>
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Email</th>
                        <th>Dog</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['dog_name']) ?></td>
                            <td><?= htmlspecialchars($row['application_date']) ?></td>
                            <td><?= ucfirst($row['status']) ?></td>

                            <td>
                                <div class="action-buttons">
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                            <input type="hidden" name="action" value="approved">
                                            <button class="accept-btn">Accept</button>
                                        </form>

                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                            <input type="hidden" name="action" value="rejected">
                                            <button class="decline-btn">Decline</button>
                                        </form>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No adoption applications found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
    </main>

</div>

</body>
</html>

<?php $conn->close(); ?>
