<?php

$start_time = microtime(true);

require 'xiaochi-db/src/DB.php';
require 'lib.php';
require 'logic/main.php';
require 'logic/account.php';
require 'action/main.php';
require 'action/account.php';

list($router, $args) = get_router();

$config = require 'config.php';
$dbc = $config['db'];
$db = new \xiaochi\DB($dbc['dsn'], $dbc['username'], $dbc['password']);
$db->debug = $dbc['debug'];

session_start();
$user_id = user_id();
$cur_user = null;
if ($user_id) {
    $cur_user = $db->get_user_by_id($user_id);
}

$login_check_router_list = [
    'post', 'post_new',
    'post_comment',
    'vote_thread',
    'fav', 'fav_thread',
    'setting',
    'change_password',
    'append', 'append_thread',
];
if (in_array($router, $login_check_router_list) && !$user_id) {
    die("u need login");
}

$devices = [
    1 => 'Sony',
    2 => 'iPhone',
    3 => 'Nexus',
    4 => 'iPad'
];

run($router, $args);
