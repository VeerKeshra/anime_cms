<?php
session_start();
require_once '../includes/config.php';

$anime_id = $rating = $review_text = "";
$anime_id_err = $rating_err = $review_text_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate anime ID
    if (empty(trim($_POST["anime_id"]))) {
        $anime_id_err = "Please select an anime.";
    } else {
        $anime_id = trim($_POST["anime_id"]);
    }

    // Validate rating
    if (empty(trim($_POST["rating"]))) {
        $rating_err = "Please provide a rating.";
    } elseif (intval($_POST["rating"]) < 1 || intval($_POST["rating"]) > 10) {
        $rating_err = "Rating must be between 1 and 10.";
    } else {
        $rating = intval($_POST["rating"]);
    }

    // Validate review text
    if (empty(trim($_POST["review_text"]))) {
        $review_text_err = "Please enter your review.";
    } else {
        $review_text = trim($_POST["review_text"]);
    }

    // Check input errors before inserting in database
    if (empty($anime_id_err) && empty($rating_err) && empty($review_text_err)) {
        $sql = "INSERT INTO Reviews (user_id, anime_id, rating, review_text) VALUES (:user_id, :anime_id, :rating, :review_text)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":user_id", $param_user_id, PDO::PARAM_INT);
            $stmt->bindParam(":anime_id", $param_anime_id, PDO::PARAM_INT);
            $stmt->bindParam(":rating", $param_rating, PDO::PARAM_INT);
            $stmt->bindParam(":review_text", $param_review_text, PDO::PARAM_STR);

            $param_user_id = $_SESSION["id"];
            $param_anime_id = $anime_id;
            $param_rating = $rating;
            $param_review_text = $review_text;

            if ($stmt->execute()) {
                header("location: manage_reviews.php");
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
    <title>Add Review</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Review</h2>
        <p>Please fill this form to add a review.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($anime_id_err)) ? 'has-error' : ''; ?>">
                <label>Anime ID</label>
                <input type="text" name="anime_id" class="form-control" value="<?php echo $anime_id; ?>">
                <span class="help-block"><?php echo $anime_id_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($rating_err)) ? 'has-error' : ''; ?>">
                <label>Rating</label>
                <input type="number" name="rating" class="form-control" value="<?php echo $rating; ?>" min="1" max="10">
                <span class="help-block"><?php echo $rating_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($review_text_err)) ? 'has-error' : ''; ?>">
                <label>Review Text</label>
                <textarea name="review_text" class="form-control"><?php echo $review_text; ?></textarea>
                <span class="help-block"><?php echo $review_text_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
</body>
</html>
