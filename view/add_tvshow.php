<?php
session_start();
require_once("../model/tvShowModel.php");

$username = $_SESSION['username'] ?? '';
$key = (!isset($_SESSION['status']) || $_SESSION['status'] !== true) ? 'Login' : 'Profile';

$title = $status = $start_date = $end_date = $poster = "";
$seasons = 1;
$genre = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? '');
    $start_date = htmlspecialchars($_POST['start_date'] ?? '');
    $end_date = htmlspecialchars($_POST['end_date'] ?? '');
    $seasons = (int)($_POST['seasons'] ?? 1);
    $genre = $_POST['genre'] ?? [];

    if (empty($title)) $errors['title'] = "Title is required.";
    if (empty($genre)) $errors['genre'] = "Select at least one genre.";
    if (empty($status)) $errors['status'] = "Status is required.";
    if (empty($start_date)) $errors['start_date'] = "Start date is required.";
    if ($seasons < 1) $seasons = 1;

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['poster']['tmp_name'];
        $fileName = $_FILES['poster']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
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
        $result = addTVShow([
            'title' => $title,
            'genre' => $genre,
            'status' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date ?: null, // allow NULL
            'seasons' => $seasons,
            'poster_url' => $poster,
        ]);

        if ($result) {
            header("Location: add_tvshow.php?success=1");
            exit;
        } else {
            $errors['general'] = "Failed to add TV show to the database.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add TV Show</title>
  <link rel="stylesheet" href="../assets/addmovie.css">
  <style>
    .error { color: red; font-size: 14px; }
    .genre-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 5px;
      margin-bottom: 10px;
    }
    .genre-checkbox {
      display: flex;
      align-items: center;
      gap: 5px;
      background: #f9f9f9;
      padding: 5px 10px;
      border-radius: 5px;
    }
    .tv-card {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px;
      margin-top: 20px;
      max-width: 400px;
    }
    .tv-card img {
      max-width: 100%;
      height: auto;
    }
  </style>
</head>
<body>

<h1>Add New TV Show</h1>

<?php if (isset($_GET['success'])): ?>
  <p style="color: green;">TV Show added successfully!</p>
<?php elseif (!empty($errors)): ?>
  <p style="color: red;"><?= $errors['general'] ?? 'Please fix the errors below.' ?></p>
<?php endif; ?>

<form method="POST" id="addTVForm" action="add_tvshow.php" enctype="multipart/form-data">
  <label>Title:</label>
  <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required />
  <span class="error"><?= $errors['title'] ?? '' ?></span>

  <label>Genre:</label>
  <div class="genre-container">
    <?php
    $allGenres = ["Action", "Drama", "Crime", "Adventure", "Animation"];
    foreach ($allGenres as $g):
      $checked = in_array($g, $genre) ? "checked" : "";
    ?>
      <label class="genre-checkbox">
        <input type="checkbox" name="genre[]" value="<?= htmlspecialchars($g) ?>" <?= $checked ?>> <?= htmlspecialchars($g) ?>
      </label>
    <?php endforeach; ?>
  </div>
  <span class="error"><?= $errors['genre'] ?? '' ?></span>

  <label>Status:</label>
  <select name="status" required>
    <option value="">Select Status</option>
    <option value="Ongoing" <?= ($status == "Ongoing") ? "selected" : "" ?>>Ongoing</option>
    <option value="Completed" <?= ($status == "Completed") ? "selected" : "" ?>>Completed</option>
  </select>
  <span class="error"><?= $errors['status'] ?? '' ?></span>

  <label>First Air Date:</label>
  <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required />
  <span class="error"><?= $errors['start_date'] ?? '' ?></span>

  <label>End Date (optional):</label>
  <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" />

  <label>Number of Seasons:</label>
  <input type="number" name="seasons" min="1" value="<?= htmlspecialchars($seasons) ?>" />

  <label>Upload Poster:</label>
  <input type="file" name="poster" accept="image/*" required />
  <span class="error"><?= $errors['poster'] ?? '' ?></span>

  <button type="button" onclick="previewTV()">Preview TV Show</button>
  <button type="submit">Add TV Show</button>
</form>

<h2>TV Show Preview</h2>
<div id="previewContainer"></div>

<script>
function previewTV() {
  const form = document.getElementById("addTVForm");
  const formData = new FormData(form);

  const title = formData.get("title");
  const genres = formData.getAll("genre[]");
  const status = formData.get("status");
  const start_date = formData.get("start_date");
  const end_date = formData.get("end_date");
  const seasons = formData.get("seasons");
  const fileInput = form.querySelector("input[name='poster']");
  const file = fileInput.files[0];

  const container = document.getElementById("previewContainer");
  container.innerHTML = ""; // clear old preview

  const reader = new FileReader();
  reader.onload = function(e) {
    const imgSrc = e.target.result;

    const previewHTML = `
      <div class="tv-card">
        <img src="${imgSrc}" alt="${title}" />
        <h3>${title}</h3>
        <p><strong>Genre:</strong> ${genres.join(", ")}</p>
        <p><strong>Status:</strong> ${status}</p>
        <p><strong>First Air Date:</strong> ${start_date}</p>
        <p><strong>End Date:</strong> ${end_date || "N/A"}</p>
        <p><strong>Seasons:</strong> ${seasons}</p>
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