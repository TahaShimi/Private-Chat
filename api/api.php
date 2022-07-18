<?php
require "../vendor/autoload.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use Classes\DataService;

try {
    $ds = new DataService();
    $conn = $ds->conn;
    $conn->exec("set names utf8mb4");
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$action = $_POST['action'];
$results = [];

switch ($action) {
    case 'updateMessage':
        $receiver = intval($_POST['receiver']);
        $sender = intval($_POST['sender']);
        
        $stmt = $conn->prepare("UPDATE messages SET status = 1, seen_at = NOW(), sender = (CASE WHEN sender=0 THEN :se ELSE sender END), receiver = (CASE WHEN receiver=0 THEN :se ELSE receiver END) WHERE (sender = :re and receiver = 0) or (sender =0 and receiver = :re)");
        $stmt->bindparam(":se",  $sender);
        $stmt->bindparam(":re",  $receiver);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {
            $results = ["success" => true];
            break;
        }
        $results = ["success" => false];
        break;
    case 'delete_messages':
        $receiver = $_POST['receiver'];
        $stmt = $conn->prepare("DELETE FROM `messages` WHERE (receiver=:re and sender=0) or (receiver=0 and sender=:re)");
        $stmt->bindParam(':re', $receiver);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {
            $results = ["success" => true];
            break;
        }
        $results = ["success" => false];
        break;
    case 'create_guest':
        $first_name = "guest";
        $last_name = "guest";
        $id_account = $_POST['id_account'];
        $id_website = $_POST['id_website'];
        $stmt = $conn->prepare("INSERT INTO customers (firstname, lastname, date_start, status, id_website, id_account,  balance) VALUES (:fn,:ln, NOW(), 1, :id_website, :id_account, 0)");
        $stmt->bindParam(':fn', $first_name);
        $stmt->bindParam(':ln', $last_name);
        $stmt->bindParam(':id_website', $id_website);
        $stmt->bindParam(':id_account', $id_account);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {
            $results = ["success" => true, "id" => $conn->lastInsertId()];
            break;
        }
        $results = ["success" => false];
        break;
    case 'getConsultants':
        $id_company = intval($_POST["id_company"]);
        $id_website = intval($_POST["id_website"]);
        $id_user = intval($_POST["id_user"]);
        $stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM messages where u.id_user = sender and status=0) as unread_messages_count from consultants c join users u on u.id_profile=c.id_consultant where c.id_account=:ia and (find_in_set(:idw,c.websites) or c.websites is null) and u.profile=3");
        $stmt->bindparam(":ia",  $id_company);
        $stmt->bindparam(":idw",  $id_website);
        $stmt->execute();
        $consultants = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($consultants as &$consultant) {
            $stmt2 =  $conn->prepare("SELECT Count(*) as total_unread_messages from messages where receiver=:id and sender=:ir and status=0");
            $stmt2->bindparam(":id", $id_user, PDO::PARAM_INT);
            $stmt2->bindparam(":ir", $consultant->id_user, PDO::PARAM_INT);
            $stmt2->execute();
            $consultant->unreded_messages = $stmt2->fetchObject()->total_unread_messages;
        }
        $results = [["pseudo" => "samuel", "lastMessage" => "samuel", "id_user" => "3"]];
        break;
    case 'login':
        $login = htmlentities($_POST["login"]);
        $pwd = htmlentities($_POST["password"]);
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE `login` = :LOGIN");
        $stmt->bindParam(':LOGIN', $login, PDO::PARAM_STR);
        $stmt->execute();
        $total = $stmt->rowCount();
        $result = $stmt->fetchObject();
        if ($total == 0) {
            $results['code'] = 200;
        } elseif (!password_verify($pwd, $result->password)) {
            $lastday = $result->lastday;
            $nbr_essai = (int) $result->nbr_essai;
            if ($lastday == date("Y-m-d")) {
                $results['code'] = 301;
            } else {
                $results['code'] = 302;
                $nbr_essai++;
                if ($nbr_essai == 3) {
                    $stmt2 = $conn->prepare("UPDATE `users` SET `nbr_essai` = 0, `lastday` = CURDATE() WHERE `id_user` = :ID");
                    $stmt2->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
                    $stmt2->execute();
                } else {
                    $stmt2 = $conn->prepare("UPDATE `users` SET `nbr_essai` = :nbr WHERE `id_user` = :ID");
                    $stmt2->bindParam(':nbr', $nbr_essai, PDO::PARAM_INT);
                    $stmt2->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
                    $stmt2->execute();
                }
            }
        } elseif ($login == $result->login && (password_verify($pwd, $result->password))) {
            $results['code'] = 201;
            $results['login'] = $result->profile;
            $results['id_user'] = $result->id_user;
            $results['id_account'] = $result->id_profile;
            $results['lang'] = $result->lang;
            $stmt4 = $conn->prepare("SELECT  `photo`, `firstname`, `lastname`, `balance`, `id_account`,`id_website` ,(CASE c.country WHEN NULL THEN (SELECT currency FROM accounts WHERE id_account=c.id_account) ELSE c.country END) currency FROM `customers` c WHERE c.id_customer = :ID");
            $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
            $stmt4->execute();
            $stat = $stmt4->fetchObject();
            $stmt4 = $conn->prepare("SELECT  * FROM packages p,transactionsc t WHERE t.id_customer = :ID AND t.id_package=p.id_package AND p.messages IS NULL AND date(CURRENT_TIMESTAMP) BETWEEN p.start_date AND p.end_date");
            $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
            $stmt4->execute();
            $obj = $stmt4->fetchObject();
            $results['unlimited'] = $obj ? 1 : 0;
            $results['firstname'] = $stat->firstname;
            $results['lastname'] = $stat->lastname;
            $results['email'] = $stat->emailc;
            $results['balance'] = $stat->balance;
            $results['id_website'] = $stat->id_website;
            $results['id_company'] = $stat->id_account;
            $results['avatar'] = $stat->photo;
            $results['currency'] = strlen($stat->currency) == 3 ? $stat->currency : $currencies[$stat->currency];
        }
        break;
    default:
        break;
}
echo json_encode($results);
