<?php

namespace action;

function index()
{
    global $db;
    $tab = isset($_GET['tab']) ? $_GET['tab'] : null;
    $where = [];
    if ($tab) {
        $where['root_node_id'] = $tab;
    }
    $list = \all_thread($where);
    $nodes = $db->queryAll("SELECT * from node where pid=0 limit 111");
    $sub_nodes = [];
    if ($tab) {
        $sub_nodes = $db->all_node_by_pid($tab);
    }
    foreach ($nodes as &$node) {
        $node['sub'] = $db->all_node_by_pid($node['id']);
    }
    $total_memeber = $db->count_user();
    $total_thread  = $db->count_thread();
    $total_comment = $db->count_comment();
    $data = compact(
        'list', 'nodes', 'tab', 'subtab', 'sub_nodes',
        'total_memeber',
        'total_thread',
        'total_comment'
    );
    render($data);
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
    $data['node_id'] = $node_id = node_id_input();
    if ($node_id) {
        $root = root_node($node_id);
        $data['root_node_id'] = $root['id'];
    } else {
        $data['root_node_id'] = 0;
    }
    $id = $db->insert('thread', $data);
    return \echo_json(['url' => "/thread/$id"]);
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
function thread($id)
{
    global $db;
    $thread = get_thread($id);
    $comments = all_comment($id);
    $sql = 'UPDATE thread set 
        visit_count=visit_count+1
        where id=?';
    $db->execute($sql, [$id]);
    $votes = $db->all_vote_by_user_id_and_t_id(user_id(), $id);
    $my_votes = [];
    foreach ($votes as $vote) {
        $my_votes[$vote['attitude']] = $vote;
    }
    render(compact('thread', 'comments', 'my_votes'));
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
function setting()
{
    render();
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
function post_new()
{
    global $db;
    if (!empty($_GET['node'])) {
        $node_id = $_GET['node'];
    } else {
        $node_id = 0;
        $nodes = $db->all_node(100);
    }
    render(compact('nodes', 'node_id'));
}
function vote_thread($t_id)
{
    global $db;
    if (empty($_POST['value'])) {
        return echo_json(1, 'no value');
    }
    $value = $_POST['value'];
    $user_id = user_id();
    $vote = $db->get_vote_by_user_id_and_attitude($user_id, $value);
    if ($vote) {
        return echo_json(1, 'u have vote');
    }
    $db->insert('vote', [
        'user_id' => user_id(),
        't_id' => $t_id,
        'attitude' => $value,
    ]);
    $map = [
        1 => 'vote_for',
        -1 => 'vote_against',
    ];
    $field = $map[$value];
    $num = $db->count_vote_by_user_id_and_attitude($user_id, $value);
    $db->update('thread', [$field => $num], ['id' => $t_id]);
    echo_json(compact('num'));
}
function node($id)
{
    global $db;
    $node = $db->get_node_by_id($id);
    $list = all_thread(['node_id' => $id]);
    $total = $db->count_thread_by_node_id($id);
    render(compact('list', 'node', 'total'));
}
function search()
{
    $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
    $Location = "https://www.google.com/search?"
        . http_build_query([
            'q' => "site:$_SERVER[HTTP_HOST] $kw",
            'gws_rd' => 'ssl',
        ]);
    header("Location:$Location");
}
