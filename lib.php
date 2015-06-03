<?php

function echo_json($code, $msg = 'ok')
{
    header('Content-Type: application/json; charset=utf-8');
    if (is_int($code)) {
        $res = compact('code', 'msg');
    } else {
        $res = ['code' => 0, 'msg' => $msg, 'data' => $code];
    }
    echo json_encode($res);
}
function render($data = [])
{
    extract($data);
    include "view/layout.html";
}
function run($router, $args)
{
    header('Content-Type: text/html; charset=utf-8');
    $func = "\\action\\$router";
    if (!function_exists($func)) {
        $func = "\\action\\page404";
    }
    return call_user_func_array($func, $args);
}
function get_router()
{
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
    return [$router, $args];
}
