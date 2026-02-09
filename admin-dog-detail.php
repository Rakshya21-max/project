<?php
session_start();
include 'db.php';

$dog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($dog_id <= 0) {
    $error_message = "Invalid dog ID. Please go back and try again.";
} else {
    $stmt = $conn->prepare("
        SELECT 
            id, picture, name, breed, age, gender, size, 
            location, status
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

// User/admin detection
$is_logged_in = isset($_SESSION['user_id']);
$is_admin     = isset($_SESSION['admin']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo isset($dog) ? htmlspecialchars($dog['name'] ?: 'Street Dog') : 'Not Found'; ?> - RescueTails</title>
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
        .content { flex:1; padding:40px; }
        .dog-header { display:flex; gap:30px; margin-bottom:30px; flex-wrap:wrap; }
        .dog-photo { flex:0 0 350px; }
        .dog-photo img { width:100%; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
        .dog-info { flex:1; min-width:300px; }
        .dog-name { margin:0 0 15px; color:#2f6b4f; font-size:2.2rem; }
        .status-badge { padding:8px 16px; border-radius:20px; font-weight:600; display:inline-block; margin-bottom:15px; }
        .status-ready { background:#d4edda; color:#155724; }
        .status-adopted { background:#cce5ff; color:#004085; }
        .info-grid { display:grid; grid-template-columns:1fr 2fr; gap:12px 20px; }
        .label { font-weight:600; color:#444; }
        .value { color:#555; }
        .btn-adopt, .btn-admin {
            padding:12px 30px; border:none; border-radius:8px; cursor:pointer; font-size:1.1rem; margin:10px 10px 0 0;
        }
        .btn-adopt { background:#f57c00; color:white; }
        .btn-adopt:hover { background:#e06b00; }
        .btn-admin { background:#3498db; color:white; }
        .btn-admin:hover { background:#2980b9; }
        .back-link { display:inline-block; margin-bottom:20px; color:#2f6b4f; font-weight:bold; text-decoration:none; }
        .error-box {
            background:#fff5f5; color:#c0392b; padding:40px; border-radius:12px; max-width:600px; margin:0 auto;
            box-shadow:0 4px 15px rgba(0,0,0,0.1); text-align:center;
        }
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

                    
                </div>

                <!-- Adopt button - only for normal logged-in users -->
                <?php if ($dog['status'] === 'Ready for adoption' && $is_logged_in && !$is_admin): ?>
                    <a href="adopt.php?report_id=<?php echo $dog['id']; ?>" class="btn-adopt">Adopt Me 🐾</a>
                <?php endif; ?>

                <!-- Admin-only controls - hidden for normal users -->
                <?php if (isset($_SESSION['admin'])): ?>
    <!-- Admin-only controls -->
    <a href="edit-details.php?id=<?php echo $dog['id']; ?>" class="btn-admin">Edit Details</a>
    
    <?php if ($dog['status'] !== 'Adopted'): ?>
        <form method="post" action="rescued-dogs.php" style="display:inline;">
            <input type="hidden" name="dog_id" value="<?php echo $dog['id']; ?>">
            <button type="submit" name="mark_adopted" class="btn-admin" style="background:#e67e22;">
                Post for Adoption
            </button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<!-- Adopt button – visible to normal logged-in users -->
<?php if ($dog['status'] === 'Ready for adoption' && isset($_SESSION['user_id']) && !isset($_SESSION['admin'])): ?>
    <a href="adopt.php?dog_id=<?php echo $dog['id']; ?>" class="btn-adopt">Adopt Me 🐾</a>
<?php endif; ?>
            </div>
        </div>

        </section>
        <?php if ($is_admin): ?>
            </main>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>