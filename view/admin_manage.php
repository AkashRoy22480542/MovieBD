<?php
session_start();
require_once '../Model/adminModel.php';

// Check if admin is logged in and is super admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['admin_role'] !== 'super_admin') {
    $_SESSION['error'] = 'Access denied. Only super admins can manage administrators.';
    header('Location: dashboard.php');
    exit();
}

$admin = getAdminById($_SESSION['admin_id']);
$all_admins = getAllAdmins();

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
    <title>Manage Admins - MovieDB</title>
    <link rel="stylesheet" href="../Assets/style.css">
    <link rel="stylesheet" href="../Assets/admin.css">
</head>
<body>
    <nav class="admin-navbar">
        <h1>MovieDB Admin</h1>
        <div class="nav-links">
            <span>Welcome, <?= htmlspecialchars($admin['full_name']) ?></span>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="../Controller/authController.php?action=logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="page-header">
            <h2>Manage Administrators</h2>
            <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <div class="create-admin-section">
            <h3 style="color: #dc2626; margin-bottom: 15px;">Create New Administrator</h3>
            <form method="POST" action="../Controller/adminController.php">
                <input type="hidden" name="action" value="create_admin">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password (minimum 6 characters)</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>

                <button type="submit" class="create-btn">Create Administrator</button>
            </form>
        </div>

        <div class="admins-table">
            <h3 style="color: #dc2626; margin-bottom: 15px;">All Administrators</h3>
            <?php if (empty($all_admins)): ?>
                <p>No administrators found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_admins as $admin_item): ?>
                            <tr>
                                <td><?= htmlspecialchars($admin_item['username']) ?></td>
                                <td><?= htmlspecialchars($admin_item['full_name']) ?></td>
                                <td><?= htmlspecialchars($admin_item['email']) ?></td>
                                <td>
                                    <span class="role-badge <?= $admin_item['role'] ?>">
                                        <?= $admin_item['role'] === 'super_admin' ? 'Super Admin' : 'Admin' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $admin_item['status'] ?>">
                                        <?= ucfirst($admin_item['status']) ?>
                                    </span>
                                </td>
                                <td><?= $admin_item['created_by_username'] ?? 'System' ?></td>
                                <td><?= date('M j, Y', strtotime($admin_item['created_at'])) ?></td>
                                <td><?= $admin_item['last_login'] ? date('M j, Y g:i A', strtotime($admin_item['last_login'])) : 'Never' ?></td>
                                <td>
                                    <?php if ($admin_item['role'] !== 'super_admin'): ?>
                                        <div class="action-buttons">
                                            <?php if ($admin_item['status'] === 'active'): ?>
                                                <form method="POST" action="../Controller/adminController.php" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="admin_id" value="<?= $admin_item['id'] ?>">
                                                    <input type="hidden" name="status" value="inactive">
                                                    <button type="submit" class="btn-small btn-deactivate" onclick="return confirm('Deactivate this admin?')">
                                                        Deactivate
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" action="../Controller/adminController.php" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="admin_id" value="<?= $admin_item['id'] ?>">
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="btn-small btn-activate">
                                                        Activate
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form method="POST" action="../Controller/adminController.php" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_admin">
                                                <input type="hidden" name="admin_id" value="<?= $admin_item['id'] ?>">
                                                <button type="submit" class="btn-small btn-delete" onclick="return confirm('Are you sure you want to delete this admin? This action cannot be undone.')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #666; font-style: italic;">Protected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
