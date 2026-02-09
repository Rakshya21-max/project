<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch all users (registered adopters)
$sql = "
    SELECT 
        u.user_id, u.first_name, u.last_name, u.phone, u.email
    FROM users u
    INNER JOIN adoption_applications aa ON u.user_id = aa.user_id
    WHERE aa.status = 'approved'
    GROUP BY u.user_id
    ORDER BY u.first_name ASC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Adopters - RescueTails Admin</title>
    <link rel="stylesheet" href="adopters.css" />
    <style>
        /* Header - exact match to reported_dogs1.php */
        .header {
            background: #2f6b4f;
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo {
            font-size: 2.2rem;
        }
        .h-title {
            margin: 0;
            font-size: 1.45rem;
            line-height: 1.3;
        }
        .logout button {
            background: #265c3f;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }
        .logout button:hover {
            background: #1e4a32;
        }
        .logout a {
            color: white;
            text-decoration: none;
        }

        /* Sidebar - exact match to reported_dogs1.php */
        .sidebar {
            width: 240px;
            background: #f8fff8;
            border-right: 1px solid #ddd;
            padding: 20px 0;
        }
        .side-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .nav-item {
            display: block;
            padding: 14px 24px;
            color: #2f6b4f;
            text-decoration: none;
            font-size: 1.05rem;
            transition: all 0.2s;
        }
        .nav-item:hover,
        .nav-item.active {
            background: #2a522e;
            font-weight: 600;
        }

        /* Table remains unchanged from your original */
        .table-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 20px;
        }
        .dogs-table {
            width: 100%;
            border-collapse: collapse;
        }
        .dogs-table th {
            background: #2f6b4f;
            color: white;
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
        }
        .dogs-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
        }
        .dogs-table tr:hover {
            background: #f8fff8;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="app-shell">
            <header class="header">
                <div class="header-left">
                    <div class="logo">🐾</div>
                    <div class="titles">
                        <h1 class="h-title">Welcome to NGO<br>Dashboard</h1>
                    </div>
                </div>

                <button class="logout">
                    <a href="adminlogout.php">Logout</a>
                </button>
            </header>

            <main class="main">
                <aside class="sidebar">
                    <nav class="side-nav">
                        <a class="nav-item" href="reported_dogs1.php">Reported Dogs</a>
                        <a class="nav-item" href="adoptionlist.php">Adoption List</a>
                        <a class="nav-item" href="rescued-dogs.php">Rescued Dogs</a>
                        <a class="nav-item" href="add-for-adoption.php">Post for Adoption</a>
                        <a class="nav-item active" href="adopters.php">Adopters</a>
                    </nav>
                </aside>

                <section class="content">
                    <div class="table-card">
                        <h2>Adopters</h2>

                        <table class="dogs-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $full_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                                ?>
                                        <tr>
                                            <td><?php echo $full_name; ?></td>
                                            <td><?php echo htmlspecialchars($row['email'] ?: '—'); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone'] ?: '—'); ?></td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="3" style="text-align:center; padding:40px; color:#666;">
                                            No registered adopters yet.
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

<?php $conn->close(); ?>
</body>
</html>