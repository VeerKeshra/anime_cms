<?php
session_start();
require_once '../includes/config.php';

// Check if the user is logged in and redirect to login page if not
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Include the appropriate navigation based on the user's role
if ($_SESSION['role'] == 'admin') {
    include '../includes/nav_admin.php';
} else {
    include '../includes/nav_user.php';
}

// Fetch counts for dashboard metrics
$sql_users = "SELECT COUNT(*) as count FROM Users";
$sql_anime = "SELECT COUNT(*) as count FROM Anime";
$sql_episodes = "SELECT COUNT(*) as count FROM Episodes";
$sql_reviews = "SELECT COUNT(*) as count FROM Reviews";
$sql_characters = "SELECT COUNT(*) as count FROM Characters";

$stmt_users = $pdo->prepare($sql_users);
$stmt_anime = $pdo->prepare($sql_anime);
$stmt_episodes = $pdo->prepare($sql_episodes);
$stmt_reviews = $pdo->prepare($sql_reviews);
$stmt_characters = $pdo->prepare($sql_characters);

$stmt_users->execute();
$stmt_anime->execute();
$stmt_episodes->execute();
$stmt_reviews->execute();
$stmt_characters->execute();

$count_users = $stmt_users->fetch(PDO::FETCH_ASSOC)['count'];
$count_anime = $stmt_anime->fetch(PDO::FETCH_ASSOC)['count'];
$count_episodes = $stmt_episodes->fetch(PDO::FETCH_ASSOC)['count'];
$count_reviews = $stmt_reviews->fetch(PDO::FETCH_ASSOC)['count'];
$count_characters = $stmt_characters->fetch(PDO::FETCH_ASSOC)['count'];

unset($stmt_users, $stmt_anime, $stmt_episodes, $stmt_reviews, $stmt_characters);

// Fetch details for anime, characters, and episodes
$sql_anime_details = "SELECT * FROM Anime";
$sql_characters_details = "SELECT * FROM Characters";
$sql_episodes_details = "SELECT * FROM Episodes";

$stmt_anime_details = $pdo->prepare($sql_anime_details);
$stmt_characters_details = $pdo->prepare($sql_characters_details);
$stmt_episodes_details = $pdo->prepare($sql_episodes_details);

$stmt_anime_details->execute();
$stmt_characters_details->execute();
$stmt_episodes_details->execute();

$animes = $stmt_anime_details->fetchAll(PDO::FETCH_ASSOC);
$characters = $stmt_characters_details->fetchAll(PDO::FETCH_ASSOC);
$episodes = $stmt_episodes_details->fetchAll(PDO::FETCH_ASSOC);

unset($stmt_anime_details, $stmt_characters_details, $stmt_episodes_details);

// Fetch reviews
$sql_reviews = "SELECT Reviews.*, Users.username FROM Reviews JOIN Users ON Reviews.user_id = Users.user_id";
$stmt_reviews = $pdo->prepare($sql_reviews);
$stmt_reviews->execute();
$reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

unset($stmt_reviews, $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/search.css">
</head>
<body>
    <div class="wrapper">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
        <p>This is your dashboard. Here are some statistics:</p>
        <div class="stats">
            <div class="stat">
                <h3>Users</h3>
                <p><?php echo $count_users; ?></p>
            </div>
            <div class="stat">
                <h3>Anime</h3>
                <p><?php echo $count_anime; ?></p>
            </div>
            <div class="stat">
                <h3>Episodes</h3>
                <p><?php echo $count_episodes; ?></p>
            </div>
            <div class="stat">
                <h3>Reviews</h3>
                <p><?php echo $count_reviews; ?></p>
            </div>
            <div class="stat">
                <h3>Characters</h3>
                <p><?php echo $count_characters; ?></p>
            </div>
        </div>
        <div>
            <a href="manage_reviews.php" class="btn">Manage My Reviews</a>
            <a href="add_review.php" class="btn">Add Review</a>
        </div>
        <div class="tab-buttons">
            <button id="anime-tab" class="btn">Anime</button>
            <button id="characters-tab" class="btn">Characters</button>
            <button id="episodes-tab" class="btn">Episodes</button>
        </div>

        <div id="anime-content" class="tab-content">
    <h3>Anime</h3>
    <form id="search-form-anime">
        <select id="anime-dropdown">
            <option value="">Select Anime</option>
            <?php foreach ($animes as $anime): ?>
                <option value="<?php echo htmlspecialchars($anime['title']); ?>"><?php echo htmlspecialchars($anime['title']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="anime-search" placeholder="Search Anime">
        <button type="button" onclick="searchAnime()">Search</button>
    </form>
    <div id="anime-results"></div>
</div>

<div id="characters-content" class="tab-content" style="display:none;">
    <h3>Characters</h3>
    <form id="search-form-characters">
        <select id="character-dropdown">
            <option value="">Select Character</option>
            <?php foreach ($characters as $character): ?>
                <option value="<?php echo htmlspecialchars($character['name']); ?>"><?php echo htmlspecialchars($character['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="character-search" placeholder="Search Characters">
        <button type="button" onclick="searchCharacter()">Search</button>
    </form>
    <div id="character-results"></div>
</div>

<div id="episodes-content" class="tab-content" style="display:none;">
    <h3>Episodes</h3>
    <form id="search-form-episodes">
        <select id="episode-dropdown">
            <option value="">Select Episode</option>
            <?php foreach ($episodes as $episode): ?>
                <option value="<?php echo htmlspecialchars($episode['title']); ?>"><?php echo htmlspecialchars($episode['title']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="episode-search" placeholder="Search Episodes">
        <button type="button" onclick="searchEpisode()">Search</button>
    </form>
    <div id="episode-results"></div>
</div>

    <script>
        document.getElementById('anime-tab').addEventListener('click', function() {
    document.getElementById('anime-content').style.display = 'block';
    document.getElementById('characters-content').style.display = 'none';
    document.getElementById('episodes-content').style.display = 'none';
});

document.getElementById('characters-tab').addEventListener('click', function() {
    document.getElementById('anime-content').style.display = 'none';
    document.getElementById('characters-content').style.display = 'block';
    document.getElementById('episodes-content').style.display = 'none';
});

document.getElementById('episodes-tab').addEventListener('click', function() {
    document.getElementById('anime-content').style.display = 'none';
    document.getElementById('characters-content').style.display = 'none';
    document.getElementById('episodes-content').style.display = 'block';
});

function normalizeString(str) {
    return str.toLowerCase().replace(/\s+/g, '');
}

function searchAnime() {
    console.log("Searching anime...");
    var searchValue = normalizeString(document.getElementById('anime-search').value);
    var dropdownValue = normalizeString(document.getElementById('anime-dropdown').value);
    var resultsDiv = document.getElementById('anime-results');
    resultsDiv.innerHTML = '';

    var animes = <?php echo json_encode($animes); ?>;
    var reviews = <?php echo json_encode($reviews); ?>;
    animes.forEach(function(anime) {
        var name = normalizeString(anime.title);
        if ((searchValue && name.includes(searchValue)) || (dropdownValue && name === dropdownValue)) {
            var animeDiv = document.createElement('div');
            animeDiv.className = 'anime-item';
            animeDiv.innerHTML = `<h4>${anime.title}</h4>
                                  <div class="description">
                                      <p><strong>Genre:</strong> ${anime.genre}</p>
                                      <p><strong>Release Date:</strong> ${anime.release_date}</p>
                                      <p>${anime.description}</p>
                                  </div>
                                  <h5>Reviews:</h5>`;
            var reviewList = reviews.filter(review => review.category_type === 'anime' && review.category_id == anime.anime_id);
            reviewList.forEach(function(review) {
                var reviewDiv = document.createElement('div');
                reviewDiv.className = 'review-item';
                reviewDiv.innerHTML = `<div class="review-header">
                                           <span class="review-author">${review.username}</span>
                                           <span class="review-rating">${review.rating}/10</span>
                                       </div>
                                       <p class="review-text">${review.review_text}</p>`;
                animeDiv.appendChild(reviewDiv);
            });

            animeDiv.innerHTML += `</div>`;
            resultsDiv.appendChild(animeDiv);
        }
    });
}

function searchCharacter() {
    console.log("Searching characters...");
    var searchValue = normalizeString(document.getElementById('character-search').value);
    var dropdownValue = normalizeString(document.getElementById('character-dropdown').value);
    var resultsDiv = document.getElementById('character-results');
    resultsDiv.innerHTML = '';

    var characters = <?php echo json_encode($characters); ?>;
    var reviews = <?php echo json_encode($reviews); ?>;
    characters.forEach(function(character) {
        var name = normalizeString(character.name);
        if ((searchValue && name.includes(searchValue)) || (dropdownValue && name === dropdownValue)) {
            var characterDiv = document.createElement('div');
            characterDiv.className = 'character-item';
            characterDiv.innerHTML = `<h4>${character.name}</h4>
                                      <div class="description">
                                          <p><strong>Role:</strong> ${character.role}</p>
                                          <p>${character.description}</p>
                                      </div>
                                      <h5>Reviews:</h5>`;
            var reviewList = reviews.filter(review => review.category_type === 'character' && review.category_id == character.character_id);
            reviewList.forEach(function(review) {
                var reviewDiv = document.createElement('div');
                reviewDiv.className = 'review-item';
                reviewDiv.innerHTML = `<div class="review-header">
                                           <span class="review-author">${review.username}</span>
                                           <span class="review-rating">${review.rating}/10</span>
                                       </div>
                                       <p class="review-text">${review.review_text}</p>`;
                characterDiv.appendChild(reviewDiv);
            });

            characterDiv.innerHTML += `</div>`;
            resultsDiv.appendChild(characterDiv);
        }
    });
}

function searchEpisode() {
    console.log("Searching episodes...");
    var searchValue = normalizeString(document.getElementById('episode-search').value);
    var dropdownValue = normalizeString(document.getElementById('episode-dropdown').value);
    var resultsDiv = document.getElementById('episode-results');
    resultsDiv.innerHTML = '';

    var episodes = <?php echo json_encode($episodes); ?>;
    var reviews = <?php echo json_encode($reviews); ?>;
    episodes.forEach(function(episode) {
        var name = normalizeString(episode.title);
        if ((searchValue && name.includes(searchValue)) || (dropdownValue && name === dropdownValue)) {
            var episodeDiv = document.createElement('div');
            episodeDiv.className = 'episode-item';
            episodeDiv.innerHTML = `<h4>${episode.title}</h4>
                                    <div class="description">
                                        <p><strong>Air Date:</strong> ${episode.air_date}</p>
                                        <p>${episode.description}</p>
                                    </div>
                                    <h5>Reviews:</h5>`;
            var reviewList = reviews.filter(review => review.category_type === 'episode' && review.category_id == episode.episode_id);
            reviewList.forEach(function(review) {
                var reviewDiv = document.createElement('div');
                reviewDiv.className = 'review-item';
                reviewDiv.innerHTML = `<div class="review-header">
                                           <span class="review-author">${review.username}</span>
                                           <span class="review-rating">${review.rating}/10</span>
                                       </div>
                                       <p class="review-text">${review.review_text}</p>`;
                episodeDiv.appendChild(reviewDiv);
            });

            episodeDiv.innerHTML += `</div>`;
            resultsDiv.appendChild(episodeDiv);
        }
    });
}

    </script>
</body>
</html>
