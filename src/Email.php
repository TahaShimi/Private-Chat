<?php
  
    namespace Classes;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    use \PDO;
    class Email{
        public $mail;
        function __construct(){
            $this->mail = new PHPMailer(true);
            try{
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;                     
            $this->mail->isSMTP();                                            
            $this->mail->Host       = 'smtp.gmail.com';                 
            $this->mail->SMTPAuth   = true;                                  
            $this->mail->Username   = 'aymen.dev.acc@gmail.com';                 
            $this->mail->Password   = 'zed68abdel';                              
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
            $this->mail->Port       = 25;    
            $this->mail->SMTPDebug  = 0; 
                                
            }catch(Exception $e){

            }
        }

        function sendForgetPasswordEmail($email, $token, $firstName, $lastName){
            try{          
                $this->mail->setFrom('aymen.dev.acc@gmail.com', 'Private-chat');
                $this->mail->addAddress('mr.aymenbouein@gmail.com', $firstName . ' ' . $lastName);        
                $this->mail->isHTML(true);                               
                $this->mail->Subject = 'Private-chat Password Reset Link';
                $this->mail->Body    = 'Hello <b>' .  $firstName . ' ' .  $lastName.'</b> <br/>Use the next link to reset your password <br /><a href="http://45.76.121.27/chat/accounts/reset_password.php?token='. $token . '" >reset password</a>';     
                $this->mail->send();
                return true;
            } catch (Exception $e) {
              return false;
            }
        }
    }




?>