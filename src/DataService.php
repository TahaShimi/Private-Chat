<?php
    namespace Classes;
    use Classes\DataService;
use \PDO;
use PDOException;
    class DataService{
        protected $dsn;
        protected $username;
        protected $password;
        protected $options;
        public $conn;
        public $env;
        function __construct(){
            $this->env = "prod";
            if($this->env == "dev"){
                $this->dsn = "mysql:host=localhost;dbname=privatechat";
                $this->username = "root";
                $this->password = "";
            }
            else{
                $this->dsn = "mysql:host=localhost;dbname=privatechat_v4";
                $this->username = "root";
                $this->password = "CwJeHTzxT2himnA";
            }
           
            $this->options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );
            $this->initConnection();
        }

        function initConnection(){
            try{
                $this->conn = new PDO($this->dsn,$this->username,$this->password,$this->options);
            } catch (PDOException $e){
                echo "Error!".$e->getMessage();
            }
        }
    }


?>