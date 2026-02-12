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
    // Collect and sanitize all fields
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

    // Server-side validation
    if (empty($q1)) $errors[] = "Question 1 is required";
    if (empty($q2)) $errors[] = "Question 2 is required";
    if (empty($q3)) $errors[] = "Question 3 is required";
    if (empty($q4)) $errors[] = "Question 4 is required";
    if (empty($household) || !is_numeric($household) || $household < 1) $errors[] = "Valid number of household members is required";
    if (empty($children)) $errors[] = "Please select if there are children";
    if (empty($prev_dog)) $errors[] = "Please select if you owned a dog before";
    if (empty($other_pets)) $errors[] = "Please specify about other pets";
    if (empty($caregiver)) $errors[] = "Primary caregiver name is required";
    if (empty($illness)) $errors[] = "Please explain what you would do if the dog becomes seriously ill";
    if (empty($reason)) $errors[] = "Please explain why you want to adopt this pet";
    if (empty($final_decision)) $errors[] = "Please select whether you want to adopt this pet";

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

        try {
            if ($stmt->execute()) {
                $success = true;
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $errors[] = "You have already submitted an adoption application for this dog.";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
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
    <title>Adopt <?php echo htmlspecialchars($dog_name); ?> - RescueTails</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Adopt.css">
    <style>
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .error-field {
            border: 2px solid #d32f2f !important;
            background-color: #ffebee !important;
        }
        .field .error, .radio-error {
            color: #d32f2f;
            font-size: 0.85rem;
            margin-top: 4px;
            display: block;
        }
        #thankyou-msg {
            display: none;
            color: #2e7d32;
            font-weight: 500;
            margin: 15px 0;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 6px;
            text-align: center;
        }
    </style>
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
                <div style="text-align:center; padding:40px 20px;">
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
                        • <?php echo implode('<br>• ', array_map('htmlspecialchars', $errors)); ?>
                    </div>
                <?php endif; ?>

                <form class="adopt-form" action="" method="post" id="adoptForm">
                    <p>Do you agree not to abandon or mistreat the dog?</p>
                    <label><input type="radio" name="q1" value="Yes" <?php echo isset($_POST['q1']) && $_POST['q1'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q1" value="No"  <?php echo isset($_POST['q1']) && $_POST['q1'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <div class="radio-error" id="err_q1"></div>

                    <p>Do you agree to provide proper care, love and medical attention?</p>
                    <label><input type="radio" name="q2" value="Yes" <?php echo isset($_POST['q2']) && $_POST['q2'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q2" value="No"  <?php echo isset($_POST['q2']) && $_POST['q2'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <div class="radio-error" id="err_q2"></div>

                    <p>Are you willing to train the dog patiently?</p>
                    <label><input type="radio" name="q3" value="Yes" <?php echo isset($_POST['q3']) && $_POST['q3'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q3" value="No"  <?php echo isset($_POST['q3']) && $_POST['q3'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <div class="radio-error" id="err_q3"></div>

                    <p>Is your home fenced or suitable for a dog?</p>
                    <label><input type="radio" name="q4" value="Yes" <?php echo isset($_POST['q4']) && $_POST['q4'] === 'Yes' ? 'checked' : ''; ?> required> Yes</label>
                    <label><input type="radio" name="q4" value="No"  <?php echo isset($_POST['q4']) && $_POST['q4'] === 'No'  ? 'checked' : ''; ?>> No</label>
                    <div class="radio-error" id="err_q4"></div>

                    <label class="field">How many people live in your household?
                        <input type="number" name="household" min="1" placeholder="Enter number" 
                               value="<?php echo htmlspecialchars($_POST['household'] ?? ''); ?>" required>
                        <span class="error" id="err_household"></span>
                    </label>

                    <label class="field">Are there children?
                        <select name="children" required>
                            <option value="">Select...</option>
                            <option value="Yes" <?php echo isset($_POST['children']) && $_POST['children'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No"  <?php echo isset($_POST['children']) && $_POST['children'] === 'No'  ? 'selected' : ''; ?>>No</option>
                        </select>
                        <span class="error" id="err_children"></span>
                    </label>

                    <label class="field full">Have you owned a dog before?
                        <select name="prev_dog" required>
                            <option value="">Select...</option>
                            <option value="Yes" <?php echo isset($_POST['prev_dog']) && $_POST['prev_dog'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No"  <?php echo isset($_POST['prev_dog']) && $_POST['prev_dog'] === 'No'  ? 'selected' : ''; ?>>No</option>
                        </select>
                        <span class="error" id="err_prev_dog"></span>
                    </label>

                    <label class="field full">Do you currently have other pets?
                        <textarea name="other_pets" placeholder="Specify type and number" required><?php echo htmlspecialchars($_POST['other_pets'] ?? ''); ?></textarea>
                        <span class="error" id="err_other_pets"></span>
                    </label>

                    <label class="field full">Who will be the primary caregiver for the dog?
                        <input type="text" name="caregiver" placeholder="Enter your answer" 
                               value="<?php echo htmlspecialchars($_POST['caregiver'] ?? ''); ?>" required>
                        <span class="error" id="err_caregiver"></span>
                    </label>

                    <label class="field full">What would you do if the dog becomes seriously ill?
                        <textarea name="illness" placeholder="Enter your answer" required><?php echo htmlspecialchars($_POST['illness'] ?? ''); ?></textarea>
                        <span class="error" id="err_illness"></span>
                    </label>

                    <label class="field full">Why do you want to adopt this pet?
                        <textarea name="reason" placeholder="Enter your answer" required><?php echo htmlspecialchars($_POST['reason'] ?? ''); ?></textarea>
                        <span class="error" id="err_reason"></span>
                    </label>

                    <label class="field full">Do you want to adopt this pet?
                        <select name="final_decision" required>
                            <option value="">Select...</option>
                            <option value="Yes" <?php echo isset($_POST['final_decision']) && $_POST['final_decision'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No"  <?php echo isset($_POST['final_decision']) && $_POST['final_decision'] === 'No'  ? 'selected' : ''; ?>>No</option>
                        </select>
                        <span class="error" id="err_final_decision"></span>
                        <div id="thankyou-msg">Thank you for your enquiry! We appreciate your interest in our rescued dogs. 🐾</div>
                    </label>

                    <div class="submit-wrap">
                        <button class="login2" type="submit" id="submitBtn">Submit Application</button>
                    </div>
                </form>
            <?php endif; ?>
        </article>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('adoptForm');
        if (!form) return;

        const submitBtn = document.getElementById('submitBtn');
        const thankyouMsg = document.getElementById('thankyou-msg');

        form.addEventListener('submit', function(event) {
            let hasError = false;
            let firstInvalid = null;

            // Clear previous messages
            thankyouMsg.style.display = 'none';
            document.querySelectorAll('.error, .radio-error').forEach(el => el.textContent = '');

            // 1. Radio buttons q1–q4
            ['q1','q2','q3','q4'].forEach(name => {
                const selected = document.querySelector(`input[name="${name}"]:checked`);
                if (!selected) {
                    hasError = true;
                    const errEl = document.getElementById(`err_${name}`);
                    if (errEl) errEl.textContent = 'Please select an option';
                    if (!firstInvalid) firstInvalid = document.querySelector(`input[name="${name}"]`);
                }
            });

            // 2. Required inputs/selects/textareas
            form.querySelectorAll('[required]').forEach(field => {
                if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA' || field.tagName === 'SELECT') {
                    if (!field.value.trim()) {
                        hasError = true;
                        const errId = `err_${field.name}`;
                        const errEl = document.getElementById(errId);
                        if (errEl) errEl.textContent = 'This field is required';
                        field.classList.add('error-field');
                        if (!firstInvalid) firstInvalid = field;
                    } else {
                        field.classList.remove('error-field');
                    }
                }
            });

            // 3. Final decision: special handling
            const finalSelect = document.querySelector('select[name="final_decision"]');
            if (finalSelect.value === '') {
                hasError = true;
                document.getElementById('err_final_decision').textContent = 'Please select an option';
                if (!firstInvalid) firstInvalid = finalSelect;
            } else if (finalSelect.value === 'No') {
                hasError = true;
                thankyouMsg.style.display = 'block';
                thankyouMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            if (hasError) {
                event.preventDefault();
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            // All good → submit
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        });

        // Clear errors on input
        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input', () => {
                el.classList.remove('error-field');
                const errEl = document.getElementById(`err_${el.name}`);
                if (errEl) errEl.textContent = '';
            });
        });
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>