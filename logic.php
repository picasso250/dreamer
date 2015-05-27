<?php

function all_thread()
{
    global $db;
    $sql = "SELECT * from thread order by action_time desc limit 111";
    return $list = $db->queryAll($sql);
}
