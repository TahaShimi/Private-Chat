<?php

$msgR = "";

if (isset($id_user)|| isset($_COOKIE['customer'])) {
    $customer_id = isset($id_user)? $id_user : $_COOKIE['customer'];
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `id_user` = :id or id_profile=:id");
    $stmt->bindParam(':id', $customer_id, PDO::PARAM_STR);
    $stmt->execute();
    $total = $stmt->rowCount();
    $result = $stmt->fetchObject();


    if ($total == 0) {
        $msgR = "User do not existe";
    } else {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
            ini_set('session.gc-maxlifetime', 60 * 60 * 24 * 7);
            session_start();
            session_regenerate_id();
        }

        $_SESSION['login'] = $result->profile;
        $_SESSION['id_user'] = $result->id_user;
        $_SESSION['id_account'] = $result->id_profile;
        $_SESSION['lang'] = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : $result->lang;
        setcookie("user", $result->id_user);
        setcookie("customer", $result->id_profile);
        $stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm ,`browser` =:br ,`last_connect` =NOW()  WHERE `id_user` = :ID");
        $stmt3->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
        $stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $stmt3->bindParam(':br', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
        $stmt3->execute();

        if ($result->profile == 4) {
            $stmt4 = $conn->prepare("SELECT  id_customer,`photo`, `firstname`, `lastname`, `emailc`, `balance`, `id_account`,`status`,`id_website`,`country`,`id_link`  ,(CASE c.country WHEN NULL THEN (SELECT currency FROM accounts WHERE id_account=c.id_account) ELSE c.country END) currency FROM `customers` c WHERE c.id_customer = :ID");
            $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
            $stmt4->execute();
            $stat = $stmt4->fetchObject();
            $stmt3 = $conn->prepare("UPDATE leads SET status=1,update_date=NOW()  WHERE `id_customer` = :ID AND status=0");
            $stmt3->bindParam(':ID', $stat->id_customer, PDO::PARAM_INT);
            $stmt3->execute();
            $stmt4 = $conn->prepare("SELECT  * FROM packages p,transactionsc t WHERE t.id_customer =:ID AND t.id_package=p.id_package AND p.messages IS NULL AND date(CURRENT_TIMESTAMP) BETWEEN t.date_add AND t.date_add + interval p.period day");
            $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
            $stmt4->execute();
            $obj = $stmt4->fetchObject();
            $_SESSION['unlimited'] = $obj ? 1 : 0;
            $_SESSION['account_status'] = $stat->status;
            $_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
            $_SESSION['first_name'] = $stat->firstname;
            $_SESSION['last_name'] = $stat->lastname;
            $_SESSION['email'] = $stat->emailc;
            $_SESSION['balance'] = $stat->balance;
            $_SESSION['id_website'] = $stat->id_website;
            $_SESSION['id_company'] = $stat->id_account;
            $_SESSION['avatar'] = $stat->photo;
            $_SESSION['country'] = $stat->country;
            $_SESSION['id_link'] = $stat->id_link;
            //$_SESSION['currency'] = strlen($stat->currency) == 3 ? $stat->currency : $currencies[$stat->currency];

            $stmt4 = $conn->prepare("INSERT INTO connecting_log(id_customer,date) VALUES (:ID,NOW())");
            $stmt4->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
            $stmt4->execute();
        }
    }
} elseif (isset($_POST["login"]) && isset($_POST["pwd"])) {

    $login = $_POST["login"];
    $pwd = $_POST["pwd"];
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `login` = :LOGIN");
    $stmt->bindParam(':LOGIN', $login, PDO::PARAM_STR);
    $stmt->execute();
    $total = $stmt->rowCount();
    $result = $stmt->fetchObject();
    if ($total == 0) {
        $msgR = "User does not existe";
    } elseif (password_verify($pwd, $result->password)) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
            ini_set('session.gc-maxlifetime', 60 * 60 * 24 * 7);
            session_start();
            session_regenerate_id();
        }
        
        $_SESSION['login'] = $result->profile;
        $_SESSION['id_user'] = $result->id_user;
        $_SESSION['id_account'] = $result->id_profile;
        $_SESSION['lang'] = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : $result->lang;
        setcookie("user", $result->id_user);
        setcookie("customer", $result->id_profile);
        $stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm ,`browser` =:br ,`last_connect` =NOW()  WHERE `id_user` = :ID");
        $stmt3->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
        $stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $stmt3->bindParam(':br', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
        $stmt3->execute();

        if ($result->profile == 4) {
            $stmt4 = $conn->prepare("SELECT  id_customer,`photo`, `firstname`, `lastname`, `emailc`, `balance`, `id_account`,`status`,`id_website`,`country`,`id_link`  ,(CASE c.country WHEN NULL THEN (SELECT currency FROM accounts WHERE id_account=c.id_account) ELSE c.country END) currency FROM `customers` c WHERE c.id_customer = :ID");
            $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
            $stmt4->execute();
            $stat = $stmt4->fetchObject();
            $stmt3 = $conn->prepare("UPDATE leads SET status=1,update_date=NOW()  WHERE `id_customer` = :ID AND status=0");
            $stmt3->bindParam(':ID', $stat->id_customer, PDO::PARAM_INT);
            $stmt3->execute();
            $stmt4 = $conn->prepare("SELECT  * FROM packages p,transactionsc t WHERE t.id_customer =:ID AND t.id_package=p.id_package AND p.messages IS NULL AND date(CURRENT_TIMESTAMP) BETWEEN t.date_add AND t.date_add + interval p.period day");
            $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
            $stmt4->execute();
            $obj = $stmt4->fetchObject();
            $_SESSION['unlimited'] = $obj ? 1 : 0;
            $_SESSION['account_status'] = $stat->status;
            $_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
            $_SESSION['first_name'] = $stat->firstname;
            $_SESSION['last_name'] = $stat->lastname;
            $_SESSION['email'] = $stat->emailc;
            $_SESSION['balance'] = $stat->balance;
            $_SESSION['id_website'] = $stat->id_website;
            $_SESSION['id_company'] = $stat->id_account;
            $_SESSION['avatar'] = $stat->photo;
            $_SESSION['country'] = $stat->country;
            $_SESSION['id_link'] = $stat->id_link;

            $stmt4 = $conn->prepare("INSERT INTO connecting_log(id_customer,date) VALUES (:ID,NOW())");
            $stmt4->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
            $stmt4->execute();
        }
    } else {
        $msgR = "Email or Password incorrect";
    }
} else {
    echo "Login failed";
    die;
}
