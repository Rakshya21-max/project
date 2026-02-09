<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($application_id <= 0) {
    die("<h2 style='text-align:center; color:#c0392b;'>Invalid application ID</h2>");
}

// Handle accept / reject from this page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action']; // 'approved' or 'rejected'

    $stmt = $conn->prepare("UPDATE adoption_applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $application_id);
    $stmt->execute();
    $stmt->close();

    // If approved → mark dog as adopted
    if ($action === 'approved') {
        $update_dog = $conn->prepare("
            UPDATE reports 
            SET status = 'Adopted' 
            WHERE id = (SELECT dog_id FROM adoption_applications WHERE id = ?)
        ");
        $update_dog->bind_param("i", $application_id);
        $update_dog->execute();
        $update_dog->close();
    }

    // Refresh the page
    header("Location: view_application.php?id=$application_id&updated=1");
    exit;
}

// Fetch application details
$stmt = $conn->prepare("
    SELECT 
        aa.id, aa.user_id, aa.report_id, aa.application_date, aa.status,
        aa.q1, aa.q2, aa.q3, aa.q4, aa.household, aa.children, aa.prev_dog,
        aa.other_pets, aa.caregiver, aa.illness, aa.reason, aa.final_decision,
        u.first_name, u.last_name, u.phone, u.email,
        r.name AS dog_name, r.picture, r.location, r.description
    FROM adoption_applications aa
    LEFT JOIN users u ON aa.user_id = u.user_id
    LEFT JOIN reports r ON aa.report_id = r.id
    WHERE aa.id = ?
");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$app) {
    die("<h2 style='text-align:center; color:#c0392b;'>Application not found</h2>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application #<?php echo $app['id']; ?> - RescueTails Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background:#f8f9fa; margin:0; padding:30px; color:#333; }
        .container { max-width:1000px; margin:0 auto; background:white; padding:30px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1); }
        h1 { color:#2f6b4f; text-align:center; margin-bottom:10px; }
        .back { display:inline-block; margin-bottom:20px; color:#2f6b4f; text-decoration:none; font-weight:bold; }
        .section { margin:30px 0; padding:20px; background:#f8fff8; border-left:5px solid #2f6b4f; border-radius:8px; }
        .section h3 { margin-top:0; color:#2f6b4f; }
        .grid { display:grid; grid-template-columns:1fr 2fr; gap:12px 20px; }
        .label { font-weight:600; color:#444; }
        .value { color:#555; }
        .dog-img { max-width:300px; border-radius:10px; margin:15px 0; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        .status-pending { color:#e67e22; font-weight:bold; }
        .status-approved { color:#27ae60; font-weight:bold; }
        .status-rejected { color:#c0392b; font-weight:bold; }
        .action-buttons { margin-top:30px; text-align:center; }
        .btn-action {
            padding:12px 30px; border:none; border-radius:8px; cursor:pointer; font-size:1.1rem; font-weight:500;
            margin:0 15px; transition: all 0.2s;
        }
        .btn-approve { background:#27ae60; color:white; }
        .btn-approve:hover { background:#219653; transform:translateY(-2px); }
        .btn-reject { background:#e74c3c; color:white; }
        .btn-reject:hover { background:#c0392b; transform:translateY(-2px); }
        .success-msg { background:#d4edda; color:#155724; padding:15px; border-radius:8px; text-align:center; margin:20px 0; }
    </style>
</head>
<body>

<div class="container">
    <a href="adoptionlist.php" class="back">← Back to Adoption List</a>

    <h1>Adoption Application #<?php echo $app['id']; ?></h1>

    <?php if (isset($_GET['updated'])): ?>
        <div class="success-msg">Status updated successfully!</div>
    <?php endif; ?>

    <p style="text-align:center; color:#666; margin-bottom:30px;">
        Status: <span class="status-<?php echo strtolower($app['status']); ?>">
            <?php echo ucfirst($app['status']); ?>
        </span>  
        | Submitted: <?php echo date('d M Y H:i', strtotime($app['application_date'])); ?>
    </p>

    <!-- Applicant -->
    <div class="section">
        <h3>Applicant Information</h3>
        <div class="grid">
            <div class="label">Full Name:</div>
            <div class="value"><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></div>
            
            <div class="label">Phone:</div>
            <div class="value"><?php echo htmlspecialchars($app['phone'] ?: '—'); ?></div>
            
            <div class="label">Email:</div>
            <div class="value"><?php echo htmlspecialchars($app['email'] ?: '—'); ?></div>
        </div>
    </div>

    <!-- Dog -->
    <div class="section">
        <h3>Dog Details</h3>
        <?php if ($app['picture']): ?>
            <img src="uploads/<?php echo htmlspecialchars($app['picture']); ?>" 
                 alt="<?php echo htmlspecialchars($app['dog_name'] ?? 'Dog'); ?>" 
                 class="dog-img">
        <?php endif; ?>
        
        <div class="grid">
            <div class="label">Name:</div>
            <div class="value"><?php echo htmlspecialchars($app['dog_name'] ?: 'Unnamed'); ?></div>
            
            <div class="label">Location:</div>
            <div class="value"><?php echo htmlspecialchars($app['location'] ?: '—'); ?></div>
            
            <div class="label">Description:</div>
            <div class="value"><?php echo nl2br(htmlspecialchars($app['description'] ?: 'No description')); ?></div>
        </div>
    </div>

    <!-- Answers -->
    <div class="section">
        <h3>Adoption Questions & Answers</h3>
        <div class="grid">
            <div class="label">Not abandon/mistreat dog?</div>
            <div class="value"><?php echo htmlspecialchars($app['q1'] ?: '—'); ?></div>
            
            <div class="label">Provide care & medical attention?</div>
            <div class="value"><?php echo htmlspecialchars($app['q2'] ?: '—'); ?></div>
            
            <div class="label">Train patiently?</div>
            <div class="value"><?php echo htmlspecialchars($app['q3'] ?: '—'); ?></div>
            
            <div class="label">Home fenced/suitable?</div>
            <div class="value"><?php echo htmlspecialchars($app['q4'] ?: '—'); ?></div>
            
            <div class="label">Household size:</div>
            <div class="value"><?php echo htmlspecialchars($app['household'] ?: '—'); ?></div>
            
            <div class="label">Children in home?</div>
            <div class="value"><?php echo htmlspecialchars($app['children'] ?: '—'); ?></div>
            
            <div class="label">Owned dog before?</div>
            <div class="value"><?php echo htmlspecialchars($app['prev_dog'] ?: '—'); ?></div>
            
            <div class="label">Other pets?</div>
            <div class="value"><?php echo nl2br(htmlspecialchars($app['other_pets'] ?: '—')); ?></div>
            
            <div class="label">Primary caregiver:</div>
            <div class="value"><?php echo htmlspecialchars($app['caregiver'] ?: '—'); ?></div>
            
            <div class="label">If dog seriously ill?</div>
            <div class="value"><?php echo nl2br(htmlspecialchars($app['illness'] ?: '—')); ?></div>
            
            <div class="label">Why adopt this pet?</div>
            <div class="value"><?php echo nl2br(htmlspecialchars($app['reason'] ?: '—')); ?></div>
            
            <div class="label">Final decision?</div>
            <div class="value"><?php echo htmlspecialchars($app['final_decision'] ?: '—'); ?></div>
        </div>
    </div>

    <!-- Accept / Reject Buttons -->
    <?php if ($app['status'] === 'pending'): ?>
        <div class="action-buttons">
            <form method="post" style="display:inline;">
                <button type="submit" name="action" value="approved" class="btn-action btn-approve">Approve Application</button>
            </form>
            
            <form method="post" style="display:inline;">
                <button type="submit" name="action" value="rejected" class="btn-action btn-reject">Reject Application</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
<?php $conn->close(); ?>