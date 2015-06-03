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
    $data = compact(
        'list', 'nodes', 'tab', 'subtab', 'sub_nodes'
    );
    $data['total_memeber'] = $db->count_user();
    $data['total_thread']  = $db->count_thread();
    $data['total_comment'] = $db->count_comment();
    $data['my_fav_count'] = $db->count_fav_by_user_id_and_is_delete(user_id(), 0);
    $data['top10'] = _all_thread("order by hot desc limit 10");
    render($data);
}
function thread_list()
{
    $list = \all_thread();
    include 'view/thread_list.html';
}

function post_new()
{
    global $db;
    if (!empty($_GET['node'])) {
        $node_id = $_GET['node'];
        $node = $db->get_node_by_id($node_id);
    } else {
        $node_id = 0;
        $nodes = $db->all_node(100);
    }
    render(compact('nodes', 'node_id', 'node'));
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
    $id = $db->update('user', ['karma=karma+2'], ['id' => user_id()]);
    return \echo_json(['url' => "/thread/$id"]);
}

function thread($id)
{
    global $db;
    $thread = get_thread($id);
    $comments = all_comment($id);
    visit_thread($id);
    $sql = "SELECT * from append where tid=? order by id asc";
    $appends = $db->queryAll($sql, [$id]);
    $votes = $db->all_vote_by_user_id_and_tid(user_id(), $id);
    $my_votes = [];
    foreach ($votes as $vote) {
        $my_votes[$vote['attitude']] = $vote;
    }
    $fav = $db->get_fav_by_user_id_and_tid(user_id(), $id);
    $is_my_fav = $fav ? (1 - $fav['is_delete']) : 0;
    $fav_text_map = ['加入收藏', '取消收藏'];
    $fav_count = $db->count_fav_by_tid($id);
    $data = compact(
        'thread', 'comments',
        'my_votes', 'is_my_fav', 'fav_text_map', 'fav_count',
        'appends');
    render($data);
}
function post_comment($tid)
{
    global $db;
    if (empty($_POST['content'])) {
        return \echo_json(1, 'empty content');
    }
    $content = $_POST['content'];
    $data = compact('tid', 'content');
    $data['user_id'] = user_id();
    list($device, $_) = get_device();
    $data['device'] = $device;
    $id = $db->insert('comment', $data);
    $sql = 'UPDATE thread set 
            comment_count=comment_count+1,
            hot=hot+10,
            action_time=?
            where id=?';
    $db->execute($sql, [$db::timestamp(), $tid]);
    $db->update('user', ['karma=karma+2'], ['id' => user_id()]);
    return \echo_json([]);
}
function comment_list($tid)
{
    $comments = all_comment($tid);
    include 'view/comment_list.html';
}

function user($id)
{
    global $db;
    $user = $db->get_user_by_id($id);
    $list = _all_thread("where t.user_id=?", [$id]);
    $sql = "SELECT c.*, u.name username, u.email, t.title, t.user_id tuid
            from comment c
            inner join thread t on t.id=c.tid 
            inner join user u on t.user_id=u.id 
            where c.user_id=?";
    $comments = $db->queryAll($sql, [$id]);
    render(compact('user', 'list', 'comments'));
}
function setting()
{
    render();
}

function vote_thread($tid)
{
    global $db;
    global $user;
    if (empty($_POST['value'])) {
        return echo_json(1, 'no value');
    }
    $value = $_POST['value'];
    $user_id = user_id();
    $vote = $db->get_vote_by_user_id_and_tid($user_id, $tid);
    if ($vote && $vote['attitude'] == $value) {
        return echo_json(1, 'u have vote');
    }
    $data = ['attitude' => $value,];
    $where = compact('tid', 'user_id');
    if ($vote) {
        $db->update('vote', $data, $where);
    } else {
        // 因臧否他人而增加业力
        $db->update('user', ["karma=karma+1"], ['id' => $user_id]);
        $db->insert('vote', array_merge($data, $where));
    }
    $map = [
        1 => 'vote_for',
        -1 => 'vote_against',
    ];
    $field = $map[$value];
    $num = $db->count_vote_by_user_id_and_attitude($user_id, $value);
    $db->update('thread', [$field => $num], ['id' => $tid]);
    if ($value == 1) {
        update_user_by_tid($tid, 'vote_count=vote_count+1');
    }
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
function fav_thread($tid)
{
    global $db;
    $user_id = user_id();
    $fav = $db->get_fav_by_user_id_and_tid($user_id, $tid);
    $action = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_BOOLEAN);
    if ($action) {
        if ($fav) {
            $db->update('fav', ['is_delete' => 0], compact('tid'));
        } else {
            $data = compact('user_id', 'tid');
            $db->insert('fav', $data);
        }
    } else {
        if ($fav) {
            $db->update('fav', ['is_delete' => 1], compact('tid'));
        } else {
            return echo_json(1, 'u do not have fav it');
        }
    }
    $map = ['-', '+'];
    $iaction = intval($action);
    $op = $map[$iaction];
    $data = [
        'action_time' => $db::timestamp(),
        "hot = hot $op 100",
    ];
    $db->update('thread', $data, ['id' => $tid]);
    update_user_by_tid($tid, "fav_count = fav_count $op 1");
    echo_json(0);
}
function fav()
{
    global $db;
    $user_id = user_id();
    $list = _all_thread("INNER JOIN fav f ON f.tid=t.id
            WHERE f.user_id=? AND f.is_delete=0
            ORDER BY f.id DESC LIMIT 111", [$user_id]);
    render(compact('list'));
}

function append($tid)
{
    global $db;
    $thread = $db->get_thread_by_id($tid);
    render(compact('tid', 'thread'));
}
function append_thread($tid)
{
    global $db;
    if (empty($_POST['append'])) {
        return echo_json(1, 'u do not have to append empty');
    }
    $content = $_POST['append'];
    $thread = $db->get_thread_by_id($tid);
    $user_id = user_id();
    if ($thread['user_id'] != $user_id) {
        error_log("user $user_id modify thread $tid, not his");
        return echo_json(1, 'u cannot modify others');
    }
    $id = $db->insert('append', compact('content', 'tid'));
    echo_json(['url' => "/thread/$tid"]);
}
function page404()
{
    header('HTTP/1.1 404 Not Found');
    echo 'no page';
}
