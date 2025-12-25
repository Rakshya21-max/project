<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Gallery - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="galleryafter.css" />
  </head>
  <body>
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
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
$user['profile_photo'] = null; // Placeholder until column is added
?>
		<header class="site-header">
			<div class="container header-inner">
				<div class="logo">
					<div class="logo-circle">🐾</div>
					<span class="brand">RescueTails</span>
				</div>
				<nav class="main-nav">
					<a class="nav-link" href="landingafter.php">Home</a>
					<a class="nav-link" href="#">Gallery</a>
					<a class="nav-link" href="aboutusafter.html">About Us</a>
					<a class="nav-link" href="ContactUsafter.html">Contact US</a>
				</nav>
				<div class="nav-actions profile-wrapper">
	<div class="profile-icon" onclick="toggleDropdown()">
		<img src="profile.jpg" alt="Profile">
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
          <!-- Card (repeat) -->
          <article class="card">
            <div class="card-media">
              <div class="img-box"></div>
              <button class="fav">♡</button>
            </div>
            <div class="card-body">
              <h4>Bella</h4>
              <ul class="meta">
                <li><strong>Age:</strong> 7 months</li>
                <li><strong>Breed:</strong> Beagle Mix</li>
                <li><strong>Size:</strong> small</li>
              </ul>
              <div class="card-actions">
                <a href="login.html"><button class="btn primary">Adopt Me</button></a>
                <a href="Detail.html"><button class="btn outline">View Details</button></a>
              </div>
            </div>
          </article>

          <article class="card">
            <div class="card-media">
              <div class="img-box"></div>
              <button class="fav">♡</button>
            </div>
            <div class="card-body">
              <h4>Charlie</h4>
              <ul class="meta">
                <li><strong>Age:</strong> 9 months</li>
                <li><strong>Breed:</strong> Labrador Mix</li>
                <li><strong>Size:</strong> small</li>
              </ul>
              <div class="card-actions">
                <a href="adopt.html">
                  <button class="btn primary">Adopt Me</button>
                </a>
               <a href="Details.html">
                  <button class="btn outline">View Details</button>
                </a>
              </div>
            </div>
          </article>

          <article class="card">
            <div class="card-media">
              <div class="img-box"></div>
              <button class="fav">♡</button>
            </div>
            <div class="card-body">
              <h4>Rocky</h4>
              <ul class="meta">
                <li><strong>Age:</strong> 1 year</li>
                <li><strong>Breed:</strong> Beagle Mix</li>
                <li><strong>Size:</strong> small</li>
              </ul>
              <div class="card-actions">
                <button class="btn primary">Adopt Me</button>
                <button class="btn outline">View Details</button>
              </div>
            </div>
          </article>

          <article class="card">
            <div class="card-media">
              <div class="img-box muted"></div>
              <button class="fav">♡</button>
            </div>
            <div class="card-body">
              <h4>John</h4>
              <ul class="meta">
                <li><strong>Age:</strong> 1.5 years</li>
                <li><strong>Breed:</strong> Beagle Mix</li>
                <li><strong>Size:</strong> small</li>
              </ul>
              <div class="card-actions">
                <button class="btn outline">View success story</button>
              </div>
            </div>
          </article>

          <!-- more cards (same structure) -->
          <article class="card">
            <div class="card-media"><div class="img-box"></div><button class="fav">♡</button></div>
            <div class="card-body"><h4>Seru</h4><ul class="meta"><li><strong>Age:</strong> 6 months</li><li><strong>Breed:</strong> Beagle Mix</li><li><strong>Size:</strong> small</li></ul><div class="card-actions"><button class="btn outline">View success story</button></div></div>
          </article>

          <article class="card">
            <div class="card-media"><div class="img-box"></div><button class="fav">♡</button></div>
            <div class="card-body"><h4>Luna</h4><ul class="meta"><li><strong>Age:</strong> 11 months</li><li><strong>Breed:</strong> Beagle Mix</li><li><strong>Size:</strong> small</li></ul><div class="card-actions"><button class="btn primary">Adopt Me</button><button class="btn outline">View Details</button></div></div>
          </article>

          <article class="card">
            <div class="card-media"><div class="img-box"></div><button class="fav">♡</button></div>
            <div class="card-body"><h4>Jerry</h4><ul class="meta"><li><strong>Age:</strong> 10 months</li><li><strong>Breed:</strong> Beagle Mix</li><li><strong>Size:</strong> small</li></ul><div class="card-actions"><button class="btn primary">Adopt Me</button><button class="btn outline">View Details</button></div></div>
          </article>

          <article class="card">
            <div class="card-media"><div class="img-box muted"></div><button class="fav">♡</button></div>
            <div class="card-body"><h4>Buddy</h4><ul class="meta"><li><strong>Age:</strong> 1 year</li><li><strong>Breed:</strong> Beagle Mix</li><li><strong>Size:</strong> small</li></ul><div class="card-actions"><button class="btn outline">View success story</button></div></div>
          </article>

        </div>
      </section>
    </main>

    <footer id="contact" class="site-footer">
      <div class="container footer-inner">
        <div>Contact Us Info</div>
        <div class="footer-columns">
          <div><h5>Email</h5><p>@rescuetails.com</p></div>
          <div><h5>Phone No</h5><p>+977-9849727495</p></div>
          <div><h5>Location</h5><p>New Baneswor, santinagar</p></div>
        </div>
      </div>
      <div class="site-copy">© 2024 Street Dog Adoption Services. All rights reserved.</div>
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
