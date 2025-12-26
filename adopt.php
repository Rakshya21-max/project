<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
$home_link = $_SERVER['HTTP_REFERER'] ?? 'landingafter.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Adopt a dog</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Adopt.css">
  </head>
  <body>
    <main class="page">
      <article class="adopt-card">
        <a href="<?php echo $home_link; ?>">
          <button class="back" aria-label="back">←</button>
        </a>
        <div class="card-top">
          <div class="logo">🐾</div>
          <div class="titles">
            <h1>Adopt a dog</h1>
            <p class="subtitle">Help to find a shelter for paws</p>
          </div>
        </div>

        <form class="adopt-form" action="#" method="post">
          <div class="row two-cols">
            <label class="field">First Name
              <span class="error"></span>
              <input type="text" name="first" placeholder="First Name">
            </label>
            <label class="field">Last Name
              <span class="error"></span>
              <input type="text" name="last" placeholder="Last Name">
            </label>
          </div>

          <label class="field full">Email Address
            <span class="error"></span>
            <input type="email" name="email" placeholder="Enter your email">
          </label>

          <label class="field full">Phone Number
            <span class="error" id="err_phone"></span>
            <input type="tel" name="phone" placeholder="Enter your phone number">
          </label>

          <label class="field full">Do you have other pets?
            <span class="error" id="err_pet"></span>
            <input type="text" name="other_pets" placeholder="Enter your answer">
          </label>

          <label class="field full">Address
            <span class="error" id="err_address"> </span>
            <input type="text" name="address" placeholder="Enter your address">
          </label>

          <label class="field full">Why do you want to adopt this pet?
            <span class="error" id="err_reason"></span>
            <input type="text" name="reason" placeholder="Enter you answer">
          </label>

                <label class="field full">Do you want to adopt this pet?
                <span class="error" id="err_experience"></span>
                <input type="text" name="experience" placeholder="Yes / No">
                </label>

                <div class="submit-wrap">
                <button class="login2" type="submit">Submit</button>
                </div>
              <script>
                document.querySelector('.login2').addEventListener('click', function(event) {
                  const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"]');
                  let isEmpty = false;
                  
                  inputs.forEach(input => {
                    if (input.value.trim() === '') {
                      isEmpty = true;
                    }
                  });
                  
                  if (isEmpty) {
                    e.preventDefault();
                    alert('Please fill in all fields');
                  }
                });
              </script>
              </form>
              </article>
            </main>
            <script src="adopt.js"></script>
            </body>
          </html>
