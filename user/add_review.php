<?php
session_start();
require_once '../includes/config.php';

$category_id = $rating = $review_text = "";
$category_id_err = $rating_err = $review_text_err = "";

// Fetch existing anime, episodes, and characters for the dropdown
$anime_list = $pdo->query("SELECT anime_id, title FROM Anime")->fetchAll(PDO::FETCH_ASSOC);
$episode_list = $pdo->query("SELECT episode_id, title FROM Episodes")->fetchAll(PDO::FETCH_ASSOC);
$character_list = $pdo->query("SELECT character_id, name FROM Characters")->fetchAll(PDO::FETCH_ASSOC);

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

    // Check for errors before inserting in database
    if (empty($category_id_err) && empty($rating_err) && empty($review_text_err)) {
        $category_type = $_POST['category_type'];
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
            $param_category_type = $category_type;

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
                <div class="form-group <?php echo (!empty($category_id_err)) ? 'has-error' : ''; ?>">
                    <label>Search</label>
                    <select name="category_id" class="form-control" id="category_id">
                        <option value="">Select Category</option>
                        <!-- Dynamic options based on the selected tab -->
                    </select>
                    <span class="help-block"><?php echo $category_id_err; ?></span>
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
                <input type="hidden" name="category_type" id="category_type">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-reset">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript to handle the tab selection and populate the dropdown based on the selected tab
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category_id');
            const categoryTypeInput = document.getElementById('category_type');
            const tabs = document.querySelectorAll('.tab-button');

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    const categoryType = this.dataset.category;
                    categoryTypeInput.value = categoryType;

                    // Clear the existing options
                    categorySelect.innerHTML = '<option value="">Select Category</option>';

                    // Populate the options based on the selected category type
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
        });
    </script>
</body>
</html>
