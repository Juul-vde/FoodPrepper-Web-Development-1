<?php
session_start();

require '../vendor/autoload.php';
require '../views/patternrouter.php';

$uri = trim($_SERVER['REQUEST_URI'], '/');

$router = new App\PatternRouter();
$router->route($uri);