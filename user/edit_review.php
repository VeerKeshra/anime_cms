<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'user') {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

$review_id = $_GET['id'];
$anime_id = $episode_id = $rating = $review_text = "";
$anime_id_err = $episode_id_err = $rating_err = $review_text_err = "";

// Fetch the review details
$sql = "SELECT * FROM Reviews WHERE review_id = :review_id AND user_id = :user_id";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(":review_id", $review_id, PDO::PARAM_INT);
    $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $anime_id = $row["anime_id"];
        $episode_id = $row["episode_id"];
        $rating = $row["rating"];
        $review_text = $row["review_text"];
    } else {
        // Redirect to dashboard if review not found
        header("location: dashboard.php");
        exit;
    }
    
    unset($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["anime_id"]))) {
        $anime_id_err = "Please select an anime.";
    } else {
        $anime_id = trim($_POST["anime_id"]);
    }

    if (empty(trim($_POST["episode_id"]))) {
        $episode_id_err = "Please select an episode.";
    } else {
        $episode_id = trim($_POST["episode_id"]);
    }

    if (empty(trim($_POST["rating"]))) {
        $rating_err = "Please provide a rating.";
    } else {
        $rating = trim($_POST["rating"]);
    }

    if (empty(trim($_POST["review_text"]))) {
        $review_text_err = "Please enter your review.";
    } else {
        $review_text = trim($_POST["review_text"]);
    }

    if (empty($anime_id_err) && empty($episode_id_err) && empty($rating_err) && empty($review_text_err)) {
        $sql = "UPDATE Reviews SET anime_id = :anime_id, episode_id = :episode_id, rating = :rating, review_text = :review_text WHERE review_id = :review_id AND user_id = :user_id";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":anime_id", $anime_id, PDO::PARAM_INT);
            $stmt->bindParam(":episode_id", $episode_id, PDO::PARAM_INT);
            $stmt->bindParam(":rating", $rating, PDO::PARAM_INT);
            $stmt->bindParam(":review_text", $review_text, PDO::PARAM_STR);
            $stmt->bindParam(":review_id", $review_id, PDO::PARAM_INT);
            $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("location: dashboard.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Review</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit Review</h2>
        <p>Please edit the details of your review.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $review_id; ?>" method="post">
            <div class="form-group <?php echo (!empty($anime_id_err)) ? 'has-error' : ''; ?>">
                <label>Anime</label>
                <input type="text" name="anime_id" class="form-control" value="<?php echo $anime_id; ?>">
                <span class="help-block"><?php echo $anime_id_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($episode_id_err)) ? 'has-error' : ''; ?>">
                <label>Episode</label>
                <input type="text" name="episode_id" class="form-control" value="<?php echo $episode_id; ?>">
                <span class="help-block"><?php echo $episode_id_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($rating_err)) ? 'has-error' : ''; ?>">
                <label>Rating</label>
                <input type="number" name="rating" class="form-control" value="<?php echo $rating; ?>">
                <span class="help-block"><?php echo $rating_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($review_text_err)) ? 'has-error' : ''; ?>">
                <label>Review Text</label>
                <textarea name="review_text" class="form-control"><?php echo $review_text; ?></textarea>
                <span class="help-block"><?php echo $review_text_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="dashboard.php" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
