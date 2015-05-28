<?php

$start_time = microtime(true);

require 'xiaochi-db/src/DB.php';
require 'lib.php';
require 'account.php';
require 'logic.php';
require 'action.php';

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
    'setting',
    'post',
    'post_new',
    'fav',
    'post_comment',
    'vote_thread',
    'fav_thread',
    'change_password',
    'append',
    'append_thread',
];
if (in_array($router, $login_check_router_list) && !$user_id) {
    die("u need login");
}

run($router, $args);
