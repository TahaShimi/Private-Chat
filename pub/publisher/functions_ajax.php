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
$action = $_POST['action'];
$result = "";
switch ($action) {
    case 'getWebsites':
        $val = intval($_POST['val']);
        $val = explode('-', $_POST['val']);
        $stmt2 = $conn->prepare("SELECT w.id_website as id,w.name FROM `websites` w,accounts a WHERE w.id_account=a.id_account AND a.id_account =$val[1] AND a.business_name LIKE '$val[0]%'");
        $stmt2->execute();
        $websites['existe'] = $stmt2->fetchAll() ? 1 : 0;
        $stmt2 = $conn->prepare("SELECT id_website as id,name FROM `websites` WHERE id_account=:id");
        $stmt2->bindParam(':id', $val[1], PDO::PARAM_INT);
        $stmt2->execute();
        $websites['websites'] = $stmt2->fetchAll();
        $result = $websites;
        break;
    case 'getPackeges':
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("SELECT p.id_package,p.title,pp.price,pp.currency FROM packages p,packages_price pp WHERE pp.id_package=p.id_package AND p.id_website=:id AND p.visible=1 AND pp.primary=1");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $packages = $stmt2->fetchAll();
        foreach ($packages as &$package) {
            $stmt2 = $conn->prepare("SELECT * FROM offers WHERE id_package=:id AND discount=100");
            $stmt2->bindParam(':id', $package['id_package'], PDO::PARAM_INT);
            $stmt2->execute();
            $offre = $stmt2->fetch();
            if ($offre) {
                $package['price'] = 0;
            }
        }
        $result = $packages;
        break;
    case 'getWebsites_contri':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);
        $stmt2 = $conn->prepare("SELECT id_website as id,name FROM publishers_programs pa,websites w  WHERE  pa.id_program=w.id_website AND pa.id_publisher=:pb AND w.id_account=:id AND pa.date_end IS NULL");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->bindParam(':pb', $publisher, PDO::PARAM_INT);
        $stmt2->execute();
        $result = $stmt2->fetchAll();
        break;
    case 'get_affiliation':
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("SELECT * FROM `publisher_Affiliation` WHERE id_Affiliation=:id");
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();
        $result = $stmt2->fetch();
        break;
    case 'get_bank':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);
        $stmt2 = $conn->prepare("SELECT * FROM publishers_bank  WHERE id_bank=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $result = $stmt2->fetch();
        break;
    case 'del_bank':

        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("UPDATE publishers_bank SET date_end=NOW()  WHERE id_bank=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();

        $stmt2 = $conn->prepare("SELECT * FROM publishers_bank  WHERE id_bank=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $b = $stmt2->fetch();
        $result = array($b['id_bank'], $b['Benefiary'], $b['Account_currency'], $b['name'], $trans['countries'][$b['country']], $b['address'], $b['IBAN'], $b['date_end'], '');

        break;
    case 'getWebsitesUnique':
        $id = intval($_POST['id']);
        $id_contributor = intval($_POST['contribu']);
        $stmt2 = $conn->prepare("SELECT w.id_website as id,w.name FROM contributors_programs cp,websites w,users u WHERE cp.id_advertiser=:id AND cp.id_contributor=u.id_profile AND u.id_user=:idC AND cp.id_advertiserProgram=w.id_website");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->bindParam(':idC', $id_contributor, PDO::PARAM_INT);
        $stmt2->execute();
        $result = $stmt2->fetchAll();
        break;
    case 'get_contributor':
        $a = array();
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("SELECT c.id_contributor as id,c.pseudo,c.username,c.email,u.password FROM contributors c,users u WHERE u.id_profile=c.id_contributor AND u.profile=6 AND c.id_contributor=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $a['info'] = $stmt2->fetch();
        $stmt2 = $conn->prepare("SELECT w.name,cp.id_advertiserProgram as id FROM contributors_programs cp,websites w WHERE cp.id_contributor=:id AND cp.status=1 AND w.id_website=cp.id_advertiserProgram");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $a['programs'] = $stmt2->fetchAll();
        $result = $a;
        break;
    case 'Reprendre_contributor':
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("UPDATE contributors SET date_end=NULL WHERE id_contributor=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $stmt2 = $conn->prepare("UPDATE contributors_programs  SET status=1 WHERE id_contributor=:id AND status=2");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $stmt = $conn->prepare("SELECT  *  FROM contributors  WHERE id_contributor=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $contributor = $stmt->fetchObject();
        $stmt = $conn->prepare("SELECT  *  FROM contributors_programs cp,websites w WHERE  w.id_website=cp.id_advertiserProgram AND cp.id_contributor=:id AND cp.status=1");
        $stmt->bindParam(':id', intval($id));
        $stmt->execute();
        $programs = $stmt->fetchAll();
        $result = array($contributor->id_contributor, $contributor->pseudo, $contributor->email, $contributor->date_add, implode(',', array_column($programs, 'name')), $contributor->date_end, '<a href="#" data-id="' . $contributor->id_contributor . '" class="badge badge-pill badge-warning Add">Add program</a><a href="#" type="button" data-toggle="modal" data-target="#edit" data-id="' . $contributor->id_contributor . '" class="badge badge-pill badge-info edit">Edit</a><a href="#" data-id="' . $contributor->id_contributor . '" class="badge badge-pill badge-danger Stop">Stop</a>');

        break;
    case 'stop_contributor':
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("UPDATE contributors c SET date_end=NOW() WHERE id_contributor=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $stmt2 = $conn->prepare("UPDATE contributors_programs c SET status=0,date_end=NOW() WHERE id_contributor=:id AND status=1");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $stmt = $conn->prepare("SELECT  *  FROM contributors  WHERE id_contributor=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $contributor = $stmt->fetchObject();
        $stmt = $conn->prepare("SELECT  *  FROM contributors_programs cp,websites w WHERE  w.id_website=cp.id_advertiserProgram AND cp.id_contributor=:id AND cp.status=1");
        $stmt->bindParam(':id', intval($id));
        $stmt->execute();
        $programs = $stmt->fetchAll();
        $result = array($contributor->id_contributor, $contributor->pseudo, $contributor->email, $contributor->date_add, implode(',', array_column($programs, 'name')), $contributor->date_end, '<a href="#" data-id="' . $contributor->id_contributor . '" class="badge badge-pill badge-info Continue">Continue</a>');
        break;
    case 'pause_program':
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("UPDATE contributors_programs c SET status=2 WHERE id=:id ");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();

        $stmt2 = $conn->prepare("SELECT w.name,cp.id_advertiserProgram ,cp.status,cp.date_add,cp.date_end FROM contributors_programs cp,websites w WHERE cp.id=:id AND w.id_website=cp.id_advertiserProgram");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $program = $stmt2->fetchObject();

        $result = 'Paused';
        $button = '<a href="#" class="continue badge badge-pill badge-info" data-id="' . $id . '">Continue</a><a href="#" class="stop badge badge-pill badge-danger" data-id="' . $id . '">Stop</a>';
        $result = array($program->id, $program->name, $result, $program->date_add, $program->date_end, $button);
        break;
    case 'continue_program':
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("UPDATE contributors_programs c SET status=1 WHERE id=:id ");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();

        $stmt2 = $conn->prepare("SELECT w.name,cp.id_advertiserProgram as id,cp.status,cp.date_add,cp.date_end FROM contributors_programs cp,websites w WHERE cp.id=:id AND w.id_website=cp.id_advertiserProgram");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $program = $stmt2->fetchObject();

        $result = 'Active';
        $button = '<a href="#" class="break badge badge-pill badge-info"  data-id="' . $id . '">break</a><a href="#" class="stop badge badge-pill badge-danger" data-id="' . $id . '">Stop</a>';

        $result = array($program->id, $program->name, $result, $program->date_add, $program->date_end, $button);
        break;
    case 'stop_program':
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("UPDATE contributors_programs c SET status=0,date_end=NOW() WHERE id=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();

        $stmt2 = $conn->prepare("SELECT w.name,cp.id_advertiserProgram as id,cp.status,cp.date_add,cp.date_end FROM contributors_programs cp,websites w WHERE cp.id=:id AND w.id_website=cp.id_advertiserProgram");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $program = $stmt2->fetchObject();
        $result = array($program->id, $program->name, "Ended", $program->date_add, $program->date_end, "");
        break;
    case 'setPrograms':
        $id = intval($_POST['id']);
        $program = $_POST['programs'];
        $packages = $_POST['packages'];
        $stmt = $conn->prepare("SELECT  id_advertiserProgram  FROM contributors_programs  WHERE  id_contributor=:id AND status IN (1,2)");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $pgrm = $stmt->fetchAll();
        $stmt2 = $conn->prepare("INSERT INTO contributors_programs(`id_contributor`,`id_advertiserProgram`,status,packages_id,date_add) values (:id1,:id2,1,:pk,NOW())");
            if (!in_array($program, array_column($pgrm, 'id_advertiserProgram'))) {
                $stmt2->bindParam(':id1', $id, PDO::PARAM_INT);
                $stmt2->bindParam(':id2', intval($program), PDO::PARAM_INT);
                $stmt2->bindParam(':pk', json_encode(array_column($packages, 'value'))); 
                $stmt2->execute();
            }
        $stmt = $conn->prepare("SELECT  *  FROM contributors  WHERE id_contributor=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $contributor = $stmt->fetchObject();
        $stmt = $conn->prepare("SELECT  *  FROM contributors_programs cp,websites w WHERE  w.id_website=cp.id_advertiserProgram AND cp.id_contributor=:id AND cp.status=1");
        $stmt->bindParam(':id', intval($id));
        $stmt->execute();
        $programs = $stmt->fetchAll();
        $result = array($contributor->id_contributor, $contributor->pseudo, $contributor->email, $contributor->date_add, implode(',', array_column($programs, 'name')), $contributor->date_end ? $contributor->date_end : '', '<a href="#" data-id="' . $contributor->id_contributor . '" class="badge badge-pill badge-warning Add">Add program</a><a href="#" type="button" data-toggle="modal" data-target="#edit" data-id="' . $contributor->id_contributor . '" class="badge badge-pill badge-info edit">Edit</a><a href="#" data-id="' . $contributor->id_contributor . '" class="badge badge-pill badge-danger Stop">Stop</a>');
        break;
    case 'Stop_website':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);
        $stmt2 = $conn->prepare("UPDATE publishers_programs set date_end=NOW(),status=2 WHERE id=:id ");
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();

        $stmt2 = $conn->prepare("UPDATE contributors_programs set status=0,date_end=NOW() WHERE id_advertiserProgram=(SELECT id FROM  publishers_programs WHERE id=:id) AND id_contributor IN (SELECT id_contributor from contributors where id_publisher=:idp)");
        $stmt2->bindParam(':id', $id);
        $stmt2->bindParam(':idp', $publisher);
        $stmt2->execute();

        $stmt = $conn->prepare("SELECT  pp.id,w.id_website,w.name,w.url,a.business_name,pp.date_start,pp.date_end   FROM publishers_programs pp JOIN websites w ON w.id_website=pp.id_program,accounts a WHERE w.id_account=a.id_account AND pp.id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $ad = $stmt->fetchObject();

        $result = array($ad->business_name, $ad->name, $ad->url, $ad->date_start, $ad->date_end, '<a href="#" data-id="' . $ad->id . '" class="badge badge-info badge-pill Renew">Renew</a>');
        break;
    case 'pause_website':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);
        $stmt2 = $conn->prepare("UPDATE publishers_programs set status=1 WHERE id=:id ");
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();

        $stmt2 = $conn->prepare("UPDATE contributors_programs set status=2 WHERE id_advertiserProgram=(SELECT id FROM  publishers_programs WHERE id=:id) AND id_contributor IN (SELECT id_contributor from contributors where id_publisher=:idp)");
        $stmt2->bindParam(':id', $id);
        $stmt2->bindParam(':idp', $publisher);
        $stmt2->execute();

        $stmt = $conn->prepare("SELECT  pp.id,w.id_website,w.name,w.url,a.business_name,pp.date_start,pp.date_end,pp.status  FROM publishers_programs pp JOIN websites w ON w.id_website=pp.id_program,accounts a WHERE w.id_account=a.id_account AND pp.id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $ad = $stmt->fetchObject();
        if ($ad->status == 0) {
            $button = '<a href="#" class="badge badge-warning badge-pill pause px-2" data-id="' . $ad->id . '">Break</a><a href="#" class="badge badge-danger badge-pill stop" data-id="' . $ad->id . '">Stop</a>';
        } else if ($ad->status == 2) $button = '<a href="#" class="badge badge-info badge-pill Renew" data-id="' . $ad->id . '">Renew</a>';
        else if ($ad->status == 1) $button = '<a href="#" class="badge badge-success badge-pill Continue px-2" data-id="' . $ad->id . '">Continue</a><a href="#" class="badge badge-danger badge-pill stop" data-id="' . $ad->id . '">Stop</a>';

        $result = array($ad->business_name, $ad->name, $ad->url, $ad->date_start, $ad->date_end, $button);
        break;
    case 'continue_website':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);
        $stmt2 = $conn->prepare("UPDATE publishers_programs set status=0 WHERE id=:id ");
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();

        $stmt2 = $conn->prepare("UPDATE contributors_programs set status=1 WHERE id_advertiserProgram=(SELECT id FROM  publishers_programs WHERE id=:id) AND id_contributor IN (SELECT id_contributor from contributors where id_publisher=:idp)");
        $stmt2->bindParam(':id', $id);
        $stmt2->bindParam(':idp', $publisher);
        $stmt2->execute();

        $stmt = $conn->prepare("SELECT  pp.id,w.id_website,w.name,w.url,a.business_name,pp.date_start,pp.date_end,pp.status  FROM publishers_programs pp JOIN websites w ON w.id_website=pp.id_program,accounts a WHERE w.id_account=a.id_account AND pp.id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $ad = $stmt->fetchObject();
        if ($ad->status == 0) {
            $button = '<a href="#" class="badge badge-warning badge-pill pause px-2" data-id="' . $ad->id . '">Break</a><a href="#" class="badge badge-danger badge-pill stop" data-id="' . $ad->id . '">Stop</a>';
        } else if ($ad->status == 2) $button = '<a href="#" class="badge badge-info badge-pill Renew" data-id="' . $ad->id . '">Renew</a>';
        else if ($ad->status == 1) $button = '<a href="#" class="badge badge-success badge-pill Continue px-2" data-id="' . $ad->id . '">Continue</a><a href="#" class="badge badge-danger badge-pill stop" data-id="' . $ad->id . '">Stop</a>';

        $result = array($ad->business_name, $ad->name, $ad->url, $ad->date_start, $ad->date_end, $button);
        break;
    case 'Stop_advertiser':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);
        $stmt2 = $conn->prepare("UPDATE publisher_advertiser set date_end=NOW() WHERE id=:id ");
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();

        $stmt2 = $conn->prepare("UPDATE publishers_programs set date_end=NOW(),status=2 WHERE id_program = (SELECT id_website from publisher_advertiser pa,websites w where pa.id=:id AND w.id_account=pa.id_advertiser) AND id_publisher=:idp");
        $stmt2->bindParam(':id', $id);
        $stmt2->bindParam(':idp', $publisher);
        $stmt2->execute();

        $stmt2 = $conn->prepare("UPDATE contributors_programs set status=0,date_end=NOW() WHERE id_advertiserProgram = (SELECT id_website from publisher_advertiser pa,websites w where pa.id=:id AND w.id_account=pa.id_advertiser) AND id_contributor IN(SELECT id_contributor from contributors WHERE id_publisher=:idp)");
        $stmt2->bindParam(':id', $id);
        $stmt2->bindParam(':idp', $publisher);
        $stmt2->execute();

        $stmt = $conn->prepare("SELECT  pp.id,a.id_account,a.business_name,pp.date_add,pp.date_end  FROM publisher_advertiser pp ,accounts a WHERE pp.id_advertiser=a.id_account AND pp.id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $ad = $stmt->fetchObject();

        if (!$ad->date_end) {
            $button = '<td><a href="#" class="badge badge-danger badge-pill stop" data-id="' . $ad->id . '">Stop</a></td>';
        } else  $button = '<td><a href="#" class="badge badge-info badge-pill Renew" data-id="' . $ad->id . '">Renew</a></td>';

        $result = array($ad->business_name, $ad->date_add, $ad->date_end, $button);
        break;
    case 'renew_website':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);

        $stmt = $conn->prepare("SELECT * FROM  publishers_programs WHERE id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ad = $stmt->fetchObject();

        $stmt2 = $conn->prepare("INSERT INTO publishers_programs(`id_publisher`,`id_Program`,`date_start`,status) values (:id1,:id2,NOW(),0)");
        $stmt2->bindParam(':id1', intval($ad->id_publisher), PDO::PARAM_INT);
        $stmt2->bindParam(':id2', intval($ad->id_program), PDO::PARAM_INT);
        $stmt2->execute();
        $last_id = $conn->lastInsertId();

        $stmt = $conn->prepare("SELECT  pp.status,pp.id,w.id_website,w.name,w.url,a.business_name,pp.date_start,pp.date_end  FROM publishers_programs pp JOIN websites w ON w.id_website=pp.id_program,accounts a WHERE w.id_account=a.id_account AND pp.id=:id");
        $stmt->bindParam(':id', intval($last_id), PDO::PARAM_INT);
        $stmt->execute();
        $ad = $stmt->fetch();
        if ($ad['status'] == 0) {
            $button = '<td><a href="#" class="badge badge-warning badge-pill pause px-2" data-id="' . $ad['id'] . '">Break</a><a href="#" class=" badge badge-danger badge-pill stop" data-id="' . $ad['id'] . '">Stop</a></td>';
        } else if ($ad['status'] == 2) $button = '<td><a href="#" class="badge badge-info badge-pill Renew" data-id="' . $ad['id'] . '">Renew</a></td>';
        else if ($ad['status'] == 1) $button = '<td><a href="#" class="badge badge-success badge-pill Continue px-2" data-id="' . $ad['id'] . '">Continue</a><a href="#" class=" badge badge-danger badge-pill stop" data-id="' . $ad['id'] . '">Stop</a></td>';
        $result = array('id' => $ad['id'], 'table' => array($ad['business_name'], $ad['name'], $ad['url'], $ad['date_start'], $ad['date_end'], $button));

        break;
    case 'renew_advertiser':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);

        $stmt = $conn->prepare("SELECT * FROM  publisher_advertiser WHERE id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ad = $stmt->fetchObject();

        $stmt2 = $conn->prepare("INSERT INTO publisher_advertiser(`id_advertiser`,`id_publisher`,`date_add`) values (:id1,:id2,NOW())");
        $stmt2->bindParam(':id1', intval($ad->id_advertiser), PDO::PARAM_INT);
        $stmt2->bindParam(':id2', intval($ad->id_publisher), PDO::PARAM_INT);
        $stmt2->execute();
        $last_id = $conn->lastInsertId();
        $stmt = $conn->prepare("SELECT pp.id, a.id_account,a.business_name,pp.date_add,pp.date_end  FROM publisher_advertiser pp ,accounts a WHERE pp.id_advertiser=a.id_account AND pp.id=:id");
        $stmt->bindParam(':id', intval($last_id));
        $stmt->execute();
        $ad = $stmt->fetch();
        if (!$ad['date_end']) {
            $button = '<td><a href="#" class="badge badge-danger badge-pill stop" data-id="' . $ad['id'] . '">Stop</a></td>';
        } else  $button = '<td><a href="#" class="badge badge-info badge-pill Renew" data-id="' . $ad['id'] . '">Renew</a></td>';
        $result = array('id' => $last_id, 'table' => array($ad['business_name'], $ad['date_add'], $ad['date_end'], $button));

        break;
    case 'getStat':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['id_publisher']);
        switch ($id) {
            case 1:
                $condition = 'AND cu.date_start="' . date('Y-m-d') . '"';
                break;
            case 2:
                $condition = 'AND cu.date_start="' . date('Y-m-d', strtotime("-1 days")) . '"';
                break;
            case 3:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('monday this week')) . '" AND "' . date("Y-m-d", strtotime('sunday this week')) . '"';
                break;
            case 4:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('last week monday')) . '" AND "' . date("Y-m-d", strtotime('last week sunday')) . '"';
                break;
            case 5:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of this month')) . '" AND "' . date("Y-m-d", strtotime('last day of this month')) . '"';
                break;
            case 6:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of previous month')) . '" AND "' . date("Y-m-d", strtotime('last day of previous month')) . '"';
                break;
            case 7:
                $condition = 'AND cu.date_start BETWEEN "' . $_POST['from'] . '" AND "' . $_POST['to'] . '"';
                break;
        }
        $resultat = array();

        $stmt = $conn->prepare('SELECT  c.id_contributor,c.pseudo,
        (SELECT count(*) FROM leads le,customers cu where le.id_contributor=u.id_user AND le.id_customer=cu.id_customer ' . $condition . ') as total ,
        (SELECT count(*) FROM leads le,customers cu where le.id_contributor=u.id_user AND le.status=2 AND le.id_customer=cu.id_customer ' . $condition . ') as sales
        FROM contributors c,users u WHERE   u.id_profile=c.id_contributor  AND c.id_publisher=:id AND u.profile=6');
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $contributors = $stmt->fetchAll();
        foreach ($contributors as &$contributor) {
            $contributor[4] = $contributor[2] != 0 ? intval($contributor[3]) / intval($contributor[2]) . '%' : '0%';
        }
        $resultat['table2'] = $contributors;
        $stmt = $conn->prepare("SELECT  l.id_lead,CONCAT(cu.firstname,cu.lastname),cu.date_start,l.update_date,l.status,c.pseudo,w.name FROM leads l,websites w,contributors c,users u,accounts a,customers cu WHERE w.id_account=a.id_account AND l.id_contributor=u.id_user AND u.id_profile=c.id_contributor  AND w.id_website=cu.id_website AND c.id_publisher=:id AND cu.id_customer=l.id_customer " . $condition);
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $allLeads = $stmt->fetchAll();
        foreach ($allLeads as &$lead) {
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
        }
        $resultat['table'] = $allLeads;
        $resultat['total'] = count($allLeads);

        $stmt = $conn->prepare("SELECT  l.id_lead,CONCAT(cu.firstname,cu.lastname),cu.date_start,l.update_date,l.status,c.pseudo,w.name FROM leads l,websites w,contributors c,users u,accounts a,customers cu WHERE w.id_account=a.id_account AND l.id_contributor=u.id_user AND u.id_profile=c.id_contributor  AND w.id_website=cu.id_website AND c.id_publisher=:id AND cu.id_customer=l.id_customer AND l.status=2 " . $condition);
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $saleLeads = $stmt->fetchAll();
        foreach ($saleLeads as &$lead) {
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
        }
        $resultat['table1'] = $saleLeads;

        $resultat['sales'] = count($saleLeads);

        $stmt = $conn->prepare("SELECT  *  FROM contributors WHERE id_publisher=:id " . str_replace('cu.date_start', 'date_add', $condition));
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $contributors = $stmt->fetchAll();
        $resultat['table2'] = array();
        foreach ($contributors as $contributor) {
            $stmt = $conn->prepare("SELECT  *  FROM contributors_programs cp,websites w WHERE  w.id_website=cp.id_advertiserProgram AND cp.id_contributor=:id AND cp.status=1");
            $stmt->bindParam(':id', intval($contributor['id_contributor']));
            $stmt->execute();
            $programs = $stmt->fetchAll();
            array_push($resultat['table2'], array($contributor['id_contributor'], $contributor['pseudo'], $contributor['email'], $contributor['date_add'], implode(',', array_column($programs, 'name')), $contributor['date_end']));
        }
        $resultat['Contributors'] = count($contributors);

        $stmt = $conn->prepare("SELECT  *  FROM publisher_advertiser pa,accounts a WHERE pa.id_publisher=:id AND pa.id_advertiser=a.id_account  " . str_replace('cu.date_start', 'pa.date_add', $condition));
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $advertisers = $stmt->fetchAll();
        $resultat['table3'] = array();
        foreach ($advertisers as $advertiser) {
            array_push($resultat['table3'], array($advertiser['id_account'],  $advertiser['business_name'],  $advertiser['date_add'],  $advertiser['date_end']));
        }
        $resultat['advertisers'] = count($advertisers);
        $resultat['table4'] = array();
        $stmt = $conn->prepare('SELECT  c.pseudo,c.id_contributor,
            (SELECT count(*) FROM leads le,customers cu where le.id_contributor=u.id_user AND le.id_customer=cu.id_customer ' . $condition . ') as total ,
            (SELECT count(*) FROM leads le,customers cu where le.id_contributor=u.id_user AND le.status=2 AND le.id_customer=cu.id_customer ' . $condition . ') as sales,
            (SELECT count(*) FROM leads le,customers cu where le.id_contributor=u.id_user AND le.status=1 AND le.id_customer=cu.id_customer ' . $condition . ') as withVisit,
            (SELECT count(*) FROM leads le,customers cu where le.id_contributor=u.id_user AND le.status=0 AND le.id_customer=cu.id_customer ' . $condition . ') as Unconverted
            FROM contributors c,users u WHERE   u.id_profile=c.id_contributor  AND c.id_publisher=:id AND u.profile=6');
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $contributors = $stmt->fetchAll();
        $stmt = $conn->prepare('SELECT  count(*) as total FROM contributors c,leads le, users u WHERE   le.id_contributor=u.id_user AND u.id_profile=c.id_contributor  AND c.id_publisher=:id AND u.profile=6');
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $total = $stmt->fetchObject();
        $total = count($total->total);

        foreach ($contributors as $contributor) {
            $total = count($allLeads);
            $resultat['table4'][] = array($contributor['pseudo'], $contributor['total'], (count($allLeads) != 0 ? round(intval($contributor['total']) / count($allLeads),2) * 100 : 0) . "%", $contributor['sales'], (intval($contributor['total']) != 0 ? round(intval($contributor['sales']) / intval($contributor['total']),2) * 100 : 0) . "%", $contributor['withVisit'], (intval($contributor['total']) != 0 ? round(intval($contributor['withVisit']) / intval($contributor['total']),2) * 100 : 0) . "%", $contributor['Unconverted'], (intval($contributor['total']) != 0 ? round(intval($contributor['Unconverted']) / intval($contributor['total']),2) * 100 : 0) . "%", '<a href="javascript:void(0)" type="button" data-id="' . $contributor['id_contributor'] . '" class="VBCdetails btn btn-sm waves-effect waves-light btn-info">details</a>');
        }

        $resultat['table5'] = array();
        $stmt = $conn->prepare('SELECT a.business_name,a.id_account,
        (SELECT count(*) FROM customers cu ,leads l,contributors c,users u  where a.id_account=cu.id_website  AND l.id_customer=cu.id_customer AND c.id_publisher=:id AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 ' . $condition . ') as total ,
        (SELECT count(*) FROM customers cu ,leads l,contributors c,users u  where a.id_account=cu.id_website AND l.id_lead=l.id_lead AND l.status=2 AND l.id_customer=cu.id_customer AND c.id_publisher=:id AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 ' . $condition . ') as sales,
        (SELECT count(*) FROM customers cu,leads l,contributors c,users u  where a.id_account=cu.id_website AND l.status=1 AND l.id_customer=cu.id_customer AND c.id_publisher=:id AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 ' . $condition . ') as withVisit,
        (SELECT count(*) FROM customers cu,leads l,contributors c,users u  where a.id_account=cu.id_website AND l.status=0 AND l.id_customer=cu.id_customer AND c.id_publisher=:id AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 ' . $condition . ') as Unconverted
        FROM accounts a ');
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $advertisers = $stmt->fetchAll();

        foreach ($advertisers as $advertiser) {
            $resultat['table5'][] = array($advertiser['business_name'], intval($advertiser['total']), (count($allLeads) != 0 ? round(intval($advertiser['total']) / count($allLeads),2) * 100 : 0) . "%", $advertiser['sales'], (intval($advertiser['total']) != 0 ? round(intval($advertiser['sales']) / intval($advertiser['total']),2) * 100 : 0) . "%", $advertiser['withVisit'], (intval($advertiser['total']) != 0 ? round(intval($advertiser['withVisit']) / intval($advertiser['total']),2) * 100 : 0) . "%", $advertiser['Unconverted'], (intval($advertiser['total']) != 0 ? round(intval($advertiser['Unconverted']) / intval($advertiser['total']),2) * 100 : 0) . "%", '<a href="javascript:void(0)" type="button" data-id="' . $advertiser['id_account'] . '" class="VBAdetails btn btn-sm waves-effect waves-light btn-info">details</a>');
        }

        $resultat['table6'] = array();
        $stmt = $conn->prepare('SELECT w.name,w.id_website,
        (SELECT count(*) FROM leads l,contributors c ,customers cu,users u where  c.id_publisher=:id AND l.id_customer=cu.id_customer AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 AND cu.id_website=w.id_website ' . $condition . ') as total ,
        (SELECT count(*) FROM customers cu ,leads l,contributors c,users u  where  c.id_publisher=:id AND l.id_customer=cu.id_customer AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 AND cu.id_website=w.id_website  AND l.status=2 ' . $condition . ') as sales,
        (SELECT count(*) FROM customers cu,leads l,contributors c,users u  where c.id_publisher=:id AND l.id_customer=cu.id_customer AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 AND cu.id_website=w.id_website AND l.status=1 ' . $condition . ') as withVisit,
        (SELECT count(*) FROM customers cu,leads l,contributors c,users u  where  c.id_publisher=:id AND l.id_customer=cu.id_customer AND c.id_contributor=u.id_profile AND u.id_user=l.id_contributor AND u.profile=6 AND cu.id_website=w.id_website AND l.status=0 ' . $condition . ') as Unconverted
        FROM websites w ,publishers_programs pp WHERE pp.id_publisher=22218 GROUP BY w.id_website');
        $stmt->bindParam(':id', $publisher);
        $stmt->execute();
        $programs = $stmt->fetchAll();

        foreach ($programs as $program) {
            $resultat['table6'][] = array($program['name'], intval($program['total']), (count($allLeads) != 0 ? round(intval($program['total']) / count($allLeads),2) * 100 : 0) . "%", $program['sales'], (intval($program['total']) != 0 ? round(intval($program['sales']) / intval($program['total']),2) * 100 : 0) . "%", $program['withVisit'], (intval($program['total']) != 0 ? round(intval($program['withVisit']) / intval($program['total']),2) * 100 : 0) . "%", $program['Unconverted'], (intval($program['total']) != 0 ? round(intval($program['Unconverted']) / intval($program['total']),2) * 100 : 0) . "%", '<a href="javascript:void(0)" data-id="' . $program['id_website'] . '" type="button" class="VBPdetails btn btn-sm waves-effect waves-light btn-info">details</a>');
        }
        $result = $resultat;
        break;
    case 'getCL':
        $id = intval($_POST['id']);
        $contributor = intval($_POST['contributor']);
        switch ($id) {
            case 1:
                $condition = 'AND cu.date_start="' . date('Y-m-d') . '"';
                break;
            case 2:
                $condition = 'AND cu.date_start="' . date('Y-m-d', strtotime("-1 days")) . '"';
                break;
            case 3:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('monday this week')) . '" AND "' . date("Y-m-d", strtotime('sunday this week')) . '"';
                break;
            case 4:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('last week monday')) . '" AND "' . date("Y-m-d", strtotime('last week sunday')) . '"';
                break;
            case 5:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of this month')) . '" AND "' . date("Y-m-d", strtotime('last day of this month')) . '"';
                break;
            case 6:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of previous month')) . '" AND "' . date("Y-m-d", strtotime('last day of previous month')) . '"';
                break;
            case 7:
                $condition = 'AND cu.date_start BETWEEN "' . $_POST['from'] . '" AND "' . $_POST['to'] . '"';
                break;
        }
        $resultat = array();

        $stmt = $conn->prepare('SELECT cu.id_customer,CONCAT(cu.firstname,cu.lastname),cu.date_start,le.update_date,le.status FROM leads le,customers cu ,users u where le.id_contributor=u.id_user AND le.id_customer=cu.id_customer AND le.id_contributor=u.id_user AND u.id_profile=:id AND u.profile=6 ' . $condition);
        $stmt->bindParam(':id', $contributor);
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
        }
        $result = $leads;
        break;
    case 'getAL':
        $id = intval($_POST['id']);
        $advertiser = intval($_POST['advertiser']);
        $publisher = intval($_POST['publisher']);
        switch ($id) {
            case 1:
                $condition = 'AND cu.date_start="' . date('Y-m-d') . '"';
                break;
            case 2:
                $condition = 'AND cu.date_start="' . date('Y-m-d', strtotime("-1 days")) . '"';
                break;
            case 3:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('monday this week')) . '" AND "' . date("Y-m-d", strtotime('sunday this week')) . '"';
                break;
            case 4:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('last week monday')) . '" AND "' . date("Y-m-d", strtotime('last week sunday')) . '"';
                break;
            case 5:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of this month')) . '" AND "' . date("Y-m-d", strtotime('last day of this month')) . '"';
                break;
            case 6:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of previous month')) . '" AND "' . date("Y-m-d", strtotime('last day of previous month')) . '"';
                break;
            case 7:
                $condition = 'AND cu.date_start BETWEEN "' . $_POST['from'] . '" AND "' . $_POST['to'] . '"';
                break;
        }
        $resultat = array();

        $stmt = $conn->prepare('SELECT cu.id_customer,CONCAT(cu.firstname,cu.lastname),cu.date_start,le.update_date,le.status FROM leads le,customers cu ,users u,contributors c,websites w where cu.id_customer = le.id_customer AND w.id_website = cu.id_website AND w.id_account=:id AND le.id_contributor=u.id_user AND u.id_profile=c.id_contributor AND c.id_publisher=:pub AND u.profile=6 ' . $condition);
        $stmt->bindParam(':id', $advertiser);
        $stmt->bindParam(':pub', $publisher);
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
        }
        $result = $leads;
        break;
    case 'getPL':
        $id = intval($_POST['id']);
        $program = intval($_POST['program']);
        $publisher = intval($_POST['publisher']);
        switch ($id) {
            case 1:
                $condition = 'AND cu.date_start="' . date('Y-m-d') . '"';
                break;
            case 2:
                $condition = 'AND cu.date_start="' . date('Y-m-d', strtotime("-1 days")) . '"';
                break;
            case 3:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('monday this week')) . '" AND "' . date("Y-m-d", strtotime('sunday this week')) . '"';
                break;
            case 4:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('last week monday')) . '" AND "' . date("Y-m-d", strtotime('last week sunday')) . '"';
                break;
            case 5:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of this month')) . '" AND "' . date("Y-m-d", strtotime('last day of this month')) . '"';
                break;
            case 6:
                $condition = 'AND cu.date_start BETWEEN "' . date("Y-m-d", strtotime('first day of previous month')) . '" AND "' . date("Y-m-d", strtotime('last day of previous month')) . '"';
                break;
            case 7:
                $condition = 'AND cu.date_start BETWEEN "' . $_POST['from'] . '" AND "' . $_POST['to'] . '"';
                break;
        }
        $resultat = array();

        $stmt = $conn->prepare('SELECT cu.id_customer,CONCAT(cu.firstname,cu.lastname),cu.date_start,le.update_date,le.status FROM leads le,customers cu ,users u,contributors c,websites w where cu.id_customer = le.id_customer AND w.id_website = cu.id_website AND w.id_website=:id AND le.id_contributor=u.id_user AND u.id_profile=c.id_contributor AND c.id_publisher=:pub AND u.profile=6 ' . $condition);
        $stmt->bindParam(':id', $program);
        $stmt->bindParam(':pub', $publisher);
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
        }
        $result = $leads;
        break;
    default:
        # code...
        break;
}
echo json_encode($result);
