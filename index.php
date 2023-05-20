<?php
require_once __DIR__ . "/vendor/autoload.php";

use App\Article;

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "includes/head.php" ?>
<body>
<?php require_once "includes/header.php" ?>

    <main>
        <div class="container articles mt-5">
            <?php

                if($_GET["q"])
                {
                    $articles_search = Article::search($_GET["q"]);

                    if(mysqli_num_rows($articles_search) < 1)
                    {
                        die("Ничего не нашлось...");
                    }

                    while($article_search = mysqli_fetch_assoc($articles_search))
                    {
                    ?>
                        <div class="article">
                            <img src="<?=$article_search["image"]?>" align="center" width="300">
                            <h5 class="mt-2"><?=$article_search["title"]?></h5>
                            <p class="description mt-2">
                                <?=$article_search["short"]?>
                            </p>
                            <a href="/one.php?id=<?=$article_search["id"]?>">Читать далее</a>
                        </div> 
                    <?php
                    }
                    die();
                }   

                $articles_category = Article::get_categoryid($_GET["category_id"]);

                if(mysqli_num_rows($articles_category) > 0)
                {
                    while($article_category = mysqli_fetch_assoc($articles_category))
                    {
                    ?> 
                        <div class="article">
                            <img src="<?=$article_category["image"]?>" align="center" width="300">
                            <h5 class="mt-2"><?=$article_category["title"]?></h5>
                            <p class="description mt-2">
                                <?=$article_category["short"]?>
                            </p>
                            <a href="/one.php?id=<?=$article_category["id"]?>">Читать далее</a>
                        </div>                        
                    <?php
                    }
                    die();
                }
            ?>
        </div>
        <h4 align="center">Новые статьи</h4>
        <hr>
        <div class="container">

            <div class="articles_new mt-4">
                <?php
                    $articles_new = Article::get_new();

                    while($article_new = mysqli_fetch_assoc($articles_new))
                    {
                    ?>
                        <div class="article">
                            <img src="<?=$article_new["image"]?>" align="center" width="300">
                            <h5 class="mt-2"><?=$article_new["title"]?></h5>
                            <p class="description mt-2">
                                <?=$article_new["short"]?>
                            </p>
                            <a href="/one.php?id=<?=$article_new["id"]?>">Читать далее</a>
                        </div>
                    <?php
                    }
                ?>
            </div>
        </div>
        <hr>

        <div class="container">

            <h4 align="center" class="mt-4">Ранее опубликованные</h4>
            
            <div class="articles mt-4 mb-5">
                <?php
                    $articles = Article::get();
                    $article = [];

                    foreach($articles as $row)
                    {
                        $article[] = $row;
                    }

                    $page = $_GET["page"];
                    $count = 12;
                    $page_count = ceil(count($article)) / $count;

                    for($i = $page*$count; $i < ($page+1)*$count; $i++)
                    {
                        if(!is_null($article[$i]["id"]))
                        {
                        ?>
                            <div class="article">
                                <img src="<?=$article[$i]["image"]?>" align="center" width="300">
                                <h5 class="mt-2"><?=$article[$i]["title"]?></h5>
                                <p class="description mt-2">
                                    <?=$article[$i]["short"]?>
                                </p>
                                <a href="/one.php?id=<?=$article[$i]["id"]?>">Читать далее</a>
                            </div>
                        <?php
                        }
                    }
                ?>
            </div>

            <?php
                if(mysqli_num_rows($articles) > 12)
                {
                ?>
                    <div class="page-list">
                        <?php
                            for($p = 0; $p < $page_count; $p++)
                            {
                            ?>
                                <a class="btn btn-outline-link mb-5" href="/?page=<?=$p?>" role="button"><?=$p+1?></a>
                            <?php
                            }
                        ?>
                    </div>
                <?php
                }
            ?>
        </div>
    </main>
</body>

</html>