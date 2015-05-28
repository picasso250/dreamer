<?php

function _all_thread($extra, $values=[])
{
    global $db;
    $sql = "SELECT t.*, u.name username, u.email, n.name node_name
            from thread t 
            inner join user u on u.id=t.user_id 
            left  join node n on n.id=t.node_id
            $extra";
    return $list = $db->queryAll($sql, $values);
}
function all_thread($where)
{
    global $db;
    $where_str = '';
    if ($where) {
        $where_str .= " WHERE ".$db::buildWhere($where);
    }
    return _all_thread("$where_str ORDER BY action_time DESC LIMIT 111", $where);
}
function get_thread($id)
{
    global $db;
    $sql = "SELECT t.*, u.name username, u.email, n.name node_name
            from thread t 
            inner join user u on u.id=t.user_id
            left  join node n on n.id=t.node_id
            WHERE t.id=? limit 1";
    return $thread = $db->queryRow($sql, [$id]);
}
function all_comment($t_id)
{
    global $db;
    $sql = "SELECT c.*,u.name username, u.email 
            from comment c inner join user u on u.id=c.user_id 
            where c.t_id=?";
    return $comments = $db->queryAll($sql, [$t_id]);
}
function send_mail($to, $subject, $body, $AltBody = null) {
    global $config;
    if ($config['mail']['debug']) {
        error_log("send $to, sub: $subject, body: $body");
        return [true, 'ok'];
    }
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->CharSet = "UTF-8";
    $mail->Host = 'smtp.qq.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = '281055003@qq.com';                 // SMTP username
    $mail->Password = $config['mail']['password'];                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;                                    // TCP port to connect to

    $mail->From = '281055003@qq.com';
    $mail->FromName = 'XiaoChi';
    $mail->addAddress($to);     // Add a recipient
    $mail->addReplyTo('281055003@qq.com', 'XiaoChi');

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $body;
    if ($AltBody === null) {
        $AltBody = strip_tags($body);
    }
    $mail->AltBody = $AltBody;

    $ok = $mail->send();
    return [$ok, $mail->ErrorInfo];
}
function user_verify($user)
{
    return sha1(implode('.', $user));
}
function check_user_reset()
{
    global $db;
    if (empty($_REQUEST['id'])) {
        error_log("no id");
        return [false, '参数不正确'];
    }
    $id = $_REQUEST['id'];
    if (empty($_REQUEST['verify'])) {
        error_log("no verify");
        return [false, '参数不正确'];
    }
    $verify = $_REQUEST['verify'];
    $user = $db->get_user_by_id($id);
    if (empty($user)) {
        error_log("no user $id");
        return [false, '参数不正确'];
    }
    if ($verify !== user_verify($user)) {
        error_log("verify not match");
        return [false, '参数不正确'];
    }
    return [true, $user];
}
function user_show_name($user)
{
    return htmlspecialchars($user['username'] ?: $user['email']);
}
function ensure_node_name($node_name)
{
    global $db;
    $node = $db->get_node_by_name($node_name);
    if ($node) {
        $node_id = $node['id'];
    } else {
        $data = [
            'name' => $node_name,
            'user_id' => user_id(),
        ];
        $node_id = $db->insert('node', $data);
    }
    return $node_id;
}
function root_node($node_id)
{
    global $db;
    while (true) {
        $node = $db->get_node_by_id($node_id);
        $node_id = $node['pid'];
        if (empty($node_id)) {
            return $node;
        }
    }
}
function node_id_input()
{
    if (empty($_POST['node_id'])) {
        $node_id = 0;
        if (!empty($_POST['node_name'])) {
            $node_name = trim($_POST['node_name']);
            $node_id = ensure_node_name($node_name);
        }
    } else {
        $node_id = $_POST['node_id'];
    }
    return $node_id;
}
function visit_thread($id)
{
    $sql = 'UPDATE thread set 
            visit_count=visit_count+1,
            hot=hot+1
            where id=?';
    $db->execute($sql, [$id]);
}
