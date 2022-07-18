<?php
  require '../vendor/autoload.php';
  use Classes\Offer;

  if(isset($_POST['action'])){
    $offer = new Offer();
    $action = $_POST['action'];
    $offerId = $_POST['offerId'];
    if($action=="endOffer"){
      $offer = $offer->endOffer($offerId);
      if($offer){
        echo json_encode(array("statusCode"=>200, 'msg' => "offer ended"));
        die;
      }else{
        echo json_encode(array("statusCode"=>201, 'msg' => "offer not found"));
        die;
      }
    }
    elseif($action=="deleteOffer"){
      $offer = $offer->deleteOffer($offerId);
      if($offer){
        echo json_encode(array("statusCode"=>200, 'msg' => "offer deleted"));
        die;
      }else{
        echo json_encode(array("statusCode"=>201, 'msg' => "offer not found"));
        die;
      }
    }
  
    }
    
    
  

?>