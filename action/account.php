<?php

namespace action;

function login()
{
    render();
}
function login_check()
{
    if (empty($_POST['name'])) {
        return \echo_json(1, 'no name');
    }
    $name = $_POST['name'];
    if (empty($_POST['password'])) {
        return \echo_json(1, 'no password');
    }
    $password = $_POST['password'];

    global $db;
    $sql = "SELECT * from user where name=:name or email=:name limit 1";
    $user = $db->queryRow($sql, compact('name'));
    if (empty($user)) {
        return \echo_json(2, 'no user');
    }
    if (($user['password']) !== sha1($password)) {
        error_log("$user[id] $name password try fail");
        return \echo_json(3, 'password not correct');
    }
    user_id($user['id']);
    error_log("$user[id] $name login");
    return \echo_json(['url' => '/']);
}
function logout()
{
    user_id(0);
    header('Location: /');
}
