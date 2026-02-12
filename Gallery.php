<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Gallery - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="galleryafter.css"/>
  </head>
  <body>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php';
/*
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
*/
?>

			<header class="site-header">
			<div class="container header-inner">
				<div class="logo">
					<div class="logo-circle">🐾</div>
					<span class="brand">RescueTails</span>
				</div>
			
				<nav class="main-nav">
					<a class="nav-link" href="landingpage.php">Home</a>
					<a class="nav-link active" href="#">Gallery</a>
					<a class="nav-link" href="about us.html">About Us</a>
					<a class="nav-link" href="ContactUs.html">Contact US</a>
				</nav>
				<div class="nav-actions">
					<a href="login.html">
						<button class="btn outline">Login</button>
					</a>
				</div>

			</div>
		</header>

    <main>
      <section class="hero-compact">
        <div class="container">
          <h1>Street Dog Adoption Services</h1>
          <p class="hero-sub">Give a loving home to a street dog in need</p>
          

          <div class="filters">
            <input class="search" type="search" placeholder="Search by name or breeds..." />
            
          </div>
        </div>
      </section>

      <section class="info-blocks">
        <div class="container">
          <div class="info-grid">
            <div class="info-card">
              <div class="icon">♡</div>
              <h3>Rescued with Love</h3>
              <p>Every dog is given medical care and rehabilitation</p>
            </div>
            <div class="info-card">
              <div class="icon">☎</div>
              <h3>24/7 Support</h3>
              <p>Get help with adoption process anytime</p>
            </div>
          </div>
        </div>
      </section>

      <section class="gallery container">
        <div class="cards-grid">
          <?php
          // Fetch dogs available for adoption
         $dogs_sql = "
    SELECT id, picture, name, breed, age, gender, size, status
    FROM reports 
    WHERE status IN ('Ready for adoption', 'In Adoption Process')
    ORDER BY id DESC
";
          $dogs_result = $conn->query($dogs_sql);

          if ($dogs_result && $dogs_result->num_rows > 0) {
            while ($dog = $dogs_result->fetch_assoc()) {
              echo '<article class="card">';
              echo '<div class="card-media">';
              echo '<div class="img-box" style="background-image: url(\'uploads/' . htmlspecialchars($dog['picture']) . '\'); background-size: cover; background-position: center;"></div>';
              echo '<button class="fav">♡</button>';
              echo '</div>';
              echo '<div class="card-body">';
              echo '<h4>' . htmlspecialchars($dog['name'] ?? $dog['location']) . '</h4>';
              echo '<ul class="meta">';
              echo '<li><strong>Age:</strong> ' . htmlspecialchars($dog['age'] ?? 'Unknown') . '</li>';
              echo '<li><strong>Breed:</strong> ' . htmlspecialchars($dog['breed'] ?? 'Mixed') . '</li>';
              echo '<li><strong>Size:</strong> ' . htmlspecialchars($dog['size'] ?? 'Unknown') . '</li>';
              echo '<li><strong>Gender:</strong> ' . htmlspecialchars($dog['gender'] ?? 'Unknown') . '</li>';
              echo '</ul>';
              echo '<div class="card-actions">';
              echo '<a href="adopt.php?dog_id=' . $dog['id'] . '"><button class="btn primary">Adopt Me</button></a>';
              echo '<a href="user-dog-detail.php?dog_id=' . $dog['id'] . '"><button class="btn outline">View Details</button></a>';
              echo '</div>';
              echo '</div>';
              echo '</article>';
            }
          } else {
            echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px;">';
            echo '<h3>No Dogs Available for Adoption</h3>';
            echo '<p>Check back later for new dogs ready for adoption!</p>';
            echo '</div>';
          }
          ?>
        </div>
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

			// Search functionality
			document.addEventListener('DOMContentLoaded', function() {
				const searchInput = document.querySelector('.search');
				searchInput.addEventListener('input', function() {
					const query = this.value.toLowerCase();
					const cards = document.querySelectorAll('.card');
					cards.forEach(card => {
						const name = card.querySelector('h4').textContent.toLowerCase();
						const breed = card.querySelector('.meta li:nth-child(2)').textContent.toLowerCase().replace('breed:', '');
						if (name.includes(query) || breed.includes(query)) {
							card.style.display = '';
						} else {
							card.style.display = 'none';
						}
					});
				});
			});
		</script>
  </body>
</html>
