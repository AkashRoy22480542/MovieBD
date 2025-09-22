<?php
session_start();
require_once('../model/movieModel.php');
require_once('../model/userReviewModel.php');
if (!isset($_SESSION['movie'])) {
    echo "No movie selected.";
    exit();
}

$moviedtitle = $_SESSION['movie'];
$movie=getMovieByTitle($moviedtitle['title']);
$review=getReviewsByMovie($moviedtitle['title']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Movie Details - <?= htmlspecialchars($movie['title']) ?></title>
  <link rel="stylesheet" href="../assets/style.css">
  <script src="../assets/index.js"></script>
</head>

<body>
  <button type="button" onclick="goMovie()" style="margin-top: 10px;">Back</button>

  <div class="movie-header">
    <div class="poster">
      <img src="<?=$movie['poster_url'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
    </div>

    <div class="movie-info">
      <h1><?= htmlspecialchars($movie['title']) ?></h1>
      <div class="sub-info">
        18+ | <?= htmlspecialchars($movie['release_date']) ?> | <?= htmlspecialchars($movie['genre']) ?> | <?= htmlspecialchars($movie['runtime']) ?>
      </div>

      <div class="user-score">
        <div class="circle-score"><?= getAverageRatingByMovieTitle($movie['title'])*10 ?>%</div>
        <span>User Score</span>
      </div>

      <?php if (!empty($movie['trailer_url'])): ?>
        <div style="margin-top: 20px;">
          <a href="<?= htmlspecialchars($movie['trailer_url']) ?>" target="_blank">
            <button style="padding:10px 20px; border:none; background:#f5c518; font-weight:bold; border-radius:5px; cursor:pointer;">
              ▶ Play Trailer
            </button>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="release-calendar" style="margin-top: 50px; text-align: center">
    <h2>Release Countdown</h2>
    <h3 id="countdown" style="font-size: 24px; margin-top: 20px;">Loading...</h3>
  </div>
  <script>
    const releaseDate = "<?= $movie['release_date'] ?> 00:00:00";
    updateCountdown(releaseDate);
    setInterval(() => updateCountdown(releaseDate), 1000); 
  </script>

  <div class="overview-section">
    <h2>Overview</h2>
    <p><?= nl2br(htmlspecialchars($movie['overview'])) ?></p>
  </div>

  <div class="main-content">
    <div class="left-content">
      <div class="cast-section">
        <h2>Casts</h2>
        <div class="cast-cards">
          <div class="cast-card">
            <a href="actor_profile.php?name=<?= urlencode('Tom Hardy') ?>">
              <img src="../assets/actors/tom_hardy.jpg" alt="Tom Hardy">
              <p><strong>Tom Hardy</strong></p>
              <p>Walker</p>
            </a>
          </div>
          <div class="cast-card">
            <img src="../assets/actors/jessie_mei_li.jpg" alt="Jessie Mei Li">
            <p><strong>Jessie Mei Li</strong></p>
            <p>Ellie</p>
          </div>
          <div class="cast-card">
            <img src="../assets/actors/timothy_olyphant.jpg" alt="Timothy Olyphant">
            <p><strong>Timothy Olyphant</strong></p>
            <p>Vincent</p>
          </div>
        </div>
      </div>

      <div class="review-section">
      <h2>Reviews</h2>

      <?php if (!empty($review)) : 
          foreach ($review as $r) : ?>
          <div class="review-box">
            <div style="display: flex; align-items: center; gap: 15px;">
              <div style="width: 50px; height: 50px; background: #f5c518; color: black; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <?= strtoupper(substr($r['user_email'], 0, 1)) ?>
              </div>
              <div>
                <h4 style="margin: 0;">A review by <?= explode('@', $r['user_email'])[0] ?></h4>
                <p style="margin: 0; font-size: 14px; color: #777;">
                  ⭐ <?= $r['rating'] * 10 ?>% — Written on <?= date('F j, Y', strtotime($r['created_at'])) ?>
                </p>
              </div>
            </div>
            <p style="margin-top: 15px; font-size: 16px;">
              <?= nl2br(htmlspecialchars($r['review'])) ?>
            </p>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
        <p style="margin: 20px 0;">No reviews yet. Be the first to write one!</p>
      <?php endif; ?>

      <div style="margin-top: 40px;">
        <h3>Write Your Review</h3>
        <form action="../controller/userReviewController.php" method="POST">
          <textarea name="review" placeholder="Write your thoughts about the movie..." required style="width: 100%; height: 120px; padding: 10px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc; margin-top: 10px;"></textarea>
          
          <div style="margin-top: 10px;">
            <label for="rating" style="font-size: 16px;">Your Rating:</label>
            <select name="rating" id="rating" required style="padding: 8px; margin-left: 10px; font-size: 16px;">
              <option value="10">100%</option>
              <option value="9">90%</option>
              <option value="8">80%</option>
              <option value="7">70%</option>
              <option value="6">60%</option>
              <option value="5">50%</option>
              <option value="4">40%</option>
              <option value="3">30%</option>
              <option value="2">20%</option>
              <option value="1">10%</option>
            </select>
          </div>

          <input type="hidden" name="content_title" value="<?= $movie['title'] ?>">
          <input type="hidden" name="content_type" value="movie">


          <button type="submit" style="margin-top: 20px; margin-bottom: 20px; background-color: #f5c518; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;">
            Submit Review
          </button>
        </form>
      </div>


    <div class="right-sidebar">
      <div class="sidebar-item">
        <h4>Status</h4>
        <p><?= htmlspecialchars($movie['status']) ?></p>
      </div>
      <div class="sidebar-item">
        <h4>Original Language</h4>
        <p><?= htmlspecialchars($movie['language']) ?></p>
      </div>
      <div class="sidebar-item">
        <h4>Budget</h4>
        <p>$<?= number_format($movie['budget']) ?></p>
      </div>
      <div class="sidebar-item">
        <h4>Revenue</h4>
        <p><?= $movie['revenue'] > 0 ? '$' . number_format($movie['revenue']) : '-' ?></p>
      </div>
      <div class="sidebar-item">
        <h4>Keywords</h4>
        <p><?= htmlspecialchars($movie['keywords']) ?></p>
      </div>
    </div>
  </div>

  <footer style="background-color: #1a1a1a; color: white; padding: 40px 20px; margin-top: 50px;">
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; max-width: 1200px; margin: auto;">


            <div>
                <h4>The Basics</h4>
                <p>About MovieDB</p>
             
            </div>
            <div>
                <h4>Community</h4>
                <p>Discussions</p>
             
            </div>


        </div>
    </footer> 

</body>
</html>
