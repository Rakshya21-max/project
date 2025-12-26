<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="contactusafter.css" />
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
					<a class="nav-link" href="galleryafter.php">Gallery</a>
					<a class="nav-link" href="aboutusafter.php">About Us</a>
					<a class="nav-link active" href="#">Contact US</a>
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

    <div class="hero">
      <div class="hero-inner">
        <div class="hero-icon">💬</div>
        <h1>Contact Us</h1>
        <p class="hero-sub">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
      </div>
</div>

    <main class="content">
      <section class="message">
        <h2>Send us a message</h2>
        <p class="muted">Have a question or want to work together? We're here to help.</p>

        <form class="contact-form" action="#" method="post">
          <div class="form-wrap">
            <label>
              <span class="label">Name *</span>
              <input type="text" name="name" placeholder="Enter your name" required>
            </label>

            <label>
              <span class="label">Phone Number</span>
              <input type="tel" name="phone" placeholder="Enter your phone number">
            </label>

            <label>
              <span class="label">Email Address *</span>
              <input type="email" name="email" placeholder="Enter your email" required>
            </label>

            <label>
              <span class="label">Message</span>
              <textarea name="message" rows="5" placeholder="Tell us how we can help you..."></textarea>
            </label>

            <button type="submit" class="send-btn">✈ Send Message</button>
          </div>
        </form>
      </section>

      <section class="contact-info">
        <h3>Contact Information</h3>
        <p class="muted">Reach out to us through any of these channels.</p>

        <div class="info-box">
          <div><strong>Email</strong><div class="small">info@rescuetails.com</div></div>
          <div style="margin-top:12px"><strong>Phone No</strong><div class="small">+977-9849727495</div></div>
          <div style="margin-top:12px"><strong>Location</strong><div class="small">New Baneshwor, samlingar</div></div>
        </div>
      </section>
    </main>
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
