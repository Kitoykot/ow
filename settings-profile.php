<?php
require_once __DIR__ . "/vendor/autoload.php";
session_start();

use App\Auth;
use App\User;

if(!Auth::check($_SESSION["id"]))
{
    header("location:/auth/login.php");
    die();
}

if(!User::check($_SESSION["id"], $_GET["id"]))
{
    header("location:/settings-profile.php?id=".$_SESSION["id"]);
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "includes/head.php" ?>
<body>
<?php require_once "includes/header.php" ?>
    <main>
        <div class="container mt-5">
        <?php
            $user = User::get_one($_GET["id"])
        ?>
            <div class="profile_settings">
                <img src="<?=$user["avatar"]?>" alt="" width="230">
                <br><br>
                <form method="POST" action="/settings-profile.php?id=<?=$user["id"]?>" enctype="multipart/form-data">
                <input type="hidden" value="<?=$user["id"]?>" name="id">
                <input type="hidden" value="<?=$user["avatar"]?>" name="old_avatar">
                <input type="hidden" value="storage/avatars/avatar.png" name="default_avatar">
                    <p>Обновить фотографию</p>
                    <hr>
                    <div class="form-group">
                        <label for="image">Прикрепите фото</label>
                        <input type="file" class="form-control-file" name="avatar">
                    </div>
                    <hr>
                    <?php
                        if($user["avatar"] !== "storage/avatars/avatar.png")
                        {
                        ?>
                            <a href="actions/delete_avatar.php?id=<?=$user["id"]?>">Удалить фотографию профиля</a>
                            <hr>
                        <?php
                        }
                    ?>
                    <div class="form-group">
                        <label for="description">Небольшое описание</label>
                        <textarea class="form-control" rows="3" name="description"><?=$user["description"]?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" name="submit">Обновить</button>

                    <?php
                        if(!is_null($_POST["submit"]))
                        {
                            $update = User::update($_POST["id"], $_POST["old_avatar"], $_POST["default_avatar"],
                                        $_FILES["avatar"], $_POST["description"]);
                            
                            if($update)
                            {
                                header("location:/profile.php?id=".$user["id"]);
                                die();

                            } else {
                            ?>
                                <div class="alert alert-danger mt-4" role="alert">
                                    Ошибка
                                </div>
                            <?php
                            }
                        }

                        if($_SESSION["error_message"])
                        {
                        ?>
                            <div class="alert alert-danger mt-4" role="alert">
                                <?=$_SESSION["error_message"]?>
                            </div>
                        <?php
                            unset($_SESSION["error_message"]);
                        }
                    ?>
                </form>
            </div>
        </div>
    </main>
</body>

</html>