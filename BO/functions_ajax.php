<?php
require "../vendor/autoload.php";
include('../init.php');

use Classes\DataService;
// BASE DE DONNEES
try {
    $ds = new DataService();
    $conn = $ds->conn;
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$type = $_GET["type"] ? $_GET["type"] : $_POST["type"];
$html = "";

if ($type == "remove_customer") {
    $id = intval($_GET['id']);

    $stmt2 = $conn->prepare("DELETE FROM `accounts` WHERE `id_account` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer has been removed successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer has not been removed </div>";
    }
} elseif ($type == "Solved") {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("UPDATE contact_ticket SET status = 2 WHERE id_ticket=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $affected_rows = $stmt->rowCount();
    if ($affected_rows != 0) {
        $html =1;
    }
} elseif ($type == "getContactConv") {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id_ticket=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $messages = $stmt->fetchAll();
    $html = $messages;
} elseif ($type == "getSubject") {
    $subject = intval($_POST['subject']);
    $reason = intval($_POST['reason']);
    $id = intval($_POST['id']);
    $result = "";
    $stmt = $conn->prepare("SELECT ct.*,a.business_name,c.firstname,c.lastname FROM contact_ticket ct,accounts a,customers c,users u WHERE u.id_user = ct.id_customer AND c.id_customer=u.id_profile AND ct.subject=:sb AND ct.reason=:re AND ct.id_account=:id AND a.id_account=ct.id_account AND u.profile=4 ORDER BY ct.date DESC");
    $stmt->bindParam(':sb', $subject);
    $stmt->bindParam(':re', $reason);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $tickets = $stmt->fetchAll();
    $resultat=array();
    foreach ($tickets as $ticket) {
        $id = uniqid();
        if($contact['status'] == 0) $status =  '<span class="label label-success">' . $trans['pending'] . '</span>';else if($contact['status'] == 1)$status =  '<span class="label label-danger">' . $trans['processing'] . '</span>';else $status =  '<span class="label label-danger">' . $trans['closed'] . '</span>';
        $result .= '<div class="row p-2 bg-secondary m-t-10 ticket" style="width: 100%;align-items: center;">
        <div class="col-4">
            <h4 class="text-muted">'.$ticket['firstname'].' '.$ticket['lastname'] .'</h4>
            <small class="text-muted">'.$ticket['date'].'</small>
        </div>
        <div class="col-3">
        '.$trans['reasons'][$ticket['subject']][$ticket['reason']].'
        </div>
        <div class="col-3">
        '. $status .'</span>'.'
        </div>
        <div class="col-2"><button class="btn btn-sm btn-primary float-right" type="button" data-toggle="collapse" data-target="#Collapse' . $id . '" aria-expanded="false" aria-controls="Collapse' . $id . '">Conversation</button></div>
    </div>
    <div class="chat-main-box collapse m-3" data-parent="#tick" style="width: 100%;max-height: 200px;overflow-y:scroll" id="Collapse' . $id . '">
        <div class="chat-right-aside" style="width: 100%;">
            <ul class="chat-list p-3" id="conv" style="max-height: 500px;">';
        $stmt = $conn->prepare("SELECT cm.*,u.profile FROM contact_messages cm,users u WHERE cm.id_ticket=:id AND u.id_user=cm.id_sender ");
        $stmt->bindParam(':id', $ticket['id_ticket']);
        $stmt->execute();
        $messages = $stmt->fetchAll();
        foreach ($messages as $message) {
            if ($message['profile'] == 2) {
                $result .= '<li class="reverse"><div class="chat-content"><h5>'.$ticket['business_name'].'</h5><div class="box bg-light-info">' . $message['message'] . '</div><div class="chat-time">' . $message['date'] . '</div></div></li>';
            } else if ($message['profile'] == 4) {
                $result .= '<li><div class="chat-content"><h5>'.$ticket['firstname'].' '.$ticket['lastname'] .'</h5><div class="box bg-light-info">' . $message['message'] . '</div><div class="chat-time">' . $message['date'] . '</div></div></li>';
            }
        }
        $result.='</ul></div></div>';
        $resultat[]=$result;
        $result = "";
    }
    $html = $resultat;
} elseif ($type == "sendReponse") {
    $sender = intval($_POST['sender']);
    $id = intval($_POST['id']);
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO contact_messages(message,id_ticket,date,id_sender) values (:msg,:IDT,NOW(),:IDS)");
    $stmt->bindParam(':msg', $message);
    $stmt->bindParam(':IDT', $id);
    $stmt->bindParam(':IDS', $sender);
    $stmt->execute();
    $affected_rows = $stmt->rowCount();
    if ($affected_rows != 0) {
        $stmt = $conn->prepare("UPDATE contact_ticket SET status = 1 WHERE id_ticket=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $html = '<li><div class="chat-content"><h5>Private chat</h5><div class="box bg-light-info">' . $message . '</div><div class="chat-time">' . date("Y-m-d H:i:s") . '</div></div></li>';
    }
} elseif ($type == "add_gopaid") {
    $account_name = htmlentities($_GET['account_name']);
    $shop_name = htmlentities($_GET['shop_name']);
    $email_addr = htmlentities($_GET['email_addr']);
    $return_url = htmlentities($_GET['return_url']);
    $payment_receipt = (isset($_POST['payment_receipt']) && $_POST['payment_receipt'] == 'on') ? 1 : 0;
    $payment_notification = (isset($_POST['payment_notification']) && $_POST['payment_notification'] == 'on') ? 1 : 0;
    $languages = (isset($_POST['languages'])) ? implode(",", $_POST['languages']) : NULL;
    $default_language = (isset($_POST['default_language'])) ? $_POST['default_language'] : NULL;
    $id_website = intval($_GET['id_website']);

    $data = array(
        'account_name' => $account_name,
        'shop_name' => $shop_name,
        'email_addr' => $email_addr,
        'return_url' => $return_url,
        'payment_receipt' => $payment_receipt,
        'payment_notification' => $payment_notification,
        'languages' => $languages,
        'default_language' => $default_language
    );

    $payload = json_encode($data);

    $ch = curl_init('https://gopaid.pro/API/company');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ));

    $result = curl_exec($ch);

    curl_close($ch);

    if (isset($result['id_shop'])) {
        $id_shop = intval($result['id_shop']);

        $stmt2 = $conn->prepare("UPDATE `websites` SET `id_shop`=:sh WHERE `id_website` = :ID");
        $stmt2->bindParam(':sh', $id_shop, PDO::PARAM_INT);
        $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
        $stmt2->execute();
        $affected_rows = $stmt2->rowCount();

        if ($affected_rows != 0) {
            $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The shop has been created successfully </div>";
        } else {
            $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The shop has not been created </div>";
        }
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The shop has not been created </div>";
    }
} elseif ($type == "approve") {
    $id = intval($_GET['id_account']);

    $stmt2 = $conn->prepare("UPDATE `accounts` SET `status`=2 WHERE `id_account` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer has been approved successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer has not been approved </div>";
    }
} elseif ($_POST['action'] == "approve_publisher") {
    $id = intval($_POST['id']);

    $stmt2 = $conn->prepare("UPDATE `publishers` SET `active`=1 WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = 1;
    } else {
        $html = 0;
    }
} elseif ($type == "approve2") {
    $id = intval($_GET['id_website']);

    $stmt2 = $conn->prepare("UPDATE `websites` SET `status`=2 WHERE `id_website` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website has been approved successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website has not been approved </div>";
    }
} elseif ($_POST['action'] == "getInfo") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("SELECT * FROM publishers WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $html = $stmt2->fetch();
} elseif ($_POST['action'] == "setChanges") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("UPDATE publishers SET first_name=:fn,last_name=:ln,country=:co,email=:em,phone=:ph WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->bindParam(':fn', $_POST['firstname']);
    $stmt2->bindParam(':ln', $_POST['lastname']);
    $stmt2->bindParam(':co', $_POST['country']);
    $stmt2->bindParam(':em', $_POST['email']);
    $stmt2->bindParam(':ph', $_POST['phone']);
    $stmt2->execute();

    $stmt2 = $conn->prepare("SELECT * FROM publishers WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $publisher = $stmt2->fetch();
    $html = array($publisher['id_publisher'], $publisher['first_name'], $publisher['last_name'], $publisher['email'], $publisher['country'], $publisher['date_add'], $publisher['date_end'], '<a href="#" data-id="' . $publisher['id_publisher'] . '" class="edit">Edit</a><br><a href="#" data-id="' . $publisher['id_publisher'] . '" class="delete">delete</a>');
} elseif ($_POST['action'] == "delete") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("UPDATE publishers SET date_end=NOW() WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $stmt2 = $conn->prepare("UPDATE contributors SET date_end=NOW() WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $stmt2 = $conn->prepare("UPDATE publishers_programs SET status=2 WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $stmt2 = $conn->prepare("UPDATE publisher_advertiser SET date_end=NOW() WHERE `id_publisher` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
} elseif ($_POST['action'] == "delete_aff") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("DELETE FROM publisher_Affiliation WHERE `id_Affiliation` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    if ($stmt2->execute()) {
        $html = 1;
    } else  $html = 0;
} elseif ($_POST['action'] == "Stop_pub") {
    $id = intval($_POST['id']);

    $stmt1 = $conn->prepare("UPDATE publishers SET `date_end` = NOW() WHERE id_publisher=(SELECT id_user from users WHERE id_profile=:id AND profile=5)");
    $stmt1->bindParam(':id', $id, PDO::PARAM_INT);

    $stmt2 = $conn->prepare("UPDATE contributors SET `date_end` = NOW() WHERE id_publisher=(SELECT id_user from users WHERE id_profile=:id AND profile=5)");
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt1->execute() && $stmt2->execute()) {
        $html = 1;
    } else  $html = 0;
} elseif ($_POST['action'] == "get_bank") {
    $id = intval($_POST['id']);
    $publisher = intval($_POST['publisher']);
    $stmt2 = $conn->prepare("SELECT * FROM publishers_bank  WHERE id_bank=:id");
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $html = $stmt2->fetch();
} elseif ($_POST['action'] == "del_bank") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("UPDATE publishers_bank SET date_end=NOW()  WHERE id_bank=:id");
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();

    $stmt2 = $conn->prepare("SELECT * FROM publishers_bank  WHERE id_bank=:id");
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $b = $stmt2->fetch();
    $html = array($b['id_bank'], $b['Benefiary'], $b['Account_currency'], $b['name'], $trans['countries'][$b['country']], $b['address'], $b['IBAN'], $b['date_end'], '');
} elseif ($_POST['action'] == "get_affiliation") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("SELECT * FROM `publisher_Affiliation` WHERE id_Affiliation=:id");
    $stmt2->bindParam(':id', $id);
    $stmt2->execute();
    $html = $stmt2->fetch();
} elseif ($_POST['action'] == "get_Programs") {
    $publisher = intval($_POST['publisher']);
    $advertiser = intval($_POST['advertiser']);
    $stmt2 = $conn->prepare("SELECT pp.id,w.name,pp.date_start,pp.date_end ,pp.status FROM publishers_programs pp,websites w WHERE pp.id_publisher=:publisher AND pp.id_program=w.id_website AND w.id_account=:advertiser");
    $stmt2->bindParam(':publisher', $publisher);
    $stmt2->bindParam(':advertiser', $advertiser);
    $stmt2->execute();
    $programs = $stmt2->fetchAll();
    foreach ($programs as &$program) {
        switch ($program['status']) {
            case 0:
                $program[4] = '<span class="badge badge-success badge-pill">Active</span>';
                break;
            case 1:
                $program[4] = '<span class="badge badge-warning badge-pill">Paused</span>';
                break;
            case 2:
                $program[4] = '<span class="badge badge-danger badge-pill">Stopped</span>';
                break;
        }
    }
    $html = $programs;
}

echo json_encode($html);
