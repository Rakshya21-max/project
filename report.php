<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $location = trim($_POST["location"]);
    $description = trim($_POST["description"]);
    $email = trim($_POST["email"]);

    if (
        empty($location) ||
        empty($description) ||
        empty($email) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        empty($_FILES["picture"]["name"])
    ) {
        die("Invalid input");
    }

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = time() . "_" . basename($_FILES["picture"]["name"]);
    $targetFile = $uploadDir . $imageName;

    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    $imageType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (!in_array($imageType, $allowedTypes)) {
        die("Invalid image type");
    }

    if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
        die("Image upload failed");
    }

    $stmt = $conn->prepare(
        "INSERT INTO reports (picture, location, description, email, status)
         VALUES (?, ?, ?, ?, 'Pending')"
    );
    $stmt->bind_param("ssss", $imageName, $location, $description, $email);

    if ($stmt->execute()) {
        echo "<script>
                alert('Dog reported successfully!');
                window.location.href='report.html';
              </script>";
    } else {
        echo "<script>alert('Error saving report');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
