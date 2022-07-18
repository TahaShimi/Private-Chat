<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('../../init.php');
include('config.php');


session_start();

if (isset($_GET["logout"])) {

    session_unset();
    session_destroy();
    if (isset($_COOKIE['user']) && isset($_COOKIE['customer'])) {
        setcookie("user", "", time() - (2 * 24 * 60 * 60), 'http://45.76.121.27/chat/');
        setcookie("customer", "", time() - (2 * 24 * 60 * 60), 'http://45.76.121.27/chat/');
    }

    header('Location: index.php');
    exit();
}
$lang = isset($_COOKIE["chat"]) ? $_COOKIE["chat"] : "fr";
$msgR = "";
$msgRe = (isset($_GET["cde"])) ? $_GET["cde"] : null;

if (isset($_COOKIE["id_customer"])) {
    include('../auth.php');
} else if (isset($_POST['valider'])) {
    include('../auth.php');
}

if (isset($_POST["update-avatar"])) {
    $picture = NULL;
    if (isset($_FILES["picture"]["name"]) && $_FILES["picture"]["name"] != "") {
        $dirLogo = '../uploads/customers/';
        $uploadImg = removeAccents($_FILES["picture"]["name"]);
        $uploadImgTmp = removeAccents($_FILES["picture"]["tmp_name"]);
        $fileData1 = pathinfo(basename($uploadImg));
        $Imgnom = basename($uploadImg, "." . $fileData1['extension']);
        $photo = substr($Imgnom, 0, 25) . "-" . $id_account . '.' . $fileData1['extension'];
        $target_path1 = ($dirLogo . $photo);
        move_uploaded_file($uploadImgTmp, $target_path1);
        $stmt2 = $conn->prepare("UPDATE `customers` SET `photo`=:ph WHERE `id_customer`=:ID");
        $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
        $stmt2->bindParam(':ID', $_SESSION['id_account'], PDO::PARAM_INT);
        $stmt2->execute();
        $affected_rows = $stmt2->rowCount();

        if ($affected_rows != 0) {
            $stmt2->execute();
            $result = $stmt2->fetchObject();
        }
        unset($_POST);
    }
}

if (isset($_POST['update-account'])) {
    $first_name = (isset($_POST['first_name']) && $_POST['first_name'] != '') ? htmlspecialchars($_POST['first_name']) : NULL;
    $last_name = (isset($_POST['last_name']) && $_POST['last_name'] != '') ? htmlspecialchars($_POST['last_name']) : NULL;
    $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
    $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;

    $stmt2 = $conn->prepare("UPDATE `customers` SET `firstname`=:fn,`lastname`=:ln, `emailc`=:em, `phone`=:ph0 WHERE `id_customer`=:ID");
    $stmt2->bindParam(':fn', $first_name, PDO::PARAM_STR);
    $stmt2->bindParam(':ln', $last_name, PDO::PARAM_STR);
    $stmt2->bindParam(':em', $emailc, PDO::PARAM_STR);
    $stmt2->bindParam(':ph0', $phone, PDO::PARAM_STR);
    $stmt2->bindParam(':ID', $_COOKIE['customer'], PDO::PARAM_INT);
    $stmt2->execute();
    $affected_rows = $stmt2->rowCount();

    if (isset($_POST['pwd'])) {
        $newPassword = htmlentities($_POST['pwd']);
        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE `users` SET `login`=:em,`password`=:pwd, `password_updated_at`=NOW() WHERE `id_user`=:ID");
        $stmt->bindParam(':em', $emailc, PDO::PARAM_STR);
        $stmt->bindParam(':pwd', $newHashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':ID', $_COOKIE['user'], PDO::PARAM_INT);
        $stmt->execute();
        $affected_rows = $stmt->rowCount();
        unset($_POST);
    } else {
        unset($_POST);
    }

    if ($affected_rows != 0) {
        $stmt->execute();
        $result = $stmt->fetchObject();
    }
    unset($_POST);
}


$stmt = $conn->prepare("SELECT a.*, b.*, c.`name` AS website_name FROM `users` a LEFT JOIN `customers` b ON a.`id_profile` = b.`id_customer` LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE a.`id_user` = :ID");
$stmt->bindParam(':ID', $_SESSION["id_user"], PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchObject();


$stmt2 = $conn->prepare("SELECT * from accounts WHERE `id_account` = :IDA");
$stmt2->bindParam(':IDA', $id_account, PDO::PARAM_INT);
$stmt2->execute();
$company = $stmt2->fetchObject();
$admin_name = $company->business_name;


if (isset($_SESSION['id_user'])) {
    $stmt = $conn->prepare("SELECT *, (SELECT count(*) FROM `messages` WHERE status=0 AND sender =u.id_user AND receiver=:id) as unread_messages_count, (SELECT count(*) FROM `messages` WHERE sender =u.id_user AND receiver=:id) as nbr_seen_mssg from consultants c join users u on u.id_profile=c.id_consultant where u.profile=3 AND id_account=:id_account AND u.id_user IN (SELECT Distinct `sender` FROM messages) and (find_in_set(:idw,c.websites) or c.websites is null)");
    $stmt->bindparam(":id_account", $id_account, PDO::PARAM_INT);
    $stmt->bindparam(":id",  $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt->bindparam(":idw", $id_website, PDO::PARAM_INT);
    $stmt->execute();
    $consultants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare("SELECT m.sender FROM messages m WHERE m.receiver =:id ORDER BY m.date_send DESC LIMIT 1");
    $stmt2->bindparam(":id",  $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt2->execute();
    $last_conv_agent = $stmt2->fetchObject();
} else if (isset($_COOKIE['customer'])) {
    $stmt = $conn->prepare("SELECT *, (SELECT count(*) FROM `messages` WHERE sender=u.id_user AND receiver=:id_cust AND status = 0) as unread_messages_count, (SELECT count(*) FROM `messages` WHERE (sender =u.id_user or receiver =u.id_user) AND (sender =:id_cust or receiver=:id_cust)) as nbr_seen_mssg from consultants c join users u on u.id_profile=c.id_consultant where u.profile=3 AND id_account=:id_account and (find_in_set(:idw,c.websites) or c.websites is null)");
    $stmt->bindparam(":id_account", $id_account, PDO::PARAM_INT);
    $stmt->bindparam(":id_cust",  $_COOKIE['customer'], PDO::PARAM_INT);
    $stmt->bindparam(":idw", $id_website, PDO::PARAM_INT);
    $stmt->execute();
    $consultants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare("SELECT m.sender FROM messages m WHERE m.receiver =:id_cust ORDER BY m.date_send DESC LIMIT 1");
    $stmt2->bindparam(":id_cust",  $_COOKIE['customer'], PDO::PARAM_INT);
    $stmt2->execute();
    $last_conv_agent = $stmt2->fetchObject();
} else {
    $stmt = $conn->prepare("SELECT * from consultants c join users u on u.id_profile=c.id_consultant where u.profile=3 AND id_account=:id_account and (find_in_set(:idw,c.websites) or c.websites is null)");
    $stmt->bindparam(":id_account", $id_account, PDO::PARAM_INT);
    $stmt->bindparam(":idw", $id_website, PDO::PARAM_INT);
    $stmt->execute();
    $consultants = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$stmt = $conn->prepare("SELECT * from terms where id_account=:id AND status = 1");
$stmt->bindparam(":id",  $id_account, PDO::PARAM_INT);
$stmt->execute();
$terms = $stmt->fetchObject();


$stmt = $conn->prepare("SELECT * from messages where ((receiver = 0) and sender=:re) or (sender = 0 and receiver=:re)");
$stmt->bindparam(":re",  $_COOKIE['customer'], PDO::PARAM_INT);
$stmt->execute();
$stmt->fetchObject();
$def_agent = $stmt->rowCount();

if (isset($_SESSION['id_user'])) {
    $stmt = $conn->prepare("SELECT * from users where id_user=:id");
    $stmt->bindparam(":id",  $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt->execute();
    $custome = $stmt->fetchObject();
    if ($stmt->rowCount() > 0) {
        $stmt = $conn->prepare("SELECT balance from customers where id_customer=:id");
        $stmt->bindparam(":id", $custome->id_profile, PDO::PARAM_INT);
        $stmt->execute();
        $balance = $stmt->fetchObject();
    }
} else {
    $balance = new stdClass();
    $balance->balance = 0;
}

/* $stmt = $conn->prepare("SELECT * from p.packages where public = 1 ");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_OBJ); */

$stmt = $conn->prepare("SELECT p.*,CASE WHEN t.content is not null then t.content ELSE p.title end title,
(SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null))
                                    and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:idc,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount,
(SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null))
                               and o.id_package=p.id_package
                               and (o.access=1 or FIND_IN_SET(:idc,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count from packages p
left join translations t on t.table='packages' and t.id_element=p.id_package and t.lang='en' where  p.status=1 AND p.public=1 and p.id_website=:IDW");
$stmt->bindparam(":idc",  $id_account);
$stmt->bindparam(":IDW",  $id_website);
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $conn->prepare("SELECT * FROM packages_price WHERE currency = (CASE WHEN (SELECT DISTINCT currency FROM packages_price pp where pp.currency='EUR' AND pp.date_end IS null) IS NOT NULL THEN 'EUR' ELSE (SELECT currency FROM accounts where id_account=:IDC) END) AND date_end IS null");
$stmt->bindParam(':IDC', $id_account, PDO::PARAM_INT);
$stmt->execute();
$pkg = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pkg = array_combine(array_column($pkg, 'id_package'), $pkg);
$stmt = $conn->prepare("SELECT * from accounts_messages where id_account=:id");
$stmt->bindparam(":id",  $id_account, PDO::PARAM_INT);
$stmt->execute();
$cont = $stmt->fetchObject();

if (isset($_SESSION['id_account'])) {
    $stmt = $conn->prepare("SELECT id_transaction from transactionsc where id_customer=:id");
    $stmt->bindparam(":id",  $_SESSION['id_account'], PDO::PARAM_INT);
    $stmt->execute();
    $transactions = $stmt->fetchObject();
}


?>

<!-- App favicon -->
<title>Private Chat</title>
<link rel="shortcut icon" href="assets/images/favicon.ico">
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<!-- magnific-popup css -->
<link href="assets/libs/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css">

<!-- owl.carousel css -->
<link rel="stylesheet" href="assets/libs/owl.carousel/assets/owl.carousel.min.css">

<link rel="stylesheet" href="assets/libs/owl.carousel/assets/owl.theme.default.min.css">

<!-- Bootstrap Css -->
<link href="assets/css/bootstrap-dark.min.css" id="bootstrap-dark-style" rel="stylesheet" type="text/css" disabled="disabled">
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css">
<!-- Icons Css -->
<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
<link href="../assets/sweetalert2.min.css" rel="stylesheet" type="text/css">
<!-- App Css-->
<link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css">
<link href="assets/css/style.css" rel="stylesheet" type="text/css">
<link href="assets/css/emojionearea.css" rel="stylesheet" type="text/css">
<style>
    ._51lp {
        margin-left: -13px;
        margin-top: -6px;
        background-clip: padding-box;
        display: inline-block;
        font-family: 'helvetica neue', Helvetica, Arial, sans-serif;
        font-size: 10px;
        -webkit-font-smoothing: subpixel-antialiased;
        line-height: 1.3;
        min-height: 13px;
        color: white !important;
    }

    ._3z_5 {
        background-color: #fa3e3e;
        border-radius: 2px;
        color: #fff;
        padding: 1px 3px;
    }
</style>

<!-- Loader -->
<div class="loading">
    <div class="loader"></div>
</div>
<!-- End Loader -->
<div class="layout-wrapper d-lg-flex hide">

    <!-- Start left sidebar-menu -->
    <div class="side-menu flex-lg-column me-lg-1 ms-lg-0" style="z-index: 0;">
        <!-- LOGO -->
        <div class="navbar-brand-box" style="height: 5%;margin: 25px auto auto;">
            <a href="index.php" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="assets/images/logo.png" alt="">
                </span>
            </a>

            <a href="index.php" class="logo logo-light">
                <span class="logo-sm">
                    <img src="assets/images/logo.svg" alt="" height="30">
                </span>
            </a>
        </div>
        <!-- end navbar-brand-box -->

        <!-- Start side-menu nav -->
        <div class="conversations-list has-slimscroll-xs" style=" height: 100%; margin: 40px 0 !important;">
            <li id="consultant-li-default">
                <a href="javascript:void(0)" class="user-item is-active" data-name="Yanni (Chat Bot)" data-id="0">
                    <div class="avatar-container">
                        <img class="user-avatar" src="assets/images/consult.png" alt="">
                        <i class="text-success ri-record-circle-fill font-size-10 d-inline-block ms-1 consult-icon" id="status-default"></i>
                    </div>
                </a>
            </li>
            <?php
            $total_unread_messages = 0;
            foreach ($consultants as $consultant) {     ?>
                <li id="consultant-li-<?= $consultant['id_user'] ?>" class="<?= (isset($consultant['nbr_seen_mssg']) && $consultant['nbr_seen_mssg'] > 0) ? '' : 'hide' ?>">
                    <a href="javascript:void(0)" class="user-item" data-name="<?= $consultant['pseudo'] ?>" data-id="<?= $consultant['id_user'] ?>">
                        <div class="avatar-container">
                            <img class="user-avatar" src="<?php echo '../uploads/consultants/' . $consultant['photo'] ?>" alt="">
                            <?php if (isset($consultant['unread_messages_count']) && $consultant['unread_messages_count'] != "0") {
                                $total_unread_messages++; ?>
                                <span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $consultant['id_user'] ?>"><?php echo $consultant['unread_messages_count'] ?></span>
                            <?php } else { ?>
                                <span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $consultant['id_user'] ?>" style="display: none">0</span>
                            <?php } ?>
                            <i class="text-danger ri-record-circle-fill font-size-10 d-inline-block ms-1 consult-icon" id="status-<?= $consultant['id_user'] ?>"></i>
                        </div>
                    </a>
                </li>
            <?php } ?>
        </div>
        <!-- end side-menu nav -->


        <!-- Side menu user -->
    </div>
    <!-- end left sidebar-menu -->


    <!-- Start User chat -->
    <div class="user-chat w-100 overflow-hidden user-chat-show">
        <div class="d-lg-flex" style="width: 100%;height: 100%;min-height: inherit;">

            <!-- start chat conversation section -->
            <div class="w-100 overflow-hidden position-relative conversation" style="z-index: 0;">
                <div class="p-3 p-lg-4 border-bottom user-chat-topbar" style="background-color: #efefef; border-bottom: 1px solid #dedede !important; backdrop-filter: blur(7px);padding: 20px;position: relative;z-index: 1000;">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-8">
                            <div class="d-flex align-items-center agent_conn d-none agent-<?= isset($consultants->id_user) ? $consultants->id_user : "default" ?>">
                                <div class="d-block d-lg-none me-2 ms-0 d-none">
                                    <a href="javascript: void(0);" class="user-chat-remove text-muted font-size-16 p-2"><i class="ri-menu-line"></i></a>
                                </div>
                                <div class="me-3 ms-0 d-flex">
                                    <img class="rounded-circle avatar-xs" src="" alt=""><i class="ri-record-circle-fill font-size-10 d-inline-block ms-1 text-danger status-xs" id="status-1" style="margin: 20px auto auto -8px !important;"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden" style="margin: 8px auto auto -10px !important;">
                                    <h4 class="font-size-16 mb-0 text-truncate"><a href="#" class="text-reset user-profile-show agent-name"></a></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1 correct-header <?= isset($_SESSION['login']) ? "d-none" : ""; ?>"></div>
                        <div class="col-sm-6 col-8" style="width: 62%; display: inline-flex; justify-content: flex-end;">
                            <div class="balances col-sm-8 col-8" style="float: right; text-align: right; margin-right: 25px; width: 100%;">
                                <small class="selected_customer_balance acheter d-flex buy <?= isset($_SESSION['login']) && (($balance->balance) == 0) ? "danger" : (($balance->balance) == 3 ? "warning" : "") ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= $cont->buy_tooltip ?>">
                                    <button type="button" style="display: contents;color: #495057;"><i class="ri-shopping-cart-2-line"></i>
                                        <p style="font-size: 14px;font-weight: 600; color: #495057;margin-bottom: 0;"><?= $cont->buy_button ?></p>
                                    </button>
                                </small>
                                <small class="selected_customer_balance d-flex">
                                    <span class="selected_customer_balance_prefix <?= isset($_SESSION['login']) && (isset($balance) && ($balance->balance) == 0) ? "text-danger" : (isset($balance) && ($balance->balance) == 3 ? "text-warning" : "") ?>" style="color: #434f68;font-size: 13px;font-weight: 600 !important;padding-right: 5px;"><span>Crédit messages : </span></span>
                                    <span class="balance <?= isset($_SESSION['id_user']) && (isset($balance) && ($balance->balance) == 0) ? "text-danger" : (isset($_SESSION['id_user']) && (isset($balance) && ($balance->balance) == 3) ? "text-warning" : "") ?>" style="color: black;font-size: 15px;font-weight: 600;" data-balance="<?= isset($_SESSION['id_user']) ? $balance->balance : 0 ?>"> <?= isset($_SESSION['id_user']) ? $balance->balance : 0 ?> </span>
                                </small>
                            </div>
                            <div class="col-sm-2 col-8 <?= isset($_SESSION['login']) ? "d-none" : ""; ?>" style="float: right;text-align: right;width: auto;display:flex; justify-content: center;">
                                <button type="button" class="login btn identifier py-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="S'identifier"><span class="logintxt">S'identifier</span><i class="ri-login-box-line"></i></button>
                                <div class="dropdown position-relative" id="dd">
                                    <button class="btn dropdown-toggle menu-ident" data-boundary="dd" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ri-arrow-down-s-line"></i></button>
                                    <div tabindex="-1" role="menu" aria-hidden="false" class="dropdown-menu-end dropdown-menu" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-101px, 40px, 0px);" x-placement="bottom-start">
                                        <!-- text-->
                                        <a href="javascript:" data-bs-target="#termsModal" data-bs-toggle="modal" class="dropdown-item <?= ($terms->content == "") ? 'disabled' : '' ?>"><i class="ri-list-settings-line"></i> Conditions générales</a>
                                        <!-- text-->
                                        <div class="dropdown-divider"></div>
                                        <!-- text-->
                                        <a href="javascript:" data-bs-target="#contactModal" data-bs-toggle="modal" class="dropdown-item"><i class="ri-phone-line"></i> Contactez-Nous</a>
                                        <!-- text-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['login'])) { ?>
                            <div class="col-sm-1 col-8" style="width: 3.33333% !important;">
                                <ul class="nav side-menu-nav justify-content-center" style="float: right;">
                                    <li class="nav-item btn-group dropup profile-user-dropdown">
                                        <div class="dropdown position-relative" id="dd" style="float: right;">
                                            <a class="nav-link dropdown-toggle" href="#" data-boundary="dd" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <img src="<?= isset($result->photo) ? '../uploads/customers/' . $result->photo : '../uploads/customers/img-1.png' ?>" alt="" class="profile-user rounded-circle">
                                            </a>
                                            <div tabindex="-1" role="menu" aria-hidden="false" class="dropdown-menu-end dropdown-menu" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-101px, 40px, 0px);" x-placement="bottom-start">
                                                <!-- text-->
                                                <a href="javascript:" data-bs-target="#profileModal" data-bs-toggle="modal" class="dropdown-item"><i class="ri-user-line"></i> My Profile</a>
                                                <!-- text-->
                                                <!-- text-->
                                                <div class="dropdown-divider"></div>
                                                <!-- text-->
                                                <a href="javascript:" data-bs-target="#termsModal" data-bs-toggle="modal" class="dropdown-item <?= ($terms->content == "") ? 'disabled' : '' ?>"><i class="ri-list-settings-line"></i> Conditions générales</a>
                                                <!-- text-->
                                                <div class="dropdown-divider"></div>
                                                <!-- text-->
                                                <a href="javascript:" data-bs-target="#contactModal" data-bs-toggle="modal" class="dropdown-item"><i class="ri-phone-line"></i> Contactez-Nous</a>
                                                <!-- text-->
                                                <div class="dropdown-divider"></div>
                                                <!-- text-->
                                                <a href="?logout" id="btn-logout" class="dropdown-item"><i class="ri-logout-circle-r-line"></i> Logout</a>
                                                <!-- text-->
                                            </div>
                                        </div>

                                    </li>
                                </ul>
                            </div>
                        <?php } ?>

                    </div>
                </div>
                <!-- end chat user head -->

                <!-- start chat conversation -->
                <div class="chat-conversation p-3 p-lg-4" data-simplebar="init" style="padding-bottom: 6rem !important;">
                    <div class="simplebar-wrapper" style="margin: -12px;">
                        <div class="simplebar-height-auto-observer-wrapper">
                            <div class="simplebar-height-auto-observer"></div>
                        </div>
                        <span id="loading" style="display: none; font-size: 16px; font-weight: 600; margin: 0px 47%;">chargement...</span>
                        <div class="simplebar-mask chat-rbox">
                            <div class="simplebar-offset">
                                <div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden scroll; padding-right: 20px; padding-bottom: 0px;">
                                    <div class="simplebar-content">
                                        <div id="chat-welcome" class="fto">
                                            <div>
                                                <h1 class="fto-r1"><?= $cont->welcome_title ?></h1>
                                                <h1 class="fto-r2"> ~~</h1>
                                                <h1 class="fto-r3"><?= $cont->welcome_description ?></h1>
                                            </div>
                                        </div>
                                        <ul class="list-unstyled mb-0 chat_container">
                                            <!-- chat Row -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="simplebar-placeholder" style="width: auto; height: 1229px;"></div>
                    </div>
                    <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                        <div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
                    </div>
                    <div class="simplebar-track simplebar-vertical" style="visibility: visible; z-index: 0 !important;">
                        <div class="simplebar-scrollbar" style="height: 509px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                    </div>
                </div>
                <!-- end chat conversation end -->

                <!-- start chat input section -->
                <div class="chat-input-section p-3 p-lg-4 border-top mb-0" style=" position: absolute; width: 100%; bottom: 0px;">

                    <div class="row g-0" style=" margin: auto;">
                        <div class="col d-block" style=" margin-left: 15px !important;">
                            <div class="col-10" style="text-align: right; margin-bottom: 10px;  padding-top: 10px; width: 99.3%;">
                                <?php if ($company->max_length > 0) { ?>
                                    <small id="content_watcher_container"><?php echo ($trans["chat"]["remain"]) ?> <span id="content_watcher">0</span>/ <?= $company->max_length ?> <?php echo ($trans["chat"]["characters"]) ?></small>
                                <?php } ?>
                            </div>
                            <div class="col" style="height: 6vh !important; margin-bottom: 6px;">
                                <textarea <?php if ($company->max_length > 0) {
                                                echo 'maxlength="' . $company->max_length . '"';
                                            } ?> type="text" id="content" class="form-control form-control-lg bg-light border-light one-content <?= (isset($_SESSION['login'])) && (($balance->balance) == 0) ? 'disabled' : '' ?>" placeholder="<?= (isset($_SESSION['login'])) && (($balance->balance) == 0) ? "Il faut acheter des messages..." : "Saisissez votre message ici..." ?>" style="margin: 20px !important;background-color: #ffffff!important; height: 3vh !important;z-index: 1000;"></textarea>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" id="send_message" class="btn btn-primary font-size-17 btn-lg chat-send waves-effect waves-light" style="margin: 0 11px 0 0; top: 39%;">
                                <i class="ri-send-plane-2-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- end chat input section -->
            </div>
            <!-- end chat conversation section -->

        </div>
        <!-- End User chat -->
    </div>
    <!-- end  layout wrapper -->
    <!-- start chat-rightsidebar -->
    <div class="chat-rightsidebar <?= (isset($_SESSION['login'])) && (($balance->balance) == 0) ? "" : "d-none" ?>" style="background: #f5f7fb;width: 50%;border-left: 4px solid #dedede !important;height: 100vh;">
        <div class="tab-content">
            <div class="container pricing-tab" style="display: block;margin: auto;height: 100%;padding: 0px;">
                <div class="close text-end me-2 mt-2">
                    <button type="button" class="btn-close" id="close-1" aria-label="Close"></button>
                </div>
                <h1 class="my-5 text-center" style="font-family: 'default',sans-serif !important;color: #33539e;"><?= $cont->credit_box_title ?></h1>
                <p style="font-family: 'default',sans-serif !important;color: #545e72;" class="text-center fs-5 px-5 my-5"><?= $cont->credit_box_description ?></p>
                <div class="row pricing-card" style="    display: table;  clear: both; width: 100% !important;text-align: center;">
                    <?php foreach ($packages as $package) {
                        if ($package->total_discount == 0) { ?>
                            <div class="col-lg-3 col-md-12 right-pack" style="width: 45%; float: none; margin: 10px auto; VERTICAL-ALIGN: middle;display: inline-block;">
                                <div class="price-table-1">
                                    <div class="price-header">
                                        <div class="price-value-1">
                                            <h2><span>€</span><?= $pkg[$package->id_package]['price'] ?></h2><span><?= $package->title ?></span>
                                        </div>
                                        <h3 class="price-title-1"><?= $package->messages ?><span style="font-size: 10px;">Messages</span></h3><a class="buy-cred btn-1 btn-theme btn-circle my-3" data-idagent="0" data-id="<?= $package->id_package ?>" data-price="<?= $pkg[$package->id_package]['price'] ?>" data-text="Purchase Now">Buy Now</a>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="col-lg-3 col-md-12 right-pack" style="width: 45%; float: none; margin: 10px auto; VERTICAL-ALIGN: middle;display: inline-block;">
                                <div class="price-table-1">
                                    <div class="price-header">
                                        <div class="shape-box-1">
                                            <div class="shape-bg">
                                                <h5 class="disc-1"> <?= $package->total_discount ?>%</h5>
                                            </div>
                                        </div>
                                        <div class="price-value-1">
                                            <h2><span>€</span><?= round(((100 - $package->total_discount) * ($pkg[$package->id_package]['price'] / 100)), 2)  ?></h2><span><?= $package->title ?></span>
                                        </div>
                                        <h3 class="price-title-1"><?= $package->messages ?><span style="font-size: 10px;">Messages</span></h3><a class="buy-cred btn-1 btn-theme btn-circle my-3" data-idagent="0" data-id="<?= $package->id_package ?>" data-price="<?= round(((100 - $package->total_discount) * ($pkg[$package->id_package]['price'] / 100)), 2)  ?>" data-text="Purchase Now">Buy Now</a>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- end chat-rightsidebar -->
</div>
<form id="payment-redirection" method="get" action="resp_form.php" class="hide">
    <input type="hidden" name="id_company" value="48" />
    <input type="hidden" name="id_shop" value="12" />
    <input type="hidden" name="first_name" value="<?= isset($_SESSION['login']) ? $_SESSION['first_name'] : '' ?>" />
    <input type="hidden" name="last_name" value="<?= isset($_SESSION['login']) ? $_SESSION['last_name'] : '' ?>" />
    <input type="hidden" name="email" value="<?= isset($_SESSION['login']) ? $_SESSION['email'] : '' ?>" />
    <input type="hidden" name="id_guest" value="<?= isset($_COOKIE['customer']) ? $_COOKIE['customer'] : $_SESSION['id_account'] ?>" />
    <input type="hidden" name="id_agent" value="">
    <input type="hidden" name="id_pack" value="">
    <input type="hidden" name="amount" value="">
    <input type="hidden" name="currency" value="" />
    <input type="submit" name="submit" style="display: none;" value="Validate">
</form>
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content d-flex">

            <div class="modal-body">
                <div class="tab-pane active" id="account" role="tabpanel">
                    <div class="card-body">
                        <div class="row d-block">
                            <div class="col-md-8" style="margin: 14px auto;text-align: center;">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="avatar-wrapper m-b-10">
                                        <img class="profile-pic" src="<?php echo '../uploads/customers/img-1.png' ?>">
                                        <div class="upload-button">
                                        </div>
                                        <input class="file-upload" name="picture" type="file" accept="image/*">
                                    </div>
                                    <button type="submit" name="update-avatar" id="update-avatar" class="btn btn-success btn-rounded"><i class="ri-check-line"></i> Mettre à jour l'avatar</button>
                                </form>
                            </div>
                        </div>
                        <div class="row d-block">
                            <div class="col-md-12" style="margin: 14px auto;text-align: center;">
                                <div class='alert alert-success alert-dismissable'> Votre Mot de passe est le meme que votre Email </div>
                            </div>
                        </div>
                        <form class="form-horizontal form-material" id="profileform" action="" method="POST" style="  margin: auto;">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="acctInput1">Prénom</label>
                                    <input type="text" name="first_name" class="form-control form-control-line shadow" id="acctInput1" value="<?= (isset($_SESSION['login'])) ? $result->firstname : ""  ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="acctInput2">Nom</label>
                                    <input type="text" name="last_name" class="form-control form-control-line shadow" id="acctInput2" value="<?= (isset($_SESSION['login'])) ? $result->lastname : ""  ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="example-email">Email</label>
                                    <input type="email" value="<?= (isset($_SESSION['login'])) ? $result->emailc : ""  ?>" class="form-control form-control-line shadow" name="emailc" id="emailc" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Téléphone</label>
                                    <input type="text" value="<?= (isset($_SESSION['login'])) ? $result->phone : ""  ?>" class="form-control form-control-line shadow" name="phone" id="phonc" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="acctInput3">Password</label>
                                    <div class=" position-relative">
                                        <input type="password" name="pwd" class="form-control form-control-line shadow" id="pwd" value="" required="">
                                        <button class="btn btn-show btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button"><i class="ri-eye-fill align-middle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <button type="submit" name="update-account" class="btn btn-primary btn-upda">Mettre à jour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content d-flex">
            <div class="modal-header">
                <h5 class="modal-title">Contactez Nous</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-family: 'default',sans-serif !important;">
                <div class="tab-pane active" id="account" role="tabpanel">
                    <div class="card-body" style="padding: 1.25rem 2.25rem 2.25rem !important;">
                        <div class="row d-block">
                            <!-- <div class="col-md-8" style="margin: auto;text-align: center;">
                                <h1 style="margin: auto;font-family: 'default',sans-serif !important;font-style: normal !important;font-weight: normal !important;"></h1>
                            </div> -->
                            <!-- <div class="grid-x title-border">
                                <div class="cell small-5 title-border-left inline">&nbsp;</div>
                                <div class="cell small-2 title-border-center inline">&nbsp;</div>
                                <div class="cell small-5 title-border-right inline">&nbsp;</div> 
                            </div>-->
                        </div>
                        <form class="form-horizontal form-material" action="" method="POST" style="  margin: auto;">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="acctInput1">Prénom</label>
                                    <input type="text" name="first_name" class="form-control form-control-line shadow" id="first_name" value="<?= (isset($_SESSION['login'])) ? $result->firstname : "" ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="acctInput2">Nom</label>
                                    <input type="text" name="last_name" class="form-control form-control-line shadow" id="last_name" value="<?= (isset($_SESSION['login'])) ? $result->lastname : "" ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="example-email">Email</label>
                                    <input type="email" value="<?= (isset($_SESSION['login'])) ? $result->emailc : "" ?>" class="form-control form-control-line shadow" name="emailc" id="email" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Téléphone</label>
                                    <input type="text" value="<?= (isset($_SESSION['login'])) ? $result->phone : "" ?>" class="form-control form-control-line shadow" name="phone" id="phone" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="raison">Raison</label>
                                    <select name="raison" id="raison" class="form-control form-control-line shadow">
                                        <option value="1">Réclamation</option>
                                        <option value="2">Remerciement</option>
                                        <option value="3">Erreur</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="acctInput3">Message</label>
                                    <textarea name="message" id="message" class="form-control form-control-line shadow"></textarea>
                                </div>
                            </div>
                            <div class="form-group " style="float: right;">
                                <button type="submit" name="update-account" class="btn btn-primary btn-upda d-flex">Envoyer <i class="ri-send-plane-2-fill" style="margin-left: 10px;text-align: center;"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content d-flex">
            <div class="modal-header">
                <h5 class="modal-title"><?= $terms->title ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex">
                <div class="d-flex flex-column" style="margin: auto !important;padding: 50px 50px;">
                    <!-- <h1 class="text-center mb-5"><?= $terms->title ?></h1> -->
                    <p class="font-size-14 text-justify"><?= $terms->content ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" style="max-height: 50vh;">
                <div class="row m-0">
                    <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="right: 5px;top: 5px;z-index: 1;background-color: white;opacity: 1;"></button>
                    <div class="d-flex flex-column text-center col-12 col-md-6 py-5">
                        <div class="form-title text-center">
                            <h4 style="font-weight: bolder;font-family: Poppins,sans-serif;">S'identifier</h4>
                        </div>
                        <div id="error" class="d-none">User does not existe</div>
                        <form id="loginform" action="" method="POST">
                            <div class="form-group mx-2 my-3">
                                <input type="email" class="form-control" id="email1" name="login" placeholder="Adresse Mail..." required="" style="border-radius: 25px;background-color: #f4f4f4c4;color: #50a5f1 !important;font-family: monospace !important;">
                            </div>
                            <div class="form-group position-relative mx-2 my-3">
                                <input type="password" class="form-control" id="password1" name="pwd" placeholder="Mot de passe..." required="" style="border-radius: 25px;background-color: #f4f4f4c4;color: #50a5f1 !important;font-family: monospace !important;">
                                <button class="btn btn-show btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button"><i class="ri-eye-fill align-middle"></i></button>
                            </div>
                            <button type="submit" name="valider" class="col-10 col-md-6 btn btn-block btn-round mt-3" id="btn-log" style="background-color: #ff4f5a;color: white;font-family: Poppins,sans-serif;">S'identifier</button>
                        </form>
                    </div>
                    <div class="overlay-right col-md-6">
                        <h1>Content de vous revoir cher membre <span style="color: #ffa593;">♥</span> S'identifier ici Si vous avez déjà un compte</h1>
                        <p class="text-center font-size-13">vous pouvez utiliser vos accès reçus précédemment sur votre boite mail pour vous
                            connecter. Si vous êtes nouveau, achetez des crédits et vous serez automatiquement
                            authentifié.</p>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer d-flex justify-content-center">
                <div class="signup-section">Not a member yet? <a href="#a" class="text-info"> Sign Up</a>.</div>
            </div> -->
        </div>
    </div>
</div>
<!-- JAVASCRIPT -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>

<!-- Magnific Popup-->
<script src="assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>

<!-- owl.carousel js -->
<script src="assets/libs/owl.carousel/owl.carousel.min.js"></script>

<!-- page init -->
<script src="assets/js/index.init.js"></script>
<script src="assets/js/app.js"></script>
<script src="../../assets/js/env.js"></script>
<script src="../../assets/js/moment.js"></script>
<script src="../assets/sweetalert2.all.min.js"></script>
<script src="assets/js/emojionearea.js"></script>

<?php if ($msgR != "") {
    echo "<script>$(document).ready(function(){ $('#loginModal').modal('show'); $('#error').removeClass('d-none');})</script>";
}
?>
<?php if (isset($_POST['valider'])) {
    echo "<script>if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}</script>";
}
?>

<script>
    var sender = "<?= isset($_SESSION['login']) ? $_SESSION['id_user'] : (isset($_COOKIE['customer']) ? $_COOKIE['customer'] : "") ?>";
    var id_group = parseInt('<?= isset($_SESSION['id_company']) ? $_SESSION['id_company'] : $id_account ?>');
    var sender_role = <?= isset($_SESSION['login']) ?  4 : 7 ?>;
    var conn = new WebSocket(wsCurEnv);
    conn.onopen = function(e) {
        if (sender != "") {
            conn.send(JSON.stringify({
                command: "attachAccount",
                account: sender,
                role: sender_role,
                name: 'guest' + sender,
                sender_avatar: 'img-1.png',
                id_group: id_group
            }));
        } else {
            conn.send(JSON.stringify({
                command: "attachAccount",
                id_group: id_group
            }));
        }
    };

    conn.onerror = function(e) {
        console.log(e);
    }
</script>
<script>
    var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.profile-pic').attr('src', e.target.result);
                $('#avatar').val(e.target.result);
                $('#update-avatar').css("display", 'block');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".file-upload").on('change', function() {
        readURL(this);
    });
    $(".upload-button").on('click', function() {
        $(".file-upload").click();
    });
    $(document).ready(function() {
        $('.dropdown-toggle').dropdown();
        $('.layout-wrapper').removeClass('hide');
        $('.loading').addClass('d-none');
    });
    $("#content").emojioneArea();
    $('.emoji-tool').on('click', function() {
        $('.emoji-tool').tooltip('hide');
    })
    $("#content_watcher").text(<?= $company->max_length ?>);


    $('.col').on("keyup", ".emojionearea-editor", function(e) {

        if (<?= $company->max_length ?> > 0) {
            set = <?= $company->max_length ?>;
            var tval = $('.emojionearea-editor').text();
            tlength = tval.length;
            remain = parseInt(set - tlength);
            $("#content_watcher").text(set - tlength);
            if (remain == 0) {
                $("#content_watcher_container").css("color", "red");
                $(".emojionearea-editor").blur();
                e.preventDefault();
            } else {
                $("#content_watcher_container").css("color", "black");
            }
        }
        $(".emojionearea-editor").removeAttr('onkeypress', 'return false');
        var limit = parseInt($(".emojionearea-editor").attr('data-maxlength'));
        var text = $(".emojionearea-editor").html();
        var chars = text.length;
        if (chars >= limit && e.keyCode != 32) {
            var new_text = text.substr(0, limit);
            $(".emojionearea-editor").html(new_text);
            $(".emojionearea-editor").attr('onkeypress', 'return false');
            e.preventDefault();
        }

    });

    $('.btn-show').on('click', function() {
        $('#password1').prop('type', $('#password1').prop('type') == 'text' ? 'password' : 'text');
        $('#pwd').prop('type', $('#pwd').prop('type') == 'text' ? 'password' : 'text');
    })
    $('#close-1').on('click', function() {
        $('.chat-rightsidebar').addClass('d-none');
        sendMessage({
            command: "buying",
            sender: sender,
            is_buying: 0,
            receiver: receiver,
            id_group: id_group
        });
    })
    $('.buy').on('click', function() {
        is_buying = 1;
        if ($('.chat-rightsidebar').hasClass('d-none')) {
            $('.chat-rightsidebar').removeClass('d-none');
        } else {
            $('.chat-rightsidebar').addClass('d-none');
            is_buying = 0;
        }
        $('.buy').tooltip('hide')
        sendMessage({
            command: "buying",
            sender: sender,
            is_buying: is_buying,
            receiver: receiver,
            id_group: id_group
        });
    })
    $('#btn-log').on('click', function() {
        sessionStorage.setItem("Show", "false");
        $('.login').addClass('hide');
        $('#loginModal').modal('hide');
        $('.layout-wrapper').addClass('hide');
        $('.loading').removeClass('d-none');
    })
    $('#btn-logout').on('click', function() {
        $('.layout-wrapper').addClass('hide');
        $('.loading').removeClass('d-none');
    })
    $('.login').on('click', function() {
        $('#loginModal').modal('show');
    })
    /* $(document).on('click', function(e) {
        if (!$('.chat-leftsidebar').is($(e.target)) && !$('.profile-user').is($(e.target)) && !$('.profile').is($(e.target)) && !$('.condition').is($(e.target)) && !$('.contact').is($(e.target))) {
            $('.chat-leftsidebar').addClass('d-none');
        }
    }); */
    /* $('.profile-user').on('click', function() {
        if ($('.chat-leftsidebar').hasClass('d-none')) {
            $('.chat-leftsidebar').removeClass('d-none');
        } else {
            $('.chat-leftsidebar').addClass('d-none');
        }
    }); */
    /* $('.profile-user-dropdown').on('click', function() {
        $('.chat-leftsidebar').removeClass('hide');
        $('.profile').addClass('active');
    }); */
    /* new EmojiPicker({
        trigger: [{
            selector: '.emojionearea-button',
            insertInto: '#content'
        }],
        closeButton: true,
    }); */
    $('#more').on("click", function() {
        /*if ($('#chatinputmorecollapse').hasClass('show')) {
            $('#chatinputmorecollapse').removeClass('show');
        } else {
            $('#chatinputmorecollapse').addClass('show');
        }*/
        $('.more').tooltip('hide');
    });
    var enter_option = <?= $company->enter_option ?>;
    if (enter_option == 1) {
        $('.col').on('keypress', '.emojionearea-editor', function(e) {
            if (e.keyCode == 13) {
                var content = $("#content").data("emojioneArea").getText();
                if (receiver == null || receiver_role == null) {
                    return false;
                }else {
                    $("#send_message").click();
                    return false;
                }
                $('#chat-welcome').addClass('hide');
            }

        });
    }


    $(document).on('click', '.buy-cred', function() {
        $('.chat-rightsidebar').addClass('d-none');
        $('#payment-redirection').find('[name="id_agent"]').val($(this).data("idagent"));
        $('#payment-redirection').find('[name="id_pack"]').val($(this).data("id"));
        $('#payment-redirection').find('[name="amount"]').val($(this).data("price"));
        $('#payment-redirection').find('[name="currency"]').val('EUR');
        $('#payment-redirection').find('[name="submit"]').click();

        if ($(this).hasClass('content-card')) {
            sendMessage({
                command: "buying",
                sender: sender,
                receiver: receiver,
                is_buying: 1,
                id_group: id_group
            });
        }
    })

    var cookie_customer = (document.cookie.match(new RegExp('(^| )customer=([^;]+)')) || ['', ''])[2];
    var max = 0;
    var receiver = null;
    var receiver_role = 3;
    var balance = 0;
    var customer_id = '<?= isset($_SESSION['login']) ? $_SESSION['id_account'] : "" ?>' || cookie_customer;
    var unlimited = '<?= isset($_SESSION['unlimited']) ? $_SESSION['unlimited'] : "" ?>';
    var unlimitedtext = '<?= $trans['unlimited'] ?>';
    var id_trans = '<?= isset($_SESSION['id_user']) ? $transactions->id_transaction : '' ?>';
    var first = false;
    var trans_class = "";
    var consultants = <?= json_encode(array_map('intval', array_column($consultants, 'id_user'))); ?>;
    var active_consultants = [];
    var id_account = parseInt(<?= $id_account ?>);
    var def_agent = <?= ($def_agent > 0) ? 'true' : 'false' ?>;
    var last_agent = <?= isset($last_conv_agent->sender) ? $last_conv_agent->sender : 0 ?>;
    var remain_chars = <?= $company->max_length ?>;


    $("#send_message").click(async function() {
        if (!first && sender == "") {
            await create_guest();
        }
        //j'ai corriger l'envoie des emojis 
        var content = $("#content").data("emojioneArea").getText();
        if (receiver == null || receiver_role == null) {} else if (!content.replace(/\s/g, '').length) {} else {
            sendMessage({
                command: "message",
                msg: content,
                sender: sender,
                sender_role: sender_role,
                receiver: receiver,
                receiver_role: receiver_role,
                id_account: id_account,
                account: receiver,
                customer_id: customer_id,
                id_group: id_group,
                unlimited: unlimited
            });
            $('#content').val('');
            first = true;
        }
        $('#chat-welcome').addClass('d-none');
        $('#content').val('');
        $('.emojionearea-editor').text('');
        $('#content_watcher').text(remain_chars);
        $("#content_watcher_container").css("color", "black");
        $('.simplebar-content-wrapper').scrollTop(1E10);
    });
    $('.col').on('focusin', '.emojionearea-editor', function() {
        $('.col .emojionearea-editor').attr('data-maxlength', <?= $company->max_length ?>);
    });
    $('.col').on('focusout', '.emojionearea-editor', function() {});
    $('.simplebar-content-wrapper').scroll(function(e) {
        if ($('.simplebar-content-wrapper').scrollTop() == 0) {
            $('#loading').show();
            $.when($.ajax({
                url: "../conversationTrait.php",
                type: "POST",
                data: {
                    action: "getConversationsList2",
                    customer_id: customer_id,
                    sender: sender,
                    sender_role: sender_role,
                    receiver: receiver,
                    receiver_role: receiver_role,
                    last_id: max
                },
                dataType: "json",
                success: function(dataResult) {
                    if (dataResult.statusCode == 200) {
                        $('.simplebar-content-wrapper').scrollTop(0);
                        dataResult.conversations.reverse().forEach(function(value) {
                            if (value.trans_change == "after") {
                                trans_class = "cust";
                            } else if (value.trans_change == "after") {
                                trans_class = "";
                            }
                            if (value.hasOwnProperty("sender_role") || value.hasOwnProperty("receiver_role")) {
                                if (value.origin == "1") {
                                    $(".chat_container").prepend(`<li data-message id="` + value.id_message + `">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="../../assets/images/users/2.jpg" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <h5><?= $admin_name ?></h5>
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content" style="order:0!important;background-color:#c79f93!important;">
                                                    <p class="mb-0 content">
                                                    ${value.content}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle" style="color: black;">${moment.utc(value.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>

                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                                } else if (value.sender_role == sender_role) {
                                    $(".chat_container").prepend(`<li class="right" data-message id="` + value.id_message + `">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="<?= isset($_SESSION['login']) && isset($result->photo) ? '../uploads/customers/' . $result->photo : "assets/images/img-1.png"; ?>" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content ${value.trans_change == "after"?'cust':''} " style="order: 0 !important;">
                                                    <p class="mb-0 content">
                                                    ${value.content}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle" style="color: black;">${moment.utc(value.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>

                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                                    status = value.status;
                                } else {
                                    $(".chat_container").prepend(`<li data-message id="` + value.id_message + `">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="${$('.user-item.is-active img').attr('src')}" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content"style="order: 0 !important;">
                                                    <p class="mb-0 content">
                                                    ${value.content}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle">${moment.utc(value.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>
                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                                }

                            } else {
                                if (value.trans_status == 1) {
                                    $('.chat_container').prepend(`<li>
                                                <div role="heading" tabindex="1" style="position: relative;display: flex;flex-direction: row;/* flex-grow: 1; *//* flex-shrink: 1; *//* overflow: hidden; */align-items: center;align-self: stretch;justify-content: center;padding: 10px 0px 0px;margin: auto !important;width: 60%;">
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 5px 5px 0px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                    <div aria-hidden="true" data-text-as-pseudo-element="samedi 26 février 2022" dir="auto" style="position: relative; display: inline; flex-grow: 0; flex-shrink: 0; overflow: hidden; white-space: nowrap; overflow-wrap: break-word; font-size: 12px; color: rgb(138, 141, 145); font-family: &quot;SF Regular&quot;, &quot;Segoe System UI Regular&quot;, &quot;Segoe UI Regular&quot;, sans-serif; font-weight: 400; align-self: center; padding: 5px 10px; cursor: inherit;">
                                                    Vous avez acheter le paquet ${value.title} <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> (${value.messages} messages) </span> at <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> ${moment.utc(value.date_send).local().format("ddd, h:mm a")} </span>
                                                    </div>
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 0px 5px 5px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                </div>
                                            </li>`);

                                } else {
                                    $('.chat_container').prepend(`<li>
                                                <div role="heading" tabindex="1" style="position: relative;display: flex;flex-direction: row;/* flex-grow: 1; *//* flex-shrink: 1; *//* overflow: hidden; */align-items: center;align-self: stretch;justify-content: center;padding: 10px 0px 0px;margin: auto !important;width: 60%;">
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 5px 5px 0px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                    <div aria-hidden="true" data-text-as-pseudo-element="samedi 26 février 2022" dir="auto" style="position: relative; display: inline; flex-grow: 0; flex-shrink: 0; overflow: hidden; white-space: nowrap; overflow-wrap: break-word; font-size: 12px; color: rgb(138, 141, 145); font-family: &quot;SF Regular&quot;, &quot;Segoe System UI Regular&quot;, &quot;Segoe UI Regular&quot;, sans-serif; font-weight: 400; align-self: center; padding: 5px 10px; cursor: inherit;">
                                                    L'achat du paquet ${value.title} <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> (${value.messages} messages) </span> at <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> ${moment.utc(value.date_send).local().format("ddd, h:mm a")} </span> a été échoué
                                                    </div>
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 0px 5px 5px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                </div>
                                            </li>`);

                                }
                            }
                            if (value.hasOwnProperty("id_message") && (LastMSG == null || LastMSG > value.id_message)) {
                                max = value.id_message;
                                LastMSG = value.id_message;
                            }
                        });
                    }
                }
            })).done(function() {
                $('#loading').hide();
            });
        }
    });

    function sendMessage(message) {
        if (conn.readyState == WebSocket.OPEN) {
            conn.send(JSON.stringify(message));
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Reconnecting ...',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: 2000
            }).then((result) => {
                conn = new WebSocket(wsCurEnv);
                conn.onopen = function(e) {
                    if (sender !== "") {
                        conn.send(JSON.stringify({
                            command: "attachAccount",
                            account: sender,
                            role: sender_role,
                            name: 'guest' + sender,
                            sender_avatar: 'img-1.png',
                            id_group: id_group
                        }));
                    } else {
                        conn.send(JSON.stringify({
                            command: "attachAccount",
                            id_group: id_group
                        }));
                    }
                };
                console.log(conn);
            });
        }
    }

    conn.onmessage = function(e) {
        var dt = jQuery.parseJSON(e.data);
        if (dt.status == 200) {
            if (dt.action == "newMessage") {
                if (dt.sender == sender) {
                    let count = parseInt($('#messagesCount_' + receiver).text());
                    if (count != 0) {
                        let total = parseInt($("#total_unread_messages").text());
                        total--;
                        $("#total_unread_messages").text(total);
                    }
                    $('#messagesCount_' + receiver).text('0');
                    $('#messagesCount_' + receiver).hide();

                    $(".chat_container").append(`<li class="right">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img class="avatar" src="<?= isset($_SESSION['login']) && isset($result->photo) ? '../uploads/customers/' . $result->photo : "assets/images/img-1.png"; ?>" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content ${trans_class}" style="order: 0 !important;">
                                                    <p class="mb-0 content">
                                                    ${dt.message}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle" style="color: black;">${moment.utc(dt.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>

                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                    $('.simplebar-content-wrapper').scrollTop(1E10);
                } else {
                    //displayNotif(dt, receiver_avatar);
                    if (dt.sender !== 0 && def_agent == true) {
                        $.ajax({
                            url: '../../api/api.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                action: 'updateMessage',
                                receiver: dt.receiver,
                                sender: dt.sender
                            },
                            success: function(dataResult) {
                                $('#consultant-li-default').addClass('d-none');
                                $('#consultant-li-' + dt.sender).children().click();
                                def_agent = false;
                                conn.send(JSON.stringify({
                                    command: "affected_guest",
                                    id_guest: dt.receiver,
                                    id_agent: dt.sender,
                                    id_group: id_group
                                }));
                            },
                            error: function(err) {
                                console.log(err)
                            }
                        })
                    }
                    if (dt.sender == receiver) {
                        $(".writing_perview").hide();
                        if (dt.admin == 2) {
                            $(".chat_container").append(`<li>
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="../../assets/images/users/2.jpg" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <h5><?= $admin_name ?></h5>
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content" style="order:0!important;background-color:#c79f93!important;">
                                                    <p class="mb-0 content">
                                                    ${dt.message}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle" style="color: black;">${moment.utc(dt.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>

                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                        } else {
                            $(".chat_container").append(`<li>
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="${$('.user-item.is-active img').attr('src')}" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content" style="order: 0 !important;">
                                                    <p class="mb-0 content">
                                                    ${dt.message}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle">${moment.utc(dt.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>
                                    </div>
                                </li>`).children(':last').fadeIn(1000);

                            if (receiver == 0) {
                                def_agent = true;
                            }
                        }
                        $('.pricing-tab').parent().addClass('transp');
                    } else {

                        $('#messagesCount_' + dt.sender).show();
                        let count = parseInt($('#messagesCount_' + dt.sender).text());
                        if (count == 0) {
                            let count1 = 0;
                            count1++;
                            $('#messagesCount_' + dt.sender).text(count1);
                        } else {
                            count++;
                            $('#messagesCount_' + dt.sender).text(count);
                        }
                        count++;
                    }
                    $('.pricing-tab').parent().addClass('transp');

                }
                $('.simplebar-content-wrapper').scrollTop(1E10);
            }else if (dt.action == "connected") {
                intersectionResult = consultants.filter(x => (dt.users).indexOf(x) !== -1);
                if (intersectionResult.length >= 0) {
                    if (def_agent == false && last_agent !== 0) {
                        $('#consultant-li-default').addClass('d-none');
                    }
                    $.each(dt.users, function() {
                        $('#consultant-li-' + this).removeClass('hide');
                        $('.agent-' + this).removeClass('d-none');
                        $("#status-" + this).removeClass("text-danger");
                        $("#status-1-" + this).removeClass("text-danger");
                        $("#status-" + this).addClass("text-success");
                        $("#status-1-" + this).addClass("text-success");
                        var element = $("#consultant-li-" + this);
                        $("#consultant-li-" + this).remove();
                        $('.conversations-list').append(element);
                        $("#consultant-li-" + last_agent).children().click();
                        if (consultants.includes(this)) {
                            active_consultants.push(this);
                        }
                        active_consultants = active_consultants.filter(item => item != sender);
                        active_consultants = active_consultants.filter(item => item != 0);
                    });
                } else {
                    $('#consultant-li-default').removeClass('d-none');
                }
            } else if (dt.action == "newConnection") {
                if ($("#consultant-li-" + dt.id_user).length > 0) {
                    $('#consultant-li-' + dt.id_user).removeClass('hide');
                    $('.agent-' + dt.id_user).removeClass('d-none');
                    $("#status-" + dt.id_user).removeClass("text-danger");
                    $("#status-1-" + dt.id_user).removeClass("text-danger");
                    $("#status-" + dt.id_user).addClass("text-success");
                    $("#status-1-" + dt.id_user).addClass("text-success");
                    var element = $("#consultant-li-" + dt.id_user);
                    $("#consultant-li-" + last_agent).children().click();
                    if (def_agent == false && last_agent !== 0) {
                        $('#consultant-li-default').addClass('d-none');
                    } else {
                        $('#consultant-li-default').removeClass('d-none');
                    }
                    intersectionResult = active_consultants.length;
                    if (intersectionResult == 0) {
                        $('.conversations-list').append(element);
                    } else {
                        $('.conversations-list').append(element);
                    }
                    active_consultants.push(dt.id_user);
                }
            } else if (dt.action == "closedConnection") {
                intersectionResult = (active_consultants.indexOf(dt.id_user)).toString.length;
                if (intersectionResult > 0) {
                    active_consultants = active_consultants.filter(item => item != dt.id_user);
                    active_consultants = active_consultants.filter(item => item != 0);
                    if (active_consultants.length == 0 && last_agent == 0) {
                        $('#consultant-li-default').removeClass('d-none');
                        $('#consultant-li-default').children().click();
                    }
                } else {
                    $('#consultant-li-default').addClass('d-none');
                }

                if (dt.id_user != sender) {
                    $('.agent-' + dt.id_user).removeClass('d-none');
                    $("#status-" + dt.id_user).removeClass("text-success");
                    $("#status-1-" + dt.id_user).removeClass("text-success");
                    $("#status-" + dt.id_user).addClass("text-danger");
                    $("#status-1-" + dt.id_user).addClass("text-danger");

                    var element = $("#consultant-li-" + dt.id_user);
                    $('.conversations-list').append(element);
                    if (last_agent == 0) {
                        element.addClass('hide');
                    }
                }
            } else if (dt.action == "balance") {
                $('.balance').text(dt.balance);
                if (dt.balance == 3) {
                    $('.acheter').addClass('warning');
                    $('.selected_customer_balance_prefix').addClass('text-warning');
                    $('.balance').addClass('text-warning');
                    $('.balance').data('balance', 3);
                } else if (dt.balance == 0) {
                    $('.acheter').removeClass('warning');
                    $('.acheter').addClass('danger');
                    $('.selected_customer_balance_prefix').addClass('text-danger');
                    $('.balance').addClass('text-danger');
                    $('.balance').data('balance', 0);
                    $('.emojionearea-editor').attr('placeholder', 'Il faut acheter des messages');
                    $('.emojionearea').addClass('disabled');
                    $('.emojionearea').addClass('emojionearea-disable');
                    $('.chat-rightsidebar').removeClass('d-none');
                }
            } else if (dt.action == "writing") {
                if (dt.receiver == sender && dt.sender == receiver) {
                    $(".chat_container").append(`<li class="writing_perview">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="${$('.user-item.is-active img').attr('src')}" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content"style="order: 0 !important;">
                                                    <p class="mb-0 content">
                                                    <?php echo utf8_encode($trans["writing"]); ?> ..
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                    $('.simplebar-content-wrapper').scrollTop(1E10);
                }
            } else if (dt.action == "stopWriting") {
                if (dt.receiver == sender && dt.sender == receiver) {
                    $(".writing_perview").fadeOut(0);
                    var dh = $(".chat-rbox").css("top").slice(0, -2);
                    $('.chat-rbox').css('top', 10 + "px");
                }
            }
        } else if (dt.status == "customer") {
            $('.emojionearea').removeClass('disabled');
            $('.emojionearea').removeClass('emojionearea-disable');
            $('.emojionearea-editor').attr('placeholder', 'saisissez votre message ici ...');
            if (dt.new_cust == "new_cust") {
                Swal.fire({
                    icon: 'success',
                    title: 'Bienvenue ' + dt.lastname + " " + dt.firstname,
                    text: 'Votre Mot De Passe est le meme que votre email <?= $cont->alert_payment_success ?> ' + dt.balance + ' messages',
                    showConfirmButton: true,
                }).then(function() {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Paiement traité avec succès',
                    text: '<?= $cont->alert_payment_success ?> ' + dt.balance + ' messages',
                    showConfirmButton: false,
                    timer: 5000
                });
            }
            $('.balance').html(($('.balance').data('balance')) + parseInt(dt.balance));
            $('.balance').data('balance', ($('.balance').data('balance')) + parseInt(dt.balance));
            $('.acheter').removeClass('danger');
            $('.selected_customer_balance_prefix').removeClass('text-danger');
            $('.selected_customer_balance_prefix').removeClass('text-warning');
            $('.balance').removeClass('text-danger');
            $('.balance').removeClass('text-warning');
            $('.chat_container').append(`<li>
                                                <div role="heading" tabindex="1" style="position: relative;display: flex;flex-direction: row;/* flex-grow: 1; *//* flex-shrink: 1; *//* overflow: hidden; */align-items: center;align-self: stretch;justify-content: center;padding: 10px 0px 0px;margin: auto !important;width: 60%;">
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 5px 5px 0px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                    <div aria-hidden="true" data-text-as-pseudo-element="samedi 26 février 2022" dir="auto" style="position: relative; display: inline; flex-grow: 0; flex-shrink: 0; overflow: hidden; white-space: nowrap; overflow-wrap: break-word; font-size: 12px; color: rgb(138, 141, 145); font-family: &quot;SF Regular&quot;, &quot;Segoe System UI Regular&quot;, &quot;Segoe UI Regular&quot;, sans-serif; font-weight: 400; align-self: center; padding: 5px 10px; cursor: inherit;">
                                                    Vous avez acheter le paquet ${dt.title} <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> (${dt.balance} messages) </span> at <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> ${moment.utc(dt.date_send).local().format("ddd, h:mm a")} </span>
                                                    </div>
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 0px 5px 5px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                </div>
                                            </li>`);

        } else if (dt.status == "lead") {
            $('.chat_container').append(`<li>
                                                <div role="heading" tabindex="1" style="position: relative;display: flex;flex-direction: row;/* flex-grow: 1; *//* flex-shrink: 1; *//* overflow: hidden; */align-items: center;align-self: stretch;justify-content: center;padding: 10px 0px 0px;margin: auto !important;width: 60%;">
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 5px 5px 0px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                    <div aria-hidden="true" data-text-as-pseudo-element="samedi 26 février 2022" dir="auto" style="position: relative; display: inline; flex-grow: 0; flex-shrink: 0; overflow: hidden; white-space: nowrap; overflow-wrap: break-word; font-size: 12px; color: rgb(138, 141, 145); font-family: &quot;SF Regular&quot;, &quot;Segoe System UI Regular&quot;, &quot;Segoe UI Regular&quot;, sans-serif; font-weight: 400; align-self: center; padding: 5px 10px; cursor: inherit;">
                                                    L'achat du paquet ${dt.title} <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> (${dt.balance} messages) </span> at <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> ${moment.utc(value.date_send).local().format("ddd, h:mm a")}  </span> a été échoué
                                                    </div>
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 0px 5px 5px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                </div>
                                            </li>`);
            Swal.fire({
                icon: 'error',
                text: '<?= $cont->alert_payment_error ?>',
                showConfirmButton: false,
                timer: 5000
            })
            if (dt.id_agent !== 0) {
                $('#content').attr('placeholder', 'Saisissez votre message ici...');
            }

        }
    };


    async function create_guest() {
        let result;

        try {
            result = await $.ajax({
                type: "POST",
                url: "../../api/api.php",
                dataType: "json",
                data: {
                    action: 'create_guest',
                    id_account: '<?= $id_account ?>',
                    id_website: '<?= $id_website ?>',
                },
                success: function(dataResult) {
                    if (dataResult.success) {
                        sender = dataResult.id;
                        if (sender !== "") {
                            conn.send(JSON.stringify({
                                command: "attachAccount",
                                account: sender,
                                role: sender_role,
                                name: 'guest' + sender,
                                sender_avatar: 'img-1.png',
                                id_group: id_group
                            }));
                        } else {
                            conn.send(JSON.stringify({
                                command: "connected",
                                id_group: id_group
                            }));
                        }
                        cookie_string = "customer = " + sender + "; path='http://45.76.121.27/chat/'; max-age=31536000";
                        document.cookie = cookie_string;
                        customer_id = sender;
                        $('input[name="id_guest"]').val(dataResult.id);
                    }
                },
                error: function(err) {
                    console.log(err)
                }
            })

            return result;
        } catch (error) {
            console.error(error);
        }
    }
    $(document).on('click', '.is-active', function(e) {
        e.preventDefault();
    });

    $(document).on('click', '.user-item', function() {
        $('.is-active').removeClass('is-active');
        $(this).addClass('is-active');
        $('.avatar-xs').attr('src', $('img', $(this)).attr('src'));
        $('.agent-name').text($(this).data('name'));
        $('.agent_conn').addClass('agent-' + $(this).data('id'));
        $('.agent-' + $(this).data('id')).removeClass('d-none');
        $('.status-xs').attr('id', 'status-1-' + $(this).data('id'));
        $('.buy-cred').attr('data-idagent', $(this).data('id'));
        receiver = $(this).data('id');
        if ($('#status-' + $(this).data('id')).hasClass('text-danger')) {
            $("#status-1-" + $(this).data('id')).removeClass("text-success");
            $("#status-1-" + $(this).data('id')).addClass("text-danger");
        }
        switchElement($(this));
        $('.user-chat').removeClass('is-mobile-active');
        $('.user-chat').addClass('user-chat-show');
        if ($('.status-xs').attr('id') == 'status-1-0') {
            $("#status-1-0").removeClass("text-danger");
            $("#status-1-0").addClass("text-success");
        }
    });






    $(document).on('click', '.ri-menu-line', function() {
        $('.user-chat').addClass('is-mobile-active');
    });

    $(document).ready(function() {
        $('.user-item:nth(0)').click();
        $('.pricing-tab').parent().addClass('transp');
    });


    function switchElement(element) {
        var $this = $(element);
        $(element).addClass("is-active");
        receiver = $(element).data("id");
        if ($('#status-' + receiver).hasClass('text-success')) {
            $('#status-1-' + receiver).removeClass('text-danger');
            $('#status-1-' + receiver).addClass('text-success');
        } else {
            $('#status-1-' + receiver).removeClass('text-success');
            $('#status-1-' + receiver).addClass('text-danger');
        }
        $.ajax({
            url: "../conversationTrait.php",
            type: "POST",
            data: {
                action: "getConversationsList2",
                customer_id: customer_id,
                sender: sender,
                sender_role: sender_role,
                receiver: receiver,
                receiver_role: receiver_role,
                last_id: 0
            },
            dataType: "json",
            success: function(dataResult) {
                if (dataResult.statusCode == 200) {
                    $('.simplebar-content-wrapper').scrollTop(1);
                    $('#chat-welcome').addClass('d-none');
                    $(".chat_container").empty();
                    LastMSG = null;
                    $.each(dataResult.conversations, function(key, value) {
                        if (value.trans_change == "after") {
                            trans_class = "cust";
                        } else if (value.trans_change == "after") {
                            trans_class = "";
                        }
                        if (value.hasOwnProperty("sender_role") || value.hasOwnProperty("receiver_role")) {
                            if (value.origin == "1") {
                                $(".chat_container").append(`<li id="` + value.id_message + `">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="../../assets/images/users/2.jpg" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <h5><?= $admin_name ?></h5>
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content" style="order:0!important;background-color:#c79f93!important;">
                                                    <p class="mb-0 content">
                                                    ${value.content}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle" style="color: black;">${moment.utc(value.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>

                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                            } else if (value.sender_role == sender_role) {
                                $(".chat_container").append(`<li class="right" id="` + value.id_message + `">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="<?= isset($_SESSION['login']) && isset($result->photo) ? '../uploads/customers/' . $result->photo : "assets/images/img-1.png"; ?>" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content ${value.trans_change == "after"?'cust':''} " style="order: 0 !important;">
                                                    <p class="mb-0 content">
                                                    ${value.content}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle" style="color: black;">${moment.utc(value.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>

                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                                status = value.status;
                            } else if (value.origin != "2") {
                                $(".chat_container").append(`<li id="` + value.id_message + `">
                                    <div class="conversation-list">
                                        <div class="chat-avatar">
                                            <img src="${$('.user-item.is-active img').attr('src')}" alt="">
                                        </div>

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content"style="order: 0 !important;">
                                                    <p class="mb-0 content">
                                                    ${value.content}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle">${moment.utc(value.date_send).local().format("ddd, h:mm a")}</span></p>
                                        </div>
                                    </div>
                                </li>`).children(':last').fadeIn(1000);
                            }

                        } else {
                            if (value.trans_status == 1) {
                                $('.chat_container').append(`<li>
                                                <div role="heading" tabindex="1" style="position: relative;display: flex;flex-direction: row;/* flex-grow: 1; *//* flex-shrink: 1; *//* overflow: hidden; */align-items: center;align-self: stretch;justify-content: center;padding: 10px 0px 0px;margin: auto !important;width: 60%;">
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 5px 5px 0px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                    <div aria-hidden="true" data-text-as-pseudo-element="samedi 26 février 2022" dir="auto" style="position: relative; display: inline; flex-grow: 0; flex-shrink: 0; overflow: hidden; white-space: nowrap; overflow-wrap: break-word; font-size: 12px; color: rgb(138, 141, 145); font-family: &quot;SF Regular&quot;, &quot;Segoe System UI Regular&quot;, &quot;Segoe UI Regular&quot;, sans-serif; font-weight: 400; align-self: center; padding: 5px 10px; cursor: inherit;">
                                                    Vous avez acheter le paquet ${value.title} <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> (${value.messages} messages) </span> at <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> ${moment.utc(value.date_send).local().format("ddd, h:mm a")} </span>
                                                    </div>
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 0px 5px 5px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                </div>
                                            </li>`);
                                status = value.status;

                            } else {
                                $('.chat_container').append(`<li>
                                                <div role="heading" tabindex="1" style="position: relative;display: flex;flex-direction: row;/* flex-grow: 1; *//* flex-shrink: 1; *//* overflow: hidden; */align-items: center;align-self: stretch;justify-content: center;padding: 10px 0px 0px;margin: auto !important;width: 60%;">
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 5px 5px 0px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                    <div aria-hidden="true" data-text-as-pseudo-element="samedi 26 février 2022" dir="auto" style="position: relative; display: inline; flex-grow: 0; flex-shrink: 0; overflow: hidden; white-space: nowrap; overflow-wrap: break-word; font-size: 12px; color: rgb(138, 141, 145); font-family: &quot;SF Regular&quot;, &quot;Segoe System UI Regular&quot;, &quot;Segoe UI Regular&quot;, sans-serif; font-weight: 400; align-self: center; padding: 5px 10px; cursor: inherit;">
                                                    L'achat du paquet ${value.title} <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> (${value.messages} messages) </span> at <span style="font-size: 10px;font-weight: bolder;font-style: italic;"> ${moment.utc(value.date_send).local().format("ddd, h:mm a")} </span> a été échoué
                                                    </div>
                                                    <div role="none" style="position: relative; display: flex; flex-direction: row; flex-grow: 1; flex-shrink: 1; overflow: hidden; align-items: stretch; margin: 5px 0px 5px 5px; height: 1px; opacity: 0.4; background-color: rgb(138, 141, 145);"></div>
                                                </div>
                                            </li>`);
                                status = value.status;

                            }
                        }

                        if (value.hasOwnProperty("id_message") && LastMSG == null) {
                            max = value.id_message;
                            LastMSG = value.id_message;
                        }
                    });
                    $('.simplebar-content-wrapper').scrollTop(1E10);
                } else if (dataResult.statusCode == 201) {
                    $("#content").val("");
                    $('#chat-welcome').removeClass('d-none');
                    $(".chat_container").empty();
                    $('.simplebar-content-wrapper').scrollTop(0);
                }
                $('.pricing-tab').parent().addClass('transp');
            }
        });
    }

    $('#payment-redirection').on('submit', function(e) {
        var w = window.open('', 'Popup_Window', "toolbar=yes,scrollbars=yes,resizable=yes,left=500,top=100,width=800,height=800");
        this.target = "Popup_Window";
    });
</script>

</body>

</html>