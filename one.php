<?php
require_once __DIR__ . "/vendor/autoload.php";
session_start();

use App\Article;
use App\User;
use App\Auth;
use App\Comment;
use App\Category;

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "includes/head.php" ?>
<body>
<?php require_once "includes/header.php" ?>
    <main>
        <?php
            $article = Article::get_one($_GET["id"]);
        ?>
        <h4 align="center" class="mt-4"><?=$article["title"]?></h4>
        <hr>
        <div class="container">
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
            ?>
            <div class="one">
                <img src="<?=$article["image"]?>">
                <p class="name mt-2">
                    Автор:  <a href="profile.php?id=<?=$article["user_id"]?>">
                                <?=User::get_one($article["user_id"])["name"]?> <?=User::get_one($article["user_id"])["surname"]?>
                            </a>
                </p>
                <p class="name"><?=$article["time"]?></p>
            </div>
        </div>
        <hr>
        <div class="container">
            <p><?=$article["body"]?></p>

            <h4 class="mt-4">Комментарии:</h4>

            <?php
                if(Auth::check($_SESSION["id"]))
                {
                ?>
                    <p>Оставьте комментарий:</p>
                    <form method="POST" action="/one.php?id=<?=$_GET["id"]?>">
                        <div class="form-group">
                            <label for="body">Введите комментарий</label>
                            <textarea class="form-control" rows="3" name="body" placeholder="Текст комментария"><?=$_COOKIE["body"]?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Отправить</button>
                        
                        <?php
                            if(!is_null($_POST["submit"]))
                            {
                                $create = Comment::create($_SESSION["name"], $_POST["body"], $_SESSION["id"], $article["id"]);

                                if($create)
                                {
                                    setcookie("body", "", time()-3600, "/one.php");
                                    header("location:/one.php?id=".$article["id"]);

                                } else {
                                ?>
                                    <div class="alert alert-warning mt-3" role="alert">
                                        Ошибка
                                    </div>
                                <?php
                                }
                            }

                            if($_SESSION["error_message"])
                            {
                            ?>
                                <div class="alert alert-warning mt-3" role="alert">
                                    <?=$_SESSION["error_message"]?>
                                </div>
                            <?php
                                unset($_SESSION["error_message"]);
                            }
                        ?>
                    </form>
                <?php
                } else {
                    echo("<a href='/auth/login.php'>Войдите</a>, чтобы оставлять комментарии");
                }
            ?>

            <?php
                $comments = Comment::get($_GET["id"]);

                if(mysqli_num_rows($comments) < 1)
                {
                    echo "<p class='mt-3'>Будьте первым, кто оставит комментарий</p>";
                }

                while($comment = mysqli_fetch_assoc($comments))
                {
                ?>
                    <div class="list-group mt-3">
                        <div class="list-group-item flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    <?=User::get_one($comment["user_id"])["login"]?> (<?=User::get_one($comment["user_id"])["name"]?> <?=User::get_one($comment["user_id"])["surname"]?>)
                                </h5>
                                <small class="text-muted"><?=$comment["time"]?></small>
                            </div>
                            <p class="mb-1"><?=$comment["body"]?></p>
                            <?php
                                if(Comment::check($comment["id"], $_SESSION["id"]))
                                {
                                ?>
                                    <form method="POST" action="/one.php?id=<?=$_GET["id"]?>">
                                        <button style="float: right;" class="btn btn-link" name="delete_<?=$comment["id"]?>">Удалить</button>
                                    </form>
                                <?php
                                
                                    if(!is_null($_POST["delete_".$comment["id"]]))
                                    {
                                        $delete = Comment::delete($comment["id"]);

                                        if($delete)
                                        {
                                            header("location:/one.php?id=".$_GET["id"]);

                                        } else {
                                        ?>
                                            <div class="alert alert-warning mt-3" role="alert">
                                                Ошибка
                                            </div>
                                        <?php
                                        }
                                    }
                                }
                            ?>
                        </div>
                    </div>
                <?php
                }
            ?>
            <h4 align="center" class="mt-5">Другие статьи на тему <?=Category::get_one($article["category_id"])["title"]?></h4>
            <hr>
            <div class="articles_new mb-5">
                <?php
                    $articles_category = Article::get_categoryidlimit($article["category_id"]);

                    while($article_category = mysqli_fetch_assoc($articles_category))
                    {
                        if($article_category["id"] !== $article["id"])
                        {
                        ?>
                            <div class="article">
                                <img src="<?=$article_category["image"]?>" align="center" width="300">
                                <h5 class="mt-2"><?=$article_category["title"]?></h5>
                                <p class="description mt-2">
                                    <?=$article_category["short"]?>
                                </p>
                                <a href="one.php?id=<?=$article_category["id"]?>">Читать далее</a>
                            </div>
                        <?php
                        }
                    }
                ?>
            </div>
        </div>
    </main>
</body>

</html>