<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Navigation</title>
    <link rel="stylesheet" href="/wdev/anime_cms/assets/css/nav.css">
</head>
<body>
<nav>
    <ul>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <li><a href="<?php echo ($_SESSION['role'] === 'admin') ? '/wdev/anime_cms/admin/dashboard.php' : '/wdev/anime_cms/user/user_dashboard.php'; ?>">Dashboard</a></li>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>! <a href="/wdev/anime_cms/logout.php">Logout</a></p>
        <?php else: ?>
            <li><a href="/wdev/anime_cms/login.php">Login</a></li>
            <li><a href="/wdev/anime_cms/register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
</body>
</html>
