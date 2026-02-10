<?php
session_start();
include 'db.php';



// Initialize variables at the top to avoid undefined warnings
$success = false;
$error   = '';

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
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2f6b4f; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px 0; }
        label { display: block; margin: 12px 0 6px; font-weight: bold; color: #333; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        textarea { height: 120px; }
        button { background: #2f6b4f; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; width: 100%; font-size: 1.1rem; }
        button:hover { background: #265c3f; }
        .back { display: block; text-align: center; margin-top: 20px; color: #2f6b4f; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h1>Report a Street Dog</h1>
    <p style="text-align:center; color:#555;">Help us rescue a street dog in Kathmandu</p>

    <?php if ($success): ?>
        <div class="success">
            <h2>Success!</h2>
            <p>The dog has been reported successfully.<br>
               Our rescue team will review it soon.</p>
            <a href="report.php" style="color:#2f6b4f;">Report Another Dog</a> | 
            <a href="galleryafter.php" style="color:#2f6b4f;">View Gallery</a>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <h2>Please fix these issues:</h2>
            <p><?= $error ?></p>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="location">Location where the dog was seen:</label>
        <input type="text" id="location" name="location" required value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">

        <label for="description">Description (condition, color, size estimate, behavior...):</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

        <label for="email">Your Email (for follow-up):</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="picture">Upload Photo of the Dog:</label>
        <input type="file" id="picture" name="picture" accept="image/*" required>

        <button type="submit">Submit Report</button>
    </form>

   
</div>

</body>
</html>