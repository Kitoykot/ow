<?php
require_once __DIR__ . "/vendor/autoload.php";
session_start();

use App\Auth;
use App\Article;
use App\User;

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
                        Страница не найдена
                    </div>
                <?php
                    die();
                }
            ?>
            
            <p>Статьи от автора <a href="profile.php?id=<?=$_GET["id"]?>"><b><?=User::get_one($_GET["id"])["name"]?> <?=User::get_one($_GET["id"])["surname"]?></b></a></p>

            <div class="articles mt-4 mb-5">
                <?php
                    $articles = Article::get_userid($_GET["id"]);

                    if(!mysqli_num_rows($articles) > 0)
                    {
                    ?>
                        <p>Пока что тут пусто...</p>
                    <?php
                    }

                    while($article = mysqli_fetch_assoc($articles))
                    {
                    ?>
                        <div class="article">
                            <img src="<?=$article["image"]?>" align="center" width="300">
                            <h5 class="mt-2"><?=$article["title"]?></h5>
                            <p class="description mt-2">
                                <?=$article["short"]?>
                            </p>
                            <a href="/one.php?id=<?=$article["id"]?>">Читать далее</a>
                        </div>
                    <?php
                    }
                ?>
            </div>
        </div>
    </main>
</body>

</html>