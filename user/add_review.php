<?php
session_start();
require_once '../includes/config.php';

// Check if the user is logged in and redirect to login page if not
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Fetch existing anime, episodes, and characters for the dropdown
$anime_list = $pdo->query("SELECT anime_id, title FROM Anime")->fetchAll(PDO::FETCH_ASSOC);
$episode_list = $pdo->query("SELECT episode_id, title FROM Episodes")->fetchAll(PDO::FETCH_ASSOC);
$character_list = $pdo->query("SELECT character_id, name FROM Characters")->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables
$category_id = $category_type = $rating = $review_text = $captcha = "";
$category_id_err = $rating_err = $review_text_err = $captcha_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate category_id
    if (empty(trim($_POST["category_id"]))) {
        $category_id_err = "Please select an anime, episode, or character.";
    } else {
        $category_id = trim($_POST["category_id"]);
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

    // Validate CAPTCHA
    if (empty(trim($_POST["captcha"]))) {
        $captcha_err = "Please enter the CAPTCHA.";
    } elseif ($_POST["captcha"] !== $_SESSION['captcha']) {
        $captcha_err = "Incorrect CAPTCHA.";
    } else {
        $captcha = trim($_POST["captcha"]);
    }

    // Check for errors before inserting in database
    if (empty($category_id_err) && empty($rating_err) && empty($review_text_err) && empty($captcha_err)) {
        $sql = "INSERT INTO Reviews (category_id, user_id, rating, review_text, category_type) VALUES (:category_id, :user_id, :rating, :review_text, :category_type)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":category_id", $param_category_id, PDO::PARAM_INT);
            $stmt->bindParam(":user_id", $param_user_id, PDO::PARAM_INT);
            $stmt->bindParam(":rating", $param_rating, PDO::PARAM_INT);
            $stmt->bindParam(":review_text", $param_review_text, PDO::PARAM_STR);
            $stmt->bindParam(":category_type", $param_category_type, PDO::PARAM_STR);

            $param_category_id = $category_id;
            $param_user_id = $_SESSION["id"];
            $param_rating = $rating;
            $param_review_text = $review_text;
            $param_category_type = trim($_POST["category_type"]);

            if ($stmt->execute()) {
                header("location: dashboard.php");
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
                <div class="form-group">
                    <label for="category_type">Category</label>
                    <select name="category_type" id="category_type" class="form-control">
                        <option value="">Select Category</option>
                        <option value="anime">Anime</option>
                        <option value="episode">Episode</option>
                        <option value="character">Character</option>
                    </select>
                </div>
                <div class="form-group <?php echo (!empty($category_id_err)) ? 'has-error' : ''; ?>">
                    <label for="category_id">Select Item</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Select Item</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                    <span class="help-block"><?php echo $category_id_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($rating_err)) ? 'has-error' : ''; ?>">
                    <label for="rating">Rating</label>
                    <input type="number" name="rating" id="rating" class="form-control" min="1" max="10" value="<?php echo $rating; ?>">
                    <span class="help-block"><?php echo $rating_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($review_text_err)) ? 'has-error' : ''; ?>">
                    <label for="review_text">Review Text</label>
                    <textarea name="review_text" id="review_text" class="form-control"><?php echo $review_text; ?></textarea>
                    <span class="help-block"><?php echo $review_text_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($captcha_err)) ? 'has-error' : ''; ?>">
        <label for="captcha">CAPTCHA</label>
        <input type="text" name="captcha" id="captcha" class="form-control">
        <img src="/wdev/anime_cms/user/generate_captcha.php" alt="CAPTCHA">
        <span class="help-block"><?php echo $captcha_err; ?></span>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="reset" class="btn btn-reset">Reset</button>
    </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryTypeSelect = document.getElementById('category_type');
            const categorySelect = document.getElementById('category_id');

            categoryTypeSelect.addEventListener('change', function() {
                const categoryType = this.value;

                categorySelect.innerHTML = '<option value="">Select Item</option>';

                let options = [];
                if (categoryType === 'anime') {
                    options = <?php echo json_encode($anime_list); ?>;
                } else if (categoryType === 'episode') {
                    options = <?php echo json_encode($episode_list); ?>;
                } else if (categoryType === 'character') {
                    options = <?php echo json_encode($character_list); ?>;
                }

                options.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option[categoryType === 'anime' ? 'anime_id' : categoryType === 'episode' ? 'episode_id' : 'character_id'];
                    optionElement.textContent = option.title || option.name;
                    categorySelect.appendChild(optionElement);
                });
            });
        });
   
</script>
</body>
</html>