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
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "includes/head.php" ?>
<body>
<?php require_once "includes/header.php" ?>
    <main>
        <div class="container mt-5">
            <h3>Создать статью</h3>

            <form class="mt-5 mb-5" method="POST" action="/create.php" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="title">Название статьи</label>
                    <input type="text" class="form-control" name="title" placeholder="Название статьи" value="<?=$_COOKIE["title"]?>">
                </div>

                <div class="form-group">
                    <label for="short">Короткое описание</label>
                    <textarea class="form-control" rows="3" name="short" placeholder="Короткое описание"><?=$_COOKIE["short"]?></textarea>
                </div>

                <label for="category_id">Тема</label>
                <div class="form-group">
                    <select class="form-control" name="category_id">
                        <?php
                            $categories = Category::get();

                            while($category = mysqli_fetch_assoc($categories))
                            {
                            ?>
                                <option value="<?=$category["id"]?>"><?=$category["title"]?></option>
                            <?php
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="body">Текст статьи</label>
                    <textarea class="form-control" rows="15" name="body" placeholder="Текст статьи"><?=$_COOKIE["body"]?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Изображение</label>
                    <input type="file" class="form-control-file" name="image">
                </div>

                <button type="submit" class="btn btn-success" name="submit">Отправить</button>
                <?php
                    if(!is_null($_POST["submit"]))
                    {
                        $create = Article::create($_POST, $_POST["title"], $_POST["short"], $_POST["category_id"],
                                                $_POST["body"], $_FILES["image"], $_SESSION["id"]);
                        if($create)
                        {   
                            setcookie("title", "", time()-3600, "/create.php");
                            setcookie("short", "", time()-3600, "/create.php");
                            setcookie("body", "", time()-3600, "/create.php");
                            setcookie("category_id", "", time()-3600, "/create.php");

                            header("location:/my-articles.php");
                            die();
                        } else {
                        ?>
                            <div class="alert alert-danger mt-3" role="alert">
                                Ошибка при создании статьи, попробуйте ещё раз
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