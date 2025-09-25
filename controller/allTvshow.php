<?php
require_once('../model/tvShowModel.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get all TV shows
    $shows = getAllTVShows();

    $tvList = array_map(function($show) {
        return [
            'title'      => $show['title'],
            'genre'      => $show['genre'],
            'status'     => $show['status'],
            'start_date' => $show['start_date'],
            'end_date'   => $show['end_date'],
            'seasons'    => $show['seasons'],
            'poster_url' => $show['poster_url']
        ];
    }, $shows);

    $tvTrending = getTrendingTVShows();

    $tvShowList = array_map(function($tv) {
        return [
            'title'        => $tv['title'],
            'poster'       => $tv['poster_url'],
            'genre'        => $tv['genre'],
            'status'       => $tv['status'],
            'start_date'   => $tv['start_date'],
            'views'        => $tv['views']
        ];
    }, $tvTrending);

    $response = [
        'all_tv_show' => $tvList,
        'trending'    => $tvShowList
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
