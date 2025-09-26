<?php
header('Content-Type: application/json');
require_once '../Model/movieModel.php';

// Format movie data for frontend compatibility
function formatMovieForFrontend($movie) {
    return [
        'id' => $movie['id'],
        'title' => $movie['title'],
        'poster' => $movie['poster_path'],
        'release_date' => $movie['release_date'],
        'vote_average' => (float)$movie['vote_average'],
        'overview' => $movie['overview'],
        'runtime' => (int)$movie['runtime'],
        'view_count' => (int)($movie['view_count'] ?? 0)
    ];
}

// Get all movies from database
function getAllMoviesFromDB() {
    $movies = getAllMovies();
    $formattedMovies = [];
    
    foreach ($movies as $movie) {
        $formattedMovies[] = formatMovieForFrontend($movie);
    }
    
    return $formattedMovies;
}

// Get trending movies from database
function getTrendingMoviesFromDB() {
    $movies = getTrendingMovies();
    $formattedMovies = [];
    
    foreach ($movies as $movie) {
        $formattedMovies[] = formatMovieForFrontend($movie);
    }
    
    return $formattedMovies;
}

// Search movies in database
function searchMoviesInDB($query) {
    if (empty($query)) {
        return [];
    }
    
    $movies = searchMovies($query);
    $formattedMovies = [];
    
    foreach ($movies as $movie) {
        $formattedMovies[] = formatMovieForFrontend($movie);
    }
    
    // Limit to 8 results for better UX
    return array_slice($formattedMovies, 0, 8);
}

// Get movie by ID from database
function getMovieByIdFromDB($id) {
    $movie = getMovieById($id);
    
    if ($movie) {
        return formatMovieForFrontend($movie);
    }
    
    return null;
}

// Get top rated movies from database
function getTopRatedMoviesFromDB($limit = 10) {
    $movies = getTopRatedMovies($limit);
    $formattedMovies = [];
    
    foreach ($movies as $movie) {
        $formattedMovies[] = formatMovieForFrontend($movie);
    }
    
    return $formattedMovies;
}

// Main request handler
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Initialize variables
        $action = 'trending';
        $query = '';
        $id = '';
        $genre = '';
        
        // Check Content-Type header
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            // Handle JSON data
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input) {
                $action = isset($input['action']) ? $input['action'] : 'trending';
                $query = isset($input['query']) ? trim($input['query']) : '';
                $id = isset($input['id']) ? $input['id'] : '';
                $genre = isset($input['genre']) ? $input['genre'] : '';
            }
        } else {
            // Handle form-encoded data (default)
            $action = isset($_POST['action']) ? $_POST['action'] : 'trending';
            $query = isset($_POST['query']) ? trim($_POST['query']) : '';
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
        }
        
        switch ($action) {
            case 'search':
                $searchResults = searchMoviesInDB($query);
                $response = [
                    'success' => true,
                    'results' => $searchResults,
                    'total_results' => count($searchResults),
                    'query' => $query,
                    'message' => 'Search completed successfully'
                ];
                break;
                
            case 'get_movie':
                $movie = getMovieByIdFromDB($id);
                if ($movie) {
                    $response = [
                        'success' => true,
                        'movie' => $movie,
                        'message' => 'Movie retrieved successfully'
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Movie not found'
                    ];
                }
                break;
                
            case 'increment_view':
                if ($id) {
                    $result = incrementViewCount($id);
                    if ($result) {
                        $response = [
                            'success' => true,
                            'message' => 'View count incremented successfully'
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Failed to increment view count'
                        ];
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Movie ID is required'
                    ];
                }
                break;
                
            case 'get_all':
            case 'getAllMovies':
                $movies = getAllMoviesFromDB();
                $response = [
                    'success' => true,
                    'movies' => $movies,
                    'all_movies' => $movies, // Add this for compatibility with movie.php
                    'total_results' => count($movies),
                    'message' => 'All movies retrieved successfully'
                ];
                break;
                
            case 'by_genre':
                $movies = getMoviesByGenre($genre);
                $formattedMovies = [];
                foreach ($movies as $movie) {
                    $formattedMovies[] = formatMovieForFrontend($movie);
                }
                $response = [
                    'success' => true,
                    'movies' => $formattedMovies,
                    'total_results' => count($formattedMovies),
                    'genre' => $genre,
                    'message' => 'Movies by genre retrieved successfully'
                ];
                break;
                
            case 'top_rated':
                $movies = getTopRatedMoviesFromDB();
                $response = [
                    'success' => true,
                    'movies' => $movies,
                    'total_results' => count($movies),
                    'message' => 'Top rated movies retrieved successfully'
                ];
                break;
                
            case 'trending':
            default:
                $movies = getTrendingMoviesFromDB();
                $response = [
                    'success' => true,
                    'trending' => array_values($movies),
                    'total_results' => count($movies),
                    'message' => 'Trending movies fetched successfully'
                ];
                break;
        }
        
        echo json_encode($response);
    } else {
        $response = [
            'success' => false,
            'message' => 'Only POST requests are allowed'
        ];
        
        echo json_encode($response);
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error processing request: ' . $e->getMessage(),
        'data' => []
    ];
    
    echo json_encode($response);
}
?>
