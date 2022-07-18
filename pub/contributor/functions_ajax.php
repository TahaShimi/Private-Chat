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
        $id = intval($_POST['id']);
        $stmt2 = $conn->prepare("SELECT id_website as id,name FROM `websites` WHERE id_account=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $result = $stmt2->fetchAll();
        break;
    case 'getWebsites_contri':
        $id = intval($_POST['id']);
        $publisher = intval($_POST['publisher']);
        $stmt2 = $conn->prepare("SELECT id_website as id,name FROM publisher_advertiser pa,websites w ,accounts a  WHERE pa.id_advertiser = a.id_account AND pa.id_program=w.id_website AND pa.id_publisher=:pb AND pa.id_advertiser=:id");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->bindParam(':pb', $publisher, PDO::PARAM_INT);
        $stmt2->execute();
        $result = $stmt2->fetchAll();
        break;
    case 'getPackeges':
        $id = intval($_POST['id']);
        $contributor = intval($_POST['contributor']);
        $stmt2 = $conn->prepare("SELECT cp.packages_id FROM contributors_programs cp,users u  WHERE cp.id_advertiserProgram=:id AND cp.id_contributor=u.id_profile AND u.id_user=:id2 AND profile=6");
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->bindParam(':id2', $contributor, PDO::PARAM_INT);
        $stmt2->execute();
        $ids = $stmt2->fetchObject();
        $ids = json_decode($ids->packages_id);
        $stmt2 = $conn->prepare("SELECT p.id_package,p.title,pp.price,pp.currency FROM packages p,packages_price pp WHERE pp.id_package=p.id_package AND p.id_package IN (".implode(',',$ids).") AND p.visible=1 ");
        $stmt2->execute();
        $packages = $stmt2->fetchAll();
        foreach ($packages as &$package) {
            $stmt2 = $conn->prepare("SELECT * FROM offers WHERE id_package=:id AND discount=100");
            $stmt2->bindParam(':id', $package['id_package'], PDO::PARAM_INT);
            $stmt2->execute();
            $offre = $stmt2->fetch();
            if($offre){
                $package['price']=0;
            }
        }
        $result=$packages;
        break;
    case 'get_leads':
        $id = intval($_POST['id']);
        $id_contributor = intval($_POST['id_contributor']);
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
        $stmt = $conn->prepare("SELECT  l.id_lead,CONCAT(cu.firstname,' ',cu.lastname),w.name,cu.date_start,l.update_date,l.status FROM leads l,websites w,contributors c,customers cu WHERE  l.id_contributor=:id  AND w.id_website=cu.id_website AND cu.id_customer=l.id_customer " . $condition);
        $stmt->bindParam(':id', $id_contributor);
        $stmt->execute();
        $resultat['table'] = $stmt->fetchAll();
        foreach ($resultat['table'] as &$lead) {
            switch ($lead['status']) {
                case 0:
                    $lead[5] = $trans['publisher']['No_visit'];
                    break;
                case 1:
                    $lead[5] = $trans['publisher']['Visit_without_sale'];
                    break;
                case 2:
                    $lead[5] = $trans['publisher']['1st_Sale'];
                    break;

                default:
                    # code...
                    break;
            }
        }
        $total = count($resultat['table']);
        $stmt = $conn->prepare('SELECT  
            (SELECT count(*) FROM leads le,customers cu where le.id_contributor=:id AND cu.id_customer=le.id_customer AND le.status=2 ' . $condition . ') as sales,
            (SELECT count(*) FROM leads le,customers cu where le.id_contributor=:id AND cu.id_customer=le.id_customer AND le.status=1 ' . $condition . ') as withVisit,
            (SELECT count(*) FROM leads le,customers cu where le.id_contributor=:id AND cu.id_customer=le.id_customer AND le.status=0 ' . $condition . ') as Unconverted
            FROM contributors ');
        $stmt->bindParam(':id', $id_contributor);
        $stmt->execute();
        $leads = $stmt->fetchObject();

        $resultat['Total'] = $total;
        $resultat['totalWith'] = $leads->withVisit;
        $resultat['totalWithP'] = $total != 0 ? (intval($leads->withVisit) / $total) * 100 : 0;
        $resultat['totalWithNot'] = $leads->Unconverted;
        $resultat['totalWithNotP'] = $total != 0 ? (intval($leads->Unconverted) / $total) * 100 : 0;
        $resultat['totalSales'] = $leads->sales;
        $resultat['totalSalesP'] = $total != 0 ? (intval($leads->sales) / $total) * 100 : 0;
        $result = $resultat;

        break;
    default:
        # code...
        break;
}
echo json_encode($result);
