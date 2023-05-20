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

            <form class="mt-5" method="POST" action="/auth/reg.php">
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" class="form-control" name="name" placeholder="Иван" value="<?=$_COOKIE["name"]?>">
                </div>

                <div class="form-group">
                    <label for="surname">Фамилия</label>
                    <input type="text" class="form-control" name="surname" placeholder="Иванов" value="<?=$_COOKIE["surname"]?>">
                </div>

                <div class="form-group">
                    <label for="email">Электронная почта</label>
                    <input type="text" class="form-control" name="email" placeholder="ivan@mail.ru" value="<?=$_COOKIE["email"]?>">
                </div>

                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" class="form-control" name="login" placeholder="Ivan1986" value="<?=$_COOKIE["login"]?>">
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" name="password">
                </div>

                <div class="form-group">
                    <label for="password-confirm">Подтверждение пароля</label>
                    <input type="password" class="form-control" name="password-confirm">
                </div>

                <div>
                    <button name="submit" type="submit" class="btn btn-primary">Создать аккаунт</button>
                    <a href="login.php" class="pl-3">Войти</a>
                </div>

                <?php
                    if(!is_null($_POST["submit"]))
                    {
                        $reg = Auth::reg($_POST, $_POST["name"], $_POST["surname"], $_POST["email"],
                                $_POST["login"], $_POST["password"], $_POST["password-confirm"]);
                        
                        if($reg)
                        {
                            setcookie("name", "", time()-3600, "/auth/reg.php");
                            setcookie("surname", "", time()-3600, "/auth/reg.php");
                            setcookie("email", "", time()-3600, "/auth/reg.php");
                            setcookie("login", "", time()-3600, "/auth/reg.php");

                            header("location:/auth/login.php");
                            die();

                        } else {
                        ?>
                            <div class="alert alert-danger mt-4" role="alert">
                                Ошибка при регистрации
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
    </main>
</body>

</html>