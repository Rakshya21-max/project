<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="landingafter.css" />
		<title>Street Dog Adoption Services</title>
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
					<a class="nav-link active" href="#">Home</a>
					<a class="nav-link" href="Galleryafter.php">Gallery</a>
					<a class="nav-link" href="aboutusafter.php">About Us</a>
					<a class="nav-link" href="ContactUsafter.php">Contact US</a>
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
			<!-- HERO -->
			<section class="hero">
				<div class="container hero-grid">
					<div class="hero-left">
						<h1 class="hero-title">Street Dog<br />Adoption <span class="accent">Services</span></h1>
						<p class="hero-sub">Behind every pair of hopeful eyes is a dog waiting for love. At RescueTails, we connect abandoned paws with caring hearts, creating forever bonds that transform lives — both theirs and yours.</p>
							<div class="hero-ctas">
							<a href="Report.html">
							<button class="btn primary">Report</button>
							</a>
							
						</div>
					</div>

					<div class="hero-right">
								

								<div class="hero-circle">
									<img src="1.jpg" alt="photo">
									<div class="img-box img-circle" aria-label="image placeholder" ></div>
								</div>
					</div>
				</div>
			</section>

			<!-- GREEN BAND WITH 3 PHOTOS -->
			<section class="green-band">
				<div class="container grid-3">
					<div class="green-text">RescueTails is where compassion meets action. Together, we give street dogs the forever homes they truly deserve.</div>
					<div class="photo-row">
						<div class="img-box img-square" aria-label="image placeholder"><img src="photos/2.jpg" alt="photo"></div>
						<div class="img-box img-square" aria-label="image placeholder"><img src="photos/3.jpg" alt="photo"></div>
						<div class="img-box img-square" aria-label="image placeholder"><img src="photos/4.jpg" alt="photo"></div>
					</div>
				</div>
			</section>

			<!-- WHAT WE DO / WHO WE ARE -->
			<section class="info-section container two-col">
				<div class="what">
					<h3>What we do ?</h3>
					<p class="muted">RescueTails is a space where every street dog finds hope and care. We are driven by love, kindness, and the belief that no tail should be left behind. Our mission is to create lasting bonds between dogs and their forever families.</p>
					<ul class="list">
						<li>Rescue & shelter</li>
						<li>Heal & protect</li>
						<li>Connect through adoption</li>
					</ul>
				</div>
				<aside class="who">
					<h3>Who We Are?</h3>
					<ul class="list-small">
						<li>★ Compassion in action</li>
						<li>★ Voice for strays</li>
						<li>★ Hope for paws</li>
					</ul>
					<a href="Donationafter.php">
					<button class="btn primary">Donate</button>
					</a>
				</aside>
			</section>

			<!-- FEATURED RESCUES -->
			<section class="featured">
				<div class="container">
					<h2 class="section-title">Featured Rescues</h2>
					<p class="section-sub">Meet some of our amazing dogs who are looking for their forever homes</p>
							<div class="cards-row">
								<article class="rescue-card">
									<div class="rescue-img circle"><div class="img-box img-circle" aria-label="image placeholder"><img src="photos/1.jpg" alt="photo"></div></div>
									<h4>Luna</h4>
									<p class="small">3 years old • Mixed Breed • Gentle</p>
								</article>
								<article class="rescue-card">
									<div class="rescue-img circle"><div class="img-box img-circle" aria-label="image placeholder"><img src="photos/2.jpg" alt="photo"></div></div>
									<h4>Buddy</h4>
									<p class="small">2 years old • Mixed Breed • Friendly</p>
								</article>
								<article class="rescue-card">
									<div class="rescue-img circle"><div class="img-box img-circle" aria-label="image placeholder"><img src="photos/3.jpg" alt="photo"></div></div>
									<h4>Max</h4>
									<p class="small">1 year old • Energetic • Playful</p>
								</article>
								<article class="rescue-card">
									<div class="rescue-img circle"><div class="img-box img-circle" aria-label="image placeholder"><img src="photos/4.jpg" alt="photo"></div></div>
									<h4>Jerry</h4>
									<p class="small">1 year old • Energetic • Loves walks</p>
								</article>
							</div>
				</div>
			</section>

			<!-- AVAILABLE FOR ADOPTION -->
			<section class="available container">
				<h2>Available for Adoption</h2>
				<p class="muted center">These lovely dogs are waiting for their forever homes. Each one has been rescued, health-checked, and is ready to bring joy to your family.</p>
					<div class="adoption-row">
						<div class="adopt-card">
							<div class="img-box img-rect" aria-label="image placeholder"><img src="photos/5.jpg" alt="photo"></div>
							<div class="adopt-body">
								<h4>Bella</h4>
								<p class="small">7 months • Beagle Mix</p>
								<button class="btn primary">Meet Bella</button>
							</div>
						</div>
						<div class="adopt-card">
							<div class="img-box img-rect" aria-label="image placeholder"><img src="photos/6.jpg" alt="photo"></div>
							<div class="adopt-body">
								<h4>Charlie</h4>
								<p class="small">5 months • Labrador Mix</p>
								<button class="btn primary">Meet Charlie</button>
							</div>
						</div>
						<div class="adopt-card">
							<div class="img-box img-rect" aria-label="image placeholder"><img src="photos/7.jpg" alt="photo"></div>
							<div class="adopt-body">
								<h4>Rocky</h4>
								<p class="small">7 months • Husky Mix</p>
								<button class="btn primary">Meet Rocky</button>
							</div>
						</div>
					</div>
				<div class="view-all">
					<a href="galleryafter.php">
					<button class="btn outline">View all dogs →</button>
					</a>
				</div>
			</section>

			<!-- CTA + Footer area -->
			<section class="final-cta container cta-row">
				<div class="cta-left">
					<h3>Find me Paw?</h3>
					<p class="muted">Looking for your perfect companion? Browse through our rescued dogs and find the one that matches your lifestyle and heart. • Health checked and vaccinated • Behaviorally assessed • Ready for loving home</p>
					<a href="adopt.php">
					<button class="btn primary">Adopt Dogs</button>
					</a>
				</div>
				<div class="cta-right">
					<div class="img-box img-portrait cta-dog" aria-label="image placeholder"><img src="photos/9.jpg" alt="photo"></div>
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
		</script>
		
	</body>
</html>

