<!-- Save as: C:\Users\lenevo\Desktop\ResuceTails\HTML\about us.html -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>RescueTails — About Us</title>
    <link rel="stylesheet" href="aboutusafter.css" />
</head>
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
					<a class="nav-link active" href="#">About Us</a>
					<a class="nav-link" href="contactusafter.php">Contact US</a>
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
        <!-- Hero -->
        <section class="hero">
            <div class="container">
                <h1>Saving Lives, One Paw at a Time</h1>
                <p>
                    We're a dedicated team of animal lovers committed to rescuing street dogs and connecting them with loving families who will give them the forever homes they deserve.
                </p>
            </div>
        </section>

        <!-- Stats -->
        <section class="stats">
            <div class="container stats-grid">
                <div class="stat">
                    <div class="icon">🐶</div>
                    <div class="num"></div>
                    <div class="label">Dogs Rescued</div>
                </div>
                <div class="stat">
                    <div class="icon">🏡</div>
                    <div class="num"></div>
                    <div class="label">Successful Adoptions</div>
                </div>
                <div class="stat">
                    <div class="icon">🤝</div>
                    <div class="num"></div>
                    <div class="label">Active Volunteers</div>
                </div>
                <div class="stat">
                    <div class="icon">⭐</div>
                    <div class="num"></div>
                    <div class="label">Years of Service</div>
                </div>
            </div>
        </section>

        <!-- Mission -->
        <section class="mission">
            <div class="container mission-grid">
                <div class="mission-card">
                    <span class="pill">Our Mission</span>
                    <h2>Creating a World Where Every Dog Has a Home</h2>
                    <p>
                        Founded in 2026, RescueTails emerged from a simple belief: every street dog deserves a chance at a better life. What started as a small group of volunteers has grown into a comprehensive rescue organization that has transformed the lives of thousands of dogs and families.
                    </p>
                    <p>
                        We work tirelessly to rescue abandoned and stray dogs from the streets, provide medical care, rehabilitation, and rehoming services—helping them find loving forever homes where they can thrive.
                    </p>
                    <a class="btn primary" href="Learnmore.html">Learn more about our work</a>
                </div>

                <div class="mission-image">
                    <!-- Replace with actual image at images/rottweiler.jpg -->
                    <img src="images/dog.jpg" alt="Happy rescued dog" />
                </div>
            </div>
        </section>

        <!-- Values -->
        <section class="values">
            <div class="container">
                <div class="values-header">
                    <span class="pill light">Our Values</span>
                    <h3>What Drives Our Mission</h3>
                    <p>Our core values guide every decision we make and every action we take in our mission to help street dogs find their forever homes.</p>
                </div>

                <div class="values-grid">
                    <div class="value-card">
                        <div class="v-icon">🤝</div>
                        <h4>Community Driven</h4>
                        <p>Building strong partnerships with volunteers, adopters, and local organizations to increase impact.</p>
                    </div>

                    <div class="value-card">
                        <div class="v-icon">🏆</div>
                        <h4>Excellence</h4>
                        <p>Committed to the highest standards in animal care and adoption services.</p>
                    </div>

                    <div class="value-card">
                        <div class="v-icon">🛡️</div>
                        <h4>Safety First</h4>
                        <p>Ensuring the health and safety of both our rescue dogs and potential families.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="cta">
            <div class="container cta-inner">
                <h4>Join Our Mission to Save More Lives</h4>
                <p>Whether you're looking to adopt, volunteer, or simply support our cause, there are many ways you can help save the lives of dogs in need.</p>
                <div class="cta-actions">
           
                    <a class="btn ghost" href="Donation.html">Make a Donation</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>© <span id="year"></span> RescueTails. All rights reserved.</p>
        </div>
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
		
        // small helper
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>