<?php
session_start();
require_once('../Model/userModel.php');
require_once('../Model/adminModel.php');

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Register new user
function registerUser($userData) {
    // Check if user already exists
    if (emailExists($userData['email'])) {
        return [
            'success' => false,
            'message' => 'User with this email already exists'
        ];
    }
    
    // Validate email
    if (!isValidEmail($userData['email'])) {
        return [
            'success' => false,
            'message' => 'Invalid email format'
        ];
    }
    
    // Validate password strength
    if (strlen($userData['password']) < 8) {
        return [
            'success' => false,
            'message' => 'Password must be at least 8 characters long'
        ];
    }
    
    // Prepare user data for the addUser function
    $name = htmlspecialchars($userData['firstName']) . ' ' . htmlspecialchars($userData['lastName']);
    $userToAdd = [[
        'name' => $name,
        'email' => strtolower(trim($userData['email'])),
        'phone' => '', // Default empty phone
        'password' => $userData['password'], // addUser will hash this
        'image_path' => '' // Default empty image path
    ]];
    
    if (addUser($userToAdd)) {
        // Get the created user
        $user = getUserByEmail($userData['email']);
        
        return [
            'success' => true,
            'message' => 'Account created successfully',
            'user' => [
                'id' => $user['id'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Failed to create account. Please try again.'
    ];
}

// Login user
function loginUser($email, $password) {
    // Use userModel login function
    $loginData = [
        'email' => strtolower(trim($email)),
        'password' => $password
    ];
    
    if (!login($loginData)) {
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }
    
    // Get user details
    $user = getUserByEmail(strtolower(trim($email)));
    
    if (!$user) {
        return [
            'success' => false,
            'message' => 'User not found'
        ];
    }
    
    if ($user['status'] !== 'active') {
        return [
            'success' => false,
            'message' => 'Account is not active. Please contact support.'
        ];
    }
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['firstName'] = $user['first_name'];
    $_SESSION['lastName'] = $user['last_name'];
    $_SESSION['status'] = true;
    
    return [
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'username' => $user['username'],
            'email' => $user['email']
        ]
    ];
}

// Logout user or admin
function logoutUser() {
    // Log admin logout if it's an admin
    if (isset($_SESSION['admin_id'])) {
        logAdminActivity($_SESSION['admin_id'], 'logout', 'Admin logged out');
    }
    
    session_unset();
    session_destroy();
    header('Location: ../home.php');
}

// Get current user
function getCurrentUser() {
    if (isset($_SESSION['status']) && $_SESSION['status'] === true) {
        return [
            'success' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'firstName' => $_SESSION['firstName'],
                'lastName' => $_SESSION['lastName'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email']
            ]
        ];
    }
    
    return [
        'success' => false,
        'message' => 'No active session'
    ];
}

// Handle direct PHP form submissions and redirects
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'signup':
            // Validate required fields
            $requiredFields = ['firstName', 'lastName', 'username', 'email', 'password', 'confirmPassword'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                    $_SESSION['signup_error'] = 'Please fill in all required fields';
                    header('Location: ../View/signup.php');
                    exit();
                }
            }
            
            // Check password confirmation
            if ($_POST['password'] !== $_POST['confirmPassword']) {
                $_SESSION['signup_error'] = 'Passwords do not match';
                header('Location: ../View/signup.php');
                exit();
            }
            
            // Check terms agreement
            if (!isset($_POST['terms'])) {
                $_SESSION['signup_error'] = 'Please agree to the terms and conditions';
                header('Location: ../View/signup.php');
                exit();
            }
            
            // Attempt registration
            $result = registerUser($_POST);
            
            if ($result['success']) {
                $_SESSION['signup_success'] = $result['message'];
                header('Location: ../View/login.php');
                exit();
            } else {
                $_SESSION['signup_error'] = $result['message'];
                header('Location: ../View/signup.php');
                exit();
            }
            break;
            
        case 'login':
            // Validate required fields
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $_SESSION['login_error'] = 'Please fill in all fields';
                header('Location: ../View/login.php');
                exit();
            }
            
            // First try admin authentication
            $admin = authenticateAdmin($_POST['email'], $_POST['password']);
            
            if ($admin) {
                // Admin login successful
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_full_name'] = $admin['full_name'];
                $_SESSION['admin_logged_in'] = true;
                
                // Log the activity
                logAdminActivity($admin['id'], 'login', 'Admin logged in');
                
                $_SESSION['login_success'] = 'Admin login successful';
                header('Location: ../View/admin_dashboard.php');
                exit();
            }
            
            // If not admin, try regular user login
            $result = loginUser($_POST['email'], $_POST['password']);
            
            if ($result['success']) {
                $_SESSION['login_success'] = $result['message'];
                header('Location: ../home.php');
                exit();
            } else {
                $_SESSION['login_error'] = 'Invalid email or password';
                header('Location: ../View/login.php');
                exit();
            }
            break;
            
        default:
            header('Location: ../home.php');
            exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'logout':
            logoutUser();
            break;
            
        default:
            header('Location: ../home.php');
            exit();
    }
} else {
    // No valid action, redirect to home
    header('Location: ../home.php');
    exit();
}
?>
