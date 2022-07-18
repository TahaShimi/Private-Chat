<?php
  require '../vendor/autoload.php';
  use Classes\Customer;

  if(isset($_POST['action'])){
    $customer = new Customer();
    $action = $_POST['action'];
    if($action=="getCustomer"){
      $user_id = $_POST['user_id'];
      $customer = $customer->getcustomer($user_id);
      if($customer){
        echo json_encode(array("statusCode"=>200, 'msg' => "customer found", "customer" => $customer));
        die;
      }else{
        echo json_encode(array("statusCode"=>201, 'msg' => "customer not found"));
        die;
      }
    }
    else{
      $customer_id = $_POST['customer_id'];
      if($action == "getBalance"){
          $balance = $customer->getBalance($customer_id);
          echo json_encode(array("statusCode"=>200, 'msg' => "balance found", "balance" => $balance));
          die;
        }
      }
    }
    
    
  

?>