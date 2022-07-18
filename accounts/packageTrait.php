<?php
  require '../vendor/autoload.php';
  use Classes\Package;

  if(isset($_POST['action'])){
    $package = new Package();
    $action = $_POST['action'];
    $packageId = $_POST['packageId'];
    if($action=="endPackage"){
      $package = $package->endPackage($packageId);
      if($package){
        echo json_encode(array("statusCode"=>200, 'msg' => "package ended"));
        die;
      }else{
        echo json_encode(array("statusCode"=>201, 'msg' => "package not found"));
        die;
      }
    }
    elseif($action=="buy_package"){ 
      if(!isset($_POST["websiteId"]) || !isset($_POST["accountId"])){
          echo json_encode(array("statusCode"=>201, 'response' => 'Missed Data'));
          die;
      }
      else{
        $websiteId = $_POST["websiteId"];
        $accountId = $_POST["accountId"];
        $currency = $_POST["currency"];
        $response = $package->requestPackagePaymentinformation($packageId,$accountId,$websiteId,$currency);   
        if($response["status"] == 200){
         echo json_encode(array("statusCode"=>200, 'response' => $response));
         die;
         }
         else{
           echo json_encode(array("statusCode"=>201, 'response' => $response));
           die;
         }
      }
       
       
      }
      elseif($action=="get_package"){ 
        if(!isset($_POST["accountId"])){
            echo json_encode(array("statusCode"=>201, 'response' => 'Missed Data'));
            die;
        }
        else{
          $websiteId = $_POST["websiteId"];
          $accountId = $_POST["accountId"];
          $response = $package->getFreePackage($packageId,$accountId );   
          if($response["status"] == 200){
           echo json_encode(array("statusCode"=>200, 'response' => $response));
           die;
           }
           else{
             echo json_encode(array("statusCode"=>201, 'response' => $response));
             die;
           }
        }
          
         
        }
  
    }
    
    
  

?>