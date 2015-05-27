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
function login_check()
{
    if (empty($_POST['name'])) {
        echo_json_exit(1, 'no name');
    }
    $name = $_POST['name'];
    if (empty($_POST['password'])) {
        echo_json_exit(1, 'no password');
    }
    $password = $_POST['password'];

    global $db;
    $sql = "SELECT * from user where name=:name or email=:name limit 1";
    $user = $db->queryRow($sql, compact('name'));
    if (empty($user)) {
        echo_json_exit(2, 'no user');
    }
    if (($user['password']) !== sha1($password)) {
        echo_json_exit(3, 'password not correct');
    }
    user_id($user['id']);
    echo_json_exit(0);
}
function logout()
{
    user_id(0);
    header('Location: /');
}
