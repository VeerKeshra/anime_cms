<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'user') {
    header("location: ../login.php");
    exit;
}

require_once '../includes/config.php';

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $review_id = trim($_GET['id']);

    $sql = "DELETE FROM Reviews WHERE review_id = :review_id AND user_id = :user_id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":review_id", $review_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("location: dashboard.php");
            exit;
        } else {
            echo "Something went wrong. Please try again later.";
        }

        unset($stmt);
    }
}

unset($pdo);
?>
