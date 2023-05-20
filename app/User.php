<?php

namespace App;
session_start();

class User extends Connect
{
    public static function get_one($id)
    {
        $id = htmlspecialchars($id);

        $sql = "SELECT * FROM `users` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return mysqli_fetch_assoc($query);
    }

    public static function update($id, $old_avatar, $default_avatar, $avatar, $description)
    {
        $new_avatar = false;
        $path = ""; 

        $id = htmlspecialchars($id);
        $old_avatar = htmlspecialchars($old_avatar);
        $default_avatar = htmlspecialchars($default_avatar);

        $description = htmlspecialchars($description);
        $description = trim($description);
        $description = preg_replace("#\s{2,}#", " ", $description);

        if($description === "")
        {
            $_SESSION["error_message"] = "Пустые значения не допускаются";
            header("location:/settings-profile.php?id=".$id);
            die();
        }

        if($avatar["name"])
        {
            $new_avatar = true;

            if($avatar["type"] !== "image/jpeg" &&
                $avatar["type"] !== "image/jpg" && 
                $avatar["type"] !== "image/png")
            {
                $_SESSION["error_message"] = "Допускаются изображения только формата JPEG, JPG и PNG";
                header("location:/settings-profile.php?id=".$id);
                die();
            }

            $find_avatar = self::get_one($id);

            if($find_avatar["avatar"] !== $default_avatar)
            {
                if(!unlink($find_avatar["avatar"]))
                {
                    $_SESSION["error_message"] = "Ошибка, попробуйте еще раз";
                    header("location:/settings-profile.php?id=".$id);
                    die();
                }
            }

            $path = "storage/avatars/" . time() . "_" . $avatar["name"];

            if(!move_uploaded_file($avatar["tmp_name"], $path))
            {
                $_SESSION["error_message"] = "Ошибка, попробуйте ещё раз";
                header("location:/settings-profile.php?id=".$id);
                die();
            }
        }

        if(!$new_avatar)
        {
            $path = $old_avatar;
        }

        $description = addslashes($description);

        $sql = "UPDATE `users` SET `avatar` = '$path', `description` = '$description' WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return $query ? true : false;
    }

    public static function check($user_id, $id)
    {
        $user_id = htmlspecialchars($user_id);
        $id = htmlspecialchars($id);

        $user = self::get_one($id);

        return $user["id"] === $user_id;
    }

    public static function delete($id, $user_id)
    {
        $id = htmlspecialchars($id);
        $user_id = htmlspecialchars($user_id);

        if(!self::check($user_id, $id))
        {
            $_SESSION["error_message"] = "Ошибка";
            header("location:/settings-profile.php?id=".$id);
            die();
        }

        $find_avatar = self::get_one($id);

        if($find_avatar["avatar"] !== "storage/avatars/avatar.png")
        {
            if(!unlink("../" . $find_avatar["avatar"]))
            {
                $_SESSION["error_message"] = "Ошибка при удалении аватара";
                header("location:/settings-profile.php?id=".$id);
                die();
            }

        } else {
            $_SESSION["error_message"] = "Ошибка";
            header("location:/settings-profile.php?id=".$id);
            die();
        }

        $path = "storage/avatars/avatar.png";

        $sql = "UPDATE `users` SET `avatar` = '$path' WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);

        return $query;
    }
}