<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name, profile_photo FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    session_destroy();
    header('Location: login.html');
    exit;
}
$full_name = $user['first_name'] . ' ' . $user['last_name'];
$profile_photo = $user['profile_photo'] ?? 'profile.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>My Adoption Applications - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="galleryafter.css"/> <!-- Reuse the same CSS for consistency -->
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <div class="logo">
                <div class="logo-circle">🐾</div>
                <span class="brand">RescueTails</span>
            </div>
        
            <nav class="main-nav">
                <a class="nav-link" href="landingafter.php">Home</a>
                <a class="nav-link" href="Gallery.php">Gallery</a>
                <a class="nav-link" href="aboutusafter.php">About Us</a>
                <a class="nav-link" href="contactusafter.php">Contact US</a>
            </nav>

            <div class="profile-wrapper">
                <img class="profile-photo" src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile" onclick="toggleDropdown()">
                <div class="profile-dropdown" id="profileDropdown">
                    <p class="profile-name"><?php echo htmlspecialchars($full_name); ?></p>
                    <a href="my_applications.php">My Applications</a>
                    <hr>
                    <a href="logout.php" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-compact">
            <div class="container">
                <h1>My Adoption Applications</h1>
                <p class="hero-sub">View your submitted adoption forms and their status</p>
            </div>
        </section>

        <section class="gallery container">
            <?php
            // Fetch user's applications with dog details
            $sql = "
                SELECT a.id, a.status, a.application_date, 
                       r.name, r.picture, r.breed, r.age, r.gender, r.size
                FROM adoption_applications a
                LEFT JOIN reports r ON a.report_id = r.id
                WHERE a.user_id = ?
                ORDER BY a.application_date DESC
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $num_applications = $result->num_rows;

            echo '<h3 style="text-align: center; margin: 20px 0;">You have submitted ' . $num_applications . ' adoption application(s)</h3>';

            if ($num_applications > 0) {
                echo '<div class="cards-grid">';
                while ($app = $result->fetch_assoc()) {
                    echo '<article class="card">';
                    echo '<div class="card-media">';
                    echo '<div class="img-box" style="background-image: url(\'uploads/' . htmlspecialchars($app['picture']) . '\'); background-size: cover; background-position: center;"></div>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h4>' . htmlspecialchars($app['name'] ?? 'Unnamed Dog') . '</h4>';
                    echo '<ul class="meta">';
                    echo '<li><strong>Age:</strong> ' . htmlspecialchars($app['age'] ?? 'Unknown') . '</li>';
                    echo '<li><strong>Breed:</strong> ' . htmlspecialchars($app['breed'] ?? 'Mixed') . '</li>';
                    echo '<li><strong>Size:</strong> ' . htmlspecialchars($app['size'] ?? 'Unknown') . '</li>';
                    echo '<li><strong>Gender:</strong> ' . htmlspecialchars($app['gender'] ?? 'Unknown') . '</li>';
                    echo '<li><strong>Status:</strong> ' . ucfirst(htmlspecialchars($app['status'])) . '</li>';
                    echo '<li><strong>Applied on:</strong> ' . date('Y-m-d', strtotime($app['application_date'])) . '</li>';
                    echo '</ul>';
                    echo '<div class="card-actions">';
                    echo '<button class="btn outline" disabled>View Application Details</button>'; // Placeholder, add if needed
                    echo '</div>';
                    echo '</div>';
                    echo '</article>';
                }
                echo '</div>';
            } else {
                echo '<div style="text-align: center; padding: 40px;">';
                echo '<h3>No Applications Submitted Yet</h3>';
                echo '<p>Visit the gallery to find a dog to adopt!</p>';
                echo '</div>';
            }

            $stmt->close();
            ?>
        </section>
    </main>

    <footer id="contact" class="site-footer">
        <div class="container footer-grid">
            <div class="footer-title">Contact Us Info</div>
            <div class="footer-columns">
                <div class="col">
                    <h5>Email</h5>
                    <p>@rescuetails.com</p>
                </div>
                <div class="col">
                    <h5>Phone No</h5>
                    <p>+977-9849727495</p>
                </div>
                <div class="col">
                    <h5>Location</h5>
                    <p>New Baneswor, santinagar</p>
                </div>
            </div>
        </div>
        <div class="site-copy">© 2026 Street Dog Adoption Services. All rights reserved.</div>
    </footer>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const wrapper = document.querySelector('.profile-wrapper');
            const dropdown = document.getElementById('profileDropdown');
            if (!wrapper.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>