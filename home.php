<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
if (!isset($_SESSION['status']) || $_SESSION['status'] !== true) {
    $key = 'Login';
}else{
    $key = 'Profile';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Movie Landing Page</title>
    <link rel="stylesheet" href="Assets/style.css">
    <link rel="stylesheet" href="Assets/navbar.css">
    <link rel="stylesheet" href="Assets/search.css">
    <script src="Assets/script.js"></script>

</head>

<body>

<nav class="navbar">
    <div class="logo">
      <a href="home.php">MovieDB</a>
    </div>

    <div class="menu">
      <a href="#">Home</a>
      <a href="view/movie.php">Movies</a>

      <?php if (strtolower($key) === 'profile') { ?>
        <div class="dropdown">
          <a href="#" class="dropbtn"><?= $key ?></a>
          <div class="dropdown-content">
            <a href="View/profile.php"><?= $username ?></a>
            <a href="Controller/authController.php?action=logout">Logout</a>
          </div>
        </div>
      <?php } else { ?>
        <a href="View/login.php"><?= $key ?></a>
      <?php } ?>
    </div>
  </nav>

    <div class="banner">
    <h1>Welcome.</h1>
    <p>Millions of movies, TV shows, and people to discover. Explore now.</p>

    <div class="search-box" style="position: relative;">
        <input type="text" id="search-box" placeholder="Search for a movie, tv show" onkeyup="liveSearch()" autocomplete="off">
        <div id="live-suggestions" class="suggestions-dropdown"></div>
    </div>

    <p id="searcherror"></p>
    </div>

    <div class="trending-section" id="trending-section">
     <h2>Trending Movies</h2>
     <div class="movie-slider" id="movie-slider"></div>
    </div>

     <!-- Footer -->
     <?php include 'View/footer.php'; ?>

    <script>
                let xhttp = new XMLHttpRequest();
                xhttp.open('POST', 'Controller/movieController.php', true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send();
                xhttp.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        let movies = JSON.parse(this.responseText);
                        const trending = movies.trending;
                        displayTrendingMovies(trending);
                        console.log(trending);
                    }
                };

    </script>

</body>

</html>
