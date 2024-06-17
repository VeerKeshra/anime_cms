<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ['admin', 'content_manager'])) {
    header("location: login.php");
    exit;
}

require_once '../includes/config.php';

$sql = "SELECT Episodes.*, Anime.title AS anime_title FROM Episodes INNER JOIN Anime ON Episodes.anime_id = Anime.anime_id";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->execute();
    $episodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    unset($stmt);
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Episodes</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage Episodes</h2>
        <a href="dashboard.php" class="btn btn-default">Back to Dashboard</a>
        <a href="add_episode.php" class="btn btn-primary">Add New Episode</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Anime</th>
                    <th>Episode Number</th>
                    <th>Air Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($episodes as $episode): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($episode['title']); ?></td>
                        <td><?php echo htmlspecialchars($episode['anime_title']); ?></td>
                        <td><?php echo htmlspecialchars($episode['episode_number']); ?></td>
                        <td><?php echo htmlspecialchars($episode['air_date']); ?></td>
                        <td>
                            <a href="edit_episode.php?id=<?php echo $episode['episode_id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_episode.php?id=<?php echo $episode['episode_id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
