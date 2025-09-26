<?php
session_start();
require_once '../Model/adminModel.php';
require_once '../Model/movieModel.php';

// Handle different actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'login':
            handleAdminLogin();
            break;
            
        case 'create_admin':
            handleCreateAdmin();
            break;
            
        case 'update_status':
            handleUpdateStatus();
            break;
            
        case 'delete_admin':
            handleDeleteAdmin();
            break;
            
            
        case 'add_movie':
            handleAddMovie();
            break;
            
        case 'update_movie':
            handleUpdateMovie();
            break;
            
        case 'delete_movie':
            handleDeleteMovie();
            break;
            
        default:
            $_SESSION['error'] = 'Invalid action';
            header('Location: ../View/admin_dashboard.php');
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'logout':
            handleAdminLogout();
            break;
            
        default:
            header('Location: ../View/login.php');
            break;
    }
}

// Handle admin login
function handleAdminLogin() {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username and password are required';
        header('Location: ../View/login.php');
        return;
    }
    
    $admin = authenticateAdmin($username, $password);
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['admin_full_name'] = $admin['full_name'];
        $_SESSION['admin_logged_in'] = true;
        
        // Log the activity
        logAdminActivity($admin['id'], 'login', 'Admin logged in');
        
        $_SESSION['success'] = 'Login successful';
        header('Location: ../View/admin_dashboard.php');
    } else {
        $_SESSION['error'] = 'Invalid username or password';
        header('Location: ../View/login.php');
    }
}

// Handle admin logout
function handleAdminLogout() {
    if (isset($_SESSION['admin_id'])) {
        logAdminActivity($_SESSION['admin_id'], 'logout', 'Admin logged out');
    }
    
    // Clear admin session
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_full_name']);
    unset($_SESSION['admin_logged_in']);
    
    $_SESSION['success'] = 'Logged out successfully';
    header('Location: ../View/login.php');
}

// Handle create admin (only super admin)
function handleCreateAdmin() {
    // Check if user is logged in as super admin
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'super_admin') {
        $_SESSION['error'] = 'Access denied. Only super admin can create admins.';
        header('Location: ../View/admin_dashboard.php');
        return;
    }
    
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $role = $_POST['role'] ?? 'admin';
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: ../View/admin_manage.php');
        return;
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters long';
        header('Location: ../View/admin_manage.php');
        return;
    }
    
    $adminData = [
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'full_name' => $full_name,
        'role' => $role
    ];
    
    $result = createAdmin($adminData, $_SESSION['admin_id']);
    
    if ($result['success']) {
        logAdminActivity($_SESSION['admin_id'], 'create_admin', 'Created admin: ' . $username);
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
    
    header('Location: ../View/admin_manage.php');
}

// Handle update admin status
function handleUpdateStatus() {
    // Check if user is logged in as super admin
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'super_admin') {
        $_SESSION['error'] = 'Access denied. Only super admin can update admin status.';
        header('Location: ../View/admin_dashboard.php');
        return;
    }
    
    $admin_id = $_POST['admin_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (empty($admin_id) || empty($status)) {
        $_SESSION['error'] = 'Admin ID and status are required';
        header('Location: ../View/admin_manage.php');
        return;
    }
    
    // Don't allow changing super admin status
    $admin = getAdminById($admin_id);
    if ($admin['role'] === 'super_admin') {
        $_SESSION['error'] = 'Cannot change super admin status';
        header('Location: ../View/admin_manage.php');
        return;
    }
    
    if (updateAdminStatus($admin_id, $status)) {
        logAdminActivity($_SESSION['admin_id'], 'update_status', 'Updated admin status: ' . $admin['username'] . ' to ' . $status);
        $_SESSION['success'] = 'Admin status updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update admin status';
    }
    
    header('Location: ../View/admin_manage.php');
}

// Handle delete admin
function handleDeleteAdmin() {
    // Check if user is logged in as super admin
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'super_admin') {
        $_SESSION['error'] = 'Access denied. Only super admin can delete admins.';
        header('Location: ../View/admin_dashboard.php');
        return;
    }
    
    $admin_id = $_POST['admin_id'] ?? '';
    
    if (empty($admin_id)) {
        $_SESSION['error'] = 'Admin ID is required';
        header('Location: ../View/admin_manage.php');
        return;
    }
    
    $admin = getAdminById($admin_id);
    if (!$admin) {
        $_SESSION['error'] = 'Admin not found';
        header('Location: ../View/admin_manage.php');
        return;
    }
    
    if (deleteAdmin($admin_id)) {
        logAdminActivity($_SESSION['admin_id'], 'delete_admin', 'Deleted admin: ' . $admin['username']);
        $_SESSION['success'] = 'Admin deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete admin or cannot delete super admin';
    }
    
    header('Location: ../View/admin_manage.php');
}


// Handle add movie (all admins can do this)
function handleAddMovie() {
    // Check if user is logged in as admin
    if (!isset($_SESSION['admin_logged_in'])) {
        $_SESSION['error'] = 'Please login first';
        header('Location: ../View/login.php');
        return;
    }
    
    $title = $_POST['title'] ?? '';
    $overview = $_POST['overview'] ?? '';
    $release_date = $_POST['release_date'] ?? '';
    $runtime = $_POST['runtime'] ?? 0;
    $vote_average = $_POST['vote_average'] ?? 0.0;
    $popularity = $_POST['popularity'] ?? 0.0;
    $poster_path = $_POST['poster_path'] ?? '';
    $backdrop_path = $_POST['backdrop_path'] ?? '';
    $trailer_url = $_POST['trailer_url'] ?? '';
    $tagline = $_POST['tagline'] ?? '';
    
    // Validate required fields
    if (empty($title)) {
        $_SESSION['error'] = 'Movie title is required';
        header('Location: ../View/admin_movies.php');
        return;
    }
    
    $movieData = [
        'title' => $title,
        'original_title' => $title,
        'overview' => $overview,
        'release_date' => $release_date,
        'runtime' => (int)$runtime,
        'vote_average' => (float)$vote_average,
        'vote_count' => 0,
        'popularity' => (float)$popularity,
        'poster_path' => $poster_path,
        'backdrop_path' => $backdrop_path,
        'trailer_url' => $trailer_url,
        'tagline' => $tagline,
        'status' => 'released'
    ];
    
    if (addMovie($movieData)) {
        logAdminActivity($_SESSION['admin_id'], 'add_movie', 'Added movie: ' . $title);
        $_SESSION['success'] = 'Movie added successfully';
    } else {
        $_SESSION['error'] = 'Failed to add movie';
    }
    
    header('Location: ../View/admin_movies.php');
}

// Handle update movie (all admins can do this)
function handleUpdateMovie() {
    // Check if user is logged in as admin
    if (!isset($_SESSION['admin_logged_in'])) {
        $_SESSION['error'] = 'Please login first';
        header('Location: ../View/login.php');
        return;
    }
    
    $movie_id = $_POST['movie_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $overview = $_POST['overview'] ?? '';
    $release_date = $_POST['release_date'] ?? '';
    $runtime = $_POST['runtime'] ?? 0;
    $vote_average = $_POST['vote_average'] ?? 0.0;
    $popularity = $_POST['popularity'] ?? 0.0;
    $poster_path = $_POST['poster_path'] ?? '';
    $backdrop_path = $_POST['backdrop_path'] ?? '';
    $trailer_url = $_POST['trailer_url'] ?? '';
    $tagline = $_POST['tagline'] ?? '';
    
    // Validate required fields
    if (empty($movie_id) || empty($title)) {
        $_SESSION['error'] = 'Movie ID and title are required';
        header('Location: ../View/admin_movies.php');
        return;
    }
    
    // Check if movie exists
    $movie = getMovieById($movie_id);
    if (!$movie) {
        $_SESSION['error'] = 'Movie not found';
        header('Location: ../View/admin_movies.php');
        return;
    }
    
    $movieData = [
        'title' => $title,
        'original_title' => $title,
        'overview' => $overview,
        'release_date' => $release_date,
        'runtime' => (int)$runtime,
        'vote_average' => (float)$vote_average,
        'vote_count' => $movie['vote_count'], // Keep existing vote count
        'popularity' => (float)$popularity,
        'poster_path' => $poster_path,
        'backdrop_path' => $backdrop_path,
        'trailer_url' => $trailer_url,
        'tagline' => $tagline
    ];
    
    if (updateMovie($movie_id, $movieData)) {
        logAdminActivity($_SESSION['admin_id'], 'update_movie', 'Updated movie: ' . $title);
        $_SESSION['success'] = 'Movie updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update movie';
    }
    
    header('Location: ../View/admin_movies.php');
}

// Handle delete movie (all admins can do this)
function handleDeleteMovie() {
    // Check if user is logged in as admin
    if (!isset($_SESSION['admin_logged_in'])) {
        $_SESSION['error'] = 'Please login first';
        header('Location: ../View/login.php');
        return;
    }
    
    $movie_id = $_POST['movie_id'] ?? '';
    
    if (empty($movie_id)) {
        $_SESSION['error'] = 'Movie ID is required';
        header('Location: ../View/admin_movies.php');
        return;
    }
    
    // Get movie details for logging
    $movie = getMovieById($movie_id);
    if (!$movie) {
        $_SESSION['error'] = 'Movie not found';
        header('Location: ../View/admin_movies.php');
        return;
    }
    
    if (deleteMovie($movie_id)) {
        logAdminActivity($_SESSION['admin_id'], 'delete_movie', 'Deleted movie: ' . $movie['title']);
        $_SESSION['success'] = 'Movie deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete movie';
    }
    
    header('Location: ../View/admin_movies.php');
}
?>
