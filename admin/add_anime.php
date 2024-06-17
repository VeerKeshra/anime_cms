<?php
session_start();
require_once '../includes/config.php';

$title = $description = $genre = $release_date = $image = "";
$title_err = $description_err = $genre_err = $release_date_err = $image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter the title.";
    } else {
        $title = trim($_POST["title"]);
    }

    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter the description.";
    } else {
        $description = trim($_POST["description"]);
    }

    if (empty(trim($_POST["genre"]))) {
        $genre_err = "Please enter the genre.";
    } else {
        $genre = trim($_POST["genre"]);
    }

    if (empty(trim($_POST["release_date"]))) {
        $release_date_err = "Please enter the release date.";
    } else {
        $release_date = trim($_POST["release_date"]);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/anime/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $image_err = "File is not an image.";
        } else {
            // Check file size
            if ($_FILES["image"]["size"] > 500000) {
                $image_err = "Sorry, your file is too large.";
            } else {
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                } else {
                    // Upload file
                    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image_err = "Sorry, there was an error uploading your file.";
                    } else {
                        $image = basename($_FILES["image"]["name"]);
                    }
                }
            }
        }
    } else {
        $image_err = "Please upload an image.";
    }

    if (empty($title_err) && empty($description_err) && empty($genre_err) && empty($release_date_err) && empty($image_err)) {
        $sql = "INSERT INTO Anime (title, description, genre, release_date, image) VALUES (:title, :description, :genre, :release_date, :image)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":title", $title, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":genre", $genre, PDO::PARAM_STR);
            $stmt->bindParam(":release_date", $release_date, PDO::PARAM_STR);
            $stmt->bindParam(":image", $image, PDO::PARAM_STR);

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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Anime</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Anime</h2>
        <p>Please fill this form to add an anime.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                <span class="help-block"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
                <span class="help-block"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($genre_err)) ? 'has-error' : ''; ?>">
                <label>Genre</label>
                <input type="text" name="genre" class="form-control" value="<?php echo $genre; ?>">
                <span class="help-block"><?php echo $genre_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($release_date_err)) ? 'has-error' : ''; ?>">
                <label>Release Date</label>
                <input type="date" name="release_date" class="form-control" value="<?php echo $release_date; ?>">
                <span class="help-block"><?php echo $release_date_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($image_err)) ? 'has-error' : ''; ?>">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
                <span class="help-block"><?php echo $image_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
</body>
</html>
