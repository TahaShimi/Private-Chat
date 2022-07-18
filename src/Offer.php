<?php
  
    namespace Classes;

    use Exception;
use \PDO;
    class Offer{
        public $conn;
        public $ds;
        function __construct(){

        }
        function initDS(){
            $this->ds = new DataService();    
            $this->conn = $this->ds->conn;
        }

        function endOffer($offerId){
            try {
            $this->initDS();
            $stmt2 =  $this->conn->prepare("UPDATE offers set end_date=CURDATE() where id_offer=:id");
            $stmt2->bindparam(":id", $offerId, PDO::PARAM_INT);
            return ($stmt2->execute());
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }


        function deleteOffer($offerId){
            try {
            $this->initDS();
            $test = true;
            $stmt2 =  $this->conn->prepare("DELETE from offers where id_offer=:id");
            $stmt2->bindparam(":id", $offerId, PDO::PARAM_INT);
            if ($stmt2->execute()){
                $stmt3 =  $this->conn->prepare("DELETE from offers_customers where id_offer=:id");
                $stmt3->bindparam(":id", $offerId, PDO::PARAM_INT);
                $test =$stmt3->execute();
            }
            return $test;
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }
    }




?>