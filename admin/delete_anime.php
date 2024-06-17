<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ['admin', 'content_manager'])) {
    header("location: login.php");
    exit;
}

require_once '../includes/config.php';

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql = "DELETE FROM Anime WHERE anime_id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $param_id = $id;

        if ($stmt->execute()) {
            header("location: manage_anime.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }

        unset($stmt);
    }
}

unset($pdo);
?>
