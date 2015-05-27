<?php

require 'xiaochi-db/src/DB.php';
require 'lib.php';
require 'action.php';

$REQUEST_URI = $_SERVER['REQUEST_URI'];
$path = explode('?', $REQUEST_URI)[0];
if ($path === '/') {
    $router = 'index';
} else {
    $router = substr($path, 1);
}

$config = require 'config.php';
$dbc = $config['db'];
$db = new \xiaochi\DB($dbc['dsn'], $dbc['username'], $dbc['password']);

header('Content-Type: text/html; charset=utf-8');
$func = "\\action\\$router";
$func();
