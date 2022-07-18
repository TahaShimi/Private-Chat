<?php

namespace Classes;

use Classes\DataService;

use Exception;
use \PDO;

class Conversation
{

    public $id, $message, $sender, $senderRole, $receiver, $receiverRole, $sendAt;
    public $conn;
    public $ds;
    function __construct()
    {
    }

    function initDS()
    {
        $this->ds = new DataService();
        $this->conn =  $this->ds->conn;
        $this->conn->exec("set names utf8mb4");
    }
    function updateConversationStatus(array $dta)
    {
        try {
            $this->initDS();
            $stmt2 =  $this->conn->prepare("UPDATE messages set status=1, seen_at=NOW() where receiver=:re and sender=:se and receiver_role=:rer and sender_role=:ser and status=0");
            $stmt2->bindparam(":re", $dta["sender"], PDO::PARAM_INT);
            $stmt2->bindparam(":rer", $dta["sender_role"], PDO::PARAM_INT);
            $stmt2->bindparam(":se", $dta["receiver"], PDO::PARAM_INT);
            $stmt2->bindparam(":ser", $dta["receiver_role"], PDO::PARAM_INT);
            $stmt2->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    function checkFirst($sender)
    {
        try {
            $this->initDS();
            $stmt = $this->conn->prepare("SELECT COUNT(*) as checking FROM messages WHERE receiver=:se AND sender=0");
            $stmt->bindparam(":se", $sender);
            $stmt->execute();
            $check = $stmt->fetchObject();
            return $check;
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    function getdefaultMessage($id_account)
    {
        try {
            $this->initDS();
            $stmt = $this->conn->prepare("SELECT agent_default_message FROM accounts_messages WHERE id_account =:id");
            $stmt->bindparam(":id", $id_account);
            $stmt->execute();
            return $stmt->fetchObject();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    function getConversations(array $dta)
    {
        $cond =  $dta["sender_role"] == 4 ? ' AND origin != 2' : '';
        try {
            $this->initDS();
            if (!isset($dta["inversed"])) {
                $this->updateConversationStatus($dta);
            }
            if ($dta["last_id"] != 0 && $dta['inversed'] != "1") {
                $stmt2 =  $this->conn->prepare("SELECT * from messages where ((sender=:se or sender=0) and sender_role=:ser and receiver=:re and receiver_role=:rer and id_message< :lst) or (sender=:re and sender_role=:rer and (receiver=:se or receiver=0) and receiver_role=:ser and id_message< :lst)$cond order by date_send DESC LIMIT 10");
                $stmt2->bindparam(":lst", $dta["last_id"], PDO::PARAM_INT);
            } else if ($dta["last_id"] != 0 && $dta['inversed'] == "1") {
                $stmt2 =  $this->conn->prepare("SELECT * from (SELECT * FROM messages me where ((sender=:se or sender=0) and sender_role=:ser and receiver=:re and receiver_role=:rer and id_message > :lst) or (sender=:re and sender_role=:rer and (receiver=:se or receiver=0) and receiver_role=:ser and id_message> :lst) order by  id_message ASC LIMIT 10) msg ");
                $stmt2->bindparam(":lst", $dta["last_id"], PDO::PARAM_INT);
            } else if ($dta["last_id"] == 0 && $dta['inversed'] == "1") {
                $stmt2 =  $this->conn->prepare("SELECT *  from (SELECT * FROM messages me where (sender=:se or sender=0) and sender_role=:ser and receiver=:re and receiver_role=:rer ) or (sender=:re and sender_role=:rer and (receiver=:se or receiver=0) and receiver_role=:ser ) order by  id_message ASC LIMIT 10) msg ORDER BY date_send DESC");
            } else {
                $stmt2 =  $this->conn->prepare("SELECT * from messages where ((sender=:se or sender=0) and sender_role=:ser and receiver=:re and receiver_role=:rer) or (sender=:re and sender_role=:rer and (receiver=:se or receiver=0) and receiver_role=:ser)$cond order by date_send DESC LIMIT 10");
            }
            $stmt2->bindparam(":se", $dta["sender"], PDO::PARAM_INT);
            $stmt2->bindparam(":ser", $dta["sender_role"], PDO::PARAM_STR);
            $stmt2->bindparam(":re", $dta["receiver"], PDO::PARAM_INT);
            $stmt2->bindparam(":rer", $dta["receiver_role"], PDO::PARAM_STR);
            if ($stmt2->execute()) {
                return $stmt2->fetchAll();
            } else {
                return null;
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    function date_sort($a, $b)
    {
        return $a['date_send'] > $b['date_send'];
    }
    function getConversations2(array $dta)
    {
        $cond =  $dta["sender_role"] == 4 ? ' AND origin != 2' : '';
        try {
            $this->initDS();
            if (!isset($dta["inversed"])) {
                $this->updateConversationStatus($dta);
            }
            if ($dta["last_id"] != 0 && $dta['inversed'] != "1") {
                $stmt2 =  $this->conn->prepare("SELECT *,(CASE  when (SELECT date_add from transactionsc where id_customer=:IDC order by id_transaction ASC limit 1) < msg.date_send then 'after' else 'before' end) as trans_change from messages msg where ((sender=:se or sender=0) and sender_role=:ser and (receiver=:re or receiver=0) and receiver_role=:rer and id_message< :lst) or ((sender=:re or sender=0) and sender_role=:rer and (receiver=:se or receiver=0) and receiver_role=:ser and origin != 2 and id_message< :lst)$cond order by date_send DESC LIMIT 10");
                $stmt2->bindparam(":lst", $dta["last_id"], PDO::PARAM_INT);
            } else if ($dta["last_id"] != 0 && $dta['inversed'] == "1") {
                $stmt2 =  $this->conn->prepare("SELECT *,(CASE  when (SELECT date_add from transactionsc where id_customer=:IDC order by id_transaction ASC limit 1) < msg.date_send then 'after' else 'before' end) as trans_change  from (SELECT * FROM messages me where ((sender=:se or sender=0) and sender_role=:ser and (receiver=:re or receiver=0) and receiver_role=:rer and id_message > :lst) or ((sender=:re or sender=0) and sender_role=:rer and (receiver=:se or receiver=0) and origin != 2 and receiver_role=:ser and id_message> :lst) order by  id_message ASC LIMIT 10) msg ");
                $stmt2->bindparam(":lst", $dta["last_id"], PDO::PARAM_INT);
            } else if ($dta["last_id"] == 0 && $dta['inversed'] == "1") {
                $stmt2 =  $this->conn->prepare("SELECT *,(CASE  when (SELECT date_add from transactionsc where id_customer=:IDC order by id_transaction ASC limit 1) < msg.date_send then 'after' else 'before' end) as trans_change   from (SELECT * FROM messages me where ((sender=:se or sender=0) and sender_role=:ser and (receiver=:re or receiver=0) and receiver_role=:rer ) or ((sender=:re or sender=0) and sender_role=:rer and (receiver=:se or receiver=0) and receiver_role=:ser and origin != 2) order by  id_message ASC LIMIT 10) msg ORDER BY date_send DESC");
            } else {
                $stmt2 =  $this->conn->prepare("SELECT *,(CASE  when (SELECT date_add from transactionsc where id_customer=:IDC order by id_transaction ASC limit 1) < msg.date_send then 'after' else 'before' end) as trans_change from messages msg where ((sender=:se or sender=0) and sender_role=:ser and (receiver=:re or receiver=0) and receiver_role=:rer) or ((sender=:re or sender=0) and sender_role=:rer and origin != 2 and (receiver=:se or receiver=0) and receiver_role=:ser)$cond order by id_message DESC LIMIT 10");
            }
            $stmt2->bindparam(":se", $dta["sender"], PDO::PARAM_INT);
            $stmt2->bindparam(":ser", $dta["sender_role"], PDO::PARAM_STR);
            $stmt2->bindparam(":re", $dta["receiver"], PDO::PARAM_INT);
            $stmt2->bindparam(":rer", $dta["receiver_role"], PDO::PARAM_STR);
            $stmt2->bindparam(":IDC", $dta["customer_id"], PDO::PARAM_INT);
            if ($stmt2->execute() && $stmt2->rowCount() > 0 && $dta["customer_id"] != 0) {


                $messages = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                $first = $messages[0]['date_send'];
                $last = $messages[count($messages) - 1]['date_send'];
                $stmt2 =  $this->conn->prepare("SELECT t.id_transaction, t.status as trans_status, t.id_customer, t.date_add as date_send, p.title, p.messages FROM transactionsc t JOIN packages p on t.id_package = p.id_package where t.date_add BETWEEN :ds2 AND :ds1 AND t.id_customer =:id");
                $stmt2->bindparam(":id", $dta["customer_id"], PDO::PARAM_INT);
                if ($dta['last_id'] != 0) {
                    $stmtMSG =  $this->conn->prepare("SELECT id_message, sender, receiver, date_send FROM messages where id_message = :id");
                    $stmtMSG->bindparam(":id", intval($dta["last_id"]));
                    $stmtMSG->execute();
                    $msg = $stmtMSG->fetchObject();
                    $stmt2->bindparam(":ds1", $msg->date_send, PDO::PARAM_STR);
                    $stmt2->bindparam(":ds2", $last, PDO::PARAM_STR);
                    $stmt2->execute();
                    array_push($messages, ...$stmt2->fetchAll(PDO::FETCH_ASSOC));
                    usort($messages, function ($a, $b) {
                        if ($a['receiver'] == 0) {
                            return $a['date_send'] > $b['date_send'] && $a['id_message'] > $b['id_message'];
                        }else{
                            return $a['date_send'] > $b['date_send'] && $a['id_message'] > $b['id_message'];
                        }
                    });
                } else {
                    $stmt2->bindparam(":ds1", $first, PDO::PARAM_STR);
                    $stmt2->bindparam(":ds2", $last, PDO::PARAM_STR);
                    $stmt2->execute();
                    array_push($messages, ...$stmt2->fetchAll(PDO::FETCH_ASSOC));
                    usort($messages, function ($a, $b) {
                        if ($a['receiver'] == 0) {
                            return $a['date_send'] > $b['date_send'] && $a['id_message'] > $b['id_message'];
                        }else{
                            return $a['date_send'] > $b['date_send'] && $a['id_message'] > $b['id_message'];
                        }
                    });
                }

                return ["messages" => $messages];
            } else {
                return null;
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }


    function getConversationReview($customer, $consultant)
    {
        try {
            $this->initDS();
            $stmt2 = $this->conn->prepare("SELECT * from messages where (sender=:cu and receiver=:co) or (sender=:co and receiver=:cu)");
            $stmt2->bindparam(":cu", $customer, PDO::PARAM_INT);
            $stmt2->bindparam(":co", $consultant, PDO::PARAM_INT);
            if ($stmt2->execute()) {
                return $stmt2->fetchAll();
            } else {
                return null;
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }



    function countUnreadMessages($id)
    {
        try {
            $this->initDS();
            $stmt2 =  $this->conn->prepare("SELECT sender from messages where receiver=:id and status=0 GROUP BY sender");
            $stmt2->bindparam(":id", $id, PDO::PARAM_INT);
            $stmt2->execute();
            $dta = $stmt2->fetchAll();
            return count($dta);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }



    function getConsultantConversationsPartners($id)
    {
        try {
            $this->initDS();
            $stmt2 =  $this->conn->prepare("SELECT DISTINCT CASE WHEN receiver_role=3 THEN sender ELSE receiver END as id_customer  from messages where sender=:id or receiver=:id");
            $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt2->execute();

            return $stmt2->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    function storeMessage(array $dta)
    {
        try {
            $this->initDS();
            if (isset($dta["sender"]) && $dta["sender"] == 0) {
                $date_send = date('Y-m-d H:i:s', strtotime("+ 2 seconds"));
                $stmt2 =  $this->conn->prepare("INSERT INTO messages (sender, sender_role, receiver, receiver_role, content, date_send, status,origin) VALUES (:se, :sr, :re, :rr, :ct, :ds, 0,:or)");
                $stmt2->bindParam(':ds', $date_send, PDO::PARAM_STR);
            } else {
                $stmt2 =  $this->conn->prepare("INSERT INTO messages (sender, sender_role, receiver, receiver_role, content, date_send, status,origin) VALUES (:se, :sr, :re, :rr, :ct, NOW(), 0,:or)");
            }
            if (isset($dta["admin"]) && $dta["admin"] == 2) {
                $val = 1;
            } else if (isset($dta["admin"]) && $dta["admin"] == 4) {
                $val = 2;
            } else {
                $val = 0;
            }
            $stmt2->bindParam(':or', $val, PDO::PARAM_INT);
            $stmt2->bindParam(':se', $dta["sender"], PDO::PARAM_INT);
            $stmt2->bindParam(':sr', $dta["sender_role"], PDO::PARAM_STR);
            $stmt2->bindParam(':re', $dta["receiver"], PDO::PARAM_INT);
            $stmt2->bindParam(':rr', $dta["receiver_role"], PDO::PARAM_STR);
            $stmt2->bindParam(':ct', $dta["content"], PDO::PARAM_STR);
            return $stmt2->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    function storeNotification(array $dta)
    {
        try {
            $this->initDS();
            $customer = $dta["sender"] ? $dta["sender"] : NULL;
            $consultant = $dta["receiver"] ? $dta["receiver"] : NULL;
            $stmt2 =  $this->conn->prepare("INSERT INTO notifications(id_customer,id_consultant,action,id_account,date,seen) values(:CU,:CO,:AC,:IDA,NOW(),0) ");
            $stmt2->bindParam(':CU', $customer, PDO::PARAM_INT);
            $stmt2->bindParam(':CO', $consultant, PDO::PARAM_INT);
            $stmt2->bindParam(':AC', $dta["type"], PDO::PARAM_INT);
            $stmt2->bindParam(':IDA', $dta["id_account"], PDO::PARAM_INT);
            return $stmt2->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}
