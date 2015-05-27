<?php
function user_id($user_id=null)
{
    if ($user_id === null) {
        return isset($_SESSION['s_user_id_']) ? $_SESSION['s_user_id_'] : 0;
    } else {
        $_SESSION['s_user_id_'] = $user_id;
    }
}
