<?php
session_start();

require_once("../model/moviemodel.php");
$username = $_SESSION['username'] ?? '';
$key = (!isset($_SESSION['status']) || $_SESSION['status'] !== true) ? 'Login' : 'Profile';

$title = $status = $date = $poster = $overview = $score = $trailer = $language = $budget = $revenue = $keywords = $runtime = "";
$genre = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? '');
    $date = htmlspecialchars($_POST['date'] ?? '');
    $genre = $_POST['genre'] ?? [];
    $overview = htmlspecialchars($_POST['overview'] ?? '');
    $score = htmlspecialchars($_POST['score'] ?? '');
    $trailer = htmlspecialchars($_POST['trailer'] ?? '');
    $language = htmlspecialchars($_POST['language'] ?? '');
    $budget = htmlspecialchars($_POST['budget'] ?? '');
    $revenue = htmlspecialchars($_POST['revenue'] ?? '');
    $keywords = htmlspecialchars($_POST['keywords'] ?? '');
    $runtime = htmlspecialchars($_POST['runtime'] ?? '');

    if (empty($title)) $errors['title'] = "Title is required.";
    if (empty($genre)) $errors['genre'] = "Select at least one genre.";
    if (empty($status)) $errors['status'] = "Status is required.";
    if (empty($date)) $errors['date'] = "Release date is required.";

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['poster']['tmp_name'];
        $fileName = $_FILES['poster']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../assets/image/';
            if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0755, true);
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $poster = $dest_path;
            } else {
                $errors['poster'] = 'Error moving the uploaded file.';
            }
        } else {
            $errors['poster'] = 'Allowed file types: ' . implode(', ', $allowedfileExtensions);
        }
    } else {
        $errors['poster'] = 'Poster upload is required.';
    }

    if (!$errors) {
        $result = addMovie([
            'title' => $title,
            'status' => $status,
            'date' => $date,
            'poster_url' => $poster,
            'overview' => $overview,
            'score' => $score,
            'trailer' => $trailer,
            'language' => $language,
            'budget' => $budget,
            'revenue' => $revenue,
            'keywords' => $keywords,
            'runtime' => $runtime,
            'genre' => $genre
        ]);

        if ($result) {
            header("Location: addMovie.php?success=1");
            exit;
        } else {
            $errors['general'] = "Failed to add movie to the database.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Movie</title>
  <link rel="stylesheet" href="../assets/addmovie.css">
  
</head>
<body>

<h1>Add New Movie</h1>

<?php if (isset($_GET['success'])): ?>
  <p style="color: green;">Movie added successfully!</p>
<?php elseif (!empty($errors)): ?>
  <p style="color: red;"><?= $errors['general'] ?? 'Please fix the errors below.' ?></p>
<?php endif; ?>

<form method="POST" id="addMovieForm" action="addMovie.php" enctype="multipart/form-data">
  <label>Title:</label>
  <input type="text" name="title" value="<?= $title ?>" required />
  <span class="error"><?= $errors['title'] ?? '' ?></span>

  <label>Genre:</label>
  <div class="genre-container">
    <?php
    $allGenres = ["Action", "Drama", "Crime", "Adventure", "Animation"];
    foreach ($allGenres as $g):
      $checked = in_array($g, $genre) ? "checked" : "";
    ?>
      <label class="genre-checkbox">
        <input type="checkbox" name="genre[]" value="<?= $g ?>" <?= $checked ?>> <?= $g ?>
      </label>
    <?php endforeach; ?>
  </div>
  <span class="error"><?= $errors['genre'] ?? '' ?></span>

  <label>Status:</label>
  <select name="status" required>
    <option value="">Select Status</option>
    <option value="Released" <?= ($status == "Released") ? "selected" : "" ?>>Released</option>
    <option value="Upcoming" <?= ($status == "Upcoming") ? "selected" : "" ?>>Upcoming</option>
  </select>
  <span class="error"><?= $errors['status'] ?? '' ?></span>

  <label>Release Date:</label>
  <input type="date" name="date" value="<?= $date ?>" required />
  <span class="error"><?= $errors['date'] ?? '' ?></span>

  <label>Upload Poster:</label>
  <input type="file" name="poster" accept="image/*" required />
  <span class="error"><?= $errors['poster'] ?? '' ?></span>

  <label>Overview:</label>
  <textarea name="overview"><?= $overview ?></textarea>

  <label>User Score:</label>
  <input type="number" name="score" min="0" max="100" value="<?= $score ?>" />

  <label>Trailer URL:</label>
  <input type="url" name="trailer" value="<?= $trailer ?>" />

  <label>Language:</label>
  <input type="text" name="language" value="<?= $language ?>" />

  <label>Budget (in $):</label>
  <input type="number" name="budget" value="<?= $budget ?>" />

  <label>Revenue (in $):</label>
  <input type="number" name="revenue" value="<?= $revenue ?>" />

  <label>Keywords:</label>
  <input type="text" name="keywords" value="<?= $keywords ?>" />

  <label>Runtime:</label>
  <input type="text" name="runtime" placeholder="e.g. 1h 52m" value="<?= $runtime ?>" />

  <button type="button" onclick="previewMovie()">Preview Movie</button>
  <button type="submit">Add Movie</button>
</form>

<h2>Movie Preview</h2>
<div id="previewContainer"></div>

<script>
function previewMovie() {
  const form = document.getElementById("addMovieForm");
  const formData = new FormData(form);

  const title = formData.get("title");
  const genres = formData.getAll("genre[]");
  const status = formData.get("status");
  const date = formData.get("date");
  const score = formData.get("score");
  const runtime = formData.get("runtime");
  const overview = formData.get("overview");
  const fileInput = form.querySelector("input[name='poster']");
  const file = fileInput.files[0];

  const container = document.getElementById("previewContainer");
  container.innerHTML = ""; // clear old preview

  const reader = new FileReader();
  reader.onload = function(e) {
    const imgSrc = e.target.result;

    const previewHTML = `
      <div class="movie-card">
        <img src="${imgSrc}" alt="${title}" />
        <h3>${title}</h3>
        <p><strong>Genre:</strong> ${genres.join(", ")}</p>
        <p><strong>Status:</strong> ${status}</p>
        <p><strong>Date:</strong> ${date}</p>
        <p><strong>Score:</strong> ${score}</p>
        <p><strong>Runtime:</strong> ${runtime}</p>
        <p><strong>Overview:</strong> ${overview}</p>
      </div>
    `;
    container.innerHTML = previewHTML;
  };

  if (file) {
    reader.readAsDataURL(file);
  } else {
    container.innerHTML = "<p style='color:red;'>Please select a poster image to preview.</p>";
  }
}
</script>

</body>
</html>