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
