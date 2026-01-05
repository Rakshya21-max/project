<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
$home_link = $_SERVER['HTTP_REFERER'] ?? 'landingafter.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $dog_id = intval($_POST['dog_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($dog_id <= 0) {
        echo "<script>alert('Please select a dog to adopt.'); window.history.back();</script>";
        exit;
    }

    // Check if user already applied for this dog
    $check_sql = "SELECT id FROM adoption_applications WHERE dog_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $dog_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('You have already applied for this dog.'); window.location.href='galleryafter.php';</script>";
        exit;
    }

    // Insert application
    $stmt = $conn->prepare("INSERT INTO adoption_applications (dog_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $dog_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Your adoption application has been submitted successfully! We will contact you soon.'); window.location.href='galleryafter.php';</script>";
    } else {
        echo "<script>alert('Error submitting application. Please try again.'); window.history.back();</script>";
    }

    $check_stmt->close();
    $stmt->close();
    $conn->close();
    exit;
}

// If no dog_id provided, show dog selection
$show_dog_selection = !isset($_GET['dog_id']) || intval($_GET['dog_id']) <= 0;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Adopt a dog</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Adopt.css">
    <style>
      .dog-selection {
        padding: 20px;
        text-align: center;
      }
      .dogs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
      }
      .dog-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.2s;
      }
      .dog-card:hover {
        transform: translateY(-5px);
      }
      .dog-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
      }
      .dog-info {
        padding: 15px;
      }
      .dog-info h4 {
        margin: 0 0 10px 0;
        color: #333;
      }
      .dog-info p {
        margin: 0 0 15px 0;
        color: #666;
        font-size: 14px;
        line-height: 1.4;
      }
      .select-dog-btn {
        display: inline-block;
        padding: 8px 16px;
        background: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background 0.2s;
      }
      .select-dog-btn:hover {
        background: #218838;
      }
      .confirmation-message {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
      }
      .back-btn {
        display: inline-block;
        margin-left: 10px;
        padding: 8px 16px;
        background: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
      }
      .back-btn:hover {
        background: #5a6268;
      }
    </style>
  </head>
  <body>
    <main class="page">
      <article class="adopt-card">
        <a href="<?php echo $home_link; ?>">
          <button class="back" aria-label="back">←</button>
        </a>
        <div class="card-top">
          <div class="logo">🐾</div>
          <div class="titles">
            <h1>Adopt a dog</h1>
            <p class="subtitle">Help to find a shelter for paws</p>
          </div>
        </div>

        <?php if ($show_dog_selection): ?>
        <!-- Dog Selection Section -->
        <div class="dog-selection">
          <h3>Choose a Dog to Adopt</h3>
          <p>Select from our available dogs ready for adoption:</p>
          <?php
          include 'db.php';
          $dogs_sql = "SELECT id, picture, location, description FROM reports WHERE status = 'Ready for adoption' ORDER BY id DESC";
          $dogs_result = $conn->query($dogs_sql);

          if ($dogs_result && $dogs_result->num_rows > 0):
          ?>
          <div class="dogs-grid">
            <?php while ($dog = $dogs_result->fetch_assoc()): ?>
            <div class="dog-card">
              <img src="uploads/<?php echo htmlspecialchars($dog['picture']); ?>" alt="Dog" class="dog-image">
              <div class="dog-info">
                <h4><?php echo htmlspecialchars($dog['location']); ?></h4>
                <p><?php echo htmlspecialchars(substr($dog['description'], 0, 100)); ?>...</p>
                <a href="?dog_id=<?php echo $dog['id']; ?>" class="select-dog-btn">Apply to Adopt</a>
              </div>
            </div>
            <?php endwhile; ?>
          </div>
          <?php else: ?>
          <p>No dogs are currently available for adoption.</p>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Confirmation Form -->
        <form class="adopt-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
          <input type="hidden" name="dog_id" value="<?php echo intval($_GET['dog_id']); ?>">

          <div class="confirmation-message">
            <h3>Confirm Your Adoption Application</h3>
            <p>Are you sure you want to apply for adopting this dog?</p>
            <p><strong>Note:</strong> Your contact information will be shared with the NGO for verification purposes.</p>
          </div>

          <div class="submit-wrap">
            <button class="login2" type="submit">Yes, Submit Application</button>
            <a href="adopt.php" class="back-btn">Choose Different Dog</a>
          </div>
        </form>
        <?php endif; ?>
      </article>
    </main>
  </body>
</html>
