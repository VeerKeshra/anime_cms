<?php
session_start();
require_once '../includes/config.php';

$anime_id = $rating = $review_text = "";
$anime_id_err = $rating_err = $review_text_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate anime_id
    if (empty(trim($_POST["anime_id"]))) {
        $anime_id_err = "Please select an anime, episode, or character.";
    } else {
        $anime_id = trim($_POST["anime_id"]);
    }

    // Validate rating
    if (empty(trim($_POST["rating"]))) {
        $rating_err = "Please enter a rating.";
    } elseif (!ctype_digit(trim($_POST["rating"])) || trim($_POST["rating"]) < 1 || trim($_POST["rating"]) > 10) {
        $rating_err = "Please enter a rating between 1 and 10.";
    } else {
        $rating = trim($_POST["rating"]);
    }

    // Validate review text
    if (empty(trim($_POST["review_text"]))) {
        $review_text_err = "Please enter your review.";
    } else {
        $review_text = trim($_POST["review_text"]);
    }

    // Check for errors before inserting in database
    if (empty($anime_id_err) && empty($rating_err) && empty($review_text_err)) {
        $sql = "INSERT INTO Reviews (anime_id, user_id, rating, review_text) VALUES (:anime_id, :user_id, :rating, :review_text)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":anime_id", $param_anime_id, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $param_user_id, PDO::PARAM_INT);
            $stmt->bindParam(":rating", $param_rating, PDO::PARAM_INT);
            $stmt->bindParam(":review_text", $param_review_text, PDO::PARAM_STR);

            $param_anime_id = $anime_id;
            $param_user_id = $_SESSION["id"];
            $param_rating = $rating;
            $param_review_text = $review_text;

            if ($stmt->execute()) {
                header("location: user_dashboard.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
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
    <link rel="stylesheet" href="../assets/css/review.css">
</head>
<body class="review-page">
    <div class="login-container">
        <div class="login-form">
            <h2 class="login-head">Add Review</h2>
            <p>Please fill this form to add a review.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($anime_id_err)) ? 'has-error' : ''; ?>">
                    <label>Search</label>
                    <input type="text" name="anime_id" class="form-control" value="<?php echo $anime_id; ?>" id="search">
                    <span class="help-block"><?php echo $anime_id_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($rating_err)) ? 'has-error' : ''; ?>">
                    <label>Rating</label>
                    <input type="number" name="rating" class="form-control" min="1" max="10" value="<?php echo $rating; ?>">
                    <span class="help-block"><?php echo $rating_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($review_text_err)) ? 'has-error' : ''; ?>">
                    <label>Review Text</label>
                    <textarea name="review_text" class="form-control"><?php echo $review_text; ?></textarea>
                    <span class="help-block"><?php echo $review_text_err; ?></span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-reset">Reset</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
