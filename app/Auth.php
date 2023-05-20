<?php

namespace App;
session_start();

class Auth extends Connect
{
    public static function reg($post, $name, $surname, $email, $login, $password, $password_confirm)
    {
        foreach($post as $k => $v)
        {
            $v = htmlspecialchars($v);
            $v = trim($v);
            $v = preg_replace("#\s{1,}#", "", $v);

            setcookie($k, $v, time()+9999, "/auth/reg.php");
            setcookie("password", "", time()-3600, "/auth/reg.php");
            setcookie("password-confirm", "", time()-3600, "/auth/reg.php");
        }

        $name = htmlspecialchars($name);
        $name = trim($name);
        $name = preg_replace("#\s{1,}#", "", $name);

        $surname = htmlspecialchars($surname);
        $surname = trim($surname);
        $surname = preg_replace("#\s{1,}#", "", $surname);

        $email = htmlspecialchars($email);
        $email = trim($email);
        $email = preg_replace("#\s{1,}#", "", $email);

        $login = htmlspecialchars($login);
        $login = trim($login);
        $login = preg_replace("#\s{1,}#", "", $login);

        $password = htmlspecialchars($password);
        $password_confirm = htmlspecialchars($password_confirm);

        if($name === "" || $surname === "" || $email === "" ||
            $login === "" || $password === "" || $password_confirm === "")
        {
            $_SESSION["error_message"] = "Пожалуйста, заполните все поля";
            header("location:/auth/reg.php");
            die();
        }

        $sql_find = "SELECT `email`, `login` FROM `users`";
        $query_find = mysqli_query(self::db(), $sql_find);
        $user = mysqli_fetch_assoc($query_find);

        if($email === $user["email"])
        {
            $_SESSION["error_message"] = "Пользователь с таким email уже существует";
            header("location:/auth/reg.php");
            die(); 
        }

        if($login === $user["login"])
        {
            $_SESSION["error_message"] = "Пользователь с таким логином уже существует";
            header("location:/auth/reg.php");
            die(); 
        }

        if($password !== $password_confirm)
        {
            $_SESSION["error_message"] = "Пароли не совпадают";
            header("location:/auth/reg.php");
            die();
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `users` (`name`, `surname`, `email`, `login`, `password`) 
                VALUES ('$name', '$surname', '$email', '$login', '$hash')";
        
        $query = mysqli_query(self::db(), $sql);

        return $query ? true : false;
    }

    public static function login($login, $password)
    {
        $login = htmlspecialchars($login);     
        $password = htmlspecialchars($password);

        setcookie("login", $login, time()+9999, "/auth/login.php");

        $sql = "SELECT * FROM `users` WHERE `login` = '$login'";
        $query = mysqli_query(self::db(), $sql);
        $count = mysqli_num_rows($query);

        if(!$count > 0)
        {
            $_SESSION["error_message"] = "Неверный логин или пароль";
            header("location:/auth/login.php");
            die();
        }

        $user = mysqli_fetch_assoc($query);

        $verify = password_verify($password, $user["password"]);

        if($verify)
        {
            setcookie("login", "", time()-3600, "/auth/login.php");

            $_SESSION["id"] = $user["id"];
            $_SESSION["name"] = $user["name"];

            header("location:/profile.php?id=".$user["id"]);
            die();

        } else {
            $_SESSION["error_message"] = "Неверный логин или пароль";
            header("location:/auth/login.php");
            die();
        }
    }

    public static function check($id)
    {
        $id = htmlspecialchars($id);
        
        $sql = "SELECT * FROM `users` WHERE `id` = '$id'";
        $query = mysqli_query(self::db(), $sql);
        
        return mysqli_num_rows($query) > 0;
    }

    public static function logout()
    {
        unset($_SESSION["id"]);
        unset($_SESSION["name"]);
        unset($_SESSION);
        
        session_destroy();
    }
}