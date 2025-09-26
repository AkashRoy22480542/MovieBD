<?php
session_start();
require_once '../Model/adminModel.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$admin = getAdminById($_SESSION['admin_id']);
$stats = getAdminStats();
$recent_activities = getAdminActivities(null, 10);

$success_message = $_SESSION['success'] ?? '';
$error_message = $_SESSION['error'] ?? '';

// Clear messages after displaying
unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MovieDB</title>
    <link rel="stylesheet" href="../Assets/style.css">
    <link rel="stylesheet" href="../Assets/admin.css">
</head>
<body>
    <nav class="admin-navbar">
        <h1>MovieDB Admin</h1>
        <div class="nav-links">
            <span>Welcome, <?= htmlspecialchars($admin['full_name']) ?> 
                <span class="role-badge <?= $admin['role'] === 'super_admin' ? 'super-admin' : '' ?>">
                    <?= $admin['role'] === 'super_admin' ? 'Super Admin' : 'Admin' ?>
                </span>
            </span>
            <a href="admin_movies.php">Manage Movies</a>
            <?php if ($admin['role'] === 'super_admin'): ?>
                <a href="admin_manage.php">Manage Admins</a>
            <?php endif; ?>
            <a href="../Controller/authController.php?action=logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <div class="welcome-section">
            <h2>Dashboard Overview</h2>
            <p>Welcome to the MovieDB admin panel. Here you can manage the system and view important statistics.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total_admins'] ?></h3>
                <p>Total Active Admins</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['super_admins'] ?></h3>
                <p>Super Administrators</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['regular_admins'] ?></h3>
                <p>Regular Administrators</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['recent_activities'] ?></h3>
                <p>Activities (24h)</p>
            </div>
        </div>

        <div class="activities-section">
            <h2>Recent Admin Activities</h2>
            <?php if (empty($recent_activities)): ?>
                <p>No recent activities found.</p>
            <?php else: ?>
                <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-details">
                            <div class="activity-action"><?= htmlspecialchars($activity['action']) ?></div>
                            <div class="activity-description">
                                <?= htmlspecialchars($activity['full_name']) ?> (<?= htmlspecialchars($activity['username']) ?>)
                                <?php if ($activity['description']): ?>
                                    - <?= htmlspecialchars($activity['description']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="activity-time">
                            <?= date('M j, Y g:i A', strtotime($activity['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="admin_movies.php" class="action-btn">Manage Movies</a>
                <?php if ($admin['role'] === 'super_admin'): ?>
                    <a href="admin_manage.php" class="action-btn">Manage Administrators</a>
                <?php endif; ?>
                <a href="../home.php" class="action-btn">View Main Site</a>
                <a href="../Controller/authController.php?action=logout" class="action-btn">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
