<?php

namespace App;
session_start();

class Comment extends Connect
{
    public static function create($name, $body, $user_id, $article_id)
    {
        $name = htmlspecialchars($name);
        $article_id = htmlspecialchars($article_id);
        $user_id = htmlspecialchars($user_id);

        $body = htmlspecialchars($body);
        $body = trim($body);
        $body = preg_replace("#\s{2,}#", " ", $body);

        setcookie("body", $body, time()+9999, "/one.php");
        
        if($body === "")
        {
            $_SESSION["error_message"] = "Пустое значение не допускается";
            header("location:/one.php?id=".$article_id);
            die();
        }

        $sql = "INSERT INTO `comments` (`name`, `body`, `user_id`, `article_id`)
                VALUES ('$name', '$body', '$user_id', '$article_id')";
        $query = mysqli_query(self::db(), $sql);

        return $query ? true : false;
    }

    public static function get($id)
    {
        $id = htmlspecialchars($id);

        $sql = "SELECT * FROM `comments` WHERE `article_id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function check($id, $user_id)
    {
        $id = htmlspecialchars($id);
        $user_id = htmlspecialchars($user_id);

        $sql = "SELECT * FROM `comments` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);
        $comment = mysqli_fetch_assoc($query);

        return $user_id === $comment["user_id"];
    }

    public static function delete($id)
    {
        $id = htmlspecialchars($id);

        $sql = "DELETE FROM `comments` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }
}