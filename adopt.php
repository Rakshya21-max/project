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
       <p>Do you agree not to abandon or mistreat the dog?</p>
<label><input type="radio" name="q1" value="Yes"> Yes</label>
<label><input type="radio" name="q1" value="No"> No</label>

<p>Do you agree to provide proper care, love and medical attention?</p>
<label><input type="radio" name="q2" value="Yes"> Yes</label>
<label><input type="radio" name="q2" value="No"> No</label>

<p>Are you willing to train the dog patiently?</p>
<label><input type="radio" name="q3" value="Yes"> Yes</label>
<label><input type="radio" name="q3" value="No"> No</label>

<p>Is your home fenced or suitable for a dog?</p>
<label><input type="radio" name="q4" value="Yes"> Yes</label>
<label><input type="radio" name="q4" value="No"> No</label>


            <label class="field">How many people live in your household?
              <span class="error"></span>
              <input type="text" name="1" placeholder="">
            </label>


            <label class="field">Are there childrens?
              <span class="error"></span>
              <input type="text" name="2" placeholder="yes/no">
            </label>
          

          <label class="field full">Have you owned a dog before?
            <span class="error"></span>
            <input type="text" name="3" placeholder="yes/no">
          </label>

          <label class="field full">Do you currently have other pets?
            <span class="error" id="err_phone"></span>
            <input type="text" name="4" placeholder="Specify type and number">
          </label>

          <label class="field full">Who will be the primary caregiver for the dog?
            <span class="error" id="err_pet"></span>
            <input type="text" name="5" placeholder="Enter your answer">
          </label>

          <label class="field full">What would you fo if the dog becomes seriously ill?
            <span class="error" id="err_address"> </span>
            <input type="text" name="6" placeholder="Enter your answer">
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
