<?php
session_start();
require_once '../Model/adminModel.php';
require_once '../Model/movieModel.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$admin = getAdminById($_SESSION['admin_id']);
$all_movies = getAllMovies();
$total_movies = getMovieCount();

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
    <title>Manage Movies - MovieDB Admin</title>
    <link rel="stylesheet" href="../Assets/style.css">
    <link rel="stylesheet" href="../Assets/admin.css">
</head>
<body>
    <nav class="admin-navbar">
        <h1>MovieDB Admin</h1>
        <div class="nav-links">
            <span>Welcome, <?= htmlspecialchars($admin['full_name']) ?></span>
            <a href="admin_dashboard.php">Dashboard</a>
            <?php if ($admin['role'] === 'super_admin'): ?>
                <a href="admin_manage.php">Manage Admins</a>
            <?php endif; ?>
            <a href="../Controller/authController.php?action=logout">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="page-header">
            <h2>Manage Movies</h2>
            <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number"><?= $total_movies ?></div>
                <div class="stat-label">Total Movies</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= count(getTrendingMovies()) ?></div>
                <div class="stat-label">Trending Movies</div>
            </div>
            <div class="stat-item">
                <button type="button" class="toggle-form" onclick="toggleAddForm()">+ Add New Movie</button>
            </div>
        </div>

        <div id="addMovieForm" class="add-movie-section form-hidden">
            <h3 style="color: #dc2626; margin-bottom: 15px;">Add New Movie</h3>
            <form method="POST" action="../Controller/adminController.php">
                <input type="hidden" name="action" value="add_movie">
                
                <div class="form-group">
                    <label for="title">Movie Title *</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="overview">Overview/Synopsis</label>
                    <textarea id="overview" name="overview" placeholder="Brief description of the movie..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="release_date">Release Date</label>
                        <input type="date" id="release_date" name="release_date">
                    </div>
                    <div class="form-group">
                        <label for="runtime">Runtime (minutes)</label>
                        <input type="number" id="runtime" name="runtime" min="0">
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label for="vote_average">Rating (1-10)</label>
                        <input type="number" id="vote_average" name="vote_average" min="0" max="10" step="0.1">
                    </div>
                    <div class="form-group">
                        <label for="popularity">Popularity Score</label>
                        <input type="number" id="popularity" name="popularity" min="0" step="0.1">
                    </div>
                    <div class="form-group">
                        <label for="tagline">Tagline</label>
                        <input type="text" id="tagline" name="tagline">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="poster_path">Poster Image URL</label>
                        <input type="url" id="poster_path" name="poster_path" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label for="backdrop_path">Backdrop Image URL</label>
                        <input type="url" id="backdrop_path" name="backdrop_path" placeholder="https://...">
                    </div>
                </div>

                <div class="form-group">
                    <label for="trailer_url">Trailer URL</label>
                    <input type="url" id="trailer_url" name="trailer_url" placeholder="https://youtube.com/...">
                </div>

                <button type="submit" class="add-btn">Add Movie</button>
            </form>
        </div>

        <div id="editMovieForm" class="add-movie-section form-hidden">
            <h3 style="color: #dc2626; margin-bottom: 15px;">Edit Movie</h3>
            <form method="POST" action="../Controller/adminController.php">
                <input type="hidden" name="action" value="update_movie">
                <input type="hidden" id="edit_movie_id" name="movie_id">
                
                <div class="form-group">
                    <label for="edit_title">Movie Title *</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="edit_overview">Overview/Synopsis</label>
                    <textarea id="edit_overview" name="overview" placeholder="Brief description of the movie..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_release_date">Release Date</label>
                        <input type="date" id="edit_release_date" name="release_date">
                    </div>
                    <div class="form-group">
                        <label for="edit_runtime">Runtime (minutes)</label>
                        <input type="number" id="edit_runtime" name="runtime" min="0">
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label for="edit_vote_average">Rating (1-10)</label>
                        <input type="number" id="edit_vote_average" name="vote_average" min="0" max="10" step="0.1">
                    </div>
                    <div class="form-group">
                        <label for="edit_popularity">Popularity Score</label>
                        <input type="number" id="edit_popularity" name="popularity" min="0" step="0.1">
                    </div>
                    <div class="form-group">
                        <label for="edit_tagline">Tagline</label>
                        <input type="text" id="edit_tagline" name="tagline">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_poster_path">Poster Image URL</label>
                        <input type="url" id="edit_poster_path" name="poster_path" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label for="edit_backdrop_path">Backdrop Image URL</label>
                        <input type="url" id="edit_backdrop_path" name="backdrop_path" placeholder="https://...">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_trailer_url">Trailer URL</label>
                    <input type="url" id="edit_trailer_url" name="trailer_url" placeholder="https://youtube.com/...">
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="add-btn">Update Movie</button>
                    <button type="button" class="add-btn" onclick="cancelEdit()" style="background: #6b7280;">Cancel</button>
                </div>
            </form>
        </div>

        <div class="movies-table">
            <h3 style="color: #dc2626; margin-bottom: 15px;">All Movies</h3>
            <?php if (empty($all_movies)): ?>
                <p>No movies found in the database.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>Title</th>
                            <th>Overview</th>
                            <th>Release Date</th>
                            <th>Runtime</th>
                            <th>Rating</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_movies as $movie): ?>
                            <tr>
                                <td>
                                    <?php if ($movie['poster_path']): ?>
                                        <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="movie-poster">
                                    <?php else: ?>
                                        <div style="width:40px;height:60px;background:#ddd;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#666;">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="movie-title" title="<?= htmlspecialchars($movie['title']) ?>">
                                        <?= htmlspecialchars($movie['title']) ?>
                                    </div>
                                    <?php if ($movie['tagline']): ?>
                                        <div style="font-size:0.8rem;color:#999;"><?= htmlspecialchars($movie['tagline']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="movie-overview" title="<?= htmlspecialchars($movie['overview']) ?>">
                                        <?= htmlspecialchars($movie['overview']) ?>
                                    </div>
                                </td>
                                <td><?= $movie['release_date'] ? date('M j, Y', strtotime($movie['release_date'])) : 'N/A' ?></td>
                                <td><?= $movie['runtime'] ? $movie['runtime'] . ' min' : 'N/A' ?></td>
                                <td>
                                    <?php if ($movie['vote_average'] > 0): ?>
                                        <span class="rating"><?= number_format($movie['vote_average'], 1) ?></span>
                                    <?php else: ?>
                                        <span style="color:#999;">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= number_format($movie['view_count']) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-small btn-edit" onclick="editMovie(<?= $movie['id'] ?>)">
                                            Edit
                                        </button>
                                        <form method="POST" action="../Controller/adminController.php" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_movie">
                                            <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                                            <button type="submit" class="btn-small btn-delete" onclick="return confirm('Are you sure you want to delete this movie? This action cannot be undone.')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Pass movie data from PHP to JavaScript for edit functionality
        <?php echo "const moviesData = " . json_encode($all_movies) . ";"; ?>
    </script>
    <script src="../Assets/admin.js"></script>
</body>
</html>
