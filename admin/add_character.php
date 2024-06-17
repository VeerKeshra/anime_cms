<?php
session_start();
require_once '../includes/config.php';

$name = $role = $description = $anime_id = $image = "";
$name_err = $role_err = $description_err = $anime_id_err = $image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter the character name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["role"]))) {
        $role_err = "Please enter the character role.";
    } else {
        $role = trim($_POST["role"]);
    }

    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter the description.";
    } else {
        $description = trim($_POST["description"]);
    }

    if (empty(trim($_POST["anime_id"]))) {
        $anime_id_err = "Please select an anime.";
    } else {
        $anime_id = trim($_POST["anime_id"]);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/characters/";
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

    if (empty($name_err) && empty($role_err) && empty($description_err) && empty($anime_id_err) && empty($image_err)) {
        $sql = "INSERT INTO Characters (name, role, description, anime_id, image) VALUES (:name, :role, :description, :anime_id, :image)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":role", $role, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":anime_id", $anime_id, PDO::PARAM_INT);
            $stmt->bindParam(":image", $image, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("location: manage_characters.php");
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
    <title>Add Character</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Character</h2>
        <p>Please fill this form to add a character.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($role_err)) ? 'has-error' : ''; ?>">
                <label>Role</label>
                <input type="text" name="role" class="form-control" value="<?php echo $role; ?>">
                <span class="help-block"><?php echo $role_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
                <span class="help-block"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($anime_id_err)) ? 'has-error' : ''; ?>">
                <label>Anime</label>
                <select name="anime_id" class="form-control">
                    <?php
                    // Fetch anime list for the dropdown
                    $sql = "SELECT anime_id, title FROM Anime";
                    if ($stmt = $pdo->prepare($sql)) {
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $row['anime_id'] . '">' . $row['title'] . '</option>';
                        }
                        unset($stmt);
                    }
                    ?>
                </select>
                <span class="help-block"><?php echo $anime_id_err; ?></span>
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
