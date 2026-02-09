<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

$dog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($dog_id <= 0) {
    die("<h2 style='color:red; text-align:center;'>Invalid dog ID</h2>");
}

// Fetch current dog data
$stmt = $conn->prepare("
    SELECT id, picture, name, breed, age, gender, size, location, description, status
    FROM reports 
    WHERE id = ?
");
$stmt->bind_param("i", $dog_id);
$stmt->execute();
$dog = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dog) {
    die("<h2 style='color:red; text-align:center;'>Dog not found</h2>");
}

// Handle form submission (update)
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $breed       = trim($_POST['breed'] ?? '');
    $age         = trim($_POST['age'] ?? '');
    $gender      = $_POST['gender'] ?? '';
    $size        = $_POST['size'] ?? '';
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = $_POST['status'] ?? $dog['status'];

    // Basic validation
    if (empty($name))        $error = "Dog name is required.";
    elseif (empty($location)) $error = "Location is required.";

    if (!$error) {
        $stmt = $conn->prepare("
            UPDATE reports 
            SET name = ?, breed = ?, age = ?, gender = ?, size = ?, 
                location = ?, description = ?, status = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssssssssi", 
            $name, $breed, $age, $gender, $size, 
            $location, $description, $status, $dog_id
        );

        if ($stmt->execute()) {
            // SUCCESS → redirect to admin-dog-detail.php
            header("Location: admin-dog-detail.php?id=" . $dog_id);
            exit;
        } else {
            $error = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dog #<?php echo $dog_id; ?> - RescueTails Admin</title>
    <link rel="stylesheet" href="adopters.css" />
    <style>
        body { background:#f8f9fa; font-family:Arial,sans-serif; }
        .container { max-width:900px; margin:30px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 2px 15px rgba(0,0,0,0.1); }
        h1 { color:#2f6b4f; text-align:center; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#444; }
        .form-group input, .form-group select, .form-group textarea {
            width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; font-size:1rem;
        }
        .form-group textarea { min-height:120px; }
        .btn-save { background:#27ae60; color:white; border:none; padding:12px 30px; border-radius:8px; cursor:pointer; font-size:1.1rem; }
        .btn-save:hover { background:#219653; }
        .back { display:inline-block; margin-bottom:20px; color:#2f6b4f; font-weight:bold; text-decoration:none; }
        .error { background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin-bottom:20px; text-align:center; }
    </style>
</head>
<body>

<div class="container">
    <a href="rescued-dogs.php" class="back">← Back to Rescued Dogs</a>
    
    <h1>Edit Dog #<?php echo $dog_id; ?></h1>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Dog Name *</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($dog['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Breed</label>
            <input type="text" name="breed" value="<?php echo htmlspecialchars($dog['breed'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Age</label>
            <input type="text" name="age" value="<?php echo htmlspecialchars($dog['age'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender">
                <option value="">Select</option>
                <option value="male"   <?php echo ($dog['gender'] ?? '') === 'male'   ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($dog['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Size</label>
            <select name="size">
                <option value="">Select</option>
                <option value="small"       <?php echo ($dog['size'] ?? '') === 'small'       ? 'selected' : ''; ?>>Small</option>
                <option value="medium"      <?php echo ($dog['size'] ?? '') === 'medium'      ? 'selected' : ''; ?>>Medium</option>
                <option value="large"       <?php echo ($dog['size'] ?? '') === 'large'       ? 'selected' : ''; ?>>Large</option>
                <option value="extra large" <?php echo ($dog['size'] ?? '') === 'extra large' ? 'selected' : ''; ?>>Extra Large</option>
            </select>
        </div>

        <div class="form-group">
            <label>Location *</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($dog['location'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($dog['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Current Status</label>
            <select name="status">
                <option value="Rescued"             <?php echo $dog['status'] === 'Rescued'             ? 'selected' : ''; ?>>Rescued</option>
                <option value="Ready for adoption"  <?php echo $dog['status'] === 'Ready for adoption'  ? 'selected' : ''; ?>>Ready for adoption</option>
                <option value="Adopted"             <?php echo $dog['status'] === 'Adopted'             ? 'selected' : ''; ?>>Adopted</option>
            </select>
        </div>

        <button type="submit" class="btn-save">Save Changes</button>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>