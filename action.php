<?php

namespace action;

function index()
{
    $list = \all_thread();
    render(compact('list'));
}
function thread_list()
{
    $list = \all_thread();
    include 'view/thread_list.html';
}
function post()
{
    global $db;
    if (empty($_POST['title'])) {
        return \echo_json(1, 'no title');
    }
    $title = $_POST['title'];
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $data = compact('title', 'content');
    $data['user_id'] = user_id();
    $data['action_time'] = $db::timestamp();
    $id = $db->insert('thread', $data);
    return \echo_json(compact('id'));
}
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
function thread($id)
{
    $thread = get_thread($id);
    $comments = all_comment($id);
    render(compact('thread', 'comments'));
}
function post_comment($t_id)
{
    global $db;
    if (empty($_POST['content'])) {
        return \echo_json(1, 'empty content');
    }
    $content = $_POST['content'];
    $data = compact('t_id', 'content');
    $data['user_id'] = user_id();
    $id = $db->insert('comment', $data);
    $sql = 'UPDATE thread set 
        comment_count=comment_count+1,
        action_time=?
        where id=?';
    $db->execute($sql, [$db::timestamp(), $t_id]);
    return \echo_json(compact('id'));
}
function comment_list($t_id)
{
    $comments = all_comment($t_id);
    include 'view/comment_list.html';
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
function user($id)
{
    global $db;
    $user = $db->get_user_by_id($id);
    render(compact('user'));
}
