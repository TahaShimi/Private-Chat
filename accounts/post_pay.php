<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('../init.php');
include('guest/config.php');
require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET["response"])) {
    $response = $_GET["response"];
    $firstname = $_GET["first_name"];
    $lastname = $_GET["last_name"];
    $id = $_GET["id_guest"];
    $email = $_GET["email"];
    $id_agent = $_GET["id_agent"];
    $id_pack = $_GET["id_pack"];
    $amount = $_GET["amount"];
    $currency = $_GET["currency"];
    $profile = 4;
    $lang = isset($_POST['lang']) && $_POST['lang'] != "" ? $_POST['lang'] : "en";
    $status = ($response == 'ok') ? 1 : 0;
    $stat = ($response == 'ok') ? "customer" : "lead";
    $password = password_hash($email, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("SELECT title, messages FROM packages WHERE id_package=:id_pack");
    $stmt->bindParam(':id_pack', $id_pack, PDO::PARAM_INT);
    $stmt->execute();
    $credit = $stmt->fetchObject();

    if ($id == 0) {

        $new_cust = ($response == 'ok') ? "new_cust" : "lead";
        $id = create_customer($conn, $firstname, $lastname, $email, $id_website, $id_account);
        $id_user = add_user($conn, $email, $password, $profile, $id, $lang);

        sendSocket($id, $new_cust, $firstname, $lastname, $id_agent, $credit->title, $credit->messages, $id_user, $stat);
        if (!isset($_SESSION['login'])) {
            include('auth.php');
        }
        session_start();
        if (!isset($_SESSION['login'])) {
            include('auth.php');
        }
        ($response == 'ok') ? sendMail($firstname, $email) : '';
    }

    $stmt1 = $conn->prepare("SELECT a.id_user, b.status FROM users a LEFT JOIN customers b ON b.id_customer = a.id_profile WHERE a.id_profile=:id AND a.profile = 4");
    $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt1->execute();
    $exist_user = $stmt1->fetchObject();

    /* if ($stmt1->rowCount() > 0) {
        $password = generate_pass();
        add_user($conn, $email, $password, $profile, $id, $lang);
    } */


    create_payment($conn, $id, $id_pack, $status, $amount,$currency);


    if ($response == "ok") {
        if (!$exist_user) {
            update_customer($conn, $firstname, $lastname, $email, $id, 1);

            $id_user = add_user($conn, $email, $password, $profile, $id, $lang);

            $new_cust = "new_cust";


            $stmt = $conn->prepare("UPDATE messages SET sender=:id_users , sender_role = 4 WHERE sender=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_users', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $conn->prepare("UPDATE messages SET receiver=:id_users , receiver_role = 4 WHERE receiver=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_users', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            update_balance($conn, $id, $credit->messages);
            sendSocket($id, $new_cust, $firstname, $lastname, $id_agent, $id_user, $credit->title, $credit->messages, $stat);
            session_start();
            if (!isset($_SESSION['login'])) {
                include('auth.php');
            }
            sendMail($firstname, $email);
        } else {

            $new_cust = "";

            update_balance($conn, $id, $credit->messages);
            sendSocket($exist_user->id_user, $new_cust, $firstname, $lastname, $id_agent, $id_user, $credit->title, $credit->messages, $stat);
            /* session_start();
            if (!isset($_SESSION['login'])) {
                include('auth.php');
            } */
        }
    } else {

        if (!$exist_user) {
            $new_cust = "lead";

            update_customer($conn, $firstname, $lastname, $email, $id, 1);

            $stmt = $conn->prepare("UPDATE messages SET sender=:id_users , sender_role = 4 WHERE sender=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_users', $id_user, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $conn->prepare("UPDATE messages SET receiver=:id_users , receiver_role = 4 WHERE receiver=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_users', $id_user, PDO::PARAM_INT);
            $stmt->execute();

            sendSocket($id, $new_cust, $firstname, $lastname, $id_agent, $id_user, $credit->title, $credit->messages, $stat);
        } else {
            $new_cust = "";
            sendSocket($exist_user->id_user, $new_cust, $firstname, $lastname, $id_agent, $id_user, $credit->title, $credit->messages, $stat);
            session_start();
            if (!isset($_SESSION['login'])) {
                include('auth.php');
            }
        }
    }
}

function sendSocket($id, $new_cust, $firstname, $lastname, $id_agent, $id_user, $title, $credit, $stat)
{
    \Ratchet\Client\connect('ws://45.76.121.27:8081')->then(function ($conn) use ($id, $new_cust, $firstname, $lastname, $id_agent, $id_user, $title, $credit, $stat) {
        $conn->send(json_encode([
            'command' => 'guestStatus',
            'id_guest' => $id,
            'id_user' => $id_user,
            'id_agent' => $id_agent,
            'new_cust' => $new_cust,
            'firstname' => $firstname,
            'balance' => $credit,
            'title' => $title,
            'lastname' => $lastname,
            'status' => $stat
        ]));
        $conn->close();
    }, function ($e) {
        echo "Could not connect: {$e->getMessage()}\n";
    });
}


function update_customer($conn, $firstname, $lastname, $email, $id, $status)
{
    $stmt = $conn->prepare("UPDATE customers SET firstname=:firstname,lastname=:lastname,emailc=:email , status=:status WHERE id_customer=:id");
    $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

function create_customer($conn, $firstname, $lastname, $email, $id_website, $id_account)
{
    $stmt = $conn->prepare("INSERT INTO `customers` (firstname, lastname, emailc, date_start, status, id_website, id_account, photo) VALUES (:firstname, :lastname, :email, NOW(), 1, :id_website, :id_account, 'customer.jpg')");
    $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id_website', $id_website, PDO::PARAM_STR);
    $stmt->bindParam(':id_account', $id_account, PDO::PARAM_STR);
    $stmt->execute();
    $id_user = $conn->lastInsertId();
    return $id_user;
}

function update_balance($conn, $id, $credit)
{
    $stmt = $conn->prepare("UPDATE customers SET balance= balance+:credit WHERE id_customer=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':credit', $credit, PDO::PARAM_INT);
    $stmt->execute();
}

function create_payment($conn, $id, $id_pack, $status, $amount, $currency)
{
    $stmt = $conn->prepare("INSERT INTO transactionsc (id_customer, id_package, status, date_add, final_price, currency) VALUES (:id, :id_pack, :status, NOW(), :amount, :currency)");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':id_pack', $id_pack, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':currency', $currency, PDO::PARAM_STR);
    $stmt->execute();
}

function add_user($conn, $email, $password, $profile, $id, $lang)
{
    $stmt1 = $conn->prepare("INSERT INTO `users`(`login`, `password`, `profile`, `id_profile`, `date_add`, `active`, `status`,lang) VALUES (:lg,:pwd,:pr,:ip,NOW(),1,1,:lang)");
    $stmt1->bindParam(':lg', $email, PDO::PARAM_STR);
    $stmt1->bindParam(':pwd', $password, PDO::PARAM_STR);
    $stmt1->bindParam(':pr', $profile, PDO::PARAM_INT);
    $stmt1->bindParam(':ip', $id, PDO::PARAM_INT);
    $stmt1->bindparam(":lang", $lang, PDO::PARAM_STR);
    $stmt1->execute();
    $id_users = $conn->lastInsertId();
    return $id_users;
}

?>

<body>
    <?php if (isset($_GET["response"])) {
        $response = $_GET["response"];
        if ($response == "ok") { ?>
            <button id="closewindow" onclick="windowClose()"></button>
        <?php } else { ?>
            <button id="closewindow" onclick="windowClose()"></button>
    <?php }
    } ?>

    <script>
        document.getElementById("closewindow").click();

        function windowClose() {
            window.close();
        }
    </script>
</body>