<?php
session_start();
require_once '../includes/config.php';

// Fetch the character details
$character_id = $_GET['character_id'];
$character_stmt = $pdo->prepare("SELECT * FROM Characters WHERE character_id = :character_id");
$character_stmt->bindParam(':character_id', $character_id, PDO::PARAM_INT);
$character_stmt->execute();
$character = $character_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the reviews for this character
$reviews_stmt = $pdo->prepare("SELECT Reviews.*, Users.username FROM Reviews JOIN Users ON Reviews.user_id = Users.user_id WHERE category_id = :character_id AND category_type = 'character'");
$reviews_stmt->bindParam(':character_id', $character_id, PDO::PARAM_INT);
$reviews_stmt->execute();
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

unset($character_stmt, $reviews_stmt, $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($character['name']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($character['name']); ?></h1>
        <p><?php echo htmlspecialchars($character['description']); ?></p>

        <h2>Reviews</h2>
        <?php if ($reviews): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <p><strong><?php echo htmlspecialchars($review['username']); ?></strong> said:</p>
                    <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                    <p>Rating: <?php echo htmlspecialchars($review['rating']); ?>/10</p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to review this character!</p>
        <?php endif; ?>

        <h2>Add a Review</h2>
        <form action="../user/add_review.php" method="post">
            <input type="hidden" name="category_type" value="character">
            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($character['character_id']); ?>">
            <div class="form-group">
                <label for="rating">Rating</label>
                <input type="number" name="rating" id="rating" class="form-control" min="1" max="10" required>
            </div>
            <div class="form-group">
                <label for="review_text">Review Text</label>
                <textarea name="review_text" id="review_text" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="captcha">CAPTCHA</label>
                <img src="../captcha.php" alt="CAPTCHA Image">
                <input type="text" name="captcha" id="captcha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
