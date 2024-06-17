<?php
session_start();
require_once 'includes/config.php';

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);

    $sql = "SELECT * FROM Anime WHERE anime_id = :anime_id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":anime_id", $param_id, PDO::PARAM_INT);
        $param_id = $id;

        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $anime = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                echo "No records found.";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            exit();
        }

        unset($stmt);
    }
} else {
    header("location: index.php");
    exit();
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Anime Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h1>Anime Details</h1>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($anime['title']); ?></p>
        <p><strong>Genre:</strong> <?php echo htmlspecialchars($anime['genre']); ?></p>
        <p><strong>Release Date:</strong> <?php echo htmlspecialchars($anime['release_date']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($anime['description']); ?></p>
        <p><a href="index.php" class="btn btn-default">Back to List</a></p>
    </div>
</body>
</html>
