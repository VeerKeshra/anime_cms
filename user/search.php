<?php
session_start();
require_once '../includes/config.php';

$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Normalize the search term
$normalized_search_term = '%' . strtolower($search_term) . '%';

$results = [];

if ($category == 'anime') {
    $stmt = $pdo->prepare("SELECT * FROM Anime WHERE LOWER(title) LIKE :search_term");
} elseif ($category == 'character') {
    $stmt = $pdo->prepare("SELECT * FROM Characters WHERE LOWER(name) LIKE :search_term");
} elseif ($category == 'episode') {
    $stmt = $pdo->prepare("SELECT * FROM Episodes WHERE LOWER(title) LIKE :search_term");
} else {
    // Default to searching in all categories
    $stmt = $pdo->prepare("
        SELECT 'anime' AS type, anime_id AS id, title AS name FROM Anime WHERE LOWER(title) LIKE :search_term
        UNION ALL
        SELECT 'character' AS type, character_id AS id, name FROM Characters WHERE LOWER(name) LIKE :search_term
        UNION ALL
        SELECT 'episode' AS type, episode_id AS id, title AS name FROM Episodes WHERE LOWER(title) LIKE :search_term
    ");
}

$stmt->bindParam(':search_term', $normalized_search_term, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

unset($stmt, $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Search Results for "<?php echo htmlspecialchars($search_term); ?>"</h1>
        <?php if ($results): ?>
            <?php foreach ($results as $result): ?>
                <div class="result-item">
                    <p><strong><?php echo htmlspecialchars(ucfirst($result['type'])); ?>:</strong> <a href="view_<?php echo htmlspecialchars($result['type']); ?>.php?<?php echo htmlspecialchars($result['type']); ?>_id=<?php echo htmlspecialchars($result['id']); ?>"><?php echo htmlspecialchars($result['name']); ?></a></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
