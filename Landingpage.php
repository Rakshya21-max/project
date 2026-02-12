<?php
session_start();
include 'db.php';
if (!$conn) {
    die("<div style='color:red; text-align:center; padding:50px;'>
        <h1>Database Connection Error</h1>
        <p>Cannot connect to the database right now. Please check db.php settings.</p>
        <p>Error: " . mysqli_connect_error() . "</p>
    </div>");
}
?>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="Landingpage.css" />
		<title>Street Dog Adoption Services</title>
	</head>
	<body>
		<header class="site-header">
			<div class="container header-inner">
				<div class="logo">
					<div class="logo-circle">🐾</div>
					<span class="brand">RescueTails</span>
				</div>
				<nav class="main-nav">
					<a class="nav-link active" href="#">Home</a>
					<a class="nav-link" href="Gallery.php">Gallery</a>
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
    <!-- HERO -->
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-left">
                <h1 class="hero-title">Street Dog<br />Adoption <span class="accent">Services</span></h1>
                <p class="hero-sub">Behind every pair of hopeful eyes is a dog waiting for love. At RescueTails, we connect abandoned paws with caring hearts, creating forever bonds that transform lives — both theirs and yours.</p>
                <div class="hero-ctas">
                    <a href="report.php">
                        <button class="btn primary">Report</button>
                    </a>
                    <a href="signup.html">
                        <button class="btn ghost">Sign-Up</button>
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
</main>

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
						<li type="none">★ Compassion in action</li>
						<li type="none">★ Voice for strays</li>
						<li type="none">★ Hope for paws</li>
					</ul>
					
				</aside>
			</section>
<!-- FEATURED RESCUES - dynamic from database -->
<section class="featured">
    <div class="container">
        <h2 class="section-title">Featured Rescues</h2>
        <p class="section-sub">Meet some of our amazing dogs who are looking for their forever homes</p>
        <div class="cards-row">
            <?php
            $featured_sql = "
                SELECT id, picture, name, breed, age 
                FROM reports 
                WHERE LOWER(TRIM(status)) = 'ready for adoption' 
                ORDER BY id DESC 
                LIMIT 4
            ";
            $featured_result = $conn->query($featured_sql);

            if ($featured_result && $featured_result->num_rows > 0):
                while ($dog = $featured_result->fetch_assoc()):
            ?>
                <article class="rescue-card">
                    <div class="rescue-img circle">
                        <img src="uploads/<?php echo htmlspecialchars($dog['picture']); ?>" 
                             alt="<?php echo htmlspecialchars($dog['name'] ?: 'Rescued street dog'); ?>">
                    </div>
                    <h4><?php echo htmlspecialchars($dog['name'] ?: 'Unnamed Friend'); ?></h4>
                    <p class="small">
                        <?php echo htmlspecialchars($dog['age'] ?: 'Age unknown'); ?> • 
                        <?php echo htmlspecialchars($dog['breed'] ?: 'Mixed Breed'); ?>
                    </p>
                </article>
            <?php
                endwhile;
            else:
            ?>
                <p style="text-align:center; grid-column:1/-1; color:#555;">
                    No featured rescues yet — check back soon!
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- AVAILABLE FOR ADOPTION - dynamic version (same query, different presentation) -->
<section class="available container">
    <h2>Available for Adoption</h2>
    <p class="muted center">These lovely dogs are waiting for their forever homes. Each one has been rescued, health-checked, and is ready to bring joy to your family.</p>
    <div class="adoption-row">
        <?php
        // Reuse the same result or run again if needed
        $available_sql = "
            SELECT id, picture, name, breed, age 
            FROM reports 
            WHERE LOWER(TRIM(status)) = 'ready for adoption' 
            ORDER BY id DESC 
            LIMIT 3
        ";
        $available_result = $conn->query($available_sql);

        if ($available_result && $available_result->num_rows > 0):
            while ($dog = $available_result->fetch_assoc()):
        ?>
            <div class="adopt-card">
                <div class="img-box img-rect">
                    <img src="uploads/<?php echo htmlspecialchars($dog['picture']); ?>" 
                         alt="<?php echo htmlspecialchars($dog['name'] ?: 'Adoptable dog'); ?>">
                </div>
                <div class="adopt-body">
                    <h4><?php echo htmlspecialchars($dog['name'] ?: 'Unnamed Friend'); ?></h4>
                    <p class="small">
                        <?php echo htmlspecialchars($dog['age'] ?: 'Age unknown'); ?> • 
                        <?php echo htmlspecialchars($dog['breed'] ?: 'Mixed Breed'); ?>
                    </p>
                        <button class="btn primary">Meet </button>
                </div>
            </div>
        <?php
            endwhile;
        else:
        ?>
            <p style="text-align:center; grid-column:1/-1; color:#555;">
                No dogs available right now — new rescues coming soon!
            </p>
        <?php endif; ?>
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
		
	</body>
</html>

