<?php
require_once __DIR__ . "/vendor/autoload.php";
session_start();

use App\Auth;
use App\User;
use App\Article;

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "includes/head.php" ?>
<body>
<?php require_once "includes/header.php" ?>
    <main>
        <div class="container mt-5">
            <?php
                if(!Auth::check($_GET["id"]))
                {
                ?>
                    <div class="alert alert-light" role="alert">
                        Такого пользователя не существует
                    </div>
                <?php
                    die();
                }
            ?>
            <div class="profile">
                <?php
                    $user = User::get_one($_GET["id"]);
                ?>
                <h5><?=$user["name"]?> <?=$user["surname"]?></h5>
                <img src="<?=$user["avatar"]?>" alt="" width="230">
                <br><br>
                <hr>
                <?php
                    if(!User::check($_SESSION["id"], $_GET["id"]))
                    {
                    ?>
                        <a href="/articles.php?id=<?=$user["id"]?>">Статьи (<?=mysqli_num_rows(Article::get_userid($user["id"]))?>)</a>
                        <br>
                    <?php
                    } else {
                    ?>
                        <a href="/my-articles.php">Мои статьи (<?=mysqli_num_rows(Article::get_userid($user["id"]))?>)</a>         
                    <?php
                    }
                ?>
                <hr>
                <p><?=$user["description"]?></p>
                <hr>
                <p><?=$user["email"]?></p>
            </div>
        </div>
    </main>
</body>

</html>