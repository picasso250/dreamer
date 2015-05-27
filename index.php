<?php

require 'xiaochi-db/src/DB.php';
require 'lib.php';
require 'account.php';
require 'logic.php';
require 'action.php';

$REQUEST_URI = $_SERVER['REQUEST_URI'];
$path = explode('?', $REQUEST_URI)[0];
$args = [];
if ($path === '/') {
    $router = 'index';
} elseif (preg_match('#^/(\w+)$#', $path, $matches)) {
    $router = $matches[1];
} elseif (preg_match('#^/(\w+)/(\d+)$#', $path, $matches)) {
    $router = $matches[1];
    $args[] = $matches[2];
} else {
    $router = 'page404';
}

$config = require 'config.php';
$dbc = $config['db'];
$db = new \xiaochi\DB($dbc['dsn'], $dbc['username'], $dbc['password']);

session_start();
$user_id = user_id();
$user = null;
if ($user_id) {
    $user = $db->get_user_by_id($user_id);
}

header('Content-Type: text/html; charset=utf-8');
$func = "\\action\\$router";
call_user_func_array($func, $args);
