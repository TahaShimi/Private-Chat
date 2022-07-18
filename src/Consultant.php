<?php
  
    namespace Classes;
    use Classes\DataService;
    use Exception;
    use \PDO;
    class Consultant{
        public $ds;
        function __construct(){
        }

        function initDS(){
            $this->ds = new DataService();    
            $this->conn = $this->ds->conn;
        }

        function getFullName($consultantId){
            try {   
            $this->initDS();
            $stmt2 = $this->conn->prepare("SELECT CONCAT(firstname,' ',lastname) as `fullname` from consultants where id_consultant=:id");
            $stmt2->bindparam(":id", $consultantId, PDO::PARAM_INT);
            $stmt2->execute();
            $dta = $stmt2->fetch();
            return $dta["fullname"];
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

  
        


    }




?>