<?php
include('../../init.php');
if(isset($_POST["dateType"]) && isset($_POST["discount"]) && isset($_POST["accountId"])){
    $dateType = $_POST["dateType"];
    $discount = $_POST["discount"];
    $accountId = $_POST["accountId"];
    if($dateType == 1){
        $startDate = $_POST["startDate"];
        $endDate = $_POST["endDate"];      
        $parts = explode('/',$startDate);
        $startDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
        $parts = explode('/',$endDate);
        $endDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];

        $s1 = $conn->prepare("SELECT *,(SELECT sum(discount) FROM offers o where o.id_package=p.id_package and (( (o.start_date<=:sd AND (datediff(o.end_date,o.start_date)>= datediff(:ed,:sd)) ) or (o.start_date>=:sd AND (datediff(o.end_date,o.start_date)<= datediff(:ed,:sd)) ) )or (o.start_date is null))) as total_discount FROM `packages` p JOIN packages_price pp ON  pp.id_package=p.id_package WHERE pp.primary=1  AND p.id_account = :ID and p.active=1");
        $s1->bindParam(':sd', $startDate, PDO::PARAM_STR);
        $s1->bindParam(':ed', $endDate, PDO::PARAM_STR);
        $s1->bindParam(':ID', $accountId, PDO::PARAM_INT);
        $s1->execute();
        $packages = $s1->fetchAll();
        echo json_encode(array("statusCode"=>200, "packages"=> $packages));

    }
    else{
        echo json_encode(array("statusCode"=>200, "data"=> $_POST));
    }

}
?>