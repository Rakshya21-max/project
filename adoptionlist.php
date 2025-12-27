<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

// Only allow admin
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle adopt action
if (isset($_POST['adopt_id'])) {
    $adopt_id = intval($_POST['adopt_id']);
    $stmt = $conn->prepare("UPDATE reports SET status='Adopted' WHERE id=?");
    $stmt->bind_param("i", $adopt_id);
    $stmt->execute();
    $stmt->close();
    header("Location: adoptionlist.php"); // Refresh the page
    exit;
}

// Fetch all reported dogs
$sql = "SELECT id, picture, location, description, email, status FROM reports ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Adoption List</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    margin: 0;
    padding: 20px;
}
h1 {
    text-align: center;
    margin-bottom: 30px;
}
.adoption-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}
.pet-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    width: 250px;
    text-align: center;
    padding: 15px;
    transition: transform 0.2s;
}
.pet-card:hover {
    transform: scale(1.05);
}
.pet-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}
.pet-card h3 {
    margin: 10px 0 5px;
}
.pet-card p {
    margin: 5px 0;
}
.adopt-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}
.adopt-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
</head>
<body>

<h1>Adoption List</h1>
<div class="adoption-container">
<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="pet-card">';
        echo '<img src="uploads/' . htmlspecialchars($row['picture']) . '" alt="Dog Image">';
        echo '<h3>' . htmlspecialchars($row['location']) . '</h3>';
        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
        echo '<p><strong>Reported by:</strong> ' . htmlspecialchars($row['email']) . '</p>';
        if ($row['status'] === 'Available') {
            echo '<form method="POST" style="margin-top:10px;">
                    <input type="hidden" name="adopt_id" value="' . $row['id'] . '">
                    <button type="submit" class="adopt-btn">Mark as Adopted</button>
                  </form>';
        } else {
            echo '<button class="adopt-btn" disabled>Adopted</button>';
        }
        echo '</div>';
    }
} else {
    echo "<p>No reports available.</p>";
}
?>
</div>

</body>
</html>

<?php $conn->close(); ?>
