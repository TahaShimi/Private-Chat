<?php
require "../../vendor/autoload.php";
include('../../init.php');

use Classes\DataService;
// BASE DE DONNEES
try {
    $ds = new DataService();
    $conn = $ds->conn;
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$type = $_POST["type"];
$return = '';
$cust = '';
$user = '';
if ($type == "getOffers") {
    $receiver_role = intval($_POST['receiver_role']);
    if ($receiver_role == 4) {
        $id_user = intval($_POST['id']);
        $stmt = $conn->prepare("SELECT a.*, b.*, c.`name` AS website_name FROM `users` a LEFT JOIN `customers` b ON a.`id_profile` = b.`id_customer` LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE a.`id_user` = :ID");
        $stmt->bindParam(':ID', $id_user, PDO::PARAM_INT);
    }elseif ($receiver_role == 7) {
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
    $return = $results;
} else if ($type == "newCustomer") {
    $sender = intval($_POST['sender']);
    $receiver = intval($_POST['receiver']);
    $id_company = intval($_POST['id_group']);

    $stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM messages where (u.id_user = sender or receiver =:id) and status=0 and sender=:id ) as unread_messages_count from customers c join users u on u.id_profile=c.id_customer where c.id_account=:ia and u.profile=4 and u.id_user=:id");
    $stmt->bindparam(":ia",  $id_company);
    $stmt->bindparam(":id",  $receiver);
    $stmt->execute();
    $customer = $stmt->fetchObject();
    $stmt2 =  $conn->prepare("SELECT Count(*) as total_unread_messages from messages where receiver=:id and sender=:ir and status=0");
    $stmt2->bindparam(":id", $sender, PDO::PARAM_INT);
    $stmt2->bindparam(":ir", $receiver, PDO::PARAM_INT);
    $stmt2->execute();
    $dta = $stmt2->fetch();
    $photo = $customer->photo != null? $customer->photo : 'img-1.png' ;
    if ($customer) {
        $display = $dta[0] == '0' ? 'display:none' : '';
        $cust = $customer->id_profile;
        $user = $customer->id_user;
        $return = [
            'html' => '<li id="customer-li-' . $customer->id_user . '" class="customers-li" data-full-name="' . $customer->firstname . " " . $customer->lastname . '"><a href="javascript:void(0)" data-unread-message-count="' . $customer->unread_messages_count . '" data-id="' . $customer->id_user . '" data-full-name="' .  $customer->firstname . " " . $customer->lastname . '" data-avatar="' . $photo . '" data-balance="' . $customer->balance . '" class="chat_item customers-items"><img src="../uploads/customers/' . $photo . '" alt="user-img" class="img-circle"><span class="_51lp _3z_5 _5ugh" id="messagesCount_' . $customer->id_user . '" style="' . $display . '">' . $dta[0] . '</span><span>' . $customer->firstname . " " . $customer->lastname . '<small class="text-success" id="customer-status-' . $customer->id_user . '">' . ($trans["online"]) . '</small><div class="notify" id="new-message-' . $customer->id_user . '"> <span class="heartbit" id="flushing-message-' . $customer->id_user . '"></span> <span class="point hide" id="stable-message-' . $customer->id_user . '"></span></div></span></a></li>',
            'user' => $user,
            'customer' => $cust
            ];
    }
} else if ($type == "getInfo") {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT c.gender,c.firstname,c.lastname,c.emailc,c.phone,c.address,c.country,u.lang,c.photo,w.name from websites w,customers c join users u on u.id_profile=c.id_customer where  c.id_website=w.id_website AND  u.profile=4 and u.id_user =:id");
    $stmt->bindparam(":id",  $id);
    $stmt->execute();
    $customer = $stmt->fetchObject();
    $customer->gender == 1 ? $customer->gender = $trans["male"] : $customer->gender = $trans["female"];
    if ($customer->lang == 'en') {
        $customer->lang = $trans["english"];
    } else if ($customer->lang == 'fr') {
        $customer->lang = $trans["french"];
    }
    $customer->country = $trans["countries"][$customer->country];
    $return = $customer;
} 



echo json_encode($return);
