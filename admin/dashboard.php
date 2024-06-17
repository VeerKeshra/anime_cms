<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';
if ($_SESSION['role'] == 'admin') {
    include '../includes/nav_admin.php';
} else {
    include '../includes/nav_user.php';
}

// Fetch counts for dashboard metrics (as in previous example)

// Initialize variables
$count_users = $count_anime = $count_episodes = $count_reviews = $count_characters = 0;

// Fetch counts for dashboard metrics
$sql_users = "SELECT COUNT(*) as count FROM Users";
$sql_anime = "SELECT COUNT(*) as count FROM Anime";
$sql_episodes = "SELECT COUNT(*) as count FROM Episodes";
$sql_reviews = "SELECT COUNT(*) as count FROM Reviews";
$sql_characters = "SELECT COUNT(*) as count FROM Characters";

if ($stmt = $pdo->prepare($sql_users)) {
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count_users = $result['count'];
    unset($stmt);
}

if ($stmt = $pdo->prepare($sql_anime)) {
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count_anime = $result['count'];
    unset($stmt);
}

if ($stmt = $pdo->prepare($sql_episodes)) {
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count_episodes = $result['count'];
    unset($stmt);
}

if ($stmt = $pdo->prepare($sql_reviews)) {
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count_reviews = $result['count'];
    unset($stmt);
}

if ($stmt = $pdo->prepare($sql_characters)) {
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count_characters = $result['count'];
    unset($stmt);
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
        <p>This is your admin dashboard. Here are some statistics:</p>
        <div class="stats">
            <div class="stat">
                <h3>Users</h3>
                <p><?php echo $count_users; ?></p>
            </div>
            <div class="stat">
                <h3>Anime</h3>
                <p><?php echo $count_anime; ?></p>
            </div>
            <div class="stat">
                <h3>Episodes</h3>
                <p><?php echo $count_episodes; ?></p>
            </div>
            <div class="stat">
                <h3>Reviews</h3>
                <p><?php echo $count_reviews; ?></p>
            </div>
            <div class="stat">
                <h3>Characters</h3>
                <p><?php echo $count_characters; ?></p>
            </div>
        </div>
        <div class="nav-links">
            <a href="manage_users.php" class="btn">Manage Users</a>
            <a href="manage_anime.php" class="btn">Manage Anime</a>
            <a href="manage_episodes.php" class="btn">Manage Episodes</a>
            <a href="manage_reviews.php" class="btn">Manage Reviews</a>
            <a href="manage_characters.php" class="btn">Manage Characters</a>
        </div>
    </div>
</body>
</html>
