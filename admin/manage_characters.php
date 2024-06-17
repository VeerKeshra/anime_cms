<?php
session_start();
require_once '../includes/config.php';

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Check if the user is an admin
if ($_SESSION["role"] !== 'admin') {
    header("location: ../index.php");
    exit;
}

try {
    // Fetch all characters
    $sql = "SELECT c.character_id, c.name, c.role, c.description, a.title AS anime_title
            FROM Characters c
            JOIN Anime a ON c.anime_id = a.anime_id";
    $result = $pdo->query($sql);
} catch (PDOException $e) {
    die("ERROR: Could not execute $sql. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Characters</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage Characters</h2>
        <a href="add_character.php" class="btn btn-primary">Add New Character</a>
        <?php
        if ($result->rowCount() > 0) {
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Name</th>";
            echo "<th>Role</th>";
            echo "<th>Description</th>";
            echo "<th>Anime</th>";
            echo "<th>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = $result->fetch()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['anime_title']) . "</td>";
                echo "<td>";
                echo "<a href='edit_character.php?id=" . $row['character_id'] . "' class='btn btn-warning'>Edit</a>";
                echo "<a href='delete_character.php?id=" . $row['character_id'] . "' class='btn btn-danger'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            unset($result);
        } else {
            echo "<p>No characters found.</p>";
        }
        ?>
    </div>
</body>
</html>

<?php
// Close connection
unset($pdo);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Characters</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Manage Characters</h2>
        <a href="add_character.php" class="btn btn-success">Add New Character</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Description</th>
                    <th>Anime</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($characters as $character): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($character['name']); ?></td>
                        <td><?php echo htmlspecialchars($character['role']); ?></td>
                        <td><?php echo htmlspecialchars($character['description']); ?></td>
                        <td><?php echo htmlspecialchars($character['anime_title']); ?></td>
                        <td>
                            <a href="edit_character.php?id=<?php echo $character['character_id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_character.php?id=<?php echo $character['character_id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
