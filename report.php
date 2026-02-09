<?php
session_start();
include 'db.php';

$dog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Invalid ID → show nice error
if ($dog_id <= 0) {
    $error_message = "Invalid dog ID. Please go back and try again.";
} else {
    // Fetch dog details
    $stmt = $conn->prepare("
        SELECT 
            id, picture, name, breed, age, gender, size, 
            location, description, status, application_date
        FROM reports 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $dog_id);
    $stmt->execute();
    $dog = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$dog) {
        $error_message = "This dog could not be found. It may have been adopted or removed.";
    }
}

// Determine user type
$is_admin = isset($_SESSION['admin']);
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?php echo isset($dog) ? htmlspecialchars($dog['name'] ?: 'Street Dog') : 'Dog Not Found'; ?> - RescueTails</title>
    <link rel="stylesheet" href="adopters.css" />
    <style>
        body { background:#f8f9fa; font-family:Arial,sans-serif; margin:0; padding:20px; }
        .frame { max-width:1200px; margin:0 auto; background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1); overflow:hidden; }
        .header { background:#2f6b4f; color:white; padding:15px 25px; display:flex; justify-content:space-between; align-items:center; }
        .logo { font-size:2.2rem; }
        .h-title { margin:0; font-size:1.5rem; }
        .logout button { background:#265c3f; color:white; border:none; padding:8px 18px; border-radius:6px; cursor:pointer; }
        .logout button:hover { background:#1e4a32; }
        .main { display:flex; }
        .sidebar { width:240px; background:#f8fff8; border-right:1px solid #ddd; padding:20px 0; }
        .side-nav a { display:block; padding:14px 24px; color:#2f6b4f; text-decoration:none; }
        .side-nav a:hover, .side-nav a.active { background:#e8f5e9; font-weight:600; }
        .content { flex:1; padding:40px; text-align:center; }
        .error-box {
            background:#fff5f5;
            color:#c0392b;
            padding:40px;
            border-radius:12px;
            max-width:600px;
            margin:0 auto;
            box-shadow:0 4px 15px rgba(0,0,0,0.1);
        }
        .error-box h2 { margin:0 0 20px; font-size:2rem; }
        .back-btn {
            display:inline-block;
            margin-top:30px;
            padding:12px 30px;
            background:#2f6b4f;
            color:white;
            text-decoration:none;
            border-radius:8px;
            font-weight:500;
        }
        .back-btn:hover { background:#265c3f; }
    </style>
</head>
<body>

<?php if (isset($error_message)): ?>
    <div class="frame">
        <header class="header">
            <div class="header-left">
                <div class="logo">🐾</div>
                <h1 class="h-title">RescueTails</h1>
            </div>
            <?php if ($is_admin): ?>
                <button class="logout"><a href="adminlogout.php">Logout</a></button>
            <?php endif; ?>
        </header>

        <div class="content">
            <div class="error-box">
                <h2>Oops! Dog Not Found</h2>
                <p><?php echo htmlspecialchars($error_message); ?></p>
                <a href="<?php echo $is_admin ? 'rescued-dogs.php' : 'galleryafter.php'; ?>" class="back-btn">
                    ← Back to <?php echo $is_admin ? 'Dashboard' : 'Gallery'; ?>
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Normal dog details content (your previous full page code here) -->
    <div class="frame">
        <?php if ($is_admin): ?>
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
                        <a class="nav-item" href="add-for-adoption.php">Post for Adoption</a>
                        <a class="nav-item" href="adopters.php">Adopters</a>
                    </nav>
                </aside>

                <section class="content">
        <?php else: ?>
            <header class="header" style="background:#2f6b4f; padding:15px 25px;">
                <div class="header-left">
                    <div class="logo">🐾</div>
                    <h1 class="h-title" style="font-size:1.8rem; margin:0;">RescueTails</h1>
                </div>
                <?php if ($is_logged_in): ?>
                    <div><a href="logout.php" style="color:white;">Logout</a></div>
                <?php endif; ?>
            </header>
            <section class="content" style="padding:40px;">
        <?php endif; ?>

        <!-- Dog details content -->
        <a href="<?php echo $is_admin ? 'rescued-dogs.php' : 'galleryafter.php'; ?>" class="back-link">← Back</a>

        <div class="dog-header">
            <?php if ($dog['picture']): ?>
                <div class="dog-photo">
                    <img src="uploads/<?php echo htmlspecialchars($dog['picture']); ?>" 
                         alt="<?php echo htmlspecialchars($dog['name'] ?: 'Dog'); ?>">
                </div>
            <?php endif; ?>

            <div class="dog-info">
                <h1 class="dog-name"><?php echo htmlspecialchars($dog['name'] ?: 'Unnamed Friend'); ?></h1>

                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $dog['status'])); ?>">
                    <?php echo ucfirst($dog['status']); ?>
                </span>

                <div class="info-grid" style="margin-top:20px;">
                    <div class="label">Breed:</div>
                    <div class="value"><?php echo htmlspecialchars($dog['breed'] ?: '—'); ?></div>

                    <div class="label">Age:</div>
                    <div class="value"><?php echo htmlspecialchars($dog['age'] ?: '—'); ?></div>

                    <div class="label">Gender:</div>
                    <div class="value"><?php echo htmlspecialchars(ucfirst($dog['gender'] ?? '—')); ?></div>

                    <div class="label">Size:</div>
                    <div class="value"><?php echo htmlspecialchars(ucfirst($dog['size'] ?? '—')); ?></div>

                    <div class="label">Location:</div>
                    <div class="value"><?php echo htmlspecialchars($dog['location'] ?: '—'); ?></div>

                    <div class="label">Description:</div>
                    <div class="value" style="grid-column:span 2;">
                        <?php echo nl2br(htmlspecialchars($dog['description'] ?: 'No description available.')); ?>
                    </div>
                </div>

                <?php if ($dog['status'] === 'Ready for adoption' && $is_logged_in && !$is_admin): ?>
                    <a href="adopt.php?dog_id=<?php echo $dog['id']; ?>" class="btn-adopt">Adopt Me 🐾</a>
                <?php endif; ?>

                <?php if ($is_admin): ?>
                    <div style="margin-top:20px;">
                        <a href="edit-dog.php?id=<?php echo $dog['id']; ?>" class="btn-admin">Edit Details</a>
                        <?php if ($dog['status'] !== 'Adopted'): ?>
                            <form method="post" action="rescued-dogs.php" style="display:inline;">
                                <input type="hidden" name="dog_id" value="<?php echo $dog['id']; ?>">
                                <button type="submit" name="mark_adopted" class="btn-admin" style="background:#e67e22;">Mark as Adopted</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        </section>
        <?php if ($is_admin): ?>
            </main>
        <?php endif; ?>
    </div>

<?php $conn->close(); ?>
</body>
</html>