<?php

require 'xiaochi-db/src/DB.php';
require 'lib.php';
require 'account.php';
require 'logic.php';
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

session_start();
$user_id = user_id();
$user = null;
if ($user_id) {
    $user = $db->get_user_by_id($user_id);
}

header('Content-Type: text/html; charset=utf-8');
$func = "\\action\\$router";
$func();
