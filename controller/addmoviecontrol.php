<?php
require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $con = getConnection();

    
    $title = $genre = $status = $date = $poster = "";
    $titleErr = $genreErr = $statusErr = $dateErr = $posterErr = "";
    $hasError = false;

    
    if (empty($_POST["title"])) {
        $titleErr = "Title is required";
        $hasError = true;
    } else {
        $title = mysqli_real_escape_string($con, htmlspecialchars($_POST["title"]));
    }

    
    if (empty($_POST["genre"])) {
        $genreErr = "At least one genre is required";
        $hasError = true;
    } else {
        $genre = implode(",", $_POST["genre"]);
    }

    
    if (empty($_POST["status"])) {
        $statusErr = "Status is required";
        $hasError = true;
    } else {
        $status = mysqli_real_escape_string($con, htmlspecialchars($_POST["status"]));
    }

    
    if (empty($_POST["date"])) {
        $dateErr = "Release date is required";
        $hasError = true;
    } else {
        $date = mysqli_real_escape_string($con, htmlspecialchars($_POST["date"]));
    }

    
    if (empty($_POST["poster"])) {
        $posterErr = "Poster URL is required";
        $hasError = true;
    } else {
        $poster = htmlspecialchars($_POST["poster"]);
        if (!filter_var($poster, FILTER_VALIDATE_URL)) {
            $posterErr = "Invalid URL format";
            $hasError = true;
        } else {
            $poster = mysqli_real_escape_string($con, $poster);
        }
    }

    
    if (!$hasError) {
        $sql = "INSERT INTO movie (title, genre, status, release_date, poster_url)
                VALUES ('$title', '$genre', '$status', '$date', '$poster')";

        if (mysqli_query($con, $sql)) {
            mysqli_close($con);
            header("Location: addMovie.php?success=1");
            exit();
        } else {
            mysqli_close($con);
            header("Location: addMovie.php?error=1");
            exit();
        }
    } else {
        
        session_start();
        $_SESSION['form_errors'] = [
            'title' => $titleErr,
            'genre' => $genreErr,
            'status' => $statusErr,
            'date' => $dateErr,
            'poster' => $posterErr
        ];
        $_SESSION['old_input'] = $_POST;

        header("Location: addMovie.php");
        exit();
    }
}
?>