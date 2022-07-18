<?php
ini_set("display_errors", 1);
require "/usr/local/apache/htdocs/chat/init.php";
require "/usr/local/apache/htdocs/chat/vendor/websocket-client/websocket_client.php";
$server = '45.76.121.27:8081';
$currentDate = strtotime(date("Y-m-d H:i:s"));
$currentDate -= 60 * 15;
$lastDate = date("Y-m-d H:i:s", $currentDate);
$stmt = $conn->prepare('SELECT m.*,c.id_account,CONCAT(c.firstname," ",c.lastname) as name,(SELECT co.pseudo FROM consultants co,users u WHERE u.id_user=m.receiver AND co.id_consultant=u.id_profile) as pseudo FROM messages m,users u ,customers c,websites w WHERE w.id_website=c.id_website AND u.id_user= m.sender AND c.id_customer= u.id_profile AND m.sender_role=4 AND TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP(),m.date_send) )>w.max_time AND NOT EXISTS (SELECT * FROM messages msg WHERE m.sender=msg.receiver AND m.receiver=msg.sender AND msg.date_send>m.date_send)AND m.date_send > "' . $lastDate . '" GROUP BY m.sender,m.receiver');
$stmt->execute();
$notifications = $stmt->fetchAll();
$sp = websocket_open($server, 8081, '', $errstr, 10);
if ($sp) {
  foreach ($notifications as $notification) {
    $message = json_encode([
      'command' => "notification",
      'action' => 2,
      'sender' => $notification['sender'],
      'receiver' => $notification['receiver'],
      'consultant' => $notification['pseudo'],
      'customer' => $notification['name'],
      'id_group' => $notification['id_account']
    ]);
    echo $message;
    websocket_write($sp, $message);
  }
}
