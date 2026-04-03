<?php
session_start();
include 'db.php';

// Initialize variables
$success = false;
$error   = '';

// Get the previous page safely (fallback logic)
$previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'landingafter.php';

// Ensure we only allow safe previous pages
$allowed_pages = ['landingpage.php', 'landingafter.php', 'galleryafter.php', 'aboutusafter.php'];
$go_back_url = in_array(basename($previous_page), $allowed_pages) ? $previous_page : 'landingafter.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $location    = trim($_POST["location"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $email       = trim($_POST["email"] ?? '');

    $errors = [];

    if (empty($location))      $errors[] = "Location is required.";
    if (empty($description))   $errors[] = "Description is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($_FILES["picture"]["name"])) {
        $errors[] = "Picture is required.";
    }

    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    } else {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '_' . basename($_FILES["picture"]["name"]);
        $target   = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target)) {
            $stmt = $conn->prepare(
                "INSERT INTO reports (picture, location, description, email, status)
                 VALUES (?, ?, ?, ?, 'Pending')"
            );
            $stmt->bind_param("ssss", $fileName, $location, $description, $email);

            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Failed to upload image. Check folder permissions.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Street Dog - RescueTails</title>
    <style>
        body { 
            font-family: 'Poppins', Arial, sans-serif; 
            background: #f8f9fa; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            max-width: 620px; 
            margin: 40px auto; 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }
        h1 { 
            text-align: center; 
            color: #2f6b4f; 
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            padding: 25px; 
            border-radius: 12px; 
            text-align: center; 
            margin: 20px 0; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 20px; 
            border-radius: 12px; 
            margin: 20px 0; 
        }
        label { 
            display: block; 
            margin: 18px 0 8px; 
            font-weight: 600; 
            color: #333; 
        }
        input, textarea { 
            width: 100%; 
            padding: 14px; 
            margin-bottom: 20px; 
            border: 1px solid #ccc; 
            border-radius: 10px; 
            box-sizing: border-box; 
            font-size: 16px;
        }
        textarea { height: 130px; }
        button { 
            background: #2f6b4f; 
            color: white; 
            padding: 14px 24px; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 17px; 
            font-weight: 600;
        }
        button:hover { background: #265c3f; }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: #2f6b4f;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 25px;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #265c3f;
            transform: translateX(-4px);
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Back Button -->
    <a href="<?php echo htmlspecialchars($go_back_url); ?>" class="back-btn">
        ← Back to Previous Page
    </a>

    <h1>Report a Street Dog</h1>
    <p class="subtitle">Help us rescue a street dog in Kathmandu</p>

    <?php if ($success): ?>
        <div class="success">
            <h2>✅ Report Submitted Successfully!</h2>
            <p>Our rescue team will review it soon and contact you if needed.</p>
            <a href="report.php" style="color:#2f6b4f; font-weight:600;">Report Another Dog</a>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <h2>Please fix these issues:</h2>
            <p><?= $error ?></p>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="post" enctype="multipart/form-data">
        <label for="location">Location where the dog was seen:</label>
        <input type="text" id="location" name="location" required value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">

        <label for="description">Description (condition, color, size, behavior...):</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

        <label for="email">Your Email (for follow-up):</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="picture">Upload Photo of the Dog:</label>
        <input type="file" id="picture" name="picture" accept="image/*" required>

        <button type="submit">Submit Report</button>
    </form>
    <?php endif; ?>

</div>

</body>
</html>