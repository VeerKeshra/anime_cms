<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../includes/config.php';

$user_id = $_SESSION["id"];
$anime_id = $episode_id = $rating = $review_text = "";
$anime_id_err = $rating_err = $review_text_err = "";

// Get the ID from the URL
$id = isset($_GET["id"]) ? trim($_GET["id"]) : null;

// Get Anime list
$sql = "SELECT anime_id, title FROM Anime";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->execute();
    $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    unset($stmt);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["anime_id"]))) {
        $anime_id_err = "Please select an anime.";
    } else {
        $anime_id = trim($_POST["anime_id"]);
    }

    $episode_id = trim($_POST["episode_id"]);

    if (empty(trim($_POST["rating"]))) {
        $rating_err = "Please enter a rating.";
    } else {
        $rating = trim($_POST["rating"]);
    }

    if (empty(trim($_POST["review_text"]))) {
        $review_text_err = "Please enter a review.";
    } else {
        $review_text = trim($_POST["review_text"]);
    }

    if (empty($anime_id_err) && empty($rating_err) && empty($review_text_err)) {
        $sql = "UPDATE Reviews SET anime_id = :anime_id, episode_id = :episode_id, rating = :rating, review_text = :review_text WHERE review_id = :id";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":anime_id", $param_anime_id, PDO::PARAM_INT);
            $stmt->bindParam(":episode_id", $param_episode_id, PDO::PARAM_INT);
            $stmt->bindParam(":rating", $param_rating, PDO::PARAM_INT);
            $stmt->bindParam(":review_text", $param_review_text, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

            $param_anime_id = $anime_id;
            $param_episode_id = $episode_id;
            $param_rating = $rating;
            $param_review_text = $review_text;
            $param_id = $id;

            if ($stmt->execute()) {
                header("location: manage_reviews.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
} else {
    if ($id) {
        $sql = "SELECT * FROM Reviews WHERE review_id = :id";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
            $param_id = $id;

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $anime_id = $row["anime_id"];
                    $episode_id = $row["episode_id"];
                    $rating = $row["rating"];
                    $review_text = $row["review_text"];
                } else {
                    echo "No record found.";
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    } else {
        echo "ID is missing.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Review</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit Review</h2>
        <p>Please fill this form to update the review.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <div class="form-group <?php echo (!empty($anime_id_err)) ? 'has-error' : ''; ?>">
                <label>Anime</label>
                <select name="anime_id" class="form-control">
                    <option value="">Select Anime</option>
                    <?php foreach ($animes as $anime): ?>
                        <option value="<?php echo $anime['anime_id']; ?>" <?php echo ($anime_id == $anime['anime_id']) ? 'selected' : ''; ?>><?php echo $anime['title']; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block"><?php echo $anime_id_err; ?></span>
            </div>
            <div class="form-group">
                <label>Episode (optional)</label>
                <input type="number" name="episode_id" class="form-control" value="<?php echo $episode_id; ?>">
            </div>
            <div class="form-group <?php echo (!empty($rating_err)) ? 'has-error' : ''; ?>">
                <label>Rating</label>
                <input type="number" name="rating" class="form-control" value="<?php echo $rating; ?>">
                <span class="help-block"><?php echo $rating_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($review_text_err)) ? 'has-error' : ''; ?>">
                <label>Review</label>
                <textarea name="review_text" class="form-control"><?php echo $review_text; ?></textarea>
                <span class="help-block"><?php echo $review_text_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="manage_reviews.php" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
