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

        <form class="adopt-form" action="#" method="post" id="adoptForm">
       <p>Do you agree not to abandon or mistreat the dog?</p>
<label><input type="radio" name="q1" value="Yes" required> Yes</label>
<label><input type="radio" name="q1" value="No"> No</label>
<span class="error" id="err_q1"></span>

<p>Do you agree to provide proper care, love and medical attention?</p>
<label><input type="radio" name="q2" value="Yes" required> Yes</label>
<label><input type="radio" name="q2" value="No"> No</label>
<span class="error" id="err_q2"></span>

<p>Are you willing to train the dog patiently?</p>
<label><input type="radio" name="q3" value="Yes" required> Yes</label>
<label><input type="radio" name="q3" value="No"> No</label>
<span class="error" id="err_q3"></span>

<p>Is your home fenced or suitable for a dog?</p>
<label><input type="radio" name="q4" value="Yes" required> Yes</label>
<label><input type="radio" name="q4" value="No"> No</label>
<span class="error" id="err_q4"></span>


            <label class="field">How many people live in your household?
              <span class="error" id="err_household"></span>
              <input type="number" name="household" min="1" placeholder="Enter number" required>
            </label>


            <label class="field">Are there children?
              <span class="error" id="err_children"></span>
              <select name="children" required>
                <option value="">Select...</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
              </select>
            </label>
          

          <label class="field full">Have you owned a dog before?
            <span class="error" id="err_prev_dog"></span>
            <select name="prev_dog" required>
              <option value="">Select...</option>
              <option value="Yes">Yes</option>
              <option value="No">No</option>
            </select>
          </label>

          <label class="field full">Do you currently have other pets?
            <span class="error" id="err_other_pets"></span>
            <textarea name="other_pets" placeholder="Specify type and number" required></textarea>
          </label>

          <label class="field full">Who will be the primary caregiver for the dog?
            <span class="error" id="err_caregiver"></span>
            <input type="text" name="caregiver" placeholder="Enter your answer" required>
          </label>

          <label class="field full">What would you do if the dog becomes seriously ill?
            <span class="error" id="err_illness"></span>
            <textarea name="illness" placeholder="Enter your answer" required></textarea>
          </label>

          <label class="field full">Why do you want to adopt this pet?
            <span class="error" id="err_reason"></span>
            <textarea name="reason" placeholder="Enter your answer" required></textarea>
          </label>

                <label class="field full">Do you want to adopt this pet?
                <span class="error" id="err_final_decision"></span>
                <select name="final_decision" required>
                  <option value="">Select...</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
                </label>

                <div class="submit-wrap">
                <button class="login2" type="submit">Submit</button>
                </div>
              </form>
              
              <div id="successMessage" style="display:none; text-align:center; padding:40px 20px;">
                <div style="background-color: #4CAF50; color: white; padding: 30px; border-radius: 8px; margin-top: 20px;">
                  <h2 style="margin: 0 0 10px 0;">✓ Form Submitted Successfully!</h2>
                  <p style="margin: 10px 0; font-size: 16px;">Thank you for your adoption application. We will review your information and get back to you soon.</p>
                  <button onclick="location.href='<?php echo $home_link; ?>'" style="margin-top: 20px; padding: 10px 20px; background-color: white; color: #4CAF50; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Return Home</button>
                </div>
              </div>

              <script>
                document.getElementById('adoptForm').addEventListener('submit', function(event) {
                  event.preventDefault();
                  let isValid = true;
                  
                  // Validate radio buttons
                  const questions = ['q1', 'q2', 'q3', 'q4'];
                  questions.forEach(q => {
                    const checked = document.querySelector(`input[name="${q}"]:checked`);
                    const errorEl = document.getElementById(`err_${q}`);
                    if (!checked) {
                      isValid = false;
                      if (errorEl) errorEl.textContent = 'This field is required';
                    } else {
                      if (errorEl) errorEl.textContent = '';
                    }
                  });
                  
                  // Validate text inputs
                  const textInputs = document.querySelectorAll('input[type="text"], input[type="number"]');
                  textInputs.forEach(input => {
                    if (input.value.trim() === '') {
                      isValid = false;
                      const errorEl = input.parentElement.querySelector('.error');
                      if (errorEl) errorEl.textContent = 'This field is required';
                    } else {
                      const errorEl = input.parentElement.querySelector('.error');
                      if (errorEl) errorEl.textContent = '';
                    }
                  });
                  
                  // Validate select dropdowns
                  const selects = document.querySelectorAll('select');
                  selects.forEach(select => {
                    if (select.value === '') {
                      isValid = false;
                      const errorEl = select.parentElement.querySelector('.error');
                      if (errorEl) errorEl.textContent = 'Please select an option';
                    } else {
                      const errorEl = select.parentElement.querySelector('.error');
                      if (errorEl) errorEl.textContent = '';
                    }
                  });
                  
                  // Validate textareas
                  const textareas = document.querySelectorAll('textarea');
                  textareas.forEach(textarea => {
                    if (textarea.value.trim() === '') {
                      isValid = false;
                      const errorEl = textarea.parentElement.querySelector('.error');
                      if (errorEl) errorEl.textContent = 'This field is required';
                    } else {
                      const errorEl = textarea.parentElement.querySelector('.error');
                      if (errorEl) errorEl.textContent = '';
                    }
                  });
                  
                  if (!isValid) {
                    alert('Please fill in all required fields');
                    return;
                  }
                  
                  // Show success message and hide form
                  document.getElementById('adoptForm').style.display = 'none';
                  document.getElementById('successMessage').style.display = 'block';
                  
                  // Optionally send data to server
                  const formData = new FormData(document.getElementById('adoptForm'));
                  fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                  }).catch(err => console.log('Data submitted'));
                });
              </script>
              </form>
              </article>
            </main>
            <script src="adopt.js"></script>
            </body>
          </html>
