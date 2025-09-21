<?php
require_once('db.php');

function addTVShow($show) {
    $con = getConnection();

    $title = mysqli_real_escape_string($con, $show['title']);
    $genre = mysqli_real_escape_string($con, is_array($show['genre']) ? implode(",", $show['genre']) : $show['genre']);
    $status = mysqli_real_escape_string($con, $show['status']);
    $start_date = mysqli_real_escape_string($con, $show['start_date']);

    
    $end_date = isset($show['end_date']) && $show['end_date'] !== ''
        ? "'" . mysqli_real_escape_string($con, $show['end_date']) . "'"
        : "NULL";

    
    $seasons = isset($show['seasons']) ? (int)$show['seasons'] : 1;

    $poster_url = mysqli_real_escape_string($con, $show['poster_url']);

    $sql = "INSERT INTO tv_shows (title, genre, status, start_date, end_date, seasons, poster_url)
            VALUES ('$title', '$genre', '$status', '$start_date', $end_date, $seasons, '$poster_url')";

    return mysqli_query($con, $sql);
}



function getTVShowById($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "SELECT * FROM tv_shows WHERE id = '$id'";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

function getTVShowByTitle($title) {
    $con = getConnection();
    $title = mysqli_real_escape_string($con, $title);

    $sql = "SELECT * FROM tv_shows WHERE title = '$title' LIMIT 1";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

function getAllTVShows() {
    $con = getConnection();
    $sql = "SELECT * FROM tv_shows ORDER BY start_date DESC";

    $result = mysqli_query($con, $sql);
    $shows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $shows[] = $row;
    }

    return $shows;
}

function updateTVShowByTitle($title, $show) {
    $con = getConnection();

    $title = mysqli_real_escape_string($con, $title);
    $new_title = mysqli_real_escape_string($con, $show['title']);
    $genre = mysqli_real_escape_string($con, $show['genre']);
    $status = mysqli_real_escape_string($con, $show['status']);
    $start_date = mysqli_real_escape_string($con, $show['start_date']);
    $end_date = isset($show['end_date']) ? "'" . mysqli_real_escape_string($con, $show['end_date']) . "'" : "NULL";
    $seasons = (int)$show['seasons'];
    $poster_url = mysqli_real_escape_string($con, $show['poster_url']);

    $sql = "UPDATE tv_shows 
            SET title='$new_title', genre='$genre', status='$status', 
                start_date='$start_date', end_date=$end_date, seasons=$seasons, poster_url='$poster_url'
            WHERE title='$title'";

    return mysqli_query($con, $sql);
}



function deleteTVShowByTitle($title) {
    $con = getConnection();
    $title = mysqli_real_escape_string($con, $title);

    $sql = "DELETE FROM tv_shows WHERE title='$title'";
    return mysqli_query($con, $sql);
}



function getTrendingTVShows() {
    $con = getConnection();
    $sql = "SELECT * FROM tv_shows ORDER BY views DESC LIMIT 3";
    $result = mysqli_query($con, $sql);

    $tvShows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tvShows[] = $row;
    }

    return $tvShows;
}
?>
