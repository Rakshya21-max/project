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

        // Optional: refresh page to show updated status
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
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
    r.picture AS dog_picture
FROM adoption_applications aa
JOIN users u ON aa.user_id = u.user_id
JOIN reports r ON aa.dog_id = r.id
ORDER BY aa.application_date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Applications | Rescue Tails</title>
    <link rel="stylesheet" href="adoption.css">   <!-- your CSS file -->
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar always on left -->
    <aside class="sidebar">
        <div class="header-top">
            <sapn class="paw-icon">🐾</span>
            <h2 class="welcome-title">Welcome to NGO Dashboard</h2>
            <a href="adminlogout.htmls" class="logout-link">Logout</a>
        </div>

        <ul class="nav-list">
           <li><a class="nav-item" href="reported_dogs1.php">Reported Dogs</a></li>
            <li><a class="nav-item active" href="adoptionlist.php">Adoption List</a></li>
             <li><a class="nav-item" href="rescued-dogs.php">Rescued Dogs</a></li>
            <li><a class="nav-item" href="add-for-adoption.php">Post for Adoption</a></li>
            <li><a class="nav-item" href="adopters.php">Adopters</a></li>
        </ul>   
    </aside>

        <!-- MAIN CONTENT -->
         <main class ="main-content">
            <header class="main-header">
                <h1>Adoption Applications</h1>
                <a href="adminlogout.php" class="logout-btn>">Logout</a>
            <section class="content">
            <div class="table-container">
                <h2>Adoption Applications</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="photo-cell">
                                    <?php if (!empty($row['dog_picture'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['dog_picture']) ?>" 
                                             alt="Dog" class="dog-thumbnail">
                                    <?php else: ?>
                                        <span>No photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['application_date']) ?></td>
                                <td class="status-<?= $row['status'] ?>">
                                    <?= ucfirst($row['status']) ?>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                                <input type="hidden" name="action" value="approved">
                                                <button type="submit" class="accept-btn">Accept</button>
                                            </form>

                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                                <input type="hidden" name="action" value="rejected">
                                                <button type="submit" class="decline-btn">Decline</button>
                                            </form>
                                        <?php else: ?>
                                            <span>—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:#666;">
                                No adoption applications found.
                            </td>
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

<?php 
$conn->close(); 
?>