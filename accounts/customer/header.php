<?php
include('../../init.php');
session_set_cookie_params(0);
session_start();
if (empty($_SESSION['login']) || intval($_SESSION['login']) != 4) {
    header('Location: ../../index.php');
    exit();
} elseif (isset($_GET["logout"])) {
    $stmt2 = $conn->prepare("UPDATE `users` SET `status` = 0 WHERE `id_user` = :ID");
    $stmt2->bindParam(':ID', $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt2->execute();

    session_unset();
    session_destroy();

    header('Location: ../../index.php');
    exit();
}

$id_user = $_SESSION['id_user'];
$id_account = intval($_SESSION['id_account']);
$id_website = $_SESSION['id_website'];
$account_stmt = $conn->prepare("SELECT * FROM  `users` u join `customers` c on u.id_profile=c.id_customer  where id_user=:iu");
$account_stmt->bindParam(':iu',  $id_user, PDO::PARAM_INT);
$account_stmt->execute();
$account = $account_stmt->fetchObject();
$stmt2 = $conn->prepare("SELECT * from accounts WHERE `id_account` = :IDA");
$stmt2->bindParam(':IDA', $account->id_account, PDO::PARAM_INT);
$stmt2->execute();
$company = $stmt2->fetchObject();
$admin_name = $company->business_name;
$stmt2 =  $conn->prepare("SELECT sender from messages where receiver=:id and status=0 GROUP BY sender");
$stmt2->bindparam(":id", $id_user, PDO::PARAM_INT);
$stmt2->execute();
$dta = $stmt2->fetchAll();
$totalUnreadMessages = count($dta);
$contact = $conn->prepare("SELECT count(*) as unread FROM contact_ticket ct WHERE  ct.id_account=:id AND (ct.status=1 AND (SELECT cm.id_sender from contact_messages cm WHERE cm.id_ticket=ct.id_ticket ORDER by cm.date DESC LIMIT 1)!=:idu)");
$contact->bindParam(':id',  $id_account, PDO::PARAM_INT);
$contact->bindParam(':idu',  $id_user, PDO::PARAM_INT);
$contact->execute();
$unresolved = $contact->fetchObject();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" id="pageIcon" type="image/gif" sizes="16x16" href="../../assets/images/favicon.png">
    <title>Private chat </title>
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="../../assets/node_modules/morrisjs/morris.css" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <link href="../../assets/node_modules/toast-master/jquery.toast.css" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <!-- Popup CSS -->
    <link href="../../assets/node_modules/Magnific-Popup-master/magnific-popup.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../assets/node_modules/datatables.net-bs4/dataTables.bootstrap4.css">
    <!-- Page CSS -->
    <link rel="stylesheet" href="../../assets/node_modules/dropify/dropify.min.css">
    <link href="../../assets/css/pages/chat-app-page.css" rel="stylesheet">
    <link href="../../assets/css/pages/pricing-page.css" rel="stylesheet">
    <link href="../../assets/css/style.min.css" rel="stylesheet">
    <link href="../../assets/css/custom.css" rel="stylesheet">
    <link href="../../assets/css/pages/customer.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    <style>
        #credentials-main-update-success,
        #credentials-main-update-fail {
            display: none;
        }
    </style>
    <script>
        var sender_pseudo = "<?= $_SESSION['full_name'] ?>";
        var sender_avatar = "<?= $_SESSION['avatar'] ?>";
        var sender = <?= $_SESSION['id_user'] ?>;
        var customer_id = <?= $account->id_customer ?>;
        var id_group = <?= $_SESSION['id_company'] ?>;
        var sender_role = 4;
        var unlimited = <?= $_SESSION['unlimited'] ?>;
        var unlimitedtext = '<?= $trans['unlimited'] ?>';
    </script>
</head>

<body class="skin-default-dark fixed-layout">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Private chat</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <audio id="play" src="../../assets/audio/beep.mp3"></audio>
        <input id="audio_notification_value" type="hidden" value="<?= $account->audio_notification ?>">
        <input id="browser_notification_value" name="prodId" type="hidden" value="<?= $account->browser_notification ?>">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">
                        <span>
                            <img src="../../assets/images/logo_private-chat.png" alt="homepage" class="light-logo w-100 px-2" />
                        </span>
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item ">
                            <h4 class="text-themecolor title_page"><?php echo ($trans[$page_name]) ?></h4>
                        </li>

                    </ul>
                    <div class="header-balance-container">
                        <!-- User Profile-->
                        <div class="user-profile d-none d-md-block" style="width: 200px ! important;">
                            <div class="user-pro-body">
                                <div class="dropdown">
                                    <span style="color:white"><?php echo $_SESSION['full_name'] ?></span>
                                    <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo '../uploads/customers/' . $account->photo != ""?$account->photo:'img-1.png' ?>" alt="user-img" class="img-circle" style="height:45px;width:45px"> <span class="caret"></span></a>
                                    <div class="dropdown-menu animated flipInY">
                                        <!-- text-->
                                        <a href="profile.php" class="dropdown-item"><i class="mdi mdi-clipboard-account"></i> <?php echo ($trans["side_bar"]["my_profile"]) ?></a>
                                        <!-- text-->
                                        <div class="dropdown-divider"></div>
                                        <!-- text-->
                                        <a href="?logout" class="dropdown-item"><i class="mdi mdi-power"></i> <?php echo ($trans["side_bar"]["logout"]) ?></a>
                                        <!-- text-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->

                </div>


            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <div class="user-profile d-block d-md-none">
                    <div class="user-pro-body">
                        <div class="dropdown">
                            <img src="<?php echo '../uploads/customers/' . $account->photo ?>" alt="user-img" class="img-circle" style="height:45px;width:45px">
                            <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <span style="color:white"><?php echo $_SESSION['full_name'] ?></span>
                                <span class="caret"></span></a>
                            <div class="dropdown-menu animated flipInY">
                                <!-- text-->
                                <a href="profile.php" class="dropdown-item"><i class="mdi mdi-account"></i> <?php echo ($trans["side_bar"]["my_profile"]) ?></a>
                                <!-- text-->
                                <a href="packages.php" class="dropdown-item"><i class="mdi mdi-wallet"></i> <?php echo ($trans["side_bar"]["my_balance"]) ?></a>
                                <!-- text-->

                                <div class="dropdown-divider"></div>
                                <!-- text-->
                                <a href="?logout" class="dropdown-item"><i class="mdi mdi-power"></i> <?php echo ($trans["side_bar"]["logout"]) ?></a>
                                <!-- text-->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li> <a class="waves-effect waves-dark" href="index.php" aria-expanded="false"><i class="mdi mdi-wechat"></i><span class="hide-menu"> <?php echo ($trans["side_bar"]["chat_room"]) ?><span class="badge badge-pill badge-info" id="total_unread_messages"><?php echo $totalUnreadMessages ?></span></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="profile.php" aria-expanded="false"><i class="mdi mdi-clipboard-account"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["my_account"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="packages.php" aria-expanded="false"><i class="mdi mdi-package-variant"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["packages"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="transactions.php" aria-expanded="false"><i class="mdi mdi-archive"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["transactions"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="contact.php" aria-expanded="false"><i class="mdi mdi-message-text-outline"></i><span class="hide-menu"><?php echo ($trans["contact"]) ?><span class="badge badge-pill badge-info" id="total_unread_messages"><?= $unresolved->unread ?></span></span></a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside> <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->

        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">

                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 d-flex align-items-center">
                        <h6 style="margin: 0; padding: 5px;" class="balance_prefix"><span class="text-themecolor"> <?php echo ($trans["customer_account"]["header"]["balance"]) ?> : </span><span class="header-balance-text"></span> <sup> Msg</sup> </h6>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)"><?php echo ($trans["home"]) ?></a></li>
                                <li class="breadcrumb-item active m-r-15"><?php echo ($trans[$page_name]) ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class='col-md-12' id="out_of_balace" style="display: none;">
                    <a href="packages.php"><div class='alert alert-danger alert-dismissable'><?= $trans["chat"]["insufficient_credits"] ?></div></a>
                </div>
                <!-- ============================================================== -->