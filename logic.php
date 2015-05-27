<?php

function all_thread()
{
    global $db;
    $sql = "SELECT * from thread order by action_time desc limit 111";
    return $list = $db->queryAll($sql);
}
function get_thread($id)
{
    global $db;
    $sql = "SELECT t.*,u.name username, u.email from thread t join user u on u.id=t.user_id where t.id=? limit 1";
    return $thread = $db->queryRow($sql, [$id]);
}
function all_comment($t_id)
{
    global $db;
    $sql = "SELECT * from comment where t_id=?";
    return $comments = $db->queryAll($sql, [$t_id]);
}
