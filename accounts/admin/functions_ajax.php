<?php
date_default_timezone_set('UTC');
include('../../init.php');
ini_set("display_errors", 1);
$type = isset($_GET["type"]) ? $_GET["type"] : (isset($_POST["type"]) ? $_POST["type"] : '');
$html = "";

function change(array $incomes, string $currency)
{
    

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "http://myplatform.pro/exchange_rates/exchange.php?base=" . $currency);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    $result = curl_exec($curl);
    curl_close($curl);
    $change = json_decode($result, true); 
    $total = 0;
    foreach ($incomes as $income) {
        if ($income['currency'] == $currency) {
            $total += intval($income['income']);
        } else $total += intval($income['income']) * $change[$income['currency']];
    }
    return round($total, 3);
}
if ($type == "add_testimonial") {
    $username = (isset($_GET['username']) && $_GET['username'] != '') ? htmlentities($_GET['username']) : NULL;
    $title0 = (isset($_GET['title0']) && $_GET['title0'] != '') ? htmlentities($_GET['title0']) : NULL;
    $rating = (isset($_GET['rating']) && $_GET['rating'] != '') ? intval($_GET['rating']) : NULL;
    $photo = NULL;
    $content = (isset($_GET['content']) && $_GET['content'] != '') ? htmlentities($_GET['content']) : NULL;
    $id_website = intval($_GET['id_website']);

    $stmt2 = $conn->prepare("INSERT INTO `testimonials`(`username`, `rating`, `title`, `content`, `photo`, `id_website`) VALUES (:fn,:ln,:ra,:cn,:ph,:ID)");
    $stmt2->bindParam(':fn', $username, PDO::PARAM_STR);
    $stmt2->bindParam(':ra', $rating, PDO::PARAM_INT);
    $stmt2->bindParam(':ln', $title0, PDO::PARAM_STR);
    $stmt2->bindParam(':cn', $content, PDO::PARAM_STR);
    $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
    $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The testimonial has been created successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The testimonial has not been created successfully </div>";
    }
} elseif ($type == "edit_testimonial") {
    $username = (isset($_GET['username']) && $_GET['username'] != '') ? htmlentities($_GET['username']) : NULL;
    $title0 = (isset($_GET['title0']) && $_GET['title0'] != '') ? htmlentities($_GET['title0']) : NULL;
    $rating = (isset($_GET['rating']) && $_GET['rating'] != '') ? htmlentities($_GET['rating']) : NULL;
    $photo = NULL;
    $content = (isset($_GET['content']) && $_GET['content'] != '') ? htmlentities($_GET['content']) : NULL;
    $id_testimonial = intval($_GET['id_website']);

    $stmt2 = $conn->prepare("UPDATE `testimonials` SET `username`=:fn,`rating`=:ra,`title`=:ln,`content`=:cn,`photo`=:ph WHERE `id_testimonial` = :ID");
    $stmt2->bindParam(':fn', $username, PDO::PARAM_STR);
    $stmt2->bindParam(':ra', $rating, PDO::PARAM_STR);
    $stmt2->bindParam(':ln', $title0, PDO::PARAM_STR);
    $stmt2->bindParam(':cn', $content, PDO::PARAM_STR);
    $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
    $stmt2->bindParam(':ID', $id_testimonial, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The testimonial has been updated successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The testimonial has not been updated successfully </div>";
    }
} elseif ($type == "getoffers") {
    $id = implode(",", $_POST['id']);
    $s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.* FROM offers o join packages p on p.id_package=o.id_package WHERE o.id_package IN ($id) ");
    $s1->execute();
    $html = $s1->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type == "getFiltredoffers") {
    $id = implode(",", $_POST['id']);
    $s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.*,pp.* FROM offers o join packages p on p.id_package=o.id_package JOIN packages_price pp ON  pp.id_package=p.id_package WHERE (o.id_package IN ($id) OR p.public=1) AND pp.primary=1  ");
    $s1->execute();
    $html = $s1->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type == "getpackages") {
    $id = intval($_POST['id']);
    $lang = intval($_POST['lang']);
    $s1 = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title,(CASE WHEN p.id_website IS NOT NUll THEN (SELECT name FROM websites w where p.id_website=w.id_website) WHEN p.id_website IS NUll THEN 'No Website' END) as name FROM packages p JOIN packages_price pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang WHERE pp.primary=1 AND p.id_website=:ID");
    $s1->bindParam(':ID', $id, PDO::PARAM_INT);
    $s1->bindParam(':lang', $lang, PDO::PARAM_STR);
    $s1->execute();
    $html = $s1->fetchAll();
} elseif ($type == "remove_testimonial") {
    $id_testimonial = intval($_GET['id_website']);

    $stmt2 = $conn->prepare("DELETE FROM `testimonials` WHERE `id_testimonial` = :ID");
    $stmt2->bindParam(':ID', $id_testimonial, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The testimonial has been removed successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The testimonial has not been removed </div>";
    }
} elseif ($type == "genkey") {
    $id = intval($_POST['id']);
    $key = password_hash($id, PASSWORD_DEFAULT);
    $stmt2 = $conn->prepare("UPDATE accounts SET shared_key=:st WHERE id_account = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->bindParam(':st', $key, PDO::PARAM_STR);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();
    if ($affected_rows != 0) {
        $html = $key;
    } else {
        $html = 0;
    }
} elseif ($type == "update_status") {
    $id = intval($_POST['id']);
    $status = intval($_POST['status']);
    $stmt2 = $conn->prepare("UPDATE users SET status=:st WHERE id_user = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->bindParam(':st', $status, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();
    if ($affected_rows != 0) {
        $html = 1;
    } else {
        $html = 0;
    }
} elseif ($type == "seen") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("UPDATE notifications SET seen=1 WHERE id_account = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();
    if ($affected_rows != 0) {
        $html = 1;
    } else {
        $html = 0;
    }
} elseif ($type == "add_pricing") {
    $title = (isset($_GET['title']) && $_GET['title'] != '') ? htmlentities($_GET['title']) : NULL;
    $price = (isset($_GET['price']) && $_GET['price'] != '') ? floatval($_GET['price']) : NULL;
    $currency = (isset($_GET['currency']) && $_GET['currency'] != '') ? htmlentities($_GET['currency']) : NULL;
    $msg_nb = (isset($_GET['msg_nb']) && $_GET['msg_nb'] != '') ? floatval($_GET['msg_nb']) : NULL;
    $status = (isset($_GET['status']) && $_GET['status'] == 'on') ? 1 : 0;
    $id_website = intval($_GET['id_website']);

    $stmt2 = $conn->prepare("INSERT INTO `pricing`(`title`, `price`, `currency`, `messages`, `status`, `active`, `id_website`) VALUES (:tt,:pr,:cr,:ms,:st,1,:ID)");
    $stmt2->bindParam(':tt', $title, PDO::PARAM_STR);
    $stmt2->bindParam(':pr', $price, PDO::PARAM_STR);
    $stmt2->bindParam(':cr', $currency, PDO::PARAM_STR);
    $stmt2->bindParam(':ms', $msg_nb, PDO::PARAM_STR);
    $stmt2->bindParam(':st', $status, PDO::PARAM_STR);
    $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The pricing has been created successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The pricing has not been created successfully </div>";
    }
} elseif ($type == "edit_pricing") {
    $title = (isset($_GET['title']) && $_GET['title'] != '') ? htmlentities($_GET['title']) : NULL;
    $price = (isset($_GET['price']) && $_GET['price'] != '') ? floatval($_GET['price']) : NULL;
    $currency = (isset($_GET['currency']) && $_GET['currency'] != '') ? $_GET['currency'] : NULL;
    $msg_nb = (isset($_GET['msg_nb']) && $_GET['msg_nb'] != '') ? intval($_GET['msg_nb']) : NULL;
    $status = (isset($_GET['status']) && $_GET['status'] == 'on') ? 1 : 0;
    $active = (isset($_GET['active']) && $_GET['active'] == 'on') ? 1 : 0;
    $id = intval($_GET['id']);

    $stmt2 = $conn->prepare("UPDATE `pricing` SET `title`=:tt,`price`=:pr,`currency`=:cr,`messages`=:ms,`status`=:st,`active`=:ac WHERE `id_pricing` = :ID");
    $stmt2->bindParam(':tt', $title, PDO::PARAM_STR);
    $stmt2->bindParam(':pr', $price, PDO::PARAM_STR);
    $stmt2->bindParam(':cr', $currency, PDO::PARAM_STR);
    $stmt2->bindParam(':ms', $msg_nb, PDO::PARAM_INT);
    $stmt2->bindParam(':st', $status, PDO::PARAM_INT);
    $stmt2->bindParam(':ac', $active, PDO::PARAM_INT);
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The pricing has been updated successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The pricing has not been updated successfully </div>";
    }
} elseif ($type == "remove_pricing") {
    $id_pricing = intval($_GET['id_pricing']);

    $stmt2 = $conn->prepare("DELETE FROM `pricing` WHERE `id_pricing` = :ID");
    $stmt2->bindParam(':ID', $id_pricing, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The pricing has been removed successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The pricing has not been removed </div>";
    }
} elseif ($type == "remove_message") {
    $id_message = intval($_GET['id_message']);

    $stmt2 = $conn->prepare("DELETE FROM `messages` WHERE `id_message` = :ID");
    $stmt2->bindParam(':ID', $id_message, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The message has been removed successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The message has not been removed </div>";
    }
} elseif ($type == "remove_consultant") {
    $id = intval($_GET['id']);

    $stmt2 = $conn->prepare("DELETE FROM `consultants` WHERE `id_consultant` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='col-12 alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The consultant has been removed successfully </div>";
    } else {
        $html = "<div class=' col-12 alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The consultant has not been removed </div>";
    }
} elseif ($type == "remove_customer") {
    $id = intval($_GET['id']);

    $stmt2 = $conn->prepare("DELETE FROM `customers` WHERE `id_customer` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer has been removed successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer has not been removed </div>";
    }
} elseif ($type == "remove_website") {
    $id = intval($_GET['id']);

    $stmt2 = $conn->prepare("DELETE FROM `websites` WHERE `id_website` = :ID");
    $stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if ($affected_rows != 0) {
        $html = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website has been removed successfully </div>";
    } else {
        $html = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website has not been removed </div>";
    }
} elseif ($type == "Solved") {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("UPDATE contact_ticket SET status = 2 WHERE id_ticket=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $affected_rows = $stmt->rowCount();
    if ($affected_rows != 0) {
        $html = 1;
    }
} elseif ($type == "getContactConv") {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id_ticket=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $messages = $stmt->fetchAll();
    $html = $messages;
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
        $html = '<li><div class="chat-content"><h5>' . $_POST['sender_name'] . '</h5><div class="box bg-light-info">' . $message . '</div><div class="chat-time text-left">' . date("Y-m-d H:i:s") . '</div></div></li>';
    }
} elseif ($type == "getPublisher") {
    $results = array();
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("SELECT  u.id_user,c.pseudo FROM `consultants` c ,users u ,messages m where u.id_user=m.receiver AND m.sender=:id AND c.id_consultant=u.id_profile AND u.profile=3 Group by m.receiver");
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $results['consultants'] = $stmt2->fetchAll();


    $id_user = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT a.*, b.*, c.`name` AS website_name,ac.currency FROM `users` a LEFT JOIN `customers` b ON a.`id_profile` = b.`id_customer` JOIN `accounts` ac ON ac.`id_account` = b.`id_account` LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE a.`id_user` = :ID");
    $stmt->bindParam(':ID', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchObject();

    $id_website = intval($result->id_website);

    $packages = $conn->prepare("SELECT p.*,pp.price,pp.currency,CASE WHEN t.content is not null then t.content ELSE p.title end title,
                (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount,
                (SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count 
                from packages p  JOIN packages_price pp ON  pp.id_package=p.id_package left join translations t on t.table='packages' and t.id_element=p.id_package and t.lang=:lang where pp.currency=:cur AND p.status=1 and (p.id_website is null or p.id_website=:IDW) ");
    $packages->bindParam(':IDW', $id_website, PDO::PARAM_INT);
    $packages->bindParam(':IDC', intval($result->id_customer), PDO::PARAM_INT);
    $packages->bindParam(':lang', $result->lang, PDO::PARAM_STR);
    $packages->bindParam(':cur', $result->currency, PDO::PARAM_STR);
    $packages->execute();
    $pricings_rows = $packages->rowCount();
    $pricings = $packages->fetchAll();
    $class = "col-md-4";
    foreach ($pricings as $pri) {
        if ($pri['total_discount'] < 100) {
            if ($pri['total_discount'] > 0) {
                $results['packages'][] = '<option value="' . $pri["id_package"] . '" >' . $pri["title"] . ':' . round(((100 - $pri["total_discount"]) * ($pri["price"] / 100)), 2) . '' . $pri["currency"] . '(' . $pri["total_discount"] . '% Discount)</option>';
            } else {
                $results['packages'][] = '<option value="' . $pri["id_package"] . '" >' . $pri["title"] . ':' . floatval($pri["price"]) . '' . $pri["currency"] . '</option>';
            }
        }
    }
    $results['offers'] = array_combine(array_column($pricings, 'id_package'), $pricings);
    $html = $results;
} elseif ($type == "getImported") {
    $id = intval($_POST['id']);
    $stmt2 = $conn->prepare("SELECT id_customer,firstname , lastname ,emailc,country,phone, (SELECT count(*) FROM `transactionsc` as tc where c.id_customer= tc.id_customer )as buys_count,date_start,photo  FROM `customers` as c WHERE c.importation_id=:id");
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $html = $stmt2->fetchAll();
} elseif ($type == "getLeads") {
    $val = intval($_POST['id']);
    $begin = $_POST['begin'];
    $end = $_POST['end'];
    $stmt = $conn->prepare("SELECT  l.id_lead,CONCAT(cu.firstname,cu.lastname) ,cu.country,w.name as program,l.status,l.add_date,l.update_date FROM leads l,websites w,contributors c,users u,accounts a,customers cu WHERE w.id_account=a.id_account AND l.id_contributor=u.id_user AND u.id_profile=c.id_contributor  AND w.id_website=cu.id_website AND c.id_publisher=:id AND cu.id_customer=l.id_customer AND cu.date_start BETWEEN '$begin' AND '$end'");
    $stmt->bindParam(':id', $val);
    $stmt->execute();
    $leads = $stmt->fetchAll();
    foreach ($leads as &$lead) {
        switch ($lead['status']) {
            case 0:
                $lead[4] = $trans['publisher']['No_visit'];
                break;
            case 1:
                $lead[4] = $trans['publisher']['Visit_without_sale'];
                break;
            case 2:
                $lead[4] = $trans['publisher']['1st_Sale'];
                break;

            default:
                # code...
                break;
        }
        $lead[2] = $trans['countries'][$lead[2]];
    }
    $html = $leads;
} elseif ($type == "getPrograms") {
    $pub = intval($_POST['id']);
    $begin = $_POST['begin'];
    $end = $_POST['end'];
    $stmt = $conn->prepare("SELECT  w.id_website,w.name,pp.status,(SELECT count(*) from customers cu,leads l WHERE cu.id_customer=l.id_customer AND cu.id_website=w.id_website AND cu.date_start BETWEEN pp.date_start AND case  when pp.date_end IS null then NOW() ELSE pp.date_end end),pp.date_start,pp.date_end from publishers_programs pp,websites w WHERE pp.id_publisher=:id  AND w.id_website=pp.id_program AND pp.date_start BETWEEN '$begin' AND '$end'");
    $stmt->bindParam(':id', $pub);
    $stmt->execute();
    $programs = $stmt->fetchAll();
    foreach ($programs as $key => $lead) {
        switch ($lead['status']) {
            case 0:
                $programs[$key][2] = $trans['publisher']['Active'];
                break;
            case 1:
                $programs[$key][2] = $trans['publisher']['paused'];
                break;
            case 2:
                $programs[$key][2] = $trans['publisher']['finished'];
                break;

            default:
                # code...
                break;
        }
    }
    $html = $programs;
} elseif ($type == "getPrice") {
    $id = intval($_POST['id']);
    $stmt1 = $conn->prepare("SELECT p.id,p.currency,p.price,p.primary FROM `packages_price` p WHERE id=:id");
    $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt1->execute();
    $html = $stmt1->fetchObject();
} elseif ($type == "getConnected") {
    session_start();
    $users = isset($_POST['users']) ? $_POST['users'] : [];
    $agents = isset($_POST['agents']) ? $_POST['agents'] : [];
    $guests = isset($_POST['guests']) ? $_POST['guests'] : [];
    $result = array();
    $result['consultants'] = array();
    $result['customers'] = array();
    foreach ($agents as $user) {
        $stmt1 = $conn->prepare("SELECT * FROM `consultants` c,users u WHERE u.id_user =:id AND u.profile=3 AND u.id_profile=c.id_consultant AND  c.id_account=:ida");
        $stmt1->bindParam(':id', $user, PDO::PARAM_INT);
        $stmt1->bindParam(':ida', $_SESSION['id_account'], PDO::PARAM_INT);
        $stmt1->execute();
        $consultant = $stmt1->fetchObject();
        if ($consultant) {
            $result['consultants'][$consultant->id_user] = '<li id="' . $consultant->id_user . '" class="itemConsultant" data-website="' . $consultant->websites . '">
                <div class=" consultant_' . $consultant->id_user . '" id="' . $consultant->id_user . '" data-name="' . $consultant->pseudo . '" data-id="' . $consultant->id_user . '" data-avatar="' . $consultant->photo . '">
                <a href="javascript:void(0)" data-id="' . $consultant->id_user . '" data-type="3" class="expert" title="' . $trans["Show_details"] . '">  
                <img src="../uploads/consultants/' . $consultant->photo . '" alt="user-img" class="img-circle" />
                    <span>' . strtolower($consultant->pseudo) . '</span>
                    </a>
                </div>
            </li>';
        }
    }

    foreach ($guests as $user) {
        $stmt1 = $conn->prepare("SELECT c.id_customer as id_user,c.* FROM `customers` c WHERE c.id_account=:ida AND c.id_customer =:id ");
        $stmt1->bindParam(':id', $user, PDO::PARAM_INT);
        $stmt1->bindParam(':ida', $_SESSION['id_account'], PDO::PARAM_INT);
        $stmt1->execute();
        $customer = $stmt1->fetchObject();
        if ($customer) {
            $result['customers'][] = '<li id="' . $customer->id_user . '" class="itemConsultant" data-website="' . $customer->id_website . '">
                <div class="item_' . $customer->id_user . '" id="' . $customer->id_user . '" data-name="' . $customer->firstname . '" data-id="' . $customer->id_user . '" data-avatar="' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '">
                        <a href="javascript:void(0)" data-id="' . $customer->id_user . '" data-type="7" class="customer" title="' . $trans["Show_details"] . '">
                            <img src="../uploads/customers/' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '" alt="user-img" class="img-circle " ">
                                <span>
                                <span  class="mr-2">' . strtolower($customer->firstname) . ' : ' . $customer->id_user . '</span><div class="badge badge-success badge-pill">Guest</div><div class="buying d-none" style="float: right;font-size: 9px;font-weight: 800;color: white;background: #40c8ba;padding: 4px;border-radius: 6px;">buying</div>
                                <small style="display: block;font-size: 10px;">Balance: <span class="balance-' . $customer->id_user . '">' . $customer->balance . '</span></small></span>
                            <div class="notify" id="notify_' . $customer->id_user . '"> <span class="point" id=point_' . $customer->id_user . '"></span> </div>
                        </a>
                    </div>
                </li>';
        }
    }

    foreach ($users as $user) {
        $stmt1 = $conn->prepare("SELECT *,(case when EXISTS (SELECT id_lead from leads WHERE id_customer=c.id_customer) then 1 else 0 end) as lead FROM `customers` c JOIN users u on u.id_profile=c.id_customer AND u.profile=4 AND u.id_user =:id WHERE c.id_account=:ida");
        $stmt1->bindParam(':id', $user, PDO::PARAM_INT);
        $stmt1->bindParam(':ida', $_SESSION['id_account'], PDO::PARAM_INT);
        $stmt1->execute();
        $customer = $stmt1->fetchObject();
        if ($customer) {
            $result['customers'][] = '<li id="' . $customer->id_user . '" class="itemConsultant lead_' . $customer->lead . '" data-website="' . $customer->id_website . '">
                <div class="item_' . $customer->id_user . '" id="' . $customer->id_user . '" data-name="' . $customer->firstname . ' ' . $customer->lastname . '" data-id="' . $customer->id_user . '" data-avatar="' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '">
                        <a href="javascript:void(0)" data-id="' . $customer->id_user . '" data-type="4" class="customer" title="' . $trans["Show_details"] . '">
                            <img src="../uploads/customers/' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '" alt="user-img" class="img-circle " ">
                                <span>
                                ' . strtolower($customer->firstname) . ' ' . strtolower($customer->lastname) . '
                                <small style="display: block;font-size: 10px;">Balance: <span class="balance-' . $customer->id_user . '">' . $customer->balance . '</span></small></span>
                            <div class="notify" id="notify_' . $customer->id_user . '"> <span class="point" id=point_' . $customer->id_user . '"></span> </div>
                        </a>
                    </div>
                </li>';
        }
    }
    $html = $result;
} elseif ($type == "getId") {
    $id = intval($_POST['id']);
    $role = intval($_POST['role']);
    session_start();
    if ($role == 3) {
        $stmt1 = $conn->prepare("SELECT * FROM `consultants` c,users u WHERE c.id_consultant = u.id_profile AND u.profile=3 AND u.id_user=:id");
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
        $consultant = $stmt1->fetchObject();
        $html = '<li id=' . $consultant->id_user . ' class="itemConsultant"  data-website="' . $consultant->websites . '">
        <div class=" consultant_' . $consultant->id_user . '" id="' . $consultant->id_user . '" data-name="' . $consultant->pseudo . '" data-id="' . $consultant->id_user . '" data-avatar="' . ($consultant->photo != null ? $consultant->photo : 'img-1.png') . '">
        <a href="javascript:void(0)" data-id="' . $consultant->id_user . '" data-type="3" class="expert">  
        <img src="../uploads/consultants/' . ($consultant->photo != null ? $consultant->photo : 'img-1.png') . '" alt="user-img" class="img-circle" />
            <span>' . strtolower($consultant->pseudo) . '</span>
            </a>
        </div>
    </li>';
    } else if ($role == 4) {
        $stmt1 = $conn->prepare("SELECT *,(case when EXISTS (SELECT id_lead from leads WHERE id_customer=c.id_customer) then 1 else 0 end) as lead FROM `customers` c,users u WHERE c.id_customer = u.id_profile AND u.profile=4 AND u.id_user=:id");
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
        $customer = $stmt1->fetchObject();
        $html = '<li id="' . $customer->id_user . '" class="itemConsultant lead_' . $customer->lead . '" data-website="' . $customer->id_website . '">
        <div class="item_' . $customer->id_user . '" id="' . $customer->id_user . '" data-name="' . $customer->firstname . ' ' . $customer->lastname . '" data-id="' . $customer->id_user . '" data-avatar="' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '">
            <a href="javascript:void(0)" data-id="' . $customer->id_user . '" data-type="4" class="customer">
                <img src="../uploads/customers/' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '" alt="user-img" class="img-circle " ">

                    <span>  
                    ' . strtolower($customer->firstname) . ' ' . strtolower($customer->lastname) . '
                    <small style="display: block;font-size: 10px;">Balance: <span class="balance-' . $customer->id_user . '">' . $customer->balance . '</span></small></span>
            <div class="notify" id="notify_' . $customer->id_user . '"> <span class="point" id="point_' . $customer->id_user . '"></span> </div>
            </a>
        </div>
    </li>';
    } else if ($role == 7) {
        $stmt1 = $conn->prepare("SELECT c.id_customer as id_user,c.*,(case when EXISTS (SELECT id_lead from leads WHERE id_customer=c.id_customer) then 1 else 0 end) as lead FROM `customers` c WHERE c.id_customer=:id and c.id_account = :ida");
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->bindParam(':ida', $_SESSION['id_account'], PDO::PARAM_INT);
        $stmt1->execute();
        $customer = $stmt1->fetchObject();
        $html = '<li id="' . $customer->id_user . '" class="itemConsultant lead_' . $customer->lead . '" data-website="' . $customer->id_website . '">
            <div class="item_' . $customer->id_user . '" id="' . $customer->id_user . '" data-name="' . $customer->firstname . ' : ' . $customer->id_user . '" data-id="' . $customer->id_user . '" data-avatar="' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '">
                <a href="javascript:void(0)" data-id="' . $customer->id_user . '" data-type="7" class="customer">
                    <img src="../uploads/customers/' . ($customer->photo != null ? $customer->photo : 'img-1.png') . '" alt="user-img" class="img-circle " ">
                        <span>
                        <span class="mr-2">' . strtolower($customer->firstname) . ' : ' . $customer->id_user . '</span><div class="badge badge-success badge-pill">Guest</div><div class="buying d-none" style="float: right;font-size: 9px;font-weight: 800;color: white;background: #40c8ba;padding: 4px;border-radius: 6px;">buying</div>
                        <small style="display: block;font-size: 10px;">Balance: <span class="balance-' . $customer->id_user . '">' . $customer->balance . '</span></small></span>
                <div class="notify" id="notify_' . $customer->id_user . '"> <span class="point" id="point_' . $customer->id_user . '"></span> </div>
                </a>
            </div>
        </li>';
    }
} else if ($type == "getInfo") {
    $id = intval($_POST['id']);
    if ($_POST['profile'] == 4) {
        $stmt = $conn->prepare("SELECT u.id_user,c.id_customer,c.gender,c.firstname,c.lastname,c.emailc,c.phone,c.address,c.country,u.lang,c.photo,w.name from websites w,customers c left join users u on u.id_profile=c.id_customer where  c.id_website=w.id_website AND  (u.profile= 4 or u.profile is null) and (u.id_user =:id or c.id_customer =:id)");
        $stmt->bindparam(":id",  $id);
        $stmt->execute();
        $customer = $stmt->fetchObject();
        isset($customer->id_user) ? $customer->id_user = $customer->id_user : $customer->id_user = $customer->id_customer;
        if (isset($_POST['messages'])) {
            $stmt = $conn->prepare("SELECT m.receiver as id ,c.pseudo,c.photo, max(m.date_send) as LastMSG from messages m,consultants c join users u on u.id_profile=c.id_consultant where  u.profile=3 and (u.id_user =m.receiver or m.receiver=0) and m.sender=:id group by m.receiver");
            $stmt->bindparam(":id",  $id);
            $stmt->execute();
            $convs = $stmt->fetchAll();
            $customer->conv = $convs;
        }
        if ($customer->gender != null) {
            $customer->gender == 1 ? $customer->gender = $trans["male"] : $customer->gender = $trans["female"];
        } else {
            $customer->gender = '';
        }
        if ($customer->lang == 'en') {
            $customer->lang = $trans["english"];
        } else if ($customer->lang == 'fr') {
            $customer->lang = $trans["french"];
        }
        $customer->country = $customer->country != null ? $trans["countries"][$customer->country] : '';
    } else if ($_POST['profile'] == 3) {
        $stmt = $conn->prepare("SELECT u.id_user,c.gender,c.firstname,c.lastname,c.emailc,c.phone,u.lang,c.photo,c.pseudo from websites w,consultants c join users u on u.id_profile=c.id_consultant where  c.websites=w.id_website AND  u.profile=3 and u.id_user =:id");
        $stmt->bindparam(":id",  $id);
        $stmt->execute();
        $customer = $stmt->fetchObject();
        if (isset($_POST['messages'])) {
            $stmt = $conn->prepare("SELECT m.receiver as id,c.firstname,c.lastname,c.photo, max(m.date_send)  as LastMSG from messages m,customers c left join users u on u.id_profile=c.id_customer and u.profile=4 where  (u.id_user =m.receiver or c.id_customer =m.receiver or m.receiver=0) and  m.sender=:id group by m.receiver");
            $stmt->bindparam(":id",  $id);
            $stmt->execute();
            $convs = $stmt->fetchAll();
            $customer->conv = $convs;
        }
        $customer->gender == 1 ? $customer->gender = $trans["male"] : $customer->gender = $trans["female"];
        if ($customer->lang == 'en') {
            $customer->lang = $trans["english"];
        } else if ($customer->lang == 'fr') {
            $customer->lang = $trans["french"];
        }
    }
    $html = $customer;
} elseif ($type == "SetOffer") {
    $form =  $_POST['form'];
    $id_account =  $_POST['id_account'];
    $user_id =  $_POST['user_id'];
    $title_fr = $form[1]['value'];
    $dateRange = $form[4]['value'];

    $title = $form[0]['value'];
    $discount = $form[2]['value'];
    $limit = 1;
    $dateType = $form[3]['value'];
    $access = 2;
    if ($dateType == 1) {
        $dates = explode(" - ", $dateRange);
        $startDate = $dates[0];
        $endDate = $dates[1];
        $parts = explode('/', $startDate);
        $startDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
        $parts = explode('/', $endDate);
        $endDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
    } else {
        $startDate = null;
        $endDate = null;
    }

    $datec = date('Y-m-d', strtotime('now'));

    foreach ($form as $value) {
        if ($value['name'] == 'packages_ids[]') {

            $stmt1 = $conn->prepare("INSERT INTO `offers`(`title`, `discount`, `limit`, `start_date`, `end_date`, `id_account`, `created_at`, `id_package`, `access`) 
                                VALUES (:ti,:ds,:lt,:sd,:ed,:ia,NOW(),:ip,:ac)");
            $stmt1->bindParam(':ti', $title, PDO::PARAM_STR);
            $stmt1->bindParam(':ds', $discount, PDO::PARAM_STR);
            $stmt1->bindParam(':lt', $limit, PDO::PARAM_INT);
            $stmt1->bindParam(':sd', $startDate, PDO::PARAM_STR);
            $stmt1->bindParam(':ed', $endDate, PDO::PARAM_STR);
            $stmt1->bindParam(':ia', $id_account, PDO::PARAM_INT);
            $stmt1->bindParam(':ip', $value['value'], PDO::PARAM_INT);
            $stmt1->bindParam(':ac', $access, PDO::PARAM_INT);
            $stmt1->execute();
            $last_id = $conn->lastInsertId();
            $affected_rows = $stmt1->rowCount();

            if ($affected_rows != 0) {
                if ($title_fr != "") {

                    $titleFr = $title_fr;
                    $table = "offers";
                    $column = "title";
                    $lang = "fr";
                    $idElement = $last_id;
                    $stmtl = $conn->prepare("INSERT INTO `translations`(`content`, `table`, `column`, `lang`, `id_element`) VALUES (:ct,:tb,:cl,:lg,:ie)");
                    $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
                    $stmtl->bindParam(':tb', $table, PDO::PARAM_STR);
                    $stmtl->bindParam(':cl', $column, PDO::PARAM_STR);
                    $stmtl->bindParam(':lg', $lang, PDO::PARAM_STR);
                    $stmtl->bindParam(':ie', $idElement, PDO::PARAM_INT);
                    $stmtl->execute();
                }
                if ($user_id) {
                    $stmt = $conn->prepare("SELECT id_profile  from users where id_user=:ip and profile=4");
                    $stmt->bindParam(':ip', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $customer = $stmt->fetchObject();
                    $id_user = intval($customer->id_profile);
                    $stmt1 = $conn->prepare("INSERT INTO `offers_customers`(`id_customer`, `id_offer`, `created_at`) 
                                                                         VALUES (:ic,:iof,NOW())");

                    $stmt1->bindParam(':ic', $id_user, PDO::PARAM_STR);
                    $stmt1->bindParam(':iof', $last_id, PDO::PARAM_STR);
                    if ($stmt1->execute()) {

                        if ($discount == 100) {
                            $s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.* FROM `offers` o join `packages` p on p.id_package=o.id_package WHERE o.id_offer = :ID");
                            $s1->bindParam(':ID', $last_id, PDO::PARAM_INT);
                            $s1->execute();
                            $offer = $s1->fetchObject();
                            $stmt1 = $conn->prepare("UPDATE customers set balance=balance+:ba where id_customer=(SELECT id_profile FROM users WHERE id_user=:ic AND profile=4) ");
                            $stmt1->bindParam(':ic', $user_id, PDO::PARAM_INT);
                            $stmt1->bindParam(':ba', $offer->messages, PDO::PARAM_INT);
                            $stmt1->execute();
                            $description = "Free offer attached to customer";
                        }

                        $description = "New offer attached to customer";
                        $stmt2 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) 
                                                                                         VALUES(:iu,:ds,:mt,3,NOW())");
                        $stmt2->bindParam(':iu', $id_user, PDO::PARAM_INT);
                        $stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
                        $stmt2->bindParam(':mt', $last_id, PDO::PARAM_INT);
                        $stmt2->execute();
                        $html = 1;
                    } else {
                        $html = 0;
                    }
                }
            } else {
                $html = 'no';
            }
        }
    }
} elseif ($type == "getOffers") {
    $receiver_role = intval($_POST['receiver_role']);
    if ($receiver_role == 4) {
        $id_user = intval($_POST['id']);
        $stmt = $conn->prepare("SELECT a.*, b.*, c.`name` AS website_name FROM `users` a LEFT JOIN `customers` b ON a.`id_profile` = b.`id_customer` LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE a.`id_user` = :ID");
        $stmt->bindParam(':ID', $id_user, PDO::PARAM_INT);
    } elseif ($receiver_role == 7) {
        $id_customer = intval($_POST['id']);
        $stmt = $conn->prepare("SELECT b.*, c.`name` AS website_name FROM `customers` b LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE b.`id_customer` = :ID");
        $stmt->bindParam(':ID', $id_customer, PDO::PARAM_INT);
    }
    $stmt->execute();
    $result = $stmt->fetchObject();
    $id_website = intval($result->id_website);

    if ($result->id_link != null) {
        $packages = $conn->prepare("SELECT p.*,CASE WHEN t.content is not null then t.content ELSE p.title end title, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount, (SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count from packages p  left join translations t on t.table='packages' and t.id_element=p.id_package and t.lang=:lang where   (p.public=1 OR p.id_package IN (SELECT id_package FROM customers_packages cp WHERE  cp.id_customer=:idu) OR p.id_package IN (SELECT id_package FROM links_packages lp WHERE  lp.id_link=:IDL))  AND p.status=1 and (p.id_website is null or p.id_website=:IDW) ");
        $packages->bindParam(':IDW', $id_website, PDO::PARAM_INT);
        $packages->bindParam(':IDC', $result->id_account, PDO::PARAM_INT);
        $packages->bindParam(':idu', $result->id_customer, PDO::PARAM_INT);
        $packages->bindParam(':IDL', $result->id_link, PDO::PARAM_INT);
        $packages->bindParam(':lang', $result->lang, PDO::PARAM_STR);
        $packages->bindParam(':cur', $currencies[$result->country], PDO::PARAM_STR);
        $packages->execute();
        $pricings_rows = $packages->rowCount();
        $pricings = $packages->fetchAll();
    } else {
        $packages = $conn->prepare("SELECT p.*,CASE WHEN t.content is not null then t.content ELSE p.title end title, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount, (SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count from packages p left join translations t on t.table='packages' and t.id_element=p.id_package and t.lang=:lang where  p.status=1 AND (p.public=1 OR p.id_package IN (SELECT id_package FROM customers_packages cp WHERE  cp.id_customer=:idu)) and (p.id_website is null or p.id_website=:IDW) ");
        $packages->bindParam(':IDW', $id_website, PDO::PARAM_INT);
        $packages->bindParam(':IDC', $result->id_account, PDO::PARAM_INT);
        $packages->bindParam(':idu', $result->id_customer, PDO::PARAM_INT);
        $packages->bindParam(':lang', $result->lang, PDO::PARAM_STR);
        $packages->bindParam(':cur', $currencies[$result->country], PDO::PARAM_STR);
        $packages->execute();
        $pricings_rows = $packages->rowCount();
        $pricings = $packages->fetchAll();
    }
    $class = "col-md-4";
    $results = array();
    foreach ($pricings as $key => $pri) {
        if ($pri['total_discount'] < 100) {
            $packages = $conn->prepare("SELECT * FROM packages_price WHERE id_package=:idp AND currency=(CASE WHEN (SELECT currency FROM packages_price pp where pp.id_package=:idp AND pp.currency=:cur AND pp.primary=1) IS NOT NULL THEN (SELECT currency FROM packages_price pp where pp.id_package=:idp AND pp.currency=:cur AND pp.primary=1) ELSE (SELECT currency FROM accounts where id_account=:IDC) END) AND date_end IS null");
            $packages->bindParam(':IDC', $result->id_account, PDO::PARAM_INT);
            $packages->bindParam(':idp', $pri["id_package"], PDO::PARAM_INT);
            $packages->bindParam(':cur', $currencies[$result->country], PDO::PARAM_STR);
            $packages->execute();
            $pkg = $packages->fetchObject();
            $pricings[$key]['finalPrice'] = round(((100 - $pri["total_discount"]) * ($pkg->price / 100)), 2);
            $pricings[$key]['currency'] = $pkg->currency;
            $pricings[$key]['price'] = $pkg->price;
            if ($pri['total_discount'] > 0) {
                $results['packages'][] = '<option value="' . $pri["id_package"] . '" >' . $pri["title"] . ':' . round(((100 - $pri["total_discount"]) * ($pkg->price / 100)), 2) . '' . $pkg->currency . '(' . $pri["total_discount"] . '% Discount)</option>';
            } else {
                $results['packages'][] = '<option value="' . $pri["id_package"] . '" >' . $pri["title"] . ':' . floatval($pkg->price) . '' . $pkg->currency . '</option>';
            }
        }
    }

    $results['offers'] = array_combine(array_column($pricings, 'id_package'), $pricings);

    $html = $results;
} elseif ($type == "getConv") {
    $val = intval($_POST['id']);
    $stmt1 = $conn->prepare("SELECT m.receiver,c.pseudo,c.id_consultant FROM messages m,consultants c,users u WHERE m.receiver=u.id_user AND u.id_profile=c.id_consultant AND u.profile=3 AND  m.sender=:id AND m.sender_role=4 group by m.receiver");
    $stmt1->bindParam(':id', $val, PDO::PARAM_INT);
    $stmt1->execute();
    $html = $stmt1->fetchAll();
} elseif ($type == "getLink") {
    $id = $_POST['id'];
    $result = array();
    $stmt = $conn->prepare("SELECT * FROM links WHERE id_link=:id");
    $stmt->bindparam(":id",  $id);
    $stmt->execute();
    $link = $stmt->fetchObject();
    $result['id_website'] = $link->id_website;
    $result['date_end'] = $link->date_end;
    $s1 = $conn->prepare("SELECT id_package FROM links_packages lp WHERE  lp.id_link=:ID ");
    $s1->bindParam(':ID', $id, PDO::PARAM_INT);
    $s1->execute();
    $result['packages'] = $s1->fetchAll();
    $s1 = $conn->prepare("SELECT id_offer FROM links_offers lo WHERE  lo.id_link=:ID ");
    $s1->bindParam(':ID', $id, PDO::PARAM_INT);
    $s1->execute();
    $result['offers'] = $s1->fetchAll();
    $html = $result;
} elseif ($type == "stopLink") {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE links SET status=0 WHERE id_link=:id");
    $stmt->bindparam(":id",  $id);
    $stmt->execute();
    $html = $stmt->rowCount() > 0 ? 1 : 0;
} elseif ($type == "getPick") {
    $id = intval($_POST['id']);
    $val = intval($_POST['periode']);
    $condition = array();
    switch ($val) {
        case 1:
            $condition[0] = "AND date(msg.date_send )='" . date('Y-m-d') . "'";
            break;
        case 2:
            $condition[0] = "AND date(msg.date_send )='" . date('Y-m-d', strtotime("-1 days")) . "' ";
            break;
        case 3:
            $condition[0] = "AND date(msg.date_send ) BETWEEN '" . date("Y-m-d", strtotime('monday this week')) . "' AND '" . date("Y-m-d", strtotime('sunday this week')) . "'";
            break;
        case 4:
            $condition[0] = "AND date(msg.date_send ) BETWEEN '" . date("Y-m-d", strtotime('last week monday')) . "' AND '" . date("Y-m-d", strtotime('last week sunday')) . "'";
            break;
        case 5:
            $condition[0] = "AND date(msg.date_send ) BETWEEN '" . date("Y-m-d", strtotime("first day of this month")) . "' AND '" . date("Y-m-d", strtotime("last day of this month")) . "' ";
            break;
        case 6:
            $condition[0] = "AND date(msg.date_send ) BETWEEN '" . date("Y-m-d", strtotime("first day of previous month")) . "' AND '" . date("Y-m-d", strtotime("last day of previous month")) . "'";
            break;
        case 7:
            $condition[0] = "AND date(msg.date_send ) BETWEEN '" . $_POST['from'] . "' AND '" . $_POST['to'] . "'";
            break;
        default:
            break;
    }

    $stmt = $conn->prepare(" SELECT firstname,lastname,sender,receiver,date_send,TIME_TO_SEC(TIMEDIFF(
        CASE when (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender=firstMsg.receiver order by date_send LIMIT 1) is not null then (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender = firstMsg.receiver order by date_send LIMIT 1)else now() end,firstMsg.date_send )) as pick,(SELECT date_send FROM messages WHERE sender=firstMsg.receiver AND receiver=firstMsg.sender AND date_send>firstMsg.date_send limit 1) as date_receive from (SELECT sender,receiver ,date_send,c.firstname,c.lastname from messages ,customers c,users u where c.id_customer=u.id_profile and u.id_user =sender AND receiver=:IDU and sender_role=4 GROUP BY sender) as firstMsg  where  " . str_replace("AND date(msg.date_send )", "date(date_send)", $condition[0]));
    $stmt->bindparam(":IDU", $id);
    $stmt->execute();
    $customers = $stmt->fetchAll();
    $result = array();
    foreach ($customers as $customer) {
        $senderR = 3;
        $receiverR = 4;
        $stmt2 = $conn->prepare("SELECT max(id_message) as maxID , min(id_message) as minID from messages msg where (sender=:se and sender_role=:ser and receiver=:re and receiver_role=:rer " . $condition[0] . ") or (sender=:re and sender_role=:rer and receiver=:se and receiver_role=:ser " . $condition[0] . ") ");
        $stmt2->bindparam(":se", $id);
        $stmt2->bindparam(":ser", $senderR);
        $stmt2->bindparam(":re", intval($customer['sender']));
        $stmt2->bindparam(":rer", $receiverR);
        $stmt2->execute();
        $periode = $stmt2->fetchObject();
        $stmt = $conn->prepare("SELECT AVG(AV.average_time) as moy,MAX(AV.average_time) as max,MIN(AV.average_time) as min from ( SELECT TIME_TO_SEC(TIMEDIFF(CASE when (SELECT msg.date_send from messages msg where msg.receiver=m.sender and msg.date_send>m.date_send AND msg.sender=:IDU " . $condition[0] . " order by msg.id_message LIMIT 1) is not null then (SELECT msg.date_send from messages msg where  msg.receiver=m.sender and msg.date_send>m.date_send AND msg.sender=:IDU " . $condition[0] . " order by msg.id_message  LIMIT 1)else now() end,m.date_send )) as average_time from messages m  where  m.sender=:IDC  " . str_replace("msg", "m", $condition[0]) . " order by m.id_message) AV");
        $stmt->bindparam(":IDU", $id);
        $stmt->bindparam(":IDC", intval($customer['sender']));
        $stmt->execute();
        $average = $stmt->fetch();

        $resultat = array();
        foreach ($average as $key => $av) {
            $jours = round($av / 86400);
            $secondes = $av - ($jours * 86400);
            $resultat[$key] = "(" . $jours . "j)" . gmdate("H:i:s", $secondes);
        }
        $button = '<button class="btn btn-sm waves-effect waves-light btn-color" onClick="MyWindow=window.open(' . "'" . 'superConversation.php?sender=' . $customer['sender'] . '&receiver=' . $customer['receiver'] . '&form=1&from=' . $periode->minID . '&to=' . $periode->maxID . '' . "'" . ",'" . $customer['sender'] . "-" . $id . "','width=800,height=500'); return false;" . '"' . 'href="#" class="dropdown-item"> Rejoindre</button>';
        $result[] = array($customer['sender'], $customer['firstname'], $customer['lastname'], gmdate("H:i:s", $customer['pick']), $customer['date_send'], $customer['date_receive'], "<span>" . $resultat['moy'] . "<small class='text-muted m-b-0 px-2' style='float: left'>MAX : " . $resultat['max'] . "</small><small class='text-muted m-b-0 px-2' style='float: left'>MIN : " . $resultat['min'] . "</small></span>", $button);
    }


    $html = $result;
} elseif ($type == "getStat") {
    $val = intval($_POST['id']);
    $condition = array();
    $result = array();
    switch ($val) {
        case 1:
            $condition[0] = "AND date(date_send )='" . date('Y-m-d') . "'";
            $condition[1] = " AND cl.date='" . date('Y-m-d') . "'";
            $result['currentDate'] = date('d/m/Y');
            break;
        case 2:
            $condition[0] = "AND date(date_send )='" . date('Y-m-d', strtotime("-1 days")) . "' ";
            $condition[1] = " AND cl.date='" . date('Y-m-d', strtotime("-1 days")) . "'";
            $result['currentDate'] = date('d/m/Y', strtotime("-1 days"));
            break;
        case 3:

            $condition[0] = "AND date(date_send ) BETWEEN '" . date("Y-m-d", strtotime('monday this week')) . "' AND '" . date("Y-m-d", strtotime('sunday this week')) . "'";
            $condition[1] = " AND cl.date BETWEEN '" . date("Y-m-d", strtotime('monday this week')) . "' AND '" . date("Y-m-d", strtotime('sunday this week')) . "'";
            $result['currentDate'] = date("d/m/Y", strtotime('monday this week')) . ' ... ' . date("d/m/Y", strtotime('sunday this week'));
            break;
        case 4:
            $condition[0] = "AND date(date_send ) BETWEEN '" . date("Y-m-d", strtotime('last week monday')) . "' AND '" . date("Y-m-d", strtotime('last week sunday')) . "'";
            $condition[1] = " AND cl.date BETWEEN '" . date("Y-m-d", strtotime('last week monday')) . "' AND '" . date("Y-m-d", strtotime('last week sunday')) . "'";
            $result['currentDate'] = date("d/m/Y", strtotime('last week monday')) . ' ... ' . date("d/m/Y", strtotime('last week sunday'));
            break;
        case 5:
            $condition[0] = "AND date(date_send ) BETWEEN '" . date("Y-m-d", strtotime("first day of this month")) . "' AND '" . date("Y-m-d", strtotime("last day of this month")) . "' ";
            $condition[1] = " AND cl.date BETWEEN '" . date("Y-m-d", strtotime("first day of this month")) . "' AND '" . date("Y-m-d", strtotime("last day of this month")) . "'";
            $result['currentDate'] = date("d/m/Y", strtotime('first day of this month')) . ' ... ' . date("d/m/Y", strtotime('last day of this month'));
            break;
        case 6:
            $condition[0] = "AND date(date_send ) BETWEEN '" . date("Y-m-d", strtotime("first day of previous month")) . "' AND '" . date("Y-m-d", strtotime("last day of previous month")) . "'";
            $condition[1] = "  AND cl.date BETWEEN '" . date("Y-m-d", strtotime("first day of previous month")) . "' AND '" . date("Y-m-d", strtotime("last day of previous month")) . "'";
            $result['currentDate'] = date("d/m/Y", strtotime('first day of previous month')) . ' ... ' . date("d/m/Y", strtotime('last day of previous month'));
            break;
        case 7:
            $condition[0] = "AND date(date_send ) BETWEEN '" . $_POST['from'] . "' AND '" . $_POST['to'] . "'";
            $condition[1] = "  AND cl.date BETWEEN '" . $_POST['from'] . "' AND '" . $_POST['to'] . "'";
            $result['currentDate'] = date("d/m/Y", strtotime($_POST['from'])) . ' ... ' . date("d/m/Y", strtotime($_POST['to']));
            break;
        default:
            break;
    }
    switch ($_POST['tab']) {
        case '#visits':
            $stmt1 = $conn->prepare("SELECT c.id_customer,firstname , lastname ,emailc,country,phone, (SELECT count(*) FROM `transactionsc` as tc where c.id_customer= tc.id_customer )as buys_count,date_start FROM customers c,users u, connecting_log cl WHERE cl.id_customer NOT IN (SELECT sender FROM messages WHERE sender_role=4 " . $condition[0] . " ) " . $condition[1] . "  AND u.id_user = cl.id_customer AND c.id_account=:AC  AND c.id_customer=u.id_profile AND u.profile=4 GROUP BY cl.id_customer");
            $stmt1->bindParam(':AC', intval($_POST['id_account']));
            $stmt1->execute();
            $result['table2'] = $stmt1->fetchAll();
            $stmt1 = $conn->prepare("SELECT c.id_customer,firstname , lastname ,emailc,country,phone, (SELECT count(*) FROM `transactionsc` as tc where c.id_customer= tc.id_customer )as buys_count,date_start FROM customers c,users u, connecting_log cl WHERE cl.id_customer  IN (SELECT sender FROM messages WHERE sender_role=4 " . $condition[0] . " )  " . $condition[1] . "  AND u.id_user = cl.id_customer AND c.id_account=:AC  AND c.id_customer=u.id_profile AND u.profile=4 GROUP BY cl.id_customer");
            $stmt1->bindParam(':AC', intval($_POST['id_account']));
            $stmt1->execute();
            $result['table3'] = $stmt1->fetchAll();
            break;
        case '#chat_activity':
            //page 2
            $stmt = $conn->prepare("SELECT count(*) as sended from messages msg,users u,customers c where c.id_customer = u.id_profile AND u.id_user = msg.sender AND msg.sender_role=4 AND c.id_account=:AC " . $condition[0]);
            $stmt->bindParam(':AC', intval($_POST['id_account']));
            $stmt->execute();
            $stat = $stmt->fetchObject();
            $result['customerSend'] =  $stat->sended;
            $stmt = $conn->prepare("SELECT count(*) as sended from messages msg,users u,consultants c where c.id_consultant = u.id_profile AND u.id_user = msg.sender AND msg.sender_role=3 AND c.id_account=:AC " . $condition[0]);
            $stmt->bindParam(':AC', intval($_POST['id_account']));
            $stmt->execute();
            $stat = $stmt->fetchObject();
            $result['consultantSend'] = $stat->sended;
            
            $stmt = $conn->prepare("SELECT 
                AVG(AV.average_time) as moy,
                MAX(AV.average_time) as max,
                MIN(AV.average_time) as min
                from ( SELECT  TIME_TO_SEC(
                            TIMEDIFF(
                            COALESCE((SELECT msg.date_send from messages msg JOIN users u on msg.sender=u.id_user and u.profile =3 JOIN consultants c on c.id_consultant=u.id_profile and c.id_account=:IDU WHERE msg.sender_role =3 " . $condition[0] . " and msg.receiver=m.sender and m.receiver=msg.sender AND msg.date_send > m.date_send order by msg.id_message LIMIT 1),now()),m.date_send)) as average_time from messages m  where m.sender_role = 4 " . $condition[0] . ") AV");
            //$stmt = $conn->prepare("SELECT AVG(AV.average_time) as moy,MAX(AV.average_time) as max,MIN(AV.average_time) as min from ( SELECT TIME_TO_SEC(TIMEDIFF(CASE when (SELECT msg.date_send from messages msg where msg.receiver=m.sender and msg.date_send>m.date_send AND msg.sender_role=3 " . $condition[0] . " order by msg.id_message LIMIT 1) is not null then (SELECT msg.date_send from messages msg where  msg.receiver=m.sender and msg.date_send>m.date_send AND msg.sender_role=3 " . $condition[0] . " order by msg.id_message  LIMIT 1)else now() end,m.date_send )) as average_time from messages m ,consultants c,users u where c.id_consultant = u.id_profile AND m.sender_role=4 AND m.sender=u.id_user " . str_replace("msg", "m", $condition[0]) . " order by m.id_message) AV");
            $stmt->bindParam(':IDU', intval($_POST['id_account']));
            $stmt->execute();
            $average = $stmt->fetch(PDO::FETCH_ASSOC);
            foreach ($average as $key => $av) {
                $jours = round($av / 86400);
                $secondes = $av - ($jours * 86400);
                $result[$key] = "(" . $jours . "j)" . gmdate("H:i:s", $secondes);
            }
            $stmtC = $conn->prepare("SELECT count(*) as total,msg.*,cu.* from messages msg,users u,customers cu where msg.sender=u.id_user AND cu.id_customer=u.id_profile AND  msg.sender_role=4 AND msg.status=0 AND NOT EXISTS (SELECT * from messages where sender=msg.receiver AND receiver=msg.sender AND date_send>msg.date_send) AND cu.id_account=:IDU  " . $condition[0] . " GROUP BY receiver  ORDER BY msg.date_send DESC");
            $stmtC->bindParam(':IDU', intval($_POST['id_account']));
            $stmtC->execute();
            $customers = $stmtC->fetchAll();
            $table1 = array();
            if (!empty($customers)) {
                foreach ($customers  as $customer) {
                    $stmt = $conn->prepare("SELECT *  from consultants cu,users u where cu.id_consultant=u.id_profile AND u.id_user=:AC AND u.profile=3");
                    $stmt->bindparam(':AC', intval($customer[4]));
                    $stmt->execute();
                    $consultant = $stmt->fetchObject();
                    $button = '<button class="btn btn-sm waves-effect waves-light btn-color" onClick="MyWindow=window.open(' . "'" . 'superConversation.php?sender=' . $customer['sender'] . '&receiver=' . $consultant->id_user . '' . "'" . ",'" . $customer['sender'] . "-" . $consultant->id_user . "','width=800,height=500'); return false;" . '"' . 'href="#" class="dropdown-item">Conversation</button>';
                    array_push($table1, array($customer['firstname'] . " " . $customer['lastname'], $customer['total'], $customer['content'], $customer['date_send'], $consultant->firstname . " " . $consultant->lastname, $button));
                }
            }
            $result['table1'] = $table1;
            $stmt = $conn->prepare("SELECT 
            AVG(AV.average_time) as moy
                from ( SELECT  TIME_TO_SEC(
                TIMEDIFF(
                    COALESCE(
                    (SELECT msg.date_send from messages msg JOIN users u on msg.sender=u.id_user and u.profile =3 JOIN consultants c on c.id_consultant=u.id_profile and c.id_account=:IDU WHERE msg.sender_role =3  and msg.receiver=m.sender and m.receiver=msg.sender AND msg.date_send > m.date_send order by msg.id_message LIMIT 1),now()),m.date_send)) as average_time from messages m  where m.sender_role = 4 " . $condition[0] . "  GROUP BY m.sender) AV;");
            $stmt->bindParam(':IDU', intval($_POST['id_account']));
            $stmt->execute();
            $pick = $stmt->fetchObject();
            $jours = round($pick->moy / 86400);
            $secondes = $pick->moy - ($jours * 86400);
            $result['pick'] = "(" . $jours . "j)" . gmdate("H:i:s", $secondes);
            /* $pickTot = array();
            foreach ($statCustomer as $consultant) {
                $stmt = $conn->prepare("SELECT AVG(TIME_TO_SEC(TIMEDIFF(CASE when (SELECT msg.date_send from messages msg where msg.sender=:IDU and msg.receiver=m.sender and msg.date_send>m.date_send " . $condition[0] . " order by msg.id_message LIMIT 1) is not null then (SELECT msg.date_send from messages msg where msg.sender=:IDU and msg.receiver=m.sender and msg.date_send>m.date_send " . $condition[0] . " order by msg.id_message  LIMIT 1)else now() end,m.date_send ))) as average_time from messages m where m.receiver=:IDU " . str_replace("msg", "m", $condition[0]) . " order by m.id_message");
                $stmt->bindparam(":IDU", intval($consultant['id_user']));
                $stmt->execute();
                $av = $stmt->fetchObject();
                $stmt = $conn->prepare("SELECT sender,receiver,date_send,TIME_TO_SEC(TIMEDIFF(
                    CASE when (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender=firstMsg.receiver order by date_send LIMIT 1) is not null then (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender = firstMsg.receiver order by date_send LIMIT 1)else now() end,firstMsg.date_send )) as pick from (SELECT sender,receiver ,date_send from messages where  receiver=:IDU AND sender_role=4 GROUP BY sender) as firstMsg  where  " . str_replace("AND date(date_send )", "date(date_send)", $condition[0]));
                $stmt->bindparam(":IDU", intval($consultant['id_user']));
                $stmt->execute();
                $pick = $stmt->fetchAll();

                $stmt = $conn->prepare("SELECT count(*) as sale ,SUM(c.final_price) as total_price from(SELECT t.final_price as final_price,(SELECT receiver FROM messages msg, users u  WHERE u.id_profile=t.id_customer AND u.profile=4 AND msg.sender=u.id_user AND msg.date_send<t.date_add  " . $condition[0] . " ORDER BY date_send DESC LIMIT 1) as receiver FROM transactionsc t,customers c WHERE t.id_customer=c.id_customer AND c.id_account=:AC " . str_replace("date_send", "t.date_add", $condition[0]) . ") as c WHERE c.receiver=:IDU ");
                $stmt->bindparam(":AC", intval($_POST['id_account']));
                $stmt->bindparam(":IDU", intval($consultant['id_user']));
                $stmt->execute();
                $Sales = $stmt->fetchObject();
                if (!empty($pick)) {
                    $jours = round(array_sum(array_column($pick, 'pick')) / 86400);
                    $secondes = array_sum(array_column($pick, 'pick')) - ($jours * 86400);
                    $pickC = "(" . $jours . "j)" . gmdate("H:i:s", $secondes);
                    $pickTot[] = round(array_sum(array_column($pick, 'pick')));
                } else {
                    $pickC = "(0j)" . gmdate("H:i:s", 0);
                    $pickTot[] = 0;
                }

                $jours = round($av->average_time / 86400);
                $secondes = $av->average_time - ($jours * 86400);
                array_push($table, array($consultant['pseudo'],  isset($consultant['nb_received']) ? $consultant['nb_received'] : 0, isset($consultant['nb_send']) ? $consultant['nb_send'] : 0, "(" . $jours . "j)" . gmdate("H:i:s", $secondes),  $pickC, $consultant['total_unread'] ? $consultant['total_unread'] : 0, $Sales->sale, is_null($Sales->total_price) ? 0 : $Sales->total_price, "<button class='btn btn-sm waves-effect waves-light btn-color expertDetails' data-id='" . $consultant['id_user'] . "'>Details</button>"));
            }
            $result['table'] = $table;
            $sum = array_sum($pickTot) / count($pickTot);
            $jours = round($sum / 86400);
            $secondes = $sum - ($jours * 86400);
            $result['pick'] = "(" . $jours . "j)" . gmdate("H:i:s", $secondes); */
            break;
        case '#chat_activity_per_consultant':
            $table = array();
            $stmt = $conn->prepare("SELECT u.id_user,c.id_consultant,c.pseudo,c.firstname,c.lastname,
                (SELECT count(msg.id_message) from messages msg where msg.sender=u.id_user AND msg.sender_role=3 " . $condition[0] . " GROUP by msg.sender) as nb_send ,
                (SELECT count(msg.id_message) from messages msg where msg.receiver=u.id_user AND msg.receiver_role=3 " . $condition[0] . " GROUP by msg.receiver) as nb_received,
                (SELECT count(msg.id_message) from messages msg where msg.receiver=u.id_user AND msg.receiver_role=3 AND msg.status = 0 " . $condition[0] . " GROUP by msg.receiver) as total_unread,
                (SELECT count(msg.id_message) from messages msg where msg.receiver=u.id_user AND msg.sender_role=3 AND msg.status = 0 AND NOT EXISTS (SELECT m.date_send from messages m WHERE msg.sender_role =3  and m.sender=msg.receiver and m.receiver=msg.sender AND msg.date_send < m.date_send order by msg.id_message LIMIT 1) " . $condition[0] . " GROUP by msg.sender) as total_unreply
                from users u,consultants c where c.id_consultant = u.id_profile  AND u.profile = 3 AND c.id_account=:AC GROUP by u.id_user");
            $stmt->bindParam(':AC', intval($_POST['id_account']));
            $stmt->execute();
            $statCustomer = $stmt->fetchAll();
            foreach ($statCustomer as $consultant) {
                $stmt = $conn->prepare("SELECT AVG(TIME_TO_SEC(TIMEDIFF(CASE when (SELECT msg.date_send from messages msg where msg.sender=:IDU and msg.receiver=m.sender and msg.date_send>m.date_send " . $condition[0] . " order by msg.id_message LIMIT 1) is not null then (SELECT msg.date_send from messages msg where msg.sender=:IDU and msg.receiver=m.sender and msg.date_send>m.date_send " . $condition[0] . " order by msg.id_message  LIMIT 1)else now() end,m.date_send ))) as average_time from messages m where m.receiver=:IDU " . str_replace("msg", "m", $condition[0]) . " order by m.id_message");
                $stmt->bindparam(":IDU", intval($consultant['id_user']));
                $stmt->execute();
                $av = $stmt->fetchObject();
                $stmt = $conn->prepare("SELECT sender,receiver,date_send,TIME_TO_SEC(TIMEDIFF(
                    CASE when (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender=firstMsg.receiver order by date_send LIMIT 1) is not null then (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender = firstMsg.receiver order by date_send LIMIT 1)else now() end,firstMsg.date_send )) as pick from (SELECT sender,receiver ,date_send from messages where  receiver=:IDU AND sender_role=4 GROUP BY sender) as firstMsg  where  " . str_replace("AND date(date_send )", "date(date_send)", $condition[0]));
                $stmt->bindparam(":IDU", intval($consultant['id_user']));
                $stmt->execute();
                $pick = $stmt->fetchAll();

                $stmt = $conn->prepare("SELECT count(*) as sale ,SUM(c.final_price) as total_price from(SELECT t.final_price as final_price,(SELECT receiver FROM messages msg, users u  WHERE u.id_profile=t.id_customer AND u.profile=4 AND msg.sender=u.id_user AND msg.date_send<t.date_add  " . $condition[0] . " ORDER BY date_send DESC LIMIT 1) as receiver FROM transactionsc t,customers c WHERE t.id_customer=c.id_customer AND c.id_account=:AC " . str_replace("date_send", "t.date_add", $condition[0]) . ") as c WHERE c.receiver=:IDU ");
                $stmt->bindparam(":AC", intval($_POST['id_account']));
                $stmt->bindparam(":IDU", intval($consultant['id_user']));
                $stmt->execute();
                $Sales = $stmt->fetchObject();
                if (!empty($pick)) {
                    $jours = round(array_sum(array_column($pick, 'pick')) / 86400);
                    $secondes = array_sum(array_column($pick, 'pick')) - ($jours * 86400);
                    $pickC = "(" . $jours . "j)" . gmdate("H:i:s", $secondes);
                    $pickTot[] = round(array_sum(array_column($pick, 'pick')));
                } else {
                    $pickC = "(0j)" . gmdate("H:i:s", 0);
                    $pickTot[] = 0;
                }

                $jours = round($av->average_time / 86400);
                $secondes = $av->average_time - ($jours * 86400);
                array_push($table, array($consultant['pseudo'],  isset($consultant['nb_received']) ? $consultant['nb_received'] : 0, isset($consultant['nb_send']) ? $consultant['nb_send'] : 0, "(" . $jours . "j)" . gmdate("H:i:s", $secondes),  $pickC, $consultant['total_unread'] ? $consultant['total_unread'] : 0, $Sales->sale, is_null($Sales->total_price) ? 0 : $Sales->total_price, "<button class='btn btn-sm waves-effect waves-light btn-color expertDetails' data-id='" . $consultant['id_user'] . "'>Details</button>"));
            }
            $result['table'] = $table;
            break;
        case '#customers':
            $Total_uploaded = $conn->prepare("SELECT id_customer,firstname , lastname ,emailc,country,phone, (SELECT count(*) FROM `transactionsc` as tc where c.id_customer= tc.id_customer )as buys_count,date_start from customers c WHERE c.id_account=:IDU " . str_replace("date_send", "c.date_start", $condition[0]));
            $Total_uploaded->bindParam(':IDU', intval($_POST['id_account']));
            $Total_uploaded->execute();
            $total = $Total_uploaded->fetchAll();
            $result['table5'] = $total;

            $Customers = $conn->prepare("SELECT id_customer,firstname , lastname ,emailc,country,phone, (SELECT count(*) FROM `transactionsc` as tc where c1.id_customer= tc.id_customer )as buys_count,date_start from customers c1 where c1.id_account=:IDU AND c1.id_customer IN (SELECT trs.id_customer FROM transactionsc trs,users u WHERE trs.id_customer=u.id_profile AND u.id_user IN (SELECT sender from messages WHERE sender_role=4))  " . str_replace("date_send", "c1.date_start", $condition[0]));
            $Customers->bindParam(':IDU', intval($_POST['id_account']));
            $Customers->execute();
            $cu = $Customers->fetchAll();
            $nbr1 = count($cu);
            foreach ($cu as &$t) {
                $t[4] = isset($trans['countries'][$t['country']]) ? $trans['countries'][$t['country']] : '';
            }
            $result['table7'] = $cu;

            $Contacts_without_purchase = $conn->prepare("SELECT id_customer,firstname ,lastname ,emailc,country,phone, (SELECT count(*) FROM `transactionsc` as tc where c2.id_customer= tc.id_customer )as buys_count,date_start from customers c2 where c2.id_account=:IDU AND c2.id_customer NOT IN (SELECT trs.id_customer FROM transactionsc trs,users u WHERE trs.id_customer=u.id_profile AND u.id_user NOT IN (SELECT sender from messages WHERE sender_role=4))  " . str_replace("date_send", "c2.date_start", $condition[0]));
            $Contacts_without_purchase->bindParam(':IDU', intval($_POST['id_account']));
            $Contacts_without_purchase->execute();
            $contacts = $Contacts_without_purchase->fetchAll();
            $nbr2 = count($contacts);
            $result['table6'] = $contacts;
            break;
        case '#sales':

            $s1 = $conn->prepare("SELECT cu.id_customer,cu.firstname,cu.lastname,cu.country,sum(sc.final_price) as income,count(sc.final_price) as total FROM  transactionsc sc,customers cu WHERE sc.id_customer=cu.id_customer AND cu.id_account=:ID " . str_replace("date_send", "sc.date_add", $condition[0]) . " GROUP BY sc.id_customer ");
            $s1->bindParam(':ID', intval($_POST['id_account']), PDO::PARAM_INT);
            $s1->execute();
            $end_users = $s1->fetchAll(PDO::FETCH_ASSOC);
            $result['VBEnd_user'] = array_map(function($end_user) use ($currencies){
                $total = $end_user['income'];
                if ($end_user['country'] != "" && $_POST['currency'] != $currencies[$end_user['country']]) {
                    var_dump($_POST['currency'] != $currencies[$end_user['country']]);
                    $total = change(['currency'=> $currencies[$end_user['country']],'income'=>$end_user['income']], $_POST['currency']);
                }
                $end_user= array($end_user['firstname'], $end_user['lastname'], $end_user['total'],  $total. '<sup>' . $_POST['currency'] . '</sup>');
                return $end_user;
            },$end_users);

            $s1 = $conn->prepare("SELECT w.name,w.activity,w.id_website,count(sc.final_price) as total,sum(sc.final_price) as income,c.country FROM  websites w LEFT JOIN packages p on p.id_website=w.id_website LEFT JOIN transactionsc sc on  sc.id_package=p.id_package " . str_replace("date_send", "sc.date_add", $condition[0]) . " LEFT JOIN customers c on sc.id_customer=c.id_customer WHERE  w.id_account=:ID GROUP BY w.id_website,c.country");
            $s1->bindParam(':ID', intval($_POST['id_account']), PDO::PARAM_INT);
            $s1->execute();
            $websites = $s1->fetchAll(PDO::FETCH_ASSOC);
            $result['VBwebsites'] = array_map(function($website) use ($currencies){
                $total = $website['income'];
                if ($website['country'] != "" && $_POST['currency'] != $currencies[$website['country']]) {
                    $total = change(['currency'=> $currencies[$website['country']],'income'=>$website['income']], $_POST['currency']);
                }
                $website= array($website['name'], $website['activity'], $website['total'], $total . '<sup>' . $_POST['currency'] . '</sup>');
                return $website;
            },$websites);

            $s1 = $conn->prepare("SELECT c.`id_consultant`, c.`pseudo`, u.id_user
                FROM `consultants` c
                LEFT JOIN `users` u ON u.profile = 3 AND u.id_profile = c.id_consultant
                WHERE c.`id_account` = :id;");
            //$s1 = $conn->prepare("SELECT SUM(t.final_price) as total_price ,COUNT(t.final_price) as total_count,t.expert,t.country FROM (SELECT c.country,t.final_price,(SELECT co.pseudo FROM messages m, users us JOIN consultants co on co.id_consultant=us.id_profile WHERE m.sender = u.id_user AND m.date_send >= t.date_add ORDER BY m.id_message DESC LIMIT 1) as expert FROM transactionsc t JOIN customers c on t.id_customer=c.id_customer and c.id_account=:id JOIN users u on u.id_profile = c.id_customer AND u.profile=4 " . str_replace("date_send", "t.date_add", $condition[0]) . ") t GROUP BY t.expert");
            $s1->bindParam(':id', intval($_POST['id_account']), PDO::PARAM_INT);
            $s1->execute();
            $experts = $s1->fetchAll(PDO::FETCH_ASSOC);
            $s1 = $conn->prepare("SELECT t.final_price,m.sender,cus.country
                FROM `transactionsc` t 
                INNER JOIN `customers` cus ON t.id_customer = cus.id_customer 
                LEFT JOIN `users` us ON us.profile = 4 AND us.id_profile = cus.id_customer 
                LEFT JOIN `messages` m ON m.id_message = (SELECT d.id_message FROM messages d WHERE d.receiver = us.id_user AND d.receiver_role = 4 AND d.date_send < t.date_add ORDER BY d.date_send DESC LIMIT 1)
                WHERE
                    t.status = 1 AND
                    final_price > 0 AND
                    cus.id_account = :id
                    " . str_replace("date_send", "t.date_add", $condition[0]));
            $s1->bindParam(':id', intval($_POST['id_account']), PDO::PARAM_INT);
            $s1->execute();
            $sales = $s1->fetchAll(PDO::FETCH_ASSOC);
            $sales = array_map(function ($sale) use ($currencies)
            {
                if ($sale['country'] != "" && $_POST['currency'] != $currencies[$sale['country']]) {
                    $sale['final_price'] = change(['currency'=> $currencies[$sale['country']],'income'=>$sale['final_price']], $_POST['currency']);
                }
                return $sale;
            },$sales);
            $result['VBexpert'] = array_map(function($expert) use ($sales){
                $expertSales = array_filter($sales,function($sale)use($expert){ return $sale['sender'] == $expert['id_user'];});
                $total = array_sum(array_column($expertSales,'final_price'));
                $expert= array($expert['pseudo'], count($expertSales), $total . '<sup>' . $_POST['currency'] . '</sup>');
                return $expert; 
            },$experts);
            $result['sales']['total'] = array_sum(array_column($result['VBexpert'],1));
            $result['sales']['amount'] = array_sum(array_column($result['VBexpert'],2));

            $st = $conn->prepare("SELECT sum(tc.final_price) as income,c.country from transactionsc tc join customers c on tc.id_customer=c.id_customer where c.id_account=:IDA " . str_replace("date_send", "tc.date_add", $condition[0]) . "GROUP BY c.country ");
            $st->bindparam(":IDA", intval($_POST['id_account']));
            $st->execute();
            $incomes = $st->fetchAll(PDO::FETCH_ASSOC);
            $result['incomes'] = array();
            if (!empty($incomes)) {
                foreach ($incomes as $income) {
                    $result['incomes'][] = array($income['country']!=NULL?$currencies[$income['country']]: $_POST['currency'], $income['income']);
                }
            }
            break;
        default:
            break;
    }
    $html = $result;
} elseif ($type == "getCustomers") {
    session_start();
    $statement = "SELECT id_customer,firstname , lastname ,emailc,country,phone, (SELECT count(*) FROM `transactionsc` as tc where c.id_customer= tc.id_customer )as buys_count,date_start,photo,u.id_user  FROM `customers` as c ,users u WHERE c.id_account =" . $_SESSION['id_account'] . " AND u.id_profile=c.id_customer AND u.profile=4 ";
    $conditions = array();
    if (isset($_POST['firstName']) && $_POST['firstName'] != "") {
        $conditions[] = "firstname ='" . $_POST['firstName'] . "'";
    }
    if (isset($_POST['lastName']) && $_POST['lastName'] != "") {
        $conditions[] = "lastname ='" . $_POST['lastName'] . "'";
    }
    if (isset($_POST['email']) && $_POST['email'] != "") {
        $conditions[] = "emailc = '" . $_POST['email'] . "'";
    }
    if (isset($_POST['SelectBox'])) {
        $countries = $_POST['SelectBox'];
        foreach ($countries as $i => $country) {
            $countries[$i] = "'" . $country . "'";
        }
        $conditions[] = "country IN (" . implode(',', array_values($countries)) . ")";
    }
    if (isset($_POST['gender']) && $_POST['gender'] != "") {
        $conditions[] = "gender = " . $_POST['gender'];
    }
    if (isset($_POST['phone']) && $_POST['phone'] != "") {
        $conditions[] = "phone = " . $_POST['phone'];
    }
    if (isset($_POST['from']) && $_POST['from'] != "") {
        $conditions[] = "date_start > '" . $_POST['from'] . "'";
    }
    if (isset($_POST['to']) && $_POST['to'] != "") {
        $conditions[] = "date_start < '" . $_POST['to'] . "'";
    }
    if (isset($_POST['addWay']) && $_POST['addWay'] != "") {
        if ($_POST['addWay'] == 'Imported') {
            $conditions[] = "importation_id =" . $_POST['importation'];
        } elseif ($_POST['addWay'] == 'Unit') {
            $conditions[] = "importation_id IS NULL";
        } elseif ($_POST['addWay'] == 'affiliates') {
            $conditions[] = "(SELECT l.id_customer FROM leads l WHERE  l.id_customer=c.id_customer) IS NOT NULL";
        } elseif ($_POST['addWay'] == 'link') {
            $conditions[] = "id_link =" . $_POST['link'];
        }
    }
    if (isset($_POST['publisher']) && $_POST['publisher'] != "") {
        $conditions[] = "(SELECT l.id_customer FROM leads l,contributors co,users u WHERE  l.id_customer=c.id_customer AND l.id_contributor=u.id_user AND co.id_contributor=u.id_profile AND u.profile=6 AND co.id_publisher=" . $_POST['publisher'] . ") IS NOT NULL";
    }
    if (isset($_POST['balanceOp'])) {
        if (isset($_POST['balance']) && $_POST['balance'] != "") {
            $conditions[] = "balance" . $_POST['balanceOp'] . "" . $_POST['balance'];
        } elseif (isset($_POST['minbalance']) && $_POST['minbalance'] != "" && isset($_POST['maxbalance']) && $_POST['maxbalance'] != "") {
            $conditions[] = "balance <" . $_POST['maxbalance'] . " AND balance >" . $_POST['minbalance'];
        }
    }

    if (isset($_POST['Before']) && $_POST['Before'] != "") {
        $st = $conn->prepare("SELECT  maxTable.id_customer as id  FROM (SELECT *,max(date_add) as maxdate FROM `transactionsc` GROUP BY id_customer) as maxTable where maxTable.maxdate <= " . "'" . $_POST['Before'] . "'");
        $st->execute();
        $customersId = $st->fetchAll();
        $conditions[] = "id_customer IN (" . implode(',', array_column($customersId, 'id')) . ")";
    }
    if (isset($_POST['After']) && $_POST['After'] != "") {
        $st = $conn->prepare("SELECT  maxTable.id_customer as id  FROM (SELECT id_customer,max(date_add) as maxdate FROM `transactionsc` GROUP BY id_customer) as maxTable where maxTable.maxdate >= " . "'" . $_POST['After'] . "'");
        $st->execute();
        $customersId = $st->fetchAll();
        $conditions[] = "id_customer IN (" . implode(',', array_column($customersId, 'id')) . ")";
    }
    if (isset($_POST['SelectOffre'])) {
        $offers = $_POST['SelectOffre'];
        foreach ($offers as $i => $offer) {
            $offers[$i] = "'" . $offer . "'";
        }
        $st = $conn->prepare("SELECT id_customer as id FROM `offers_customers`  where id_offer IN(" . implode(',', array_values($offers)) . ")");
        $st->execute();
        $customersId = $st->fetchAll();
        $conditions[] = "id_customer IN (" . implode(',', array_column($customersId, 'id')) . ")";
    }
    if (isset($_POST['SelectPackages'])) {
        $packages = $_POST['SelectPackages'];
        foreach ($packages as $i => $package) {
            $packages[$i] = "'" . $package . "'";
        }
        $st = $conn->prepare("SELECT DISTINCT id_customer as id FROM `transactionsc`  where id_package IN(" . implode(',', array_values($packages)) . ")");
        $st->execute();
        $customersId = $st->fetchAll();
        $conditions[] = "id_customer IN (" . implode(',', array_column($customersId, 'id')) . ")";
    }

    foreach ($conditions as $key => $condition) {
        $statement .= ' AND ' . $condition;
    }
    if (isset($_POST['operator'])) {
        if (isset($_POST['buyCount']) && $_POST['buyCount'] != "") {
            $statement = "SELECT * FROM (" . $statement . ") AS tb where tb.buys_count " . $_POST['operator'] . "" . $_POST['buyCount'];
        } elseif (isset($_POST['maxbuyCount']) && $_POST['maxbuyCount'] != "" && isset($_POST['minbuyCount']) && $_POST['minbuyCount'] != "") {
            $statement = "SELECT * FROM (" . $statement . ") AS tb where tb.buys_count <" . $_POST['maxbuyCount'] . " AND tb.buys_count >" . $_POST['minbuyCount'];
        }
    }
    $st = $conn->prepare($statement);
    $st->execute();
    $customers = $st->fetchAll();
    $html = array();
    foreach ($customers as $customer) {
        $html[] = array('<div class="custom-control custom-checkbox"><input class="custom-control-input" value="' . $customer['0'] . '" id="' . $customer['0'] . '" name="customers_ids[]" type="checkbox" /><label class="custom-control-label" for="' . $customer['0'] . '"></label>' . $customer['0'] . '</div>', '<img src="../uploads/customers/' . $customer['photo'] . '"  width="24px" alt="user-img" class="img-circle" />' . $customer['firstname'], $customer['2'], $customer['3'], $customer['4'], $customer['5'], $customer['6'], $customer['7'], '<a href="customer.php?id=' . $customer['id_customer'] . '" class="btn btn-sm waves-effect waves-light btn-color m-l-5 " title="' . $trans['edit'] . '"><i class="mdi mdi-account-edit"></i></a><a href="javascript:void(0)" data-id="' . $customer['id_user']  . '" class="btn btn-sm waves-effect waves-light btn-color sendMSG m-l-5" title="' . $trans['sendMSG'] . '"><i class="mdi mdi-message-text"></i></a><a href="javascript:void(0)" data-id="' . $customer['id_user']  . '" class="btn btn-sm waves-effect waves-light btn-color sendOffer m-l-5" title="' . $trans['Sendoffers'] . '"><i class="mdi mdi-package-variant"></i></a><a type="button" href="javascript:void(0)" class="btn btn-sm waves-effect waves-light btn-danger delete-button m-l-5" title="' . $trans['delete'] . '" data-id="' . $customer['id_customer'] . '"><i class="mdi mdi-delete"></i></a>');
    }
}

echo json_encode($html);
