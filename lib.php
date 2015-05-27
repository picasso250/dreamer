<?php

function echo_json_exit($code, $msg = 'ok')
{
    header('Content-Type: application/json; charset=utf-8');
    if (is_int($code)) {
        $res = compact('code', 'msg');
    } else {
        $res = ['code' => 0, 'msg' => $msg, 'data' => $code];
    }
    echo json_encode($res);
    exit;
}
function render($data = [])
{
    extract($data);
    include "view/layout.html";
}
