<?php

function index()
{
    include 'view/index.html';
}
function post()
{
    global $db;
    header('Content-Type: application/json; charset=utf-8');
    if (empty($_POST['title'])) {
        echo_json_exit(1, 'no title');
    }
    $title = $_POST['title'];
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $id = $db->insert('thread', compact('title', 'content'));
    echo_json_exit(compact('id'));
}
