<?php

namespace App;
session_start();

class Article extends Connect
{
    public static function create($post, $title, $short, $category_id, $body, $image, $user_id)
    {
        foreach($post as $k => $v)
        {
            $v = htmlspecialchars($v);
            $v = trim($v);
            $v = preg_replace("#\s{2,}#", " ", $v);

            setcookie($k, $v, time()+9999, "/create.php");
        }

        $title = htmlspecialchars($title);
        $title = trim($title);
        $title = preg_replace("#\s{2,}#", " ", $title);

        $short = htmlspecialchars($short);
        $short = trim($short);
        $short = preg_replace("#\s{2,}#", " ", $short);
        $short = addslashes($short);

        $category_id = htmlspecialchars($category_id);
        $user_id = htmlspecialchars($user_id);

        $body = htmlspecialchars($body);
        $body = trim($body);
        $body = preg_replace("#\s{2,}#", " ", $body);
        $body = addslashes($body);

        if($title === "" || $short === "" || $body === "")
        {
            $_SESSION["error_message"] = "Пожалуйста, заполните все поля";
            header("location:/create.php");
            die();
        }

        if($image["name"] === "")
        {
            $_SESSION["error_message"] = "Пожалуйста, прикрепите изображение";
            header("location:/create.php");
            die();

        } elseif ($image["type"] !== "image/jpeg" && $image["type"] !== "image/jpg" && $image["type"] !== "image/png")
        {
            $_SESSION["error_message"] = "Допускают изображения только формата JPEG, JPG и PNG";
            header("location:/create.php");
            die();
        }

        $path = "storage/images/" . time() . "_" . $image["name"];
        
        if(!move_uploaded_file($image["tmp_name"], $path))
        {
            $_SESSION["error_message"] = "Ошибка при загрузке изображения, попробуйте ещё раз";
            header("location:/create.php");
            die();
        }

        $sql = "INSERT INTO `articles` (`title`, `short`, `category_id`, `body`, `image`, `user_id`)
                VALUES ('$title', '$short', '$category_id', '$body', '$path', '$user_id')";
        $query = mysqli_query(self::db(), $sql);

        return $query ? true : false;
    }

    public static function get()
    {
        $sql = "SELECT * FROM `articles` WHERE `public` = 1 ORDER BY RAND()";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function get_new()
    {
        $sql = "SELECT * FROM `articles` WHERE `public` = 1 ORDER BY `id` DESC LIMIT 3";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function get_one($id)
    {
        $id = htmlspecialchars($id);

        $sql = "SELECT * FROM `articles` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return mysqli_fetch_assoc($query);
    }

    public static function check($id)
    {
        $id = htmlspecialchars($id);
        
        $sql = "SELECT * FROM `articles` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return mysqli_num_rows($query) > 0;
    }

    public static function check_user($user_id, $id)
    {
        $id = htmlspecialchars($id);
        $user_id = htmlspecialchars($user_id);

        $check_id = self::get_one($id)["user_id"];

        return $user_id === $check_id;
    }

    public static function get_userid($user_id)
    {
        $user_id = htmlspecialchars($user_id);

        $sql = "SELECT * FROM `articles` WHERE `user_id` = '$user_id'";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function get_categoryid($category_id)
    {
        $category_id = htmlspecialchars($category_id);

        $sql = "SELECT * FROM `articles` WHERE `category_id` = '$category_id'";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function get_categoryidlimit($category_id)
    {
        $category_id = htmlspecialchars($category_id);

        $sql = "SELECT * FROM `articles` WHERE `category_id` = '$category_id' ORDER BY RAND() LIMIT 3";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function search($q)
    {
        $q = htmlspecialchars($q);

        $sql = "SELECT * FROM `articles` WHERE `title` LIKE '%$q%' OR `short` LIKE '%$q%' OR `body` LIKE '%$q%' AND `public` = '1' ORDER BY `id` DESC";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function public($id)
    {
        $id = htmlspecialchars($id);

        $article = self::get_one($id);

        $public = (int)$article["public"] === 1 ? 0 : 1;

        $sql = "UPDATE `articles` SET `public` = '$public' WHERE `id` = $id";
        $query = mysqli_query(self::db(), $sql);

        if($query)
        {
            header("location:/my-articles.php");
            die();

        } else {
            $_SESSION["error_message"] = "Ошибка";
            header("location:/my-articles.php");
            die();
        }
    }

    public static function delete($id, $image)
    {
        $id = htmlspecialchars($id);
        $image = htmlspecialchars($image);

        if(!self::check($id))
        {
            $_SESSION["error_message"] = "Ошибка";
            header("location:/my-articles.php");
            die();
        }

        if(!unlink($image))
        {
            $_SESSION["error_message"] = "Ошибка";
            header("location:/my-articles.php");
            die();
        }

        $sql = "DELETE FROM `articles` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return $query ? true : false;
    }

    public static function update($post, $title, $short, $category_id, $body, $image, $old_image, $id)
    {
        $new_image = false;
        $path = "";

        $id = htmlspecialchars($id);

        foreach($post as $k => $v)
        {
            $v = htmlspecialchars($v);
            $v = trim($v);
            $v = preg_replace("#\s{2,}#", " ", $v);

            setcookie($k, $v, time()+9999, "/update.php");
        }

        $title = htmlspecialchars($title);
        $title = trim($title);
        $title = preg_replace("#\s{2,}#", " ", $title);

        $short = htmlspecialchars($short);
        $short = trim($short);
        $short = preg_replace("#\s{2,}#", " ", $short);

        $category_id = htmlspecialchars($category_id);

        $body = htmlspecialchars($body);
        $body = trim($body);
        $body = preg_replace("#\s{2,}#", " ", $body);

        $old_image = htmlspecialchars($old_image);

        if($title === "" || $short === "" || $body === "")
        {
            $_SESSION["error_message"] = "Пожалуйста, заполните все поля";
            header("location:/update.php?id=".$id);
            die();
        }

        if($image["name"])
        {
            $new_image = true;

            if($image["type"] !== "image/jpeg" && $image["type"] !== "image/jpg" && $image["type"] !== "image/png")
            {
                $_SESSION["error_message"] = "Допускают изображения только формата JPEG, JPG и PNG";
                header("location:/update.php?id=".$id);
                die(); 
            }
            
            $path = "storage/images/" . time() . "_" . $image["name"];

            if(!move_uploaded_file($image["tmp_name"], $path))
            {
                $_SESSION["error_message"] = "Ошибка при обновлении изображения, попробуйте ещё раз";
                header("location:/update.php?id=".$id);
                die();
            }

            $delete = self::get_one($id);
            
            if(!unlink($delete["image"]))
            {
                $_SESSION["error_message"] = "Ошибка при обновлении изображения, попробуйте ещё раз";
                header("location:/update.php?id=".$id);
                die();
            }
        }

        if(!$new_image)
        {
            $path = $old_image;
        }

        $sql = "UPDATE `articles` SET `title` = '$title', `short` = '$short', `category_id` = '$category_id', `body` = '$body', `image` = '$path' WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return $query ? true : false;
    }
}