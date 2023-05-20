<?php

require_once __DIR__ . "/../vendor/autoload.php";
session_start();

use App\Auth;
use App\Category;

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/"><span id="blue">Our</span><span id="green">World</span></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Статьи
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                                $categories = Category::get();

                                while($category = mysqli_fetch_assoc($categories))
                                {
                                ?>
                                    <a class="dropdown-item" href="/?category_id=<?=$category["id"]?>"><?=$category["title"]?></a>
                                <?php
                                }
                            ?>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a id="search_link" class="nav-link">Поиск</a>
                    </li>
                    <form method="GET" action="/" id="nav_search" class="form-inline my-2 my-lg-0">
                        <input name="q" class="form-control mr-sm-2" type="search" placeholder="Поиск статьи" aria-label="Search">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Поиск</button>
                    </form>
                </ul>
                <?php
                    if(!Auth::check($_SESSION["id"]))
                    {
                    ?>
                        <a class="nav-link dropdown" href="/auth/login.php">
                            Войти
                        </a>
                        <a class="nav-link dropdown" href="/auth/reg.php">
                            Регистрация
                        </a>
                    <?php
                    }

                    if(Auth::check($_SESSION["id"]))
                    {
                    ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?=$_SESSION["name"]?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="/profile.php?id=<?=$_SESSION["id"]?>">Профиль</a>
                                <a class="dropdown-item" href="/my-articles.php">Мои статьи</a>
                                <a class="dropdown-item" href="/settings-profile.php?id=<?=$_SESSION["id"]?>">Настройки</a>
                                <a class="dropdown-item" href="/actions/logout.php">Выход</a>
                            </div>
                        </li>
                    <?php
                    }
                ?>
            </div>
        </div>
    <script src="/assets/js/script.js"></script>
</nav>