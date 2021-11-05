<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deleteAvatar'])) {
        $avatarToDelete = $_POST['deleteAvatar'];

        if (file_exists($avatarToDelete)) {
            if (unlink($avatarToDelete)) {
                header('Location: http://localhost:8000/form.php?avatarDeleted=true');
            } else {
                echo 'Error, could not delete avatar';
            }
        }
    }
    $errors = [];

    $data = array_map('trim', $_POST);

    $name = htmlentities($data['name']);
    $forname = htmlentities($data['forname']);
    $age = htmlentities($data['age']);

    $uploadDir = 'public/uploads/';

    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);

    $maxFileSize = 1000000;
    $extensions_ok = ['image/jpeg', 'image/png', 'image/webp'];

    if (!file_exists($_FILES['avatar']['tmp_name'])) {
        $errors[] = "Avatar is required";
    } else {
        if (filesize($_FILES['avatar']['tmp_name']) > $maxFileSize) {
            $errors[] = "File should be less than 1MO !";
        }

        if (!in_array(mime_content_type($_FILES['avatar']['tmp_name']), $extensions_ok)) {
            $errors[] = "Image should be in jpg, png or webp format !";
        }
    }

    if (strlen($name) < 4 || strlen($name) < 4) {
        $errors[] = "Name/Forname should be at least 4 characters long.";
    }

    if (!filter_var($age) || filter_var($age) < 0 || filter_var($age) > 130) {
        $errors[] = "Age is not valid";
    }


    if (count($errors) === 0) {
        $id = uniqid('avatar', true);
        $uniqueAvatarId = $uploadDir . $id . '.' . $extension;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $uniqueAvatarId);

        header("Location: http://localhost:8000/form.php?name=$name&forname=$forname&age=$age&avatar=$uniqueAvatarId");
        exit();
    } else {
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>

<body>
    <?php
    if ($_GET && isset($_GET['avatarDeleted'])) {
        if ($_GET['avatarDeleted'] === 'true') {
            echo 'Avatar deleted !';
        }
    }
    ?>
    <h1>Create Profile</h1>
    <form method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Create profile</legend>
            <div>
                <label for="name">Name: </label>
                <input type="text" id="name" name="name">
            </div>
            <div>
                <label for="forname">Forname: </label>
                <input type="text" id="forname" name="forname">
            </div>
            <div>
                <label for="age">Age: </label>
                <input type="text" id="age" name="age">
            </div>
            <div>
                <label for="imageUpload">Upload an profile image:</label>
                <input type="file" name="avatar" id="imageUpload" />
            </div>

            <button name="send">Submit</button>
        </fieldset>

    </form>

    <h2>Profile</h2>

    <?php if ($_GET) {
        if (isset($_GET['name'])) { ?>
            <p><b>Name:</b> <?= $_GET['name'] ?>
            <p>
            <?php } ?>
            <?php if (isset($_GET['forname'])) { ?>
            <p><b>Forname:</b> <?= $_GET['forname'] ?>
            <p>
            <?php } ?>
            <?php if (isset($_GET['age'])) { ?>
            <p><b>Age:</b> <?= $_GET['age'] ?>
            <p>
            <?php } ?>
            <?php if (isset($_GET['avatar'])) { ?>
            <p><b>Avatar:</b>
            <p>
                <img src="<?= $_GET['avatar'] ?>" alt="Avatar">
            <div>
                <form method="post">
                    <input type="hidden" name="deleteAvatar" value="<?= $_GET['avatar'] ?>">
                    <button>Delete Avatar</button>
                </form>

            </div>
        <?php } ?>
    <?php } ?>



</body>

</html>