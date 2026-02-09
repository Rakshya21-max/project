<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_for_adoption') {
    $update_id = intval($_POST['update_id'] ?? 0);
    
    if ($update_id > 0) {
        $name   = trim($_POST['name'] ?? '');
        $breed  = trim($_POST['breed'] ?? '');
        $age    = trim($_POST['age'] ?? '');
        $gender = $_POST['gender'] ?? null;
        $size   = $_POST['size'] ?? null;

        $stmt = $conn->prepare("
            UPDATE reports 
            SET name = ?, breed = ?, age = ?, gender = ?, size = ?, status = 'Ready for adoption'
            WHERE id = ?
        ");
        $stmt->bind_param("sssssi", $name, $breed, $age, $gender, $size, $update_id);
        
        if ($stmt->execute()) {
            // Optional: success message
            $_SESSION['success'] = "Dog added for adoption successfully!";
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
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
                    <a class="nav-item" href="adopters.php">Adopters</a>
                </nav>
            </aside>

                      <section class="content">
                <h2>Rescued Dogs</h2>

                <?php
                // Show success/error messages (from your POST handler)
                if (isset($_SESSION['success'])) {
                    echo '<div style="background:#d4edda; color:#155724; padding:12px; margin:20px 0; border-radius:6px;">'
                       . $_SESSION['success'] . '</div>';
                    unset($_SESSION['success']);
                }
                if (isset($_SESSION['error'])) {
                    echo '<div style="background:#f8d7da; color:#721c24; padding:12px; margin:20px 0; border-radius:6px;">'
                       . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>

               <div class="rescued-container">
    <?php
    // Show success/error messages from previous form submission
    if (isset($_SESSION['success'])) {
        echo '<div style="background:#d4edda; color:#155724; padding:12px; margin:20px 0; border-radius:6px; text-align:center;">'
           . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div style="background:#f8d7da; color:#721c24; padding:12px; margin:20px 0; border-radius:6px; text-align:center;">'
           . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
    ?>
            <div class="rescued-card">
                <?php if (!empty($row['picture'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['picture']); ?>" 
                         alt="Rescued dog" class="rescued-image">
                <?php endif; ?>

                <div class="rescued-info">
                    <div class="rescued-location">
                        <strong>Location:</strong> <?php echo htmlspecialchars($row['location'] ?? 'Unknown'); ?>
                    </div>
                    <div class="rescued-description">
                        <?php echo htmlspecialchars(substr($row['description'] ?? 'No description', 0, 120)) . '...'; ?>
                    </div>
                    <div class="rescued-email" style="margin-top:8px; font-size:0.9rem; color:#666;">
                        Reported by: <?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?>
                    </div>

                    <!-- FIXED FORM – only shows for 'Rescued' dogs -->
                    <?php if ($row['status'] === 'Rescued'): ?>
                        <div class="add-for-adoption-form" style="margin-top:20px; padding:15px; background:#f8fff8; border:1px solid #c3e6cb; border-radius:8px;">
                            <h4 style="margin:0 0 12px; color:#2f6b4f;">
                                Add <?php echo htmlspecialchars($row['name'] ?? 'this dog'); ?> for adoption
                            </h4>

                            <form method="post" action="">
                                <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="action" value="add_for_adoption">

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                                    <div>
                                        <label style="font-size:0.9rem; color:#555;">Name</label>
                                        <input type="text" name="name" 
                                               value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>" 
                                               placeholder="Give a name" 
                                               style="width:100%; padding:8px; margin-top:4px;">
                                    </div>
                                    
                                    <div>
                                        <label style="font-size:0.9rem; color:#555;">Breed</label>
                                        <input type="text" name="breed" 
                                               value="<?php echo htmlspecialchars($row['breed'] ?? ''); ?>" 
                                               placeholder="e.g. Indie Mix" 
                                               style="width:100%; padding:8px; margin-top:4px;">
                                    </div>
                                    
                                    <div>
                                        <label style="font-size:0.9rem; color:#555;">Age</label>
                                        <input type="text" name="age" 
                                               value="<?php echo htmlspecialchars($row['age'] ?? ''); ?>" 
                                               placeholder="e.g. 2 years" 
                                               style="width:100%; padding:8px; margin-top:4px;">
                                    </div>
                                    
                                    <div>
                                        <label style="font-size:0.9rem; color:#555;">Gender</label>
                                        <select name="gender" style="width:100%; padding:8px; margin-top:4px;">
                                            <option value="">Select</option>
                                            <option value="male"   <?php echo (isset($row['gender']) && $row['gender'] === 'male')   ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo (isset($row['gender']) && $row['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label style="font-size:0.9rem; color:#555;">Size</label>
                                        <select name="size" style="width:100%; padding:8px; margin-top:4px;">
                                            <option value="">Select</option>
                                            <option value="small"       <?php echo (isset($row['size']) && $row['size'] === 'small')       ? 'selected' : ''; ?>>Small</option>
                                            <option value="medium"      <?php echo (isset($row['size']) && $row['size'] === 'medium')      ? 'selected' : ''; ?>>Medium</option>
                                            <option value="large"       <?php echo (isset($row['size']) && $row['size'] === 'large')       ? 'selected' : ''; ?>>Large</option>
                                            <option value="extra large" <?php echo (isset($row['size']) && $row['size'] === 'extra large') ? 'selected' : ''; ?>>Extra Large</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" 
                                        style="margin-top:16px; background:#f57c00; color:white; border:none; padding:12px; width:100%; border-radius:6px; cursor:pointer; font-weight:600;">
                                    Save & Mark Ready for Adoption
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    <?php
        }
    } else {
    ?>
        <div class="no-dogs">
            No rescued dogs found at the moment.
        </div>
    <?php
    }
    ?>
</div>
            </section>
        </main>
    </div>

    <?php $conn->close(); ?>
</body>
</html>