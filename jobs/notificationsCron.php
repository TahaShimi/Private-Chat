<?php
ini_set("display_errors", 1);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
require "/usr/local/apache/htdocs/chat/init.php";

$stmt = $conn->prepare('SELECT dt.* FROM (SELECT m.content,m.sender,m.receiver,m.sender_role,m.id_message,c.id_account,m.date_send, a.emailc , CONCAT(c.firstname," ",c.lastname) as name, Case when m.receiver_role =3 then (SELECT co.pseudo FROM consultants co,users u WHERE u.id_user=m.receiver AND co.id_consultant=u.id_profile) else "Default" end as pseudo FROM messages m LEFT JOIN users u on m.sender = u.id_user and m.sender_role = 4 LEFT JOIN customers c ON (m.sender=c.id_customer and m.sender_role = 7) or (u.id_profile=c.id_customer) JOIN accounts a on a.id_account = c.id_account LEFT JOIN messages msg on m.sender=msg.receiver AND m.receiver=msg.sender AND msg.date_send>m.date_send WHERE TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP(),m.date_send)) > a.unread_duration AND msg.id_message IS NULL ORDER BY m.date_send DESC) dt LEFT JOIN logs l on l.log_type=4 AND l.meta=dt.id_message AND l.id_user = dt.id_account WHERE l.id_log IS NULL GROUP BY dt.sender,dt.receiver ;');
$stmt->execute();
$unreadedConversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$from = MAILER_FROM;
$fromName = APP_NAME;
$subject = "Unreaded Conversations";

$mail = new PHPMailer(true);
$htmlContent = ' 
        <style>
            img{
                width : 60%
            }
        @media only screen and (min-width: 600px) {
            .container{
                width: 70%;margin:auto
            }
        }
        @media only screen and (min-width: 768px) {
            .container{
                width: 50%;margin:auto
            }
        }
        .messages-section {
            flex-shrink: 0;
            padding-bottom: 32px;
            background-color: var(--projects-section);
            flex: 1;
            width: 100%;
            border-radius: 30px;
            position: relative;
            overflow: auto;
            transition: all 300ms cubic-bezier(0.19, 1, 0.56, 1);
        }
        .messages-section .messages-close {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 3;
            border: none;
            background-color: transparent;
            color: var(--main-color);
            display: none;
        }
        .messages-section.show {
            transform: translateX(0);
            opacity: 1;
            margin-left: 0;
        }
        .messages-section .projects-section-header {
            position: sticky;
            top: 0;
            z-index: 1;
            padding: 32px 24px 0 24px;
            background-color: var(--projects-section);
        }
        
        .message-box {
            border-top: 1px solid var(--message-box-border);
            padding: 16px;
            display: flex;
            align-items: flex-start;
        }
        .message-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .message-header .name {
            font-size: 16px;
            line-height: 24px;
            font-weight: 700;
            color: var(--main-color);
            margin: 0;
        }
        
        .message-content {
            padding-left: 16px;
            width: 100%;
        }
        .message-line {
            font-size: 14px;
            line-height: 20px;
            margin: 8px 0;
            color: var(--secondary-color);
            opacity: 0.7;
        }
        .message-line.time {
            text-align: right;
            margin-bottom: 0;
        }
        </style>
    <div class="container"> 
        <div style="text-align:center">
            <img src="http://45.76.121.27/chat/assets/images/logo4.png" class="logo-box">
        </div>
        <div style="display: flex;align-items: center;justify-content: space-between;flex-wrap: wrap;width: 100%;">
            <p style="min-width:60%">
                <b>Bonjour</b>
                <br> Voici les derniéres conversations non lu !!
            </p>
            <a href="'.BASE_URL.'/accounts/admin/unread_messages.php" class="btn btn-primary" style="border-radius: 20px;background-image: linear-gradient(#ff9189 0%, #ffb199 100%);color: #FFFFFF;font-size: 12px;font-weight: bold;text-decoration: none;padding: 12px 45px;letter-spacing: 1px;transition: transform 80ms ease-in;font-family: monospace;">
                Consulter Conversations
            </a>
        </div>
        <div class="messages-section">';
$blocks = [];
if (count($unreadedConversations)) {
    return 0;
}
foreach ($unreadedConversations as $conversation) {
    if(!isset($blocks[$conversation['emailc']]))$blocks[$conversation['emailc']] = '';
    $blocks[$conversation['emailc']] .= '<div class="messages">
        <div class="message-box" style="border-bottom: 1px dashed darkcyan;">
            <div class="message-content">
            <div class="message-header">
                <div class="name">' . $conversation['name'] . '</div>
                <div><b>Agent :</b> ' . $conversation['pseudo'] . '</div>
            </div>
            <p class="message-line">
            ' . $conversation['content'] . '
            </p>
            <p class="message-line time">
            ' . $conversation['date_send'] . '
            </p>
            </div>
        </div>
    </div>';
    $stmt = $conn->prepare('INSERT INTO logs (id_user,description,meta,date,log_type) values  (:idu,"Unreaded messages",:mt,NOW(),4)');
    $stmt->bindParam(':idu',$conversation['id_account']);
    $stmt->bindParam(':mt',$conversation['id_message']);
    $stmt->execute();
}
try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
/*     $mail->Host       = MAILER_HOST;
    $mail->SMTPAuth   = true;          
    $mail->Username   = MAILER_USERNAME;
    $mail->Password   = MAILER_PASSWORD;
    $mail->SMTPSecure = MAILER_ENCRYPTION;  
    $mail->Port       = MAILER_PORT;    
 */    $mail->Host = MAILER_HOST;
    $mail->SMTPAuth = true;
    $mail->Port = MAILER_PORT;
    $mail->Username = MAILER_USERNAME;
    $mail->Password = MAILER_PASSWORD;
    $mail->setFrom(MAILER_FROM, APP_NAME);
    $mail->IsHTML(true);
    $mail->Subject = "Send email using Gmail SMTP and PHPMailer";
    foreach($blocks as $key => $block){
        if($block != ''){
            $mail->addAddress($key);
            $mail->Body = $htmlContent.$block.'</div></div>';
            $mail->send();
            $mail->clearAllRecipients();
        }
    }
    echo "Email message sent.";
} catch (Exception $e) {
    echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
}





