<?php
require_once('db.php');

// Function to authenticate admin
function authenticateAdmin($username, $password) {
    $con = getConnection();
    $username = mysqli_real_escape_string($con, $username);
    
    $sql = "SELECT * FROM admins WHERE (username = '$username' OR email = '$username') AND status = 'active'";
    $result = mysqli_query($con, $sql);
    
    if ($result && mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);
        if (password_verify($password, $admin['password'])) {
            // Update last login
            updateLastLogin($admin['id']);
            return $admin;
        }
    }
    return false;
}

// Function to update last login time
function updateLastLogin($admin_id) {
    $con = getConnection();
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    
    $sql = "UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = '$admin_id'";
    mysqli_query($con, $sql);
}

// Function to get admin by ID
function getAdminById($admin_id) {
    $con = getConnection();
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    
    $sql = "SELECT * FROM admins WHERE id = '$admin_id'";
    $result = mysqli_query($con, $sql);
    
    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

// Function to get all admins (for super admin)
function getAllAdmins() {
    $con = getConnection();
    
    $sql = "SELECT a.*, ca.username as created_by_username 
            FROM admins a 
            LEFT JOIN admins ca ON a.created_by = ca.id 
            ORDER BY a.created_at DESC";
    $result = mysqli_query($con, $sql);
    
    $admins = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
    return $admins;
}

// Function to create new admin (only super admin can do this)
function createAdmin($adminData, $created_by_id) {
    $con = getConnection();
    
    $username = mysqli_real_escape_string($con, $adminData['username']);
    $email = mysqli_real_escape_string($con, $adminData['email']);
    $password = password_hash($adminData['password'], PASSWORD_DEFAULT);
    $full_name = mysqli_real_escape_string($con, $adminData['full_name']);
    $role = mysqli_real_escape_string($con, $adminData['role']);
    $created_by_id = mysqli_real_escape_string($con, $created_by_id);
    
    // Check if username or email already exists
    $check_sql = "SELECT id FROM admins WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($con, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        return [
            'success' => false,
            'message' => 'Username or email already exists'
        ];
    }
    
    $sql = "INSERT INTO admins (username, email, password, full_name, role, created_by) 
            VALUES ('$username', '$email', '$password', '$full_name', '$role', '$created_by_id')";
    
    if (mysqli_query($con, $sql)) {
        return [
            'success' => true,
            'admin_id' => mysqli_insert_id($con),
            'message' => 'Admin created successfully'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to create admin: ' . mysqli_error($con)
        ];
    }
}

// Function to update admin status
function updateAdminStatus($admin_id, $status) {
    $con = getConnection();
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    $status = mysqli_real_escape_string($con, $status);
    
    $sql = "UPDATE admins SET status = '$status' WHERE id = '$admin_id'";
    return mysqli_query($con, $sql);
}

// Function to delete admin (only super admin can do this)
function deleteAdmin($admin_id) {
    $con = getConnection();
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    
    // Don't allow deletion of super admin
    $check_sql = "SELECT role FROM admins WHERE id = '$admin_id'";
    $result = mysqli_query($con, $check_sql);
    $admin = mysqli_fetch_assoc($result);
    
    if ($admin['role'] === 'super_admin') {
        return false;
    }
    
    $sql = "DELETE FROM admins WHERE id = '$admin_id'";
    return mysqli_query($con, $sql);
}

// Function to change admin password
function changeAdminPassword($admin_id, $new_password) {
    $con = getConnection();
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $sql = "UPDATE admins SET password = '$hashed_password' WHERE id = '$admin_id'";
    return mysqli_query($con, $sql);
}

// Function to log admin activity
function logAdminActivity($admin_id, $action, $description = '') {
    $con = getConnection();
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    $action = mysqli_real_escape_string($con, $action);
    $description = mysqli_real_escape_string($con, $description);
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    
    $sql = "INSERT INTO admin_activities (admin_id, action, description, ip_address) 
            VALUES ('$admin_id', '$action', '$description', '$ip_address')";
    
    mysqli_query($con, $sql);
}

// Function to get admin activities
function getAdminActivities($admin_id = null, $limit = 50) {
    $con = getConnection();
    $limit = (int)$limit;
    
    if ($admin_id) {
        $admin_id = mysqli_real_escape_string($con, $admin_id);
        $where_clause = "WHERE aa.admin_id = '$admin_id'";
    } else {
        $where_clause = "";
    }
    
    $sql = "SELECT aa.*, a.username, a.full_name 
            FROM admin_activities aa 
            LEFT JOIN admins a ON aa.admin_id = a.id 
            $where_clause 
            ORDER BY aa.created_at DESC 
            LIMIT $limit";
    
    $result = mysqli_query($con, $sql);
    
    $activities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = $row;
    }
    return $activities;
}

// Function to get admin statistics
function getAdminStats() {
    $con = getConnection();
    
    $stats = [];
    
    // Total admins
    $result = mysqli_query($con, "SELECT COUNT(*) as total FROM admins WHERE status = 'active'");
    $stats['total_admins'] = mysqli_fetch_assoc($result)['total'];
    
    // Super admins count
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM admins WHERE role = 'super_admin' AND status = 'active'");
    $stats['super_admins'] = mysqli_fetch_assoc($result)['count'];
    
    // Regular admins count
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM admins WHERE role = 'admin' AND status = 'active'");
    $stats['regular_admins'] = mysqli_fetch_assoc($result)['count'];
    
    // Recent activities count
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM admin_activities WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stats['recent_activities'] = mysqli_fetch_assoc($result)['count'];
    
    return $stats;
}

// Function to check if user is super admin
function isSuperAdmin($admin_id) {
    $admin = getAdminById($admin_id);
    return $admin && $admin['role'] === 'super_admin';
}

// Function to update admin profile
function updateAdminProfile($admin_id, $full_name, $email) {
    $con = getConnection();
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    $full_name = mysqli_real_escape_string($con, $full_name);
    $email = mysqli_real_escape_string($con, $email);
    
    // Check if email is already taken by another admin
    $check_sql = "SELECT id FROM admins WHERE email = '$email' AND id != '$admin_id'";
    $result = mysqli_query($con, $check_sql);
    
    if (mysqli_num_rows($result) > 0) {
        return false;
    }
    
    $sql = "UPDATE admins SET full_name = '$full_name', email = '$email' WHERE id = '$admin_id'";
    return mysqli_query($con, $sql);
}
?>
