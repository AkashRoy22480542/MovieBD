<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
if (!isset($_SESSION['status']) || $_SESSION['status'] !== true) {
    $key = 'Login';
} else {
    $key = 'Profile';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - MovieDB</title>
    <link rel="stylesheet" href="../Assets/style.css">
    <link rel="stylesheet" href="../Assets/navbar.css">
    <script src="../Assets/script.js"></script>
    <style>
        /* Movie page specific styles */
        .search-bar {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 30px 20px;
            text-align: center;
        }

        .search-bar input {
            width: 100%;
            max-width: 500px;
            padding: 12px 20px;
            border: 2px solid #dc2626;
            border-radius: 25px;
            font-size: 16px;
            margin-right: 10px;
            outline: none;
        }

        .search-bar button {
            background: #dc2626;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .movie-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .movie-card:hover {
            transform: translateY(-5px);
        }

        .movie-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .movie-card-content {
            padding: 15px;
        }

        .movie-card h3 {
            margin-bottom: 5px;
            color: #333;
            font-size: 16px;
        }

        .movie-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .movie-card .rating {
            color: #dc2626;
            font-weight: bold;
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
        <a href="#">Movies</a>

        <?php if (strtolower($key) === 'profile') { ?>
            <div class="dropdown">
                <a href="#" class="dropbtn"><?= $key ?></a>
                <div class="dropdown-content">
                    <a href="profile.php"><?= $username ?></a>
                    <a href="../Controller/authController.php?action=logout">Logout</a>
                </div>
            </div>
        <?php } else { ?>
            <a href="login.php"><?= $key ?></a>
        <?php } ?>
    </div>
</nav>

<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search movie name..." oninput="applyFilters()">
    <button onclick="applyFilters(allMovies)">Search</button>
</div>

<div class="main-content">
    <div class="movies-grid" id="moviesGrid">
        <p>Loading movies...</p>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let allMovies = [];

// Load all movies on page load
let xhttp = new XMLHttpRequest();
xhttp.open('POST', '../Controller/movieController.php', true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send('action=getAllMovies');
xhttp.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
        try {
            let response = JSON.parse(this.responseText);
            if (response.success && response.all_movies) {
                allMovies = response.all_movies;
                loadMovies(allMovies);
            } else {
                document.getElementById('moviesGrid').innerHTML = '<p>No movies found.</p>';
            }
        } catch (error) {
            console.error('Error loading movies:', error);
            document.getElementById('moviesGrid').innerHTML = '<p>Error loading movies.</p>';
        }
    }
};

// Function to load and display movies
function loadMovies(movies) {
    const moviesGrid = document.getElementById('moviesGrid');
    
    if (!movies || movies.length === 0) {
        moviesGrid.innerHTML = '<p>No movies found.</p>';
        return;
    }
    
    const movieCards = movies.map(movie => `
        <div class="movie-card" onclick="viewMovieDetails(${movie.id})">
            <img src="${movie.poster || 'https://via.placeholder.com/200x300?text=No+Image'}" 
                 alt="${movie.title}" 
                 onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
            <div class="movie-card-content">
                <h3>${movie.title}</h3>
                <p><strong>Release Date:</strong> ${movie.release_date || 'N/A'}</p>
                <p><strong>Runtime:</strong> ${movie.runtime ? movie.runtime + ' min' : 'N/A'}</p>
                <p class="rating"><strong>Rating:</strong> ${movie.vote_average || 'N/A'}/10</p>
            </div>
        </div>
    `).join('');
    
    moviesGrid.innerHTML = movieCards;
}

// Function to apply search filter
function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    if (searchTerm === '') {
        loadMovies(allMovies);
        return;
    }
    
    const filteredMovies = allMovies.filter(movie => {
        return movie.title.toLowerCase().includes(searchTerm);
    });
    
    loadMovies(filteredMovies);
}

// Function to view movie details
function viewMovieDetails(movieId) {
    window.location.href = `movie_details.php?id=${movieId}`;
}
</script>

</body>
</html>
