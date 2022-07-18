<?php
  require '../vendor/autoload.php';
  use Classes\Conversation;

  if(isset($_POST['action'])){
    $conversation = new Conversation();
    $action = $_POST['action'];
    if($action == "getConversationsList"){
      $consultant = $_POST['consultant'];
      $conversations = $conversation->getConsultantConversationsPartners($consultant);
      if($conversations){
        echo json_encode(array("statusCode"=>200, 'msg' => "messages found", "partners" => $conversations ));
        die;
      }
      else{
        echo json_encode(array("statusCode"=>201, 'msg' => "no messages found", "count" => 0));
        die;
      }
    }
    elseif($action == "getConversationsList2"){
      $dta = array();
      $dta["customer_id"] = $_POST['customer_id'];
      $dta["sender"] = $_POST['sender'];
      $dta["sender_role"] = $_POST['sender_role'];
      $dta["receiver"] = $_POST['receiver'];
      $dta["receiver_role"] = $_POST['receiver_role'];
      $dta["last_id"] = $_POST['last_id'];
        $conversations = $conversation->getConversations2($dta);
        if($conversations["messages"]){
          echo json_encode(array("statusCode"=>200, 'msg' => "messages found", "count" => count($conversations["messages"]),"conversations" => $conversations["messages"]));
          die;
        }
        else{
          echo json_encode(array("statusCode"=>201, 'msg' => "no messages found", "count" => 0));
          die;
        }
    }
    elseif($action == "reviewConversation"){
      $consultant = $_POST['consultant'];
      $customer = $_POST['customer'];
      $conversations = $conversation->getConversationReview($customer, $consultant);
      if($conversations){
        echo json_encode(array("statusCode"=>200, 'msg' => "messages found", "conversations" => $conversations ));
        die;
      }
      else{
        echo json_encode(array("statusCode"=>201, 'msg' => "no messages found", "count" => 0));
        die;
      }
    }
    else
    {

      $dta = array();
      $dta["sender"] = $_POST['sender'];
      $dta["sender_role"] = $_POST['sender_role'];
      $dta["receiver"] = $_POST['receiver'];
      $dta["receiver_role"] = $_POST['receiver_role'];
      $dta["last_id"] = $_POST['last_id'];
      $dta["inversed"] = $_POST['inversed'];
      if($action == "getConversation"){
        $conversations = $conversation->getConversations($dta);
        if($conversations){
          echo json_encode(array("statusCode"=>200, 'msg' => "messages found", "count" => count($conversations),"conversations" => $conversations));
          die;
        }
        else{
          echo json_encode(array("statusCode"=>201, 'msg' => "no messages found", "count" => 0));
          die;
        }
        }
    }



  }
