<?php
require_once('db.php');

function getAllMovies() {
    $con = getConnection();
    $sql = "SELECT * FROM movies ORDER BY created_at DESC";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}

function getTrendingMovies() {
    $con = getConnection();
    // Get top 8 most viewed movies as trending
    $sql = "SELECT * FROM movies ORDER BY view_count DESC, popularity DESC LIMIT 8";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}

function getMovieById($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "SELECT * FROM movies WHERE id='$id'";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

function getMovieByTmdbId($tmdb_id) {
    $con = getConnection();
    $tmdb_id = mysqli_real_escape_string($con, $tmdb_id);

    $sql = "SELECT * FROM movies WHERE tmdb_id='$tmdb_id'";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

function searchMovies($searchTerm) {
    $con = getConnection();
    $searchTerm = mysqli_real_escape_string($con, $searchTerm);

    $sql = "SELECT * FROM movies 
            WHERE title LIKE '%$searchTerm%' 
            OR overview LIKE '%$searchTerm%' 
            OR tagline LIKE '%$searchTerm%'
            ORDER BY popularity DESC";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}

function addMovie($movie) {
    $con = getConnection();
    
    $title = mysqli_real_escape_string($con, $movie['title']);
    $original_title = mysqli_real_escape_string($con, $movie['original_title'] ?? '');
    $overview = mysqli_real_escape_string($con, $movie['overview'] ?? '');
    $release_date = mysqli_real_escape_string($con, $movie['release_date'] ?? '');
    $runtime = isset($movie['runtime']) ? (int)$movie['runtime'] : 0;
    $vote_average = isset($movie['vote_average']) ? (float)$movie['vote_average'] : 0.0;
    $vote_count = isset($movie['vote_count']) ? (int)$movie['vote_count'] : 0;
    $popularity = isset($movie['popularity']) ? (float)$movie['popularity'] : 0.0;
    $poster_path = mysqli_real_escape_string($con, $movie['poster_path'] ?? '');
    $backdrop_path = mysqli_real_escape_string($con, $movie['backdrop_path'] ?? '');
    $trailer_url = mysqli_real_escape_string($con, $movie['trailer_url'] ?? '');
    $imdb_id = mysqli_real_escape_string($con, $movie['imdb_id'] ?? '');
    $tmdb_id = isset($movie['tmdb_id']) ? (int)$movie['tmdb_id'] : null;
    $budget = isset($movie['budget']) ? (int)$movie['budget'] : 0;
    $revenue = isset($movie['revenue']) ? (int)$movie['revenue'] : 0;
    $tagline = mysqli_real_escape_string($con, $movie['tagline'] ?? '');
    $homepage = mysqli_real_escape_string($con, $movie['homepage'] ?? '');
    $original_language = mysqli_real_escape_string($con, $movie['original_language'] ?? 'en');
    $adult = isset($movie['adult']) ? (int)$movie['adult'] : 0;
    $status = mysqli_real_escape_string($con, $movie['status'] ?? 'released');

    $sql = "INSERT INTO movies (title, original_title, overview, release_date, runtime, vote_average, vote_count, popularity, poster_path, backdrop_path, trailer_url, imdb_id, tmdb_id, budget, revenue, tagline, homepage, original_language, adult, status)
            VALUES ('$title', '$original_title', '$overview', '$release_date', $runtime, $vote_average, $vote_count, $popularity, '$poster_path', '$backdrop_path', '$trailer_url', '$imdb_id', " . ($tmdb_id ? $tmdb_id : 'NULL') . ", $budget, $revenue, '$tagline', '$homepage', '$original_language', $adult, '$status')";

    return mysqli_query($con, $sql);
}

function updateMovie($id, $movie) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);
    
    $title = mysqli_real_escape_string($con, $movie['title']);
    $original_title = mysqli_real_escape_string($con, $movie['original_title'] ?? '');
    $overview = mysqli_real_escape_string($con, $movie['overview'] ?? '');
    $release_date = mysqli_real_escape_string($con, $movie['release_date'] ?? '');
    $runtime = isset($movie['runtime']) ? (int)$movie['runtime'] : 0;
    $vote_average = isset($movie['vote_average']) ? (float)$movie['vote_average'] : 0.0;
    $vote_count = isset($movie['vote_count']) ? (int)$movie['vote_count'] : 0;
    $popularity = isset($movie['popularity']) ? (float)$movie['popularity'] : 0.0;
    $poster_path = mysqli_real_escape_string($con, $movie['poster_path'] ?? '');
    $backdrop_path = mysqli_real_escape_string($con, $movie['backdrop_path'] ?? '');
    $trailer_url = mysqli_real_escape_string($con, $movie['trailer_url'] ?? '');
    $tagline = mysqli_real_escape_string($con, $movie['tagline'] ?? '');

    $sql = "UPDATE movies SET 
            title='$title', 
            original_title='$original_title', 
            overview='$overview', 
            release_date='$release_date', 
            runtime=$runtime, 
            vote_average=$vote_average, 
            vote_count=$vote_count, 
            popularity=$popularity, 
            poster_path='$poster_path', 
            backdrop_path='$backdrop_path', 
            trailer_url='$trailer_url', 
            tagline='$tagline', 
            updated_at=CURRENT_TIMESTAMP
            WHERE id='$id'";

    return mysqli_query($con, $sql);
}

function deleteMovie($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "DELETE FROM movies WHERE id='$id'";
    return mysqli_query($con, $sql);
}

function getMoviesByGenre($genre_id) {
    $con = getConnection();
    $genre_id = mysqli_real_escape_string($con, $genre_id);

    $sql = "SELECT m.* FROM movies m 
            INNER JOIN movie_genres mg ON m.id = mg.movie_id 
            WHERE mg.genre_id = '$genre_id'
            ORDER BY m.popularity DESC";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}

function getTopRatedMovies($limit = 10) {
    $con = getConnection();
    $limit = (int)$limit;

    $sql = "SELECT * FROM movies 
            WHERE vote_count > 0 
            ORDER BY vote_average DESC, vote_count DESC 
            LIMIT $limit";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}

function getPopularMovies($limit = 10) {
    $con = getConnection();
    $limit = (int)$limit;

    $sql = "SELECT * FROM movies 
            ORDER BY popularity DESC 
            LIMIT $limit";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}

function getRecentMovies($limit = 10) {
    $con = getConnection();
    $limit = (int)$limit;

    $sql = "SELECT * FROM movies 
            ORDER BY release_date DESC 
            LIMIT $limit";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}


function getMovieCount() {
    $con = getConnection();
    $sql = "SELECT COUNT(*) as count FROM movies";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['count'];
    }
    return 0;
}

// Function to increment view count for a movie
function incrementViewCount($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "UPDATE movies SET view_count = view_count + 1 WHERE id='$id'";
    return mysqli_query($con, $sql);
}

// Function to get movies with highest view count (trending)
function getMostViewedMovies($limit = 8) {
    $con = getConnection();
    $limit = (int)$limit;

    $sql = "SELECT * FROM movies 
            ORDER BY view_count DESC, popularity DESC 
            LIMIT $limit";
    $result = mysqli_query($con, $sql);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    return $movies;
}
?>
