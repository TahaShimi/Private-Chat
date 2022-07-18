<?php
ini_set('display_errors', 1);
include('../../init.php');

session_start();
if (empty($_SESSION['login']) || intval($_SESSION['login']) != 2) {
    header('Location: ../../index.php');
    exit();
} elseif (isset($_GET["logout"])) {

    session_unset();
    session_destroy();

    header('Location: ../../index.php');
    exit();
}
$id_user = $_SESSION['id_user'];
$id_account = intval($_SESSION['id_account']);
$account_status = intval($_SESSION['account_status']);
/* $account_stmt = $conn->prepare("SELECT * FROM  `users` u join `accounts`a on u.id_profile=a.id_account  where id_user=:iu");
$account_stmt->bindParam(':iu',  $id_user, PDO::PARAM_INT);
$account_stmt->execute();
$account = $account_stmt->fetchObject(); */
$contact = $conn->prepare("SELECT count(*) as unread FROM contact_ticket ct,users u WHERE  ct.id_account=:id AND (ct.status IN (1,0) AND (SELECT cm.id_sender from contact_messages cm WHERE cm.id_ticket=ct.id_ticket ORDER by cm.date DESC LIMIT 1)!=:idu) AND u.id_user= ct.id_customer AND u.profile=4 AND ct.id_consultant IS NULL");
$contact->bindParam(':id',  $id_account, PDO::PARAM_INT);
$contact->bindParam(':idu',  $id_user, PDO::PARAM_INT);
$contact->execute();
$unresolved = $contact->fetchObject();
$contact = $conn->prepare("SELECT count(*) as unread FROM contact_ticket ct,users u WHERE  ct.id_account=:id AND (ct.status IN (1,0) AND (SELECT cm.id_sender from contact_messages cm WHERE cm.id_ticket=ct.id_ticket ORDER by cm.date DESC LIMIT 1)!=:idu) AND u.id_user= ct.id_customer AND u.profile=4 AND ct.id_consultant IS NOT NULL");
$contact->bindParam(':id',  $id_account, PDO::PARAM_INT);
$contact->bindParam(':idu',  $id_user, PDO::PARAM_INT);
$contact->execute();
$complaints = $contact->fetchObject();
$contact = $conn->prepare("SELECT n.*,(SELECT c.pseudo FROM consultants c,users u WHERE n.id_consultant=u.id_user AND c.id_consultant=u.id_profile) as expert,(SELECT CONCAT(c.firstname,' ',c.lastname) FROM customers c,users u WHERE n.id_customer=u.id_user AND c.id_customer=u.id_profile) as customer FROM notifications n WHERE  n.id_account=:id ORDER BY n.date DESC LIMIT 20");
$contact->bindParam(':id',  $id_account, PDO::PARAM_INT);
$contact->execute();
$notifications = $contact->fetchAll();
$seen = array_search("0", array_column($notifications, 'seen'));
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <title>Private chat</title>
    <link href="../../assets/css/style.min.css" rel="stylesheet">
    <link href="../../assets/css/account_custom.css" rel="stylesheet">
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <!--     <link href="../../assets/node_modules/morrisjs/morris.css" rel="stylesheet">
    <link href="../../assets/node_modules/toast-master/jquery.toast.css" rel="stylesheet">
    <link href="../../assets/css/pages/pricing-page.css" rel="stylesheet">
    <link href="../../assets/css/pages/dashboard1.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../assets/int-phone-number/css/intlTelInput.css"> -->

    <!-- Custom CSS -->
    <!--     <link href="../../assets/css/pages/contact-app-page.css" rel="stylesheet">
    <link href="../../assets/node_modules/bootstrap-switch/bootstrap-switch.min.css" rel="stylesheet">
    <link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/pages/tab-page.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/node_modules/dropify/dropify.min.css">
    <link href="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" /> -->
    <!-- Popup CSS -->
    <!--     <link href="../../assets/node_modules/Magnific-Popup-master/magnific-popup.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../assets/node_modules/datatables.net-bs4/dataTables.bootstrap4.css">
    <link href="../../assets/css/pages/user-card.css" rel="stylesheet"> -->
    <?php
    if (in_array(basename($_SERVER['PHP_SELF']), array('account.php'))) {
        echo '<link rel="stylesheet" type="text/css" href="../../assets/int-phone-number/css/intlTelInput.css"><link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />';
    }
    if (in_array(basename($_SERVER['PHP_SELF']), array('consultants.php'))) {
        echo '<link href="../../assets/css/pages/user-card.css" rel="stylesheet">';
    }
    if (in_array(basename($_SERVER['PHP_SELF']), array('consultant_add.php', 'customers_import.php', 'customers_add.php'))) {
        echo '<link rel="stylesheet" href="../../assets/node_modules/dropify/dropify.min.css"><link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />';
    }
    if (in_array(basename($_SERVER['PHP_SELF']), array('customers.php', 'website.php', 'customer_add.php'))) {
        echo '<link rel="stylesheet" type="text/css" href="../../assets/int-phone-number/css/intlTelInput.css"><link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />';
    }
    if (in_array(basename($_SERVER['PHP_SELF']), array('RealTimeConversation.php', 'consultant.php','links.php'))) {
        echo '<link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />';
    }
    if (in_array(basename($_SERVER['PHP_SELF']), array('RealTimeConversation.php', 'websites.php','links.php', 'package_edit.php', 'logs.php', 'complaint.php', 'packages.php', 'offers.php', 'publishers.php', 'leads.php', 'transactions.php', 'reportings.php', 'contact.php', 'customers.php'))) {
        echo '<link rel="stylesheet" type="text/css" href="../../assets/node_modules/datatables.net-bs4/dataTables.bootstrap4.css">';
    }
    ?>
    <!--    <link href="../../assets/css/pages/consultant.css" rel="stylesheet">
    <link href="../../assets/css/pages/chat-app-page.css" rel="stylesheet">
        <link href="../../assets/css/custom.css" rel="stylesheet">
    <link href="../../assets/css/pages/consultant.css" rel="stylesheet">
    <link href="../../assets/css/pages/bootstrap-switch.css" rel="stylesheet">
    <link href="../../assets/css/pages/file-upload.css" rel="stylesheet">
    <link href="../../assets/css/pages/form-icheck.css" rel="stylesheet"> -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    <script src="../../assets/js/env.js"></script>
    <script>
        var reportedby = '<?= $trans['reportedBy'] ?>';
        var waitingfor = '<?= $trans['waitingfor'] ?>';
        var reportedby = '<?= $trans['complaint'] ?>';
        var late = '<?= $trans['late'] ?>';
        var id_user = <?= $_SESSION['id_user'] ?>;
        var id_company = <?= $_SESSION['id_company'] ?>;
        var id_account = <?= $_SESSION['id_account'] ?>;
        var seen = '<?= $seen ?>';
    </script>
</head>
<body class="skin-default-dark fixed-layout">
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Private chat</p>
        </div>
    </div>
    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">
                        <span>
                            <img src="../../assets/images/logo_private-chat.png" alt="homepage" class="light-logo w-100 px-2" />
                        </span>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item "><h4 class="text-themecolor title_page"><?php echo ($trans[$page_name]) ?></h4></li>
                    </ul>
                    <div>
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" onclick="deleteNotif()"> <i class="mdi mdi-bell"></i>
                                    <div class="notify" id="notify" <?php if ($seen != false) {echo 'style="display:block"';} ?>> <span class="heartbit" id="heartbit" <?php if ($seen != false) {echo 'style="display:block"';} ?>></span> <span class="point" id="point"></span> </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown" style="right: 8%;">
                                    <ul>
                                        <li>
                                            <div class="drop-title">Notifications</div>
                                        </li>
                                        <li>
                                            <div class="message-center" id="notifications">
                                                <!-- Message -->
                                                <?php foreach ($notifications as $notification) { ?>
                                                    <a href="javascript:void(0)" data-id="<?= $notification['id_notif'] ?>" class="<?php if ($notification['seen'] == "0") echo 'notSeen'; ?>">
                                                        <div class="btn btn-danger btn-circle"><i class="mdi mdi-exclamation"></i></div>
                                                        <div class="mail-contnet">
                                                            <?php if ($notification['action'] == 1) { ?>
                                                                <h5><?= $trans['complaint'] ?></h5> <span class="mail-desc"> <?= $notification['expert'] . ' ' . $trans['reportedBy'] . ' ' . $notification['customer'] ?></span> <span class="time"><?= $notification['date'] ?></span>
                                                            <?php } else if ($notification['action'] == 2) { ?>
                                                                <h5><?= $trans['late'] ?></h5> <span class="mail-desc"> <?= $notification['customer'] . ' waiting ' . $notification['expert']  ?> </span> <span class="time"><?= $notification['date'] ?></span>
                                                            <?php } ?>
                                                        </div>
                                                        <?php if ($notification['seen'] == 0) { ?>
                                                            <div class="notify"> <span class="point"></span> </div>
                                                        <?php } ?>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </li>
                                        <li>
                                            <a class="nav-link text-center link" href="notifications.php"> <strong>Check all notifications</strong> <i class="mdi mdi-bell"></i> </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="user-profile">
                                <div class="user-pro-body">
                                    <div class="dropdown">
                                        <span style="color: white" class="m-r-10 d-none d-md-inline"><?= $_SESSION['business_name'] ?></span>
                                        <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <img src="../../assets/images/users/2.jpg" alt="user-img" class="img-circle" style="height: 45px;width:45px"><span class="caret"></span></a>
                                        <div class="dropdown-menu animated flipInY">
                                            <!-- text-->
                                            <a href="my_profile.php?tab=authentication_credentials" class="dropdown-item"><i class="mdi mdi-clipboard-account"></i> <?php echo ($trans["authentication"]) ?></a>
                                            <div class="dropdown-divider"></div>
                                            <!-- text-->
                                            <a href="my_profile.php?tab=application_settings" class="dropdown-item"><i class="mdi mdi-settings"></i> <?php echo ($trans["settings"]) ?></a>
                                            <div class="dropdown-divider"></div>
                                            <a href="support.php" class="dropdown-item"><i class="mdi mdi-message"></i> Support </a>
                                            <!-- text-->
                                            <div class="dropdown-divider"></div>
                                            <!-- text-->
                                            <a href="?logout" class="dropdown-item"><i class="mdi mdi-power"></i> <?php echo ($trans["side_bar"]["logout"]) ?></a>
                                            <!-- text-->
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li> <a class="waves-effect waves-dark" href="index.php" aria-expanded="false"><i class="mdi mdi-speedometer"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["dashboard"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="account.php" aria-expanded="false"><i class="mdi mdi-clipboard-account"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["my_account"]) ?></span></a>
                        </li>
                        <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('website.php'))) {echo "active";} ?>"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-checkbox-multiple-blank-outline"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["websites"]) ?></span></a>
                            <ul aria-expanded="false" class="collapse <?php if (in_array(basename($_SERVER['PHP_SELF']), array('website.php'))) {echo "in";} ?>">
                                <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('website.php'))) {echo "active";} ?>"><a href="websites.php"><?php echo ($trans["side_bar"]["websites_list"]) ?></a></li>
                                <li><a href="website_add.php"><?php echo ($trans["side_bar"]["add_website"]) ?></a></li>
                            </ul>
                        </li>
                        <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('consultant.php'))) {echo "active";} ?>"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-account-multiple"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["consultants"]) ?></span></a>
                            <ul aria-expanded="false" class="collapse <?php if (in_array(basename($_SERVER['PHP_SELF']), array('consultant.php'))) {echo "in";} ?>">
                                <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('consultant.php'))) {echo "active";} ?>"><a href="consultants.php"><?php echo ($trans["side_bar"]["consultants_list"]) ?></a></li>
                                <li><a href="consultant_add.php"><?php echo ($trans["side_bar"]["add_consultant"]) ?></a></li>
                            </ul>
                        </li>
                        <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('customer.php'))) {echo "active";} ?>"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-account-multiple"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["customers"]) ?></span></a>
                            <ul aria-expanded="false" class="collapse <?php if (in_array(basename($_SERVER['PHP_SELF']), array('customer.php'))) {echo "in";} ?>">
                                <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('customer.php'))) {echo "active";} ?>"><a href="customers.php"><?php echo ($trans["side_bar"]["customers_list"]) ?></a></li>
                                <li><a href="customer_add.php"><?php echo ($trans["side_bar"]["add_customer"]) ?></a></li>
                                <li><a href="customers_import.php"><?php echo ($trans["side_bar"]["import_customers"]) ?></a></li>
                                <li> <a href="logs.php"><?php echo ($trans["import_history"]) ?></a></li>
                                <li> <a href="links.php"><?php echo ($trans["shared_links"]) ?></a></li>
                            </ul>
                        </li>
                        <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('package_edit.php', 'offer_edit.php'))) {echo "active";} ?>"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-package-variant"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["packages"]) ?></span></a>
                            <ul aria-expanded="false" class="collapse <?php if (in_array(basename($_SERVER['PHP_SELF']), array('package_edit.php', 'offer_edit.php'))) {echo "in";} ?>">
                                <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('package_edit.php'))) {echo "active";} ?>"><a href="packages.php"><?php echo ($trans["side_bar"]["packages_list"]) ?></a></li>
                                <li><a href="package_add.php"><?php echo ($trans["side_bar"]["add_package"]) ?></a></li>
                                <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('offer_edit.php'))) {echo "active";} ?>"><a href="offers.php"><?php echo ($trans["side_bar"]["offers_list"]) ?></a></li>
                                <li><a href="offer_add.php"><?php echo ($trans["side_bar"]["add_offer"]) ?></a></li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-account-multiple-outline"></i><span class="hide-menu"><?= $trans['publishers'] ?></span></a>
                            <ul aria-expanded="false" class="collapse ">
                                <li><a href="publishers.php"><?= $trans['publishers'] ?></a></li>
                                <li><a href="leads.php"><?= $trans['leads'] ?></a></li>
                            </ul>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="transactions.php" aria-expanded="false"><i class="mdi mdi-view-list"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["transactions"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="reportings.php" aria-expanded="false"><i class="mdi mdi-chart-line"></i><span class="hide-menu"><?php echo ($trans["reportings"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="contact.php" aria-expanded="false"><i class="mdi mdi-message-text-outline"></i><span class="hide-menu"><?php echo ($trans["contact"]) ?><span class="badge badge-pill badge-info" id="total_contact"><?= $unresolved->unread ?></span></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="complaint.php" aria-expanded="false"><i class="mdi mdi-message-text-outline"></i><span class="hide-menu"><?php echo ($trans["complaint"]) ?><span class="badge badge-pill badge-info" id="total_complaint"><?= $complaints->unread ?></span></span></a>
                        </li>
                        <!-- <li> <a class="waves-effect waves-dark" href="payments.php"aria-expanded="false"><i class="ti-credit-card"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["payments"]) ?></span></a>
                        </li> -->

                        <li><a href="RealTimeConversation.php"><i class="mdi mdi-comment-processing-outline"></i><span class="hide-menu"><?php echo ($trans["realTime_supervision"]) ?></span></a></li>
                        <li> <a class="waves-effect waves-dark" href="messages.php" aria-expanded="false"><i class="mdi mdi-comment-multiple-outline"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["messages"]) ?></span></a>
                        </li>

                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row page-titles">
                    <div class="col-md-12 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)"><?php echo ($trans["home"]) ?></a></li>
                                <?php if (isset($page_folder)) { ?>
                                    <li class="breadcrumb-item"><?php echo ($trans[$page_folder]) ?></li>
                                <?php } ?>
                                <li class="breadcrumb-item m-r-10 active"><?php echo ($trans[$page_name]) ?></li>
                            </ol>
                        </div>
                    </div>
                </div>