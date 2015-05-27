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
    $mail->Password = Service('config')['mail']['password'];                           // SMTP password
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
