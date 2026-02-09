<?php
session_start();
include 'db.php';

$dog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($dog_id <= 0) {
    die("<h2 style='text-align:center; color:#c0392b;'>Invalid dog ID</h2>");
}

$stmt = $conn->prepare("
    SELECT 
        id, picture, name, breed, age, gender, size, status
    FROM reports 
    WHERE id = ?
");
$stmt->bind_param("i", $dog_id);
$stmt->execute();
$dog = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dog) {
    die("<h2 style='text-align:center; color:#c0392b;'>Dog not found</h2>");
}

$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($dog['name'] ?: 'Street Dog'); ?> - RescueTails</title>
    <link rel="stylesheet" href="adopters.css" />
    <style>
        body { background:#f8f9fa; font-family:Arial,sans-serif; margin:0; padding:30px; }
        .container { max-width:1000px; margin:auto; background:white; padding:30px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1); }
        .header { background:#2f6b4f; color:white; padding:15px 25px; text-align:center; }
        .logo { font-size:2.5rem; margin-bottom:10px; }
        .dog-header { display:flex; gap:30px; margin-bottom:30px; flex-wrap:wrap; }
        .dog-photo { flex:0 0 400px; }
        .dog-photo img { width:100%; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
        .dog-info { flex:1; min-width:300px; }
        .dog-name { margin:0 0 15px; color:#2f6b4f; font-size:2.4rem; }
        .status-badge { padding:8px 16px; border-radius:20px; font-weight:600; display:inline-block; margin-bottom:20px; }
        .status-ready { background:#d4edda; color:#155724; }
        .status-adopted { background:#cce5ff; color:#004085; }
        .info-grid { display:grid; grid-template-columns:1fr 2fr; gap:12px 20px; }
        .label { font-weight:600; color:#444; }
        .value { color:#555; }
        .btn-adopt {
            padding:14px 40px; background:#f57c00; color:white; border:none; border-radius:8px; 
            font-size:1.2rem; cursor:pointer; margin-top:20px; display:inline-block; text-decoration:none;
        }
        .btn-adopt:hover { background:#e06b00; }
        .back-link { display:block; margin:20px 0; color:#2f6b4f; font-weight:bold; text-decoration:none; }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">🐾</div>
    <h1>RescueTails - Street Dog Details</h1>
</header>

<div class="container">
    <a href="galleryafter.php" class="back-link">← Back to Gallery</a>

    <div class="dog-header">
        <?php if ($dog['picture']): ?>
            <div class="dog-photo">
                <img src="uploads/<?php echo htmlspecialchars($dog['picture']); ?>" alt="<?php echo htmlspecialchars($dog['name'] ?: 'Dog'); ?>">
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

                
            
            </div>

            <?php if ($dog['status'] === 'Ready for adoption' && $is_logged_in): ?>
                <a href="adopt.php?dog_id=<?php echo $dog['id']; ?>" class="btn-adopt">Adopt Me 🐾</a>
            <?php elseif ($dog['status'] === 'Adopted'): ?>
                <p style="color:#27ae60; font-weight:bold; font-size:1.3rem; margin-top:20px;">
                    This lovely dog has already found a forever home! ❤️
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>