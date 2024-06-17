<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ['admin', 'content_manager'])) {
    header("location: login.php");
    exit;
}

require_once '../includes/config.php';

$title = $episode_number = $air_date = $synopsis = $anime_id = "";
$title_err = $episode_number_err = $air_date_err = $synopsis_err = $anime_id_err = "";

// Get Anime list
$sql = "SELECT anime_id, title FROM Anime";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->execute();
    $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    unset($stmt);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    if (empty(trim($_POST["episode_number"]))) {
        $episode_number_err = "Please enter an episode number.";
    } else {
        $episode_number = trim($_POST["episode_number"]);
    }

    if (empty(trim($_POST["air_date"]))) {
        $air_date_err = "Please enter an air date.";
    } else {
        $air_date = trim($_POST["air_date"]);
    }

    if (empty(trim($_POST["synopsis"]))) {
        $synopsis_err = "Please enter a synopsis.";
    } else {
        $synopsis = trim($_POST["synopsis"]);
    }

    if (empty(trim($_POST["anime_id"]))) {
        $anime_id_err = "Please select an anime.";
    } else {
        $anime_id = trim($_POST["anime_id"]);
    }

    if (empty($title_err) && empty($episode_number_err) && empty($air_date_err) && empty($synopsis_err) && empty($anime_id_err)) {
        $sql = "INSERT INTO Episodes (title, episode_number, air_date, synopsis, anime_id) VALUES (:title, :episode_number, :air_date, :synopsis, :anime_id)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":episode_number", $param_episode_number, PDO::PARAM_INT);
            $stmt->bindParam(":air_date", $param_air_date, PDO::PARAM_STR);
            $stmt->bindParam(":synopsis", $param_synopsis, PDO::PARAM_STR);
            $stmt->bindParam(":anime_id", $param_anime_id, PDO::PARAM_INT);

            $param_title = $title;
            $param_episode_number = $episode_number;
            $param_air_date = $air_date;
            $param_synopsis = $synopsis;
            $param_anime_id = $anime_id;

            if ($stmt->execute()) {
                header("location: manage_episodes.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Episode</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Episode</h2>
        <p>Please fill this form to create an episode entry.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                <span class="help-block"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($episode_number_err)) ? 'has-error' : ''; ?>">
                <label>Episode Number</label>
                <input type="number" name="episode_number" class="form-control" value="<?php echo $episode_number; ?>">
                <span class="help-block"><?php echo $episode_number_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($air_date_err)) ? 'has-error' : ''; ?>">
                <label>Air Date</label>
                <input type="date" name="air_date" class="form-control" value="<?php echo $air_date; ?>">
                <span class="help-block"><?php echo $air_date_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($synopsis_err)) ? 'has-error' : ''; ?>">
                <label>Synopsis</label>
                <textarea name="synopsis" class="form-control"><?php echo $synopsis; ?></textarea>
                <span class="help-block"><?php echo $synopsis_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($anime_id_err)) ? 'has-error' : ''; ?>">
                <label>Anime</label>
                <select name="anime_id" class="form-control">
                    <option value="">Select Anime</option>
                    <?php foreach ($animes as $anime): ?>
                        <option value="<?php echo $anime['anime_id']; ?>"><?php echo $anime['title']; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block"><?php echo $anime_id_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="manage_episodes.php" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
