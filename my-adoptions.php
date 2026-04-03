<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's adoption applications
$sql = "
    SELECT aa.id, aa.application_date, aa.status,
           r.name AS dog_name, r.picture, r.location
    FROM adoption_applications aa
    LEFT JOIN reports r ON aa.report_id = r.id
    WHERE aa.user_id = ?
    ORDER BY aa.application_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Adoption Forms - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="landingafter.css" />
    <style>
        .my-adoptions-box {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .box-header {
            background: linear-gradient(135deg, #2f6b4f, #3d8b66);
            color: white;
            padding: 25px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.35);
            transform: translateX(-4px);
        }

        .box-title {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }

        .box-body {
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            color: #2f6b4f;
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8fff8;
        }

        .dog-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .dog-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid #e0f0e6;
        }

        .dog-name {
            font-weight: 600;
            font-size: 17px;
        }

        .status {
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-pending  { background: #fff3cd; color: #856404; }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }

        .empty-state h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="my-adoptions-box">

    <!-- Header with Back Button -->
    <div class="box-header">
        <button onclick="history.back()" class="back-btn">
            ← Back
        </button>
        <h2 class="box-title">My Adoption Forms</h2>
        <div></div> <!-- spacer -->
    </div>

    <!-- Body -->
    <div class="box-body">

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Dog</th>
                        <th>Applied On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        $statusClass = 'status-' . strtolower($row['status']);
                    ?>
                    <tr>
                        <td>
                            <div class="dog-info">
                                <?php if (!empty($row['picture'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['picture']); ?>" 
                                         class="dog-img" alt="Dog">
                                <?php endif; ?>
                                <div>
                                    <div class="dog-name"><?php echo htmlspecialchars($row['dog_name'] ?? 'Unknown Dog'); ?></div>
                                    <small style="color:#666;"><?php echo htmlspecialchars($row['location'] ?? ''); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo date('d M Y • h:i A', strtotime($row['application_date'])); ?></td>
                        <td>
                            <span class="status <?php echo $statusClass; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div class="empty-state">
                <h3>🐾 No Adoption Applications Yet</h3>
                <p>You haven't applied for any dogs yet.</p>
                <a href="galleryafter.php" style="color:#2f6b4f; font-weight:600; text-decoration:none;">
                    Browse Available Dogs →
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>

<?php $stmt->close(); $conn->close(); ?>