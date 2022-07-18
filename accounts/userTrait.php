<?php
  require '../vendor/autoload.php';
  use Classes\User;

  if(isset($_POST['action'])){
    $user = new User();
    $action = $_POST['action'];
    if($action == "forgot_password"){
      $username = $_POST['username'];
      $resend = $_POST['resend'];
      $response = $user->submitForgotPassword($username,$resend);
      if($response['status'] == 200){
        echo json_encode(array("statusCode"=>200, 'msg' => "request sent" , 'response' => $response));
        die;
      }
      else{
        echo json_encode(array("statusCode"=>201, 'msg' => "user not found", 'response' => $response));
        die;
      }
    }elseif($action == "updatePassword"){
      $id_user = $_POST['id_user'];
      $currentPassword = $_POST['currentPassword'];
      $newPassword = $_POST['newPassword'];
      $passwordConfirmation = $_POST['passwordConfirmation'];
      if($newPassword != $passwordConfirmation){
        echo json_encode(array("statusCode"=>201, 'msg' => "wrong password confirmation"));
        die;
      }else{
        $response = $user->updatePassword($id_user,$currentPassword,$newPassword,$passwordConfirmation);
        if($response['status'] == 200){
          echo json_encode(array("statusCode"=>200, 'msg' => "Password updated successfully" , 'response' => $response));
          die;
        }
        else{
          echo json_encode(array("statusCode"=>201, 'msg' => $response["message"], 'response' => $response));
          die;
        }
      }
      
    }
  
  
    
   
    
  } 

?>