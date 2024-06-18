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

// Fetch reviews by the logged-in user
$user_id = $_SESSION["id"];
$sql = "SELECT Reviews.*, 
            CASE 
                WHEN category_type = 'anime' THEN (SELECT title FROM Anime WHERE anime_id = Reviews.category_id)
                WHEN category_type = 'episode' THEN (SELECT title FROM Episodes WHERE episode_id = Reviews.category_id)
                WHEN category_type = 'character' THEN (SELECT name FROM Characters WHERE character_id = Reviews.category_id)
            END AS category_title
        FROM Reviews
        WHERE user_id = :user_id";
        
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

unset($stmt, $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage My Reviews</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage My Reviews</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Review</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['category_type']); ?></td>
                            <td><?php echo htmlspecialchars($review['category_title']); ?></td>
                            <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                            <td><?php echo htmlspecialchars($review['rating']); ?></td>
                            <td>
                                <a href="edit_review.php?id=<?php echo $review['review_id']; ?>" class="btn btn-success">Edit</a>
                                <a href="delete_review.php?id=<?php echo $review['review_id']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">You have no reviews.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
