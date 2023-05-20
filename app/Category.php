<?php

namespace App;

class Category extends Connect
{
    public static function get()
    {
        $sql = "SELECT * FROM `categories` ORDER BY `title`";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }

    public static function get_one($id)
    {
        $id = htmlspecialchars($id);

        $sql = "SELECT * FROM `categories` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return mysqli_fetch_assoc($query);
    }
}