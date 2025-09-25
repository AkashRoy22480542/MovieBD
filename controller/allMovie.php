<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('../model/movieModel.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $movies = getAllMovies();
    $movieList = array_map(function($movie) {
        return [
            'title' => $movie['title'],
            'poster' => $movie['poster_url'],
            'genre' => $movie['genre'],
            'status' => $movie['status'],
            'release_date' => $movie['release_date']
        ];
    }, $movies);

    $trending = getTrendingMovies(); 
    $trendingList = array_map(function($movie) {
        return [
            'title' => $movie['title'],
            'poster' => $movie['poster_url'],
            'genre' => $movie['genre'],
            'status' => $movie['status'],
            'release_date' => $movie['release_date'],
            'views' => $movie['views']
        ];
    }, $trending);

    $response = [
        'all_movies' => $movieList,
        'trending' => $trendingList
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
