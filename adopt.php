<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$dog_id = isset($_GET['dog_id']) ? intval($_GET['dog_id']) : 0;
$home_link = $_SERVER['HTTP_REFERER'] ?? 'landingafter.php';

// Fetch dog name for display (safe fallback)
$dog_name = 'this street friend';
if ($dog_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM reports WHERE id = ? AND status = 'Ready for adoption'");
    $stmt->bind_param("i", $dog_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res && !empty($res['name'])) {
        $dog_name = $res['name'];
    }
    $stmt->close();
}

// Handle form submission (server-side)
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize all fields (same names as your form)
    $q1             = $_POST['q1'] ?? '';
    $q2             = $_POST['q2'] ?? '';
    $q3             = $_POST['q3'] ?? '';
    $q4             = $_POST['q4'] ?? '';
    $household      = trim($_POST['household'] ?? '');
    $children       = $_POST['children'] ?? '';
    $prev_dog       = $_POST['prev_dog'] ?? '';
    $other_pets     = trim($_POST['other_pets'] ?? '');
    $caregiver      = trim($_POST['caregiver'] ?? '');
    $illness        = trim($_POST['illness'] ?? '');
    $reason         = trim($_POST['reason'] ?? '');
    $final_decision = $_POST['final_decision'] ?? '';

    // Server-side validation (all fields required except where logical)
    if (empty($q1)) $errors[] = "Question 1 is required (Do you agree not to abandon...)";
    if (empty($q2)) $errors[] = "Question 2 is required (proper care, love and medical attention)";
    if (empty($q3)) $errors[] = "Question 3 is required (train the dog patiently)";
    if (empty($q4)) $errors[] = "Question 4 is required (home fenced or suitable)";
    if (empty($household) || !is_numeric($household) || $household < 1) $errors[] = "Valid number of household members is required";
    if (empty($children)) $errors[] = "Please select if there are children";
    if (empty($prev_dog)) $errors[] = "Please select if you owned a dog before";
    if (empty($other_pets)) $errors[] = "Please specify about other pets";
    if (empty($caregiver)) $errors[] = "Primary caregiver name is required";
    if (empty($illness)) $errors[] = "Please explain what you would do if the dog becomes seriously ill";
    if (empty($reason)) $errors[] = "Please explain why you want to adopt this pet";
    if (empty($final_decision)) $errors[] = "Final decision (Do you want to adopt this pet?) is required";

    // If no errors → save to database
    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO adoption_applications 
            (user_id, report_id, q1, q2, q3, q4, household, children, prev_dog, other_pets, 
             caregiver, illness, reason, final_decision, status, application_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->bind_param("iissssississss",
            $_SESSION['user_id'],
            $dog_id,
            $q1, $q2, $q3, $q4,
            $household,
            $children,
            $prev_dog,
            $other_pets,
            $caregiver,
            $illness,
            $reason,
            $final_decision
        );

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
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
            <a href="<?php echo htmlspecialchars($home_link); ?>">
                <button class="back" aria-label="back">←</button>
            </a>
            <div class="card-top">
                <div class="logo">🐾</div>
                <div class="titles">
                    <h1>Adopt a dog</h1>
                    <p class="subtitle">Help to find a shelter for paws</p>
                </div>
            </div>

            <?php if ($success): ?>
                <div id="successMessage" style="display:block; text-align:center; padding:40px 20px;">
                    <div style="background-color: #4CAF50; color: white; padding: 30px; border-radius: 8px; margin-top: 20px;">
                        <h2 style="margin: 0 0 10px 0;">✓ Form Submitted Successfully!</h2>
                        <p style="margin: 10px 0; font-size: 16px;">
                            Thank you for your adoption application for <?php echo htmlspecialchars($dog_name); ?>.<br>
                            We will review your information and get back to you soon.
                        </p>
                        <button onclick="location.href='<?php echo htmlspecialchars($home_link); ?>'" 
                                style="margin-top: 20px; padding: 10px 20px; background-color: white; color: #4CAF50; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            Return Home
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div style="background:#fde8e8; color:#c0392b; padding:15px; border-radius:6px; margin-bottom:20px;">
                        <strong>Please fix these errors:</strong><br>
                        <?php echo implode('<br>• ', array_map('htmlspecialchars', $errors)); ?>
                    </div>
                <?php endif; ?>

                <form class="adopt-form" action="" method="post" id="adoptForm">
                    <p>Do you agree not to abandon or mistreat the dog?</p>
                    <label><input type="radio" name="q1" value="Yes" <?php echo isset($_POST['q1']) && $_POST['q1'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q1" value="No"  <?php echo isset($_POST['q1']) && $_POST['q1'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <span class="error" id="err_q1"></span>

                    <p>Do you agree to provide proper care, love and medical attention?</p>
                    <label><input type="radio" name="q2" value="Yes" <?php echo isset($_POST['q2']) && $_POST['q2'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q2" value="No"  <?php echo isset($_POST['q2']) && $_POST['q2'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <span class="error" id="err_q2"></span>

                    <p>Are you willing to train the dog patiently?</p>
                    <label><input type="radio" name="q3" value="Yes" <?php echo isset($_POST['q3']) && $_POST['q3'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q3" value="No"  <?php echo isset($_POST['q3']) && $_POST['q3'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <span class="error" id="err_q3"></span>

                    <p>Is your home fenced or suitable for a dog?</p>
                    <label><input type="radio" name="q4" value="Yes" <?php echo isset($_POST['q4']) && $_POST['q4'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q4" value="No"  <?php echo isset($_POST['q4']) && $_POST['q4'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <span class="error" id="err_q4"></span>

                    <label class="field">How many people live in your household?
                        <span class="error" id="err_household"></span>
                        <input type="number" name="household" min="1" placeholder="Enter number" 
                               value="<?php echo htmlspecialchars($_POST['household'] ?? ''); ?>" required>
                    </label>

                    <label class="field">Are there children?
                        <span class="error" id="err_children"></span>
                        <select name="children" required>
                            <option value="">Select...</option>
                            <option value="Yes" <?php echo isset($_POST['children']) && $_POST['children'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No"  <?php echo isset($_POST['children']) && $_POST['children'] === 'No'  ? 'selected' : ''; ?>>No</option>
                        </select>
                    </label>

                    <label class="field full">Have you owned a dog before?
                        <span class="error" id="err_prev_dog"></span>
                        <select name="prev_dog" required>
                            <option value="">Select...</option>
                            <option value="Yes" <?php echo isset($_POST['prev_dog']) && $_POST['prev_dog'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No"  <?php echo isset($_POST['prev_dog']) && $_POST['prev_dog'] === 'No'  ? 'selected' : ''; ?>>No</option>
                        </select>
                    </label>

                    <label class="field full">Do you currently have other pets?
                        <span class="error" id="err_other_pets"></span>
                        <textarea name="other_pets" placeholder="Specify type and number" required><?php echo htmlspecialchars($_POST['other_pets'] ?? ''); ?></textarea>
                    </label>

                    <label class="field full">Who will be the primary caregiver for the dog?
                        <span class="error" id="err_caregiver"></span>
                        <input type="text" name="caregiver" placeholder="Enter your answer" 
                               value="<?php echo htmlspecialchars($_POST['caregiver'] ?? ''); ?>" required>
                    </label>

                    <label class="field full">What would you do if the dog becomes seriously ill?
                        <span class="error" id="err_illness"></span>
                        <textarea name="illness" placeholder="Enter your answer" required><?php echo htmlspecialchars($_POST['illness'] ?? ''); ?></textarea>
                    </label>

                    <label class="field full">Why do you want to adopt this pet?
                        <span class="error" id="err_reason"></span>
                        <textarea name="reason" placeholder="Enter your answer" required><?php echo htmlspecialchars($_POST['reason'] ?? ''); ?></textarea>
                    </label>

                    <label class="field full">Do you want to adopt this pet?
                        <span class="error" id="err_final_decision"></span>
                        <select name="final_decision" required>
                            <option value="">Select...</option>
                            <option value="Yes" <?php echo isset($_POST['final_decision']) && $_POST['final_decision'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No"  <?php echo isset($_POST['final_decision']) && $_POST['final_decision'] === 'No'  ? 'selected' : ''; ?>>No</option>
                        </select>
                    </label>

                    <div class="submit-wrap">
                        <button class="login2" type="submit">Submit</button>
                    </div>
                </form>
            <?php endif; ?>

            <div id="successMessage" style="display:<?php echo $success ? 'block' : 'none'; ?>; text-align:center; padding:40px 20px;">
                <div style="background-color: #4CAF50; color: white; padding: 30px; border-radius: 8px; margin-top: 20px;">
                    <h2 style="margin: 0 0 10px 0;">✓ Form Submitted Successfully!</h2>
                    <p style="margin: 10px 0; font-size: 16px;">
                        Thank you for your adoption application for <?php echo htmlspecialchars($dog_name); ?>.<br>
                        We will review your information and get back to you soon.
                    </p>
                    <button onclick="location.href='<?php echo htmlspecialchars($home_link); ?>'" 
                            style="margin-top: 20px; padding: 10px 20px; background-color: white; color: #4CAF50; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        Return Home
                    </button>
                </div>
            </div>

        </article>
    </main>

    <script>
        // Your existing client-side validation (kept as-is, works well)
        document.getElementById('adoptForm')?.addEventListener('submit', function(event) {
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
            
            // Validate text inputs & number
            const textInputs = document.querySelectorAll('input[type="text"], input[type="number"]');
            textInputs.forEach(input => {
                if (input.value.trim() === '') {
                    isValid = false;
                    const errorEl = input.parentElement.querySelector('.error') || input.parentElement.parentElement.querySelector('.error');
                    if (errorEl) errorEl.textContent = 'This field is required';
                } else {
                    const errorEl = input.parentElement.querySelector('.error') || input.parentElement.parentElement.querySelector('.error');
                    if (errorEl) errorEl.textContent = '';
                }
            });
            
            // Validate select dropdowns
            const selects = document.querySelectorAll('select');
            selects.forEach(select => {
                if (select.value === '') {
                    isValid = false;
                    const errorEl = select.parentElement.querySelector('.error') || select.parentElement.parentElement.querySelector('.error');
                    if (errorEl) errorEl.textContent = 'Please select an option';
                } else {
                    const errorEl = select.parentElement.querySelector('.error') || select.parentElement.parentElement.querySelector('.error');
                    if (errorEl) errorEl.textContent = '';
                }
            });
            
            // Validate textareas
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                if (textarea.value.trim() === '') {
                    isValid = false;
                    const errorEl = textarea.parentElement.querySelector('.error') || textarea.parentElement.parentElement.querySelector('.error');
                    if (errorEl) errorEl.textContent = 'This field is required';
                } else {
                    const errorEl = textarea.parentElement.querySelector('.error') || textarea.parentElement.parentElement.querySelector('.error');
                    if (errorEl) errorEl.textContent = '';
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields');
                return;
            }
            
            // If JS validation passes → submit to server
            this.submit();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>