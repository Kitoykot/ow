<?php
require_once __DIR__ . "/vendor/autoload.php";
session_start();

use App\Auth;
use App\Article;


if(!Auth::check($_SESSION["id"]))
{
    header("location:/auth/login.php");
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
            <div class="mb-2">
                <a href="create.php">Добавить статью</a>
            </div>
            
            <?php

                if($_SESSION["error_message"])
                {
                ?>
                    <div class="alert alert-warning" role="alert">
                        <?=$_SESSION["error_message"]?>
                    </div>
                <?php
                    unset($_SESSION["error_message"]);
                }

                $articles = Article::get_userid($_SESSION["id"]);
                
                if(!mysqli_num_rows($articles) > 0)
                {
                ?>
                    <p>Пока что тут пусто...</p>
                <?php
                }
                
                while($article = mysqli_fetch_assoc($articles))
                {
                ?>
                    <ul class="list-group mb-3">
                        <a href="one.php?id=<?=$article["id"]?>" class="list-group-item list-group-item-action">
                            <b><?=$article["title"]?></b>
                            <form method="POST" action="/my-articles.php" style="float: right;">
                                <input type="hidden" value="<?=$article["id"]?>" name="delete_id">
                                <input type="hidden" value="<?=$article["image"]?>" name="delete_image">
                                <button class="btn btn-danger" type="submit" name="delete_<?=$article["id"]?>">Удалить</button>

                                <?php
                                    if(!is_null($_POST["delete_".$article["id"]]))
                                    {
                                        $delete = Article::delete($_POST["delete_id"], $_POST["delete_image"]);
                                        $delete ? header("location:/my-articles.php") : false;
                                    }
                                ?>
                            </form>

                            <form style="float: right; padding-right: 10px;" method="POST" action="/update.php?id=<?=$article["id"]?>">
                                <button class="btn btn-success">Изменить</button>
                            </form>
                            
                            <form method="POST" action="/my-articles.php" style="float: right; padding-right: 10px;">
                                <input type="hidden" value="<?=$article["id"]?>" name="public_id">
                                <button class="btn btn-<?=(int)$article["public"] === 1 ? "warning" : "primary"?>" type="submit" name="public_<?=$article["id"]?>">
                                    <?=(int)$article["public"] === 1 ? "Снять с публикации" : "Опубликовать"?>
                                </button>  
                                
                                <?php
                                    if(!is_null($_POST["public_".$article["id"]]))
                                    {
                                        Article::public($_POST["public_id"]);
                                    }
                                ?>
                            </form>
                        </a>
                    </ul>
                <?php
                }
            ?>
        </div>
    </main>
</body>

</html>