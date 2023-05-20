<?php

require_once __DIR__ . "/../vendor/autoload.php";
session_start();

use App\Auth;

Auth::logout();

header("location:/");
die();