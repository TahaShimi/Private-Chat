<?php
  
    namespace Classes;
    use Classes\DataService;

use Exception;
use \PDO;
    class Customer{
        public $ds;
        function __construct(){
        }

        function initDS(){
            $this->ds = new DataService();   
            $this->conn = $this->ds->conn; 
        }

        function getFullName($customerId){
            try {
            $this->initDS();
            $stmt2 = $this->conn->prepare("SELECT CONCAT(firstname,' ',lastname) as `fullname` from customers where id_customer=:id");
            $stmt2->bindparam(":id", $customerId, PDO::PARAM_INT);
            $stmt2->execute();
            $dta = $stmt2->fetch();
            return $dta["fullname"];
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

        function getcustomer($userId){
            try {
            $this->initDS();
            $stmt2 = $this->conn->prepare("SELECT * from users u join customers c on u.id_profile=c.id_customer where u.id_user=:id");
            $stmt2->bindparam(":id", $userId, PDO::PARAM_INT);
            $stmt2->execute();
            $dta = $stmt2->fetch();
            return $dta;
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

        function getBalance($customerId){
            try {
            $this->initDS();
            $stmt2 = $this->conn->prepare("SELECT `balance` from customers where id_customer=:id");
            $stmt2->bindparam(":id", $customerId, PDO::PARAM_INT);
            $stmt2->execute();
            $balance = $stmt2->fetchObject();
            return $balance->balance;
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

        function checkBalance($customerId){
            try {
            $this->initDS();
            $stmt2 = $this->conn->prepare("SELECT `balance` from customers where id_customer=:id");
            $stmt2->bindparam(":id", $customerId, PDO::PARAM_INT);
            $stmt2->execute();
            $balance = $stmt2->fetchObject();
            return $balance->balance > 0;
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

        function updateBalance($customerId){
            try {
            $this->initDS();
            $stmt = $this->conn->prepare("UPDATE customers set balance=balance-1 where id_customer=:id");
            $stmt->bindparam(":id", $customerId,PDO::PARAM_INT );
            $stmt->execute();
            $balance =  intval($this->getBalance($customerId));
            if($balance == 0){
                    $stmt1 = $this->conn->prepare("SELECT id_user from users where id_profile=:ip and profile=4");         
                    $stmt1->bindParam(':ip',$customerId, PDO::PARAM_INT);
                    $stmt1->execute();
                    $id_user = intval($stmt1->fetchObject()->id_user);
                    $description = "Customer use all his credits";
                    $stmt3 = $this->conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date)VALUES(:iu,:ds,null,1,NOW())");
                    $stmt3->bindParam(':iu', $id_user, PDO::PARAM_INT);
                    $stmt3->bindParam(':ds', $description, PDO::PARAM_STR);   
                    $stmt3->execute();
                    $affected_rows = $stmt3->rowCount();
            }
            return $balance;
            } catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }
    }




?>