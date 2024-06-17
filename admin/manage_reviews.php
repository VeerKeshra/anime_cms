<?php
session_start();
require_once '../includes/config.php';

if ($_SESSION['role'] == 'admin') {
    include '../includes/nav_admin.php';
} else {
    include '../includes/nav_user.php';
}

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Fetch all reviews
$sql = "SELECT Reviews.*, Anime.title as anime_title, Characters.name as character_name, Episodes.title as episode_title, Users.username as username FROM Reviews 
        LEFT JOIN Anime ON Reviews.anime_id = Anime.anime_id
        LEFT JOIN Characters ON Reviews.character_id = Characters.character_id
        LEFT JOIN Episodes ON Reviews.episode_id = Episodes.episode_id
        LEFT JOIN Users ON Reviews.user_id = Users.user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
unset($stmt);
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage All Reviews</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Review</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['username']); ?></td>
                        <td>
                            <?php
                            if ($review['anime_id']) {
                                echo 'Anime';
                            } elseif ($review['character_id']) {
                                echo 'Character';
                            } elseif ($review['episode_id']) {
                                echo 'Episode';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($review['anime_id']) {
                                echo htmlspecialchars($review['anime_title']);
                            } elseif ($review['character_id']) {
                                echo htmlspecialchars($review['character_name']);
                            } elseif ($review['episode_id']) {
                                echo htmlspecialchars($review['episode_title']);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                        <td><?php echo htmlspecialchars($review['rating']); ?></td>
                        <td>
                            <a href="edit_review.php?id=<?php echo $review['review_id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_review.php?id=<?php echo $review['review_id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
