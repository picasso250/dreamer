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
function forgot()
{
    render();
}
function send_forgot()
{
    global $db;
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (empty($email)) {
        return \echo_json(1, 'plz provide email');
    }
    $user = $db->get_user_by_email($email);
    if (empty($user)) {
        return \echo_json(1, 'no user');
    }
    $query = http_build_query([
        'verify' => user_verify($user),
        'id' => $user['id'],
    ]);
    $href = "http://$_SERVER[HTTP_HOST]/reset_password?$query";
    $body = "
    click this to reset
    <a href='$href'>$href</a>
    ";
    list($ok, $msg) = send_mail($email, "ur password", $body);
    error_log("send $email forgot email $ok, $msg");
    echo_json(compact('ok'));
}
function reset_password()
{
    list($ok, $msg) = check_user_reset();
    if (!$ok) {
        render(compact('msg'));
    } else {
        render(['msg' => '', 'user' => $msg]);
    }
}
function do_reset_password()
{
    global $db;
    list($ok, $msg) = check_user_reset();
    if (!$ok) {
        return echo_json(1, $msg);
    }
    $user = $msg;
    if (empty($_POST['password'])) {
        return echo_json(1, 'no password');
    }
    $password = $_POST['password'];
    $db->update('user', ['password' => sha1($password)], ['id' => $user['id']]);
    echo_json([]);
}
function change_password()
{
    global $cur_user;
    global $db;
    if (empty($_POST['password'])) {
        return echo_json(1, 'no password');
    }
    $password = $_POST['password'];
    if (sha1($password) !== $cur_user['password']) {
        return echo_json(1, 'old password not correct');
    }
    if (empty($_POST['password_new'])) {
        return echo_json(1, 'no new password');
    }
    $password_new = $_POST['password_new'];
    $db->update('user', ['password' => sha1($password_new)], ['id' => user_id()]);
    echo_json([]);
}
function register()
{
    render();
}
function new_user()
{
    global $db;
    if (empty($_POST['email']) && empty($_POST['username'])) {
        return echo_json(1, 'email or username, at least one');
    }
    $data = [];
    $desc = '';
    if (!empty($_POST['email'])) {
        $data['email'] = $email = $_POST['email'];
        if ($db->get_user_by_email($email)) {
            return echo_json(1, 'email taken');
        }
        $desc .= " email: $email ";
    }
    if (!empty($_POST['username'])) {
        $data['name'] = $username = $_POST['username'];
        if ($db->get_user_by_name($username)) {
            return echo_json(1, 'username taken');
        }
        $desc .= " username: $username ";
    }
    if (empty($_POST['password'])) {
        return echo_json(1, 'password need');
    }
    $data['password'] = sha1($_POST['password']);
    $id = $db->insert('user', $data);
    error_log("new user $desc ==> $id");
    echo_json(compact('id'));
}
