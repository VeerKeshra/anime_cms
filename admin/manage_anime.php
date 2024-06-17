<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ['admin', 'content_manager'])) {
    header("location: login.php");
    exit;
}

require_once '../includes/config.php';

$sql = "SELECT * FROM Anime";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->execute();
    $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    unset($stmt);
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Anime</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage Anime</h2>
        <a href="add_anime.php" class="btn btn-success">Add New Anime</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Genre</th>
                    <th>Release Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animes as $anime): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($anime['title']); ?></td>
                        <td><?php echo htmlspecialchars($anime['description']); ?></td>
                        <td><?php echo htmlspecialchars($anime['genre']); ?></td>
                        <td><?php echo htmlspecialchars($anime['release_date']); ?></td>
                        <td>
                            <a href="edit_anime.php?id=<?php echo $anime['anime_id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_anime.php?id=<?php echo $anime['anime_id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
