<?php
session_start();
require_once("../model/tvShowModel.php");

$successMessage = "";
$errorMessage = "";
$shows = getAllTVShows();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_title'])) {
    $deleteTitle = $_POST['delete_title'];
    if (deleteTVShowByTitle($deleteTitle)) {
        $successMessage = "TV show \"$deleteTitle\" deleted successfully.";
        $shows = getAllTVShows(); // Refresh list
    } else {
        $errorMessage = "Failed to delete TV show \"$deleteTitle\".";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Delete TV Show</title>
  <link rel="stylesheet" href="../assets/delete_tvshow.css">
</head>
<body>
  <div class="container">
    <h1>Delete TV Show by Title</h1>

    <?php if ($successMessage): ?>
      <div class="success"><?= $successMessage ?></div>
    <?php elseif ($errorMessage): ?>
      <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <?php if (count($shows) > 0): ?>
      <ul class="tvshow-list">
        <?php foreach ($shows as $show): ?>
          <li class="tvshow-item">
            <span class="title"><?= htmlspecialchars($show['title']) ?></span>
            <form method="POST" onsubmit="return confirmDelete();" class="delete-form">
              <input type="hidden" name="delete_title" value="<?= htmlspecialchars($show['title']) ?>">
              <button type="submit">Delete</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No TV shows found.</p>
    <?php endif; ?>
  </div>

  <script src="../assets/delete_tvshow.js"></script>
</body>
</html>
