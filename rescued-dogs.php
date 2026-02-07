<?php
session_start();
include 'db.php';

// Check if admin is logged in
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
    $stmt->execute();
    $stmt->close();
    header("Location: rescued-dogs.php");
    exit;
}

// Fetch all rescued dogs
$sql = "SELECT * FROM reports WHERE status = 'Rescued' ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rescued Dogs - Admin Dashboard</title>
    <link rel="stylesheet" href="rescued-dogs.css">
    <style>
        .rescued-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .rescued-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .rescued-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }
        .rescued-card:hover {
            transform: translateY(-5px);
        }
        .rescued-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .rescued-info {
            padding: 15px;
        }
        .rescued-location {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .rescued-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .rescued-email {
            color: #888;
            font-size: 12px;
        }
        .no-dogs {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <header class="header">
            <div class="header-left">
                <div class="logo">🐾</div>
                <h1 class="h-title">Welcome to NGO Dashboard</h1>
            </div>
            <a href="adminlogout.php" style="color: white; text-decoration: none;">Logout</a>
        </header>

        <main class="main">
            <aside class="sidebar">
                <nav class="side-nav">
                    <a class="nav-item" href="reported_dogs1.php">Reported Dogs</a>
                    <a class="nav-item" href="adoptionlist.php">Adoption List</a>
                    <a class="nav-item active" href="rescued-dogs.php">Rescued Dogs</a>
                    <a class="nav-item" href="add-for-adoption.php">Post for Adoption</a>
                    <a class="nav-item" href="adopters.html">Adopters</a>
                </nav>
            </aside>

            <section class="content">
                <h2>Rescued Dogs</h2>
                <div class="rescued-container">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="rescued-grid">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="rescued-card">
                                    <img src="uploads/<?php echo htmlspecialchars($row['picture']); ?>"
                                         alt="Rescued Dog" class="rescued-image">
                                    <div class="rescued-info">
                                        <div class="rescued-location">
                                            📍 <?php echo htmlspecialchars($row['location']); ?>
                                        </div>
                                        <div class="rescued-description">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </div>
                                        <div class="rescued-email">
                                            📧 <?php echo htmlspecialchars($row['email']); ?>
                                        </div>
                                        <form method="post" style="margin-top: 10px;">
                                            <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="new_status" value="Ready for adoption">
                                            <button type="submit" style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                                                Add for Adoption
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-dogs">
                            <h3>No Rescued Dogs Yet</h3>
                            <p>Dogs will appear here once they are marked as rescued.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <?php $conn->close(); ?>
</body>
</html>