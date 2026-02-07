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

        <form class="contact-form" id="contactForm" action="#" method="post">
          <div class="form-wrap">
            <label>
              <span class="label">Name *</span>
              <input type="text" name="name" id="name" placeholder="Enter your name" required minlength="2">
              <span class="error" id="err_name"></span>
            </label>

            <label>
              <span class="label">Phone Number</span>
              <input type="tel" name="phone" id="phone" placeholder="Enter your phone number">
              <span class="error" id="err_phone"></span>
            </label>

            <label>
              <span class="label">Email Address *</span>
              <input type="email" name="email" id="email" placeholder="Enter your email" required>
              <span class="error" id="err_email"></span>
            </label>

            <label>
              <span class="label">Message *</span>
              <textarea name="message" id="message" rows="5" placeholder="Tell us how we can help you..." required minlength="10"></textarea>
              <span class="error" id="err_message"></span>
            </label>

            <button type="submit" class="send-btn">✈ Send Message</button>
          </div>
        </form>

        <div id="successMessage" style="display:none; margin-top: 20px;">
          <div style="background-color: #4CAF50; color: white; padding: 20px; border-radius: 8px; text-align: center;">
            <h3 style="margin: 0 0 10px 0;">✓ Message Sent Successfully!</h3>
            <p style="margin: 10px 0;">Thank you for contacting us. We'll get back to you soon.</p>
          </div>
        </div>
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

			// Form Validation
			const contactForm = document.getElementById('contactForm');
			const successMessage = document.getElementById('successMessage');

			contactForm.addEventListener('submit', function(event) {
				event.preventDefault();

				let isValid = true;

				// Get form values
				const name = document.getElementById('name').value.trim();
				const phone = document.getElementById('phone').value.trim();
				const email = document.getElementById('email').value.trim();
				const message = document.getElementById('message').value.trim();

				// Clear previous errors
				document.getElementById('err_name').textContent = '';
				document.getElementById('err_phone').textContent = '';
				document.getElementById('err_email').textContent = '';
				document.getElementById('err_message').textContent = '';

				// Validate Name
				if (name === '') {
					document.getElementById('err_name').textContent = 'Name is required';
					isValid = false;
				} else if (name.length < 2) {
					document.getElementById('err_name').textContent = 'Name must be at least 2 characters';
					isValid = false;
				} else if (!/^[a-zA-Z\s'-]+$/.test(name)) {
					document.getElementById('err_name').textContent = 'Name can only contain letters, spaces, hyphens, and apostrophes';
					isValid = false;
				}

				// Validate Phone (if provided)
				if (phone !== '') {
					const phoneRegex = /^[+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/;
					if (!phoneRegex.test(phone)) {
						document.getElementById('err_phone').textContent = 'Please enter a valid phone number';
						isValid = false;
					}
				}

				// Validate Email
				if (email === '') {
					document.getElementById('err_email').textContent = 'Email is required';
					isValid = false;
				} else {
					const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
					if (!emailRegex.test(email)) {
						document.getElementById('err_email').textContent = 'Please enter a valid email address';
						isValid = false;
					}
				}

				// Validate Message
				if (message === '') {
					document.getElementById('err_message').textContent = 'Message is required';
					isValid = false;
				} else if (message.length < 10) {
					document.getElementById('err_message').textContent = 'Message must be at least 10 characters';
					isValid = false;
				}

				// If form is valid, show success message
				if (isValid) {
					contactForm.style.display = 'none';
					successMessage.style.display = 'block';

					// Optional: Send data to server
					const formData = new FormData(contactForm);
					fetch(window.location.href, {
						method: 'POST',
						body: formData
					}).catch(err => console.log('Message sent'));

					// Reset form after 3 seconds
					setTimeout(() => {
						contactForm.reset();
						contactForm.style.display = 'block';
						successMessage.style.display = 'none';
					}, 3000);
				}
			});

			// Add input event listeners for real-time validation
			document.getElementById('name').addEventListener('blur', function() {
				const name = this.value.trim();
				if (name === '') {
					document.getElementById('err_name').textContent = 'Name is required';
				} else if (name.length < 2) {
					document.getElementById('err_name').textContent = 'Name must be at least 2 characters';
				} else if (!/^[a-zA-Z\s'-]+$/.test(name)) {
					document.getElementById('err_name').textContent = 'Name can only contain letters, spaces, hyphens, and apostrophes';
				} else {
					document.getElementById('err_name').textContent = '';
				}
			});

			document.getElementById('email').addEventListener('blur', function() {
				const email = this.value.trim();
				const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				if (email === '') {
					document.getElementById('err_email').textContent = 'Email is required';
				} else if (!emailRegex.test(email)) {
					document.getElementById('err_email').textContent = 'Please enter a valid email address';
				} else {
					document.getElementById('err_email').textContent = '';
				}
			});

			document.getElementById('phone').addEventListener('blur', function() {
				const phone = this.value.trim();
				if (phone !== '') {
					const phoneRegex = /^[+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/;
					if (!phoneRegex.test(phone)) {
						document.getElementById('err_phone').textContent = 'Please enter a valid phone number';
					} else {
						document.getElementById('err_phone').textContent = '';
					}
				}
			});

			document.getElementById('message').addEventListener('blur', function() {
				const message = this.value.trim();
				if (message === '') {
					document.getElementById('err_message').textContent = 'Message is required';
				} else if (message.length < 10) {
					document.getElementById('err_message').textContent = 'Message must be at least 10 characters';
				} else {
					document.getElementById('err_message').textContent = '';
				}
			});
		</script>
  </body>
</html>
