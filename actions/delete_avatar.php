<?php

require_once __DIR__ . "/../vendor/autoload.php";
session_start();

use App\User;

$id = $_GET["id"];

User::delete($id, $_SESSION["id"]);

header("location:/profile.php?id=".$id);