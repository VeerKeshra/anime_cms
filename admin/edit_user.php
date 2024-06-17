<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

$username = $email = $role = "";
$username_err = $email_err = $role_err = "";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);

    $sql = "SELECT * FROM Users WHERE user_id = :user_id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":user_id", $param_id);
        $param_id = $id;

        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $username = $row["username"];
                $email = $row["email"];
                $role = $row["role"];
            } else {
                echo "Error: No matching record found.";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            exit();
        }

        unset($stmt);
    }
} else {
    header("location: manage_users.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id = $_POST["id"];

    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if(empty(trim($_POST["role"]))){
        $role_err = "Please enter a role.";
    } else {
        $role = trim($_POST["role"]);
    }

    if(empty($username_err) && empty($email_err) && empty($role_err)){
        $sql = "UPDATE Users SET username = :username, email = :email, role = :role WHERE user_id = :user_id";

        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $param_id, PDO::PARAM_INT);

            $param_username = $username;
            $param_email = $email;
            $param_role = $role;
            $param_id = $id;

            if($stmt->execute()){
                header("location: manage_users.php");
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
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit User</h2>
        <p>Please edit the input values and submit to update the user record.</p>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($role_err)) ? 'has-error' : ''; ?>">
                <label>Role</label>
                <input type="text" name="role" class="form-control" value="<?php echo $role; ?>">
                <span class="help-block"><?php echo $role_err; ?></span>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="manage_users.php" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
