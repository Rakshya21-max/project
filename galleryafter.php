<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

// Available dogs
$available_sql = "
    SELECT id, picture, name, breed, age, gender, size, status
    FROM reports 
    WHERE LOWER(TRIM(status)) = 'ready for adoption'
    ORDER BY id DESC
";
$available_result = $conn->query($available_sql);

// Recently adopted (last 6)
$adopted_sql = "
    SELECT id, picture, name, breed, age, gender, size, status
    FROM reports 
    WHERE LOWER(TRIM(status)) = 'adopted'
    ORDER BY id DESC
    LIMIT 6
";
$adopted_result = $conn->query($adopted_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Gallery - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="galleryafter.css"/>
    <style>
        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-size: 1.2rem;
            display: none;
        }
        .section-title {
            grid-column: 1 / -1;
            margin: 50px 0 25px;
            font-size: 1.8rem;
            font-weight: 700;
            color: #2f6b4f;
            text-align: center;
        }
        .card.available:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.14);
        }
        .card.adopted {
            background: linear-gradient(135deg, #f8fff8, #f0fff0);
            border: 2px solid #4CAF50 !important;
        }
        .adopted-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #4CAF50;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }

        /* ── Image improvements – full body visible ─────────────────────── */
        .card-media {
            position: relative;
            width: 100%;
            min-height: 180px;
            max-height: 340px;
            overflow: hidden;
            background: #f8f9fa;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dog-full-img {
            max-width: 100%;
            max-height: 340px;
            width: auto;
            height: auto;
            object-fit: contain;
            object-position: center;
            display: block;
        }

        .fav {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

    <!-- Header remains unchanged -->
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
                <a class="nav-link" href="ContactUsafter.php">Contact Us</a>
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
                    <input class="search" type="search" placeholder="Search by name or breed..." />
                </div>
            </div>
        </section>

        <section class="gallery container">
            <div class="cards-grid">

                <!-- AVAILABLE DOGS -->
    <?php
// Fetch both Ready and In Adoption Process dogs
$available_sql = "
    SELECT id, picture, name, breed, age, gender, size, status
    FROM reports 
    WHERE status IN ('Ready for adoption', 'In Adoption Process')
    ORDER BY id DESC
";
$available_result = $conn->query($available_sql);

if ($available_result && $available_result->num_rows > 0): ?>
    <?php while ($dog = $available_result->fetch_assoc()): ?>
        <article class="card available">
            <div class="card-media">
                <img 
                    src="uploads/<?php echo htmlspecialchars($dog['picture']); ?>" 
                    alt="<?php echo htmlspecialchars($dog['name'] ?: 'Street dog ready for adoption'); ?>"
                    class="dog-full-img"
                    loading="lazy"
                >
                <button class="fav">♡</button>
            </div>
            <div class="card-body">
                <h4><?php 
                    echo htmlspecialchars(
                        $dog['name'] ?: 
                        ($dog['breed'] ? $dog['breed'] . ' Friend' : 'Rescued Street Friend')
                    ); 
                ?></h4>
                
                <ul class="meta">
                    <?php if (!empty($dog['age'])): ?><li><strong>Age:</strong> <?php echo htmlspecialchars($dog['age']); ?></li><?php endif; ?>
                    <?php if (!empty($dog['breed'])): ?><li><strong>Breed:</strong> <?php echo htmlspecialchars($dog['breed']); ?></li><?php endif; ?>
                    <?php if (!empty($dog['gender'])): ?><li><strong>Gender:</strong> <?php echo ucfirst(htmlspecialchars($dog['gender'])); ?></li><?php endif; ?>
                    <?php if (!empty($dog['size'])): ?><li><strong>Size:</strong> <?php echo ucfirst(htmlspecialchars($dog['size'])); ?></li><?php endif; ?>
                </ul>

                <div class="card-actions">
                    <?php if ($dog['status'] === 'Ready for adoption'): ?>
                        <a href="adopt.php?dog_id=<?php echo $dog['id']; ?>">
                            <button class="btn primary">Adopt Me 🐾</button>
                        </a>
                    <?php else: ?>
                        <button class="btn primary" style="background:#f39c12; opacity:0.8; cursor:not-allowed;" disabled>
                            Adoption in Process
                        </button>
                    <?php endif; ?>
                    
                    <a href="user-dog-detail.php?id=<?php echo $dog['id']; ?>">
                        <button class="btn outline">View Details</button>
                    </a>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
<?php else: ?>
    <div class="no-dogs-message">
        <h3>No dogs available for adoption right now</h3>
        <p>Check back soon — new friends are rescued every week in Kathmandu!</p>
    </div>
<?php endif; ?>

<!-- RECENTLY ADOPTED SECTION (unchanged) -->
<?php if ($adopted_result && $adopted_result->num_rows > 0): ?>
    <div class="section-title">
        Recently Adopted Happy Tails 🏡❤️
    </div>

    <?php while ($dog = $adopted_result->fetch_assoc()): ?>
        <article class="card adopted">
            <div class="card-media">
                <img 
                    src="uploads/<?php echo htmlspecialchars($dog['picture']); ?>" 
                    alt="<?php echo htmlspecialchars($dog['name'] ?: 'Adopted street dog'); ?>"
                    class="dog-full-img"
                    loading="lazy"
                >
                <button class="fav">♡</button>
                <div class="adopted-badge">Adopted!</div>
            </div>
            <div class="card-body">
                <h4><?php 
                    echo htmlspecialchars(
                        $dog['name'] ?: 
                        ($dog['breed'] ? $dog['breed'] . ' Friend' : 'Happy Adopted Friend')
                    ); 
                ?></h4>
                
                <ul class="meta">
                    <?php if (!empty($dog['breed'])): ?><li><strong>Breed:</strong> <?php echo htmlspecialchars($dog['breed']); ?></li><?php endif; ?>
                    <?php if (!empty($dog['age'])): ?><li><strong>Age:</strong> <?php echo htmlspecialchars($dog['age']); ?></li><?php endif; ?>
                </ul>

                <div class="card-actions">
                    <a href="user-dog-detail.php?id=<?php echo $dog['id']; ?>">
                        <button class="btn outline">View Story</button>
                    </a>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
<?php endif; ?>

<div class="no-results" id="noResults">
    <h3>No matching dogs found</h3>
    <p>Try different name or breed keywords 🐶</p>
</div>

            </div>
        </section>
    </main>

    <!-- Footer and scripts remain unchanged -->
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
        // ... your existing toggleDropdown and search script ...
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        document.addEventListener('click', function(event) {
            const wrapper = document.querySelector('.profile-wrapper');
            const dropdown = document.getElementById('profileDropdown');
            if (!wrapper.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('.search');
            const noResults = document.getElementById('noResults');

            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.card');
                let visible = 0;

                cards.forEach(card => {
                    const name = card.querySelector('h4')?.textContent.toLowerCase() || '';
                    let breed = '';
                    const metaLis = card.querySelectorAll('.meta li');
                    metaLis.forEach(li => {
                        const txt = li.textContent.toLowerCase();
                        if (txt.includes('breed:')) {
                            breed = txt.replace('breed:', '').trim();
                        }
                    });

                    const match = name.includes(query) || breed.includes(query);
                    card.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                noResults.style.display = (query && visible === 0) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>