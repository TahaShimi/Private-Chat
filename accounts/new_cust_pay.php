<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('../init.php');
require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET["response"])) {
    $response = $_GET["response"];
    $firstname = $_GET["first_name"];
    $lastname = $_GET["last_name"];
    $email = $_GET["email"];
    $id_pack = $_GET["id_pack"];
    $amount = $_GET["amount"];

    $stmt = $conn->prepare("INSERT INTO customers (firstname, lastname, emailc) VALUES (:fstn, :lstn, :email)");
    $stmt->bindParam(':fstn', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lstn', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $id = $conn->lastInsertId();

    if ($response == "ok") { 

        $stmt = $conn->prepare("SELECT * FROM users WHERE id_profile=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo ('alredy used');
        } else {
            $lang = isset($_POST['lang']) && $_POST['lang'] != "" ? $_POST['lang'] : "en";
            $profile = 4;
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
            $pwd = substr(str_shuffle($chars), 0, 8);
            $password = password_hash($pwd, PASSWORD_BCRYPT);
            $stmt1 = $conn->prepare("INSERT INTO `users`(`login`, `password`, `profile`, `id_profile`, `date_add`, `active`, `status`,lang) VALUES (:lg,:pwd,:pr,:ip,NOW(),1,1,:lang)");
            $stmt1->bindParam(':lg', $email, PDO::PARAM_STR);
            $stmt1->bindParam(':pwd', $password, PDO::PARAM_STR);
            $stmt1->bindParam(':pr', $profile, PDO::PARAM_INT);
            $stmt1->bindParam(':ip', $id, PDO::PARAM_INT);
            $stmt1->bindparam(":lang", $lang, PDO::PARAM_STR);
            $stmt1->execute(); 

        }

        $stmt = $conn->prepare("INSERT INTO transactionsc (id_customer, id_package, status, date_add, final_price) VALUES (:id, :id_pack, 1, NOW(), :amount)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':id_pack', $id_pack, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT messages FROM packages WHERE id_package=:id_pack");
        $stmt->bindParam(':id_pack', $id_pack, PDO::PARAM_INT);
        $stmt->execute();
        $credit = $stmt->fetch(PDO::FETCH_COLUMN);

        $stmt = $conn->prepare("UPDATE customers SET balance=:credit WHERE id_customer=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':credit', $credit, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stat = "lead";

        $stmt = $conn->prepare("SELECT * FROM leads WHERE id_customer=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo ('alredy used');
        } else {
            $stmt = $conn->prepare("INSERT INTO leads (id_customer, add_date, status) VALUES (:id, NOW(), 1)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        $stmt = $conn->prepare("INSERT INTO transactionsc (id_customer, id_package, status, date_add, final_price) VALUES (:id, :id_pack, 0, NOW(), :amount)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':id_pack', $id_pack, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->execute();

    }
}
?>

<body>
    <?php if (isset($_GET["response"])) {
        $response = $_GET["response"];
        if ($response == "ok") { ?>

            <h1>All Good</h1>
        <?php } else { ?>
            <h1>you are a lead</h1>
    <?php }
    } ?>
</body>