<?php
  
    namespace Classes;
    use Classes\Email;
use Exception;
use \PDO;
    class User{
        public $ds;
        public $conn;
        function __construct(){
        }

        function initDS(){
            $this->ds = new DataService();    
            $this->conn = $this->ds->conn;
        }

        function setStatus(array $dta){
            try {
                $this->initDS();
                $stmt = $this->conn->prepare("UPDATE users set online_status=:st where id_user=:iu");
                $stmt->bindparam(":iu", $dta['id_user'],PDO::PARAM_INT );
                $stmt->bindparam(":st", $dta['status'],PDO::PARAM_INT );
                $stmt->execute();
                return $stmt->execute();
                } catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
                }
        }


        function generateRandomToken($length = 32) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }


        function sendForgetPasswordEmail($email, $token,$firstname,$lastname){
             $email = new Email();
             $email->sendForgetPasswordEmail($email,$token,$firstname,$lastname);
        }


        function updatePassword($id_user,$currentPassword,$newPassword,$passwordConfirmation){
            try {
                $this->initDS();
                $feedBack = array();
                $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `id_user` = :ID");
                $stmt->bindParam(':ID', $id_user, PDO::PARAM_STR);
                $stmt->execute();
                $total = $stmt->rowCount();
                $result = $stmt->fetchObject();
                if ($total == 0) {
                    $feedBack["status"] = 201;
                    $feedBack["message"] =  "User not found";
                    $feedBack["code"] = 202;
                    return $feedBack;
                } elseif (!password_verify( $currentPassword, $result->password)) {
                    $feedBack["status"] = 201;
                    $feedBack["message"] =  "Wrong Password";
                    $feedBack["code"] = 202;
                    return $feedBack;
                } else{
                    $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    $stmt2 = $this->conn->prepare("UPDATE users set password=:HPW,password_updated_at=NOW() where id_user=:ID");
                    $stmt2->bindparam(":ID", $id_user);
                    $stmt2->bindparam(":HPW", $newHashedPassword);
                    if($stmt2->execute()){
                        $feedBack["status"] = 200;
                        $feedBack["message"] =  "Password Updated Successfully";
                        $feedBack["code"] = 200;
                        return $feedBack;
                    }
                }
                } catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
                 }
                }  
              
        function submitForgotPassword($username,$resend){
            try {
                $this->initDS();
                $feedBack = array();
                $stmt = $this->conn->prepare("select * from users where login=:lg");
                $stmt->bindparam(":lg", $username,PDO::PARAM_STR );
                $stmt->execute();
                $user = $stmt->fetch();
                if($user){
                    $stmt2 = $this->conn->prepare("SELECT * from password_resets  where id_user=:ID");
                    $stmt2->bindparam(":ID", $user["id_user"],PDO::PARAM_STR );
                    $stmt2->execute();
                    $request = $stmt2->fetch();
                    if($request){
                        if($resend){
                            $id_user = $user["id_user"];
                            $token = $this->generateRandomToken();
                            $stmt4 = $this->conn->prepare("UPDATE password_resets set token=:tk, resend_count=resend_count+1, created_at=NOW() where id_user=:ID");
                            $stmt4->bindparam(":ID", $id_user,PDO::PARAM_INT );
                            $stmt4->bindparam(":tk", $token ,PDO::PARAM_STR );
                            if($stmt4->execute()){
                                if($user["profile"] == 2){
                                    $stmt = $this->conn->prepare("select * from accounts where id_account=:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["emailc"], $token,$acc["firstname"],null );
                                }elseif($user["profile"] == 3){
                                    $stmt = $this->conn->prepare("select * from consultants where id_account=:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["emailc"], $token,$acc["firstname"],$acc["lastname"] );
                                }elseif($user["profile"] == 4){
                                    $stmt = $this->conn->prepare("select * from customers where id_account=:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["emailc"], $token,$acc["firstname"],$acc["lastname"] );
                                }elseif($user["profile"] == 5){
                                    $stmt = $this->conn->prepare("select * from publishers p,users u where p.id_publisher=u.id_profile AND u.id_user==:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["email"], $token,$acc["firstname"],$acc["lastname"] );
                                }elseif($user["profile"] == 6){
                                    $stmt = $this->conn->prepare("select * from Contributors c,users u where c.id_contributor=u.id_profile AND u.id_user=id_account=:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["email"], $token,$acc["pseudo"],$acc["username"] );
                                }
                            $feedBack["status"] = 200;
                            $feedBack["message"] = "Reset password link sent successfully";
                            $feedBack["code"] = 200;
                            return $feedBack;
                            }
                        }
                        else{
                            $feedBack["status"] = 201;
                            $feedBack["message"] =  "Reset password link already sent";
                            $feedBack["code"] = 202;
                            return $feedBack;
                        }   
                    }
                    else{
                        $id_user = $user["id_user"];
                        $token = $this->generateRandomToken();
                        $stmt3 = $this->conn->prepare("INSERT INTO password_resets(`id_user`, `token`, `created_at`) VALUES (:ID,:tk,NOW())");
                        $stmt3->bindparam(":ID", $id_user,PDO::PARAM_INT );
                        $stmt3->bindparam(":tk", $token ,PDO::PARAM_STR );
                        if($stmt3->execute()){
                                if($user["profile"] == 2){
                                    $stmt = $this->conn->prepare("select * from accounts where id_account=:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["emailc"], $token,$acc["firstname"],null );
                                }elseif($user["profile"] == 3){
                                    $stmt = $this->conn->prepare("select * from consultants where id_account=:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["emailc"], $token,$acc["firstname"],$acc["lastname"] );
                                }elseif($user["profile"] == 4){
                                    $stmt = $this->conn->prepare("select * from customers where id_account=:ID");
                                    $stmt->bindparam(":ID", $user["id_profile"],PDO::PARAM_STR );
                                    $stmt->execute();
                                    $acc = $stmt->fetch();
                                    $this->sendForgetPasswordEmail($acc["emailc"], $token,$acc["firstname"],$acc["lastname"] );
                                }
                                $feedBack["status"] = 200;
                                $feedBack["message"] = "Reset password link sent successfully";
                                $feedBack["code"] = 200;
                                return $feedBack;
                        }
                        else{
                            $feedBack["status"] = 201;
                            $feedBack["message"] = "Can't send reset mail. please try again later.";
                            $feedBack["code"] = 205;
                            return $feedBack;
                        }
                        
                    }
                    
                }
                else{
                        $feedBack["status"] = 201;
                        $feedBack["message"] =  "User account not found";
                        $feedBack["code"] = 203;
                        return $feedBack;
                }
                } catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
            }
        }
    }




?>