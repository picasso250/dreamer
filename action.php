<?php

namespace action;

function index()
{
    include 'view/index.html';
}
function post()
{
    global $db;
    if (empty($_POST['title'])) {
        \echo_json_exit(1, 'no title');
    }
    $title = $_POST['title'];
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $id = $db->insert('thread', compact('title', 'content'));
    \echo_json_exit(compact('id'));
}
