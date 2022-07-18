<?php 
date_default_timezone_set('UTC');
require "../../vendor/autoload.php";

use Classes\DataService;
// BASE DE DONNEES
try {
    $ds = new DataService();
    $conn = $ds->conn;
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


if (isset($_POST['action']) && $_POST['action']=="logout") {
    $stmt2 = $conn->prepare("UPDATE `users` SET `status`=0 WHERE `id_user` = :ID");
    $stmt2->bindParam(':ID', $_POST['sender']);
    $stmt2->execute();
}

?>