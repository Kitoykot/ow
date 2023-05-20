<?php
require_once __DIR__ . "/../vendor/autoload.php";
session_start();

use App\Auth;

if(Auth::check($_SESSION["id"]))
{
    header("location:/profile.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "../includes/head.php" ?>
<body>
<?php require_once "../includes/header.php" ?>
    <main>
        <div class="container mt-5">
            <h3>Создать учётную запись</h3>

            <form class="mt-5" method="POST" action="/auth/login.php">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" class="form-control" name="login" value="<?=$_COOKIE["login"]?>">
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" name="password">
                </div>

                <div>
                    <button name="submit" type="submit" class="btn btn-primary">Войти</button>
                    <a href="reg.php" class="pl-3">Регистрация</a>
                </div>
                <?php
                    if(!is_null($_POST["submit"]))
                    {
                        Auth::login($_POST["login"], $_POST["password"]);
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
    </main>
</body>

</html>