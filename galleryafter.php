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
			<header class="site-header">
			<div class="container header-inner">
				<div class="logo">
					<div class="logo-circle">🐾</div>
					<span class="brand">RescueTails</span>
				</div>
			
				<nav class="main-nav">
					<a class="nav-link" href="landingafter.php">Home</a>
					<a class="nav-link active" href="#">Gallery</a>
					<a class="nav-link" href="aboutusafter.php">About Us</a>
					<a class="nav-link" href="ContactUsafter.php">Contact US</a>
				</nav>
				<div class="nav-actions profile-wrapper">
	<div class="profile-icon" onclick="toggleDropdown()">
		<img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile">
	</div>

	<div class="profile-dropdown" id="profileDropdown">
		<p class="profile-name">Hello, <?php echo htmlspecialchars($full_name); ?></p>
		<a href="profile.php">View Profile</a>
		<a href="change-email.php">Change Email</a>
		<a href="change-password.php">Change Password</a>
		<hr>
		<a href="logout.php" class="logout">Logout</a>
	</div>
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
  // Updated SQL – make sure to select the new fields
  $dogs_sql = "
      SELECT id, picture, name, breed, age, gender, size
      FROM reports 
      WHERE status = 'Ready for adoption' 
      ORDER BY id DESC
  ";
  $dogs_result = $conn->query($dogs_sql);

  if ($dogs_result && $dogs_result->num_rows > 0) {
      while ($dog = $dogs_result->fetch_assoc()) {
  ?>
          <article class="card" style="background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.08); transition: transform 0.2s;">
              <div class="card-media">
                  <div class="img-box" style="background-image: url('uploads/<?php echo htmlspecialchars($dog['picture']); ?>'); background-size: cover; background-position: center; height: 240px;"></div>
                  <button class="fav">♡</button>
              </div>

              <div class="card-body">
                  <h4 style="margin:0 0 10px; color:#2f6b4f; font-size:1.45rem;">
                      <?php echo htmlspecialchars($dog['name'] ?: 'Sweet Rescued Friend'); ?>
                  </h4>

                  <ul class="meta" style="display:flex; flex-wrap:wrap; gap:10px 18px; margin-bottom:14px; font-size:0.96rem; color:#444; list-style:none; padding:0;">
                      <?php if (!empty($dog['age'])): ?>
                          <li><strong>Age:</strong> <?php echo htmlspecialchars($dog['age']); ?></li>
                      <?php endif; ?>

                      <?php if (!empty($dog['breed'])): ?>
                          <li><strong>Breed:</strong> <?php echo htmlspecialchars($dog['breed']); ?></li>
                      <?php endif; ?>

                      <?php if (!empty($dog['gender'])): ?>
                          <li><strong>Gender:</strong> <?php echo ucfirst(htmlspecialchars($dog['gender'])); ?></li>
                      <?php endif; ?>

                      <?php if (!empty($dog['size'])): ?>
                          <li><strong>Size:</strong> <?php echo ucfirst(htmlspecialchars($dog['size'])); ?></li>
                      <?php endif; ?>
                  </ul>

                  <!--<p style="color:#555; font-size:0.95rem; line-height:1.55; margin-bottom:18px; min-height:60px;">
                      <?php 
                          $desc = $dog['description'] ?? 'No description available yet.';
                          echo htmlspecialchars(substr($desc, 0, 110)) . (strlen($desc) > 110 ? '...' : '');
                      ?> 
                  </p> -->

                  <div class="card-actions" style="display:flex; gap:12px;">
                      <a href="adopt.php?dog_id=<?php echo $dog['id']; ?>">
                          <button class="btn primary" style="background:#f57c00; color:white; border:none; padding:10px 18px; border-radius:6px; cursor:pointer;">
                              Adopt Me 🐾
                          </button>
                      </a>
                      <a href="user-dog-detail.php?id=<?php echo $dog['id']; ?>">
                          <button class="btn outline" style="background:transparent; border:1px solid #f57c00; color:#f57c00; padding:10px 18px; border-radius:6px; cursor:pointer;">
                              View Details
                          </button>
                      </a>
                  </div>
              </div>
          </article>
  <?php
      }
  } else {
  ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #666; font-size: 1.1rem;">
          <h3>No Dogs Available for Adoption</h3>
          <p>Check back later — new street friends are being rescued every day in Kathmandu! 🐶</p>
      </div>
  <?php
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
