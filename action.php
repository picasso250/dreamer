<?php

namespace action;

function index()
{
    global $db;
    $sql = "SELECT * from thread order by action_time desc limit 111";
    $list = $db->queryAll($sql);
    render(compact('list'));
}
function post()
{
    global $db;
    if (empty($_POST['title'])) {
        \echo_json_exit(1, 'no title');
    }
    $title = $_POST['title'];
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $data = compact('title', 'content');
    $data['action_time'] = $db::timestamp();
    $id = $db->insert('thread', $data);
    \echo_json_exit(compact('id'));
}
function login()
{
    render();
}
function logout()
{
    user_id(0);
    header('Location: /');
}
