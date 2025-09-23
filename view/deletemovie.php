<?php
require_once('../model/movieModel.php');

// Handle deletion by title
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $message = deleteMovieByTitle($title) ? "Movie deleted successfully." : "Failed to delete movie.";
}

// Fetch all movies
$movies = getAllMovies();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Movie by Title</title>
    <link rel="stylesheet" type="text/css" href="../assets/deletemovie.css">
</head>
<body>
    <div class="container">
        <h2>All Movies</h2>

        <?php if (isset($message)): ?>
            <p class="<?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <table>
            <tr>
                <th>Title</th>
                <th>Release Date</th>
                <th>Action</th>
            </tr>
            <?php foreach ($movies as $movie): ?>
            <tr>
                <td><?= htmlspecialchars($movie['title']) ?></td>
                <td><?= htmlspecialchars($movie['release_date']) ?></td>
                <td>
                    <form method="post" action="deletemovie.php" onsubmit="return confirm('Are you sure you want to delete this movie?');">
                        <input type="hidden" name="title" value="<?= htmlspecialchars($movie['title']) ?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
