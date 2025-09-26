<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
if (!isset($_SESSION['status']) || $_SESSION['status'] !== true) {
    $key = 'Login';
} else {
    $key = 'Profile';
}

// Get movie ID from URL parameter
$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$movieId) {
    header('Location: movie.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Details - MovieDB</title>
    <link rel="stylesheet" href="../Assets/style.css">
    <link rel="stylesheet" href="../Assets/navbar.css">
    <script src="../Assets/script.js"></script>
    <style>
        /* Movie details specific styles */
        .movie-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .movie-header {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .movie-poster {
            flex-shrink: 0;
        }

        .movie-poster img {
            width: 300px;
            height: 450px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .movie-info {
            flex: 1;
            min-width: 300px;
        }

        .movie-title {
            font-size: 2.5rem;
            color: #dc2626;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .movie-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .meta-value {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
        }

        .rating-display {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .movie-overview {
            margin-top: 20px;
        }

        .overview-title {
            font-size: 1.5rem;
            color: #dc2626;
            margin-bottom: 15px;
        }

        .overview-text {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #444;
        }

        .back-button {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s ease;
        }

        .back-button:hover {
            transform: translateY(-2px);
        }

        .loading-state {
            text-align: center;
            padding: 50px;
            font-size: 1.2rem;
            color: #666;
        }

        .error-state {
            text-align: center;
            padding: 50px;
            color: #dc2626;
            font-size: 1.2rem;
        }

        .trending-badge {
            background: #ffd700;
            color: #333;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .movie-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .movie-poster img {
                width: 250px;
                height: 375px;
            }

            .movie-title {
                font-size: 2rem;
            }

            .movie-meta {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <a href="../home.php">MovieDB</a>
    </div>

    <div class="menu">
        <a href="../home.php">Home</a>
        <a href="movie.php">Movies</a>

        <?php if (strtolower($key) === 'profile') { ?>
            <div class="dropdown">
                <a href="#" class="dropbtn"><?= $key ?></a>
                <div class="dropdown-content">
                    <a href="profile.php"><?= $username ?></a>
                    <a href="watchlist.html">My watchList</a>
                    <a href="../Controller/authController.php?action=logout">Logout</a>
                </div>
            </div>
        <?php } else { ?>
            <a href="login.php"><?= $key ?></a>
        <?php } ?>
    </div>
</nav>

<div class="movie-details-container">
    <button class="back-button" onclick="goBackToMovies()">
        ← Back to Movies
    </button>

    <div id="movieDetailsContent">
        <div class="loading-state">Loading movie details...</div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
const movieId = <?= $movieId ?>;

// Load movie details on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMovieDetails(movieId);
});

function loadMovieDetails(id) {
    // First increment the view count
    incrementMovieView(id);
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../Controller/movieController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            const contentDiv = document.getElementById('movieDetailsContent');
            
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    
                    if (response.success && response.movie) {
                        displayMovieDetails(response.movie);
                    } else {
                        contentDiv.innerHTML = '<div class="error-state">Movie not found.</div>';
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    contentDiv.innerHTML = '<div class="error-state">Error loading movie details.</div>';
                }
            } else {
                contentDiv.innerHTML = '<div class="error-state">Failed to load movie details.</div>';
            }
        }
    };
    
    xhr.send(`action=get_movie&id=${id}`);
}

function incrementMovieView(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../Controller/movieController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            try {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    console.log('View count incremented');
                }
            } catch (error) {
                console.log('Error incrementing view count:', error);
            }
        }
    };
    
    xhr.send(`action=increment_view&id=${id}`);
}

function displayMovieDetails(movie) {
    const contentDiv = document.getElementById('movieDetailsContent');
    
    // Update page title
    document.title = `${movie.title} - MovieDB`;
    
    const movieHTML = `
        <div class="movie-header">
            <div class="movie-poster">
                <img src="${movie.poster || 'https://via.placeholder.com/300x450?text=No+Image'}" 
                     alt="${movie.title}" 
                     onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
            </div>
            
            <div class="movie-info">
                <h1 class="movie-title">${movie.title}</h1>
                
                <div class="movie-meta">
                    <div class="meta-item">
                        <div class="meta-label">Release Date</div>
                        <div class="meta-value">${movie.release_date || 'N/A'}</div>
                    </div>
                    
                    <div class="meta-item">
                        <div class="meta-label">Runtime</div>
                        <div class="meta-value">${movie.runtime ? movie.runtime + ' minutes' : 'N/A'}</div>
                    </div>
                    
                    <div class="meta-item">
                        <div class="meta-label">Rating</div>
                        <div class="meta-value">
                            <div class="rating-display">${movie.vote_average || 'N/A'}/10</div>
                        </div>
                    </div>
                </div>
                
                <div class="movie-overview">
                    <h3 class="overview-title">Overview</h3>
                    <p class="overview-text">${movie.overview || 'No overview available for this movie.'}</p>
                </div>
            </div>
        </div>
    `;
    
    contentDiv.innerHTML = movieHTML;
}

function goBackToMovies() {
    window.location.href = 'movie.php';
}
</script>

</body>
</html>
