<?php
require_once __DIR__ . "/vendor/autoload.php";
session_start();

use App\Auth;
use App\Category;
use App\Article;

if(!Auth::check($_SESSION["id"]))
{
    header("location:/auth/login.php");
    die();
}

if(!Article::check_user($_SESSION["id"], $_GET["id"]))
{
    header("location:/my-articles.php");
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
                if(!Article::check($_GET["id"]))
                {
                ?>
                    <div class="alert alert-light" role="alert">
                        Страница не найдена
                    </div>
                <?php
                    die();
                }

                $article = Article::get_one($_GET["id"]);
            ?>
            <h3>Обновить статью &laquo;<?=$article["title"]?>&raquo;</h3>

            <form class="mt-5 mb-5" method="POST" action="/update.php?id=<?=$_GET["id"]?>" enctype="multipart/form-data">
                <input type="hidden" value="<?=$article["image"]?>" name="old_image">
                <input type="hidden" value="<?=$article["id"]?>" name="id">
                <div class="form-group">
                    <label for="title">Название статьи</label>
                    <input type="text" class="form-control" name="title" placeholder="Название статьи" value="<?=$_COOKIE["title"] ? $_COOKIE["title"] : $article["title"]?>">
                </div>

                <div class="form-group">
                    <label for="short">Короткое описание</label>
                    <textarea class="form-control" rows="3" name="short" placeholder="Короткое описание"><?=$_COOKIE["short"] ? $_COOKIE["short"] : $article["short"]?></textarea>
                </div>

                <label for="category_id">Тема</label>
                <div class="form-group">
                    <select class="form-control" name="category_id">
                        <?php
                            $article_category = Category::get_one($article["category_id"]);
                        ?>  
                            <option value="<?=$article_category["id"]?>"><?=$article_category["title"]?></option>
                        <?php

                            $categories = Category::get();

                            while($category = mysqli_fetch_assoc($categories))
                            {
                                if($category["title"] !== $article_category["title"])
                                {
                                ?>
                                    <option value="<?=$category["id"]?>"><?=$category["title"]?></option>
                                <?php
                                }
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="body">Текст статьи</label>
                    <textarea class="form-control" rows="15" name="body" placeholder="Текст статьи"><?=$_COOKIE["body"] ? $_COOKIE["body"] : $article["body"]?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Изображение</label>
                    <br>
                    <img src="<?=$article["image"]?>" width="250">
                    <br><br>
                    <input type="file" class="form-control-file" name="image">
                </div>

                <button type="submit" class="btn btn-success" name="submit">Обновить</button>
                <?php
                    if(!is_null($_POST["submit"]))
                    {
                        $update = Article::update($_POST, $_POST["title"], $_POST["short"], $_POST["category_id"],
                                                $_POST["body"], $_FILES["image"], $_POST["old_image"], $_POST["id"]);
                        if($update)
                        {
                            setcookie("title", "", time()-3600, "/update.php");
                            setcookie("short", "", time()-3600, "/update.php");
                            setcookie("body", "", time()-3600, "/update.php");
                            setcookie("category_id", "", time()-3600, "/update.php");
                            setcookie("id", "", time()-3600, "/update.php");
                            setcookie("old_image", "", time()-3600, "/update.php");

                            header("location:/my-articles.php");
                            die();

                        } else {
                        ?>
                            <div class="alert alert-danger mt-3" role="alert">
                                Ошибка
                            </div>
                        <?php
                        }
                    }

                    if($_SESSION["error_message"])
                    {
                    ?>
                        <div class="alert alert-danger mt-3" role="alert">
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