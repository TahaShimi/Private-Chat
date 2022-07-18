<?php
include('../../init.php');
session_start();
if (empty($_SESSION['login']) || intval($_SESSION['login']) != 3) {
    header('Location: ../../index.php');
    exit();
} elseif (isset($_GET["logout"])) {
    session_unset();
    session_destroy();

    header('Location: ../../index.php');
    exit();
}


$id_account = intval($_SESSION['id_account']);
$account_stmt = $conn->prepare("SELECT * FROM  `users` u join `consultants` c on u.id_profile=c.id_consultant  where u.id_user=:iu AND u.profile=3");
$account_stmt->bindParam(':iu',  $_SESSION['id_user'], PDO::PARAM_INT);
$account_stmt->execute();
$account = $account_stmt->fetchObject();
$rights = json_decode($account->read_rights);
$stmt2 =  $conn->prepare("SELECT sender from messages where receiver=:id and status=0 GROUP BY sender");
$stmt2->bindparam(":id", $_SESSION['id_user'], PDO::PARAM_INT);
$stmt2->execute();
$dta = $stmt2->fetchAll();
$total_unreaded = count($dta);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" id="pageIcon" type="image/gif" sizes="16x16" href="../../assets/images/favicon.png">
    <title>Private chat</title>
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="../../assets/node_modules/morrisjs/morris.css" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <link href="../../assets/node_modules/toast-master/jquery.toast.css" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="../../assets/css/pages/dashboard1.css" rel="stylesheet">

    <!-- Popup CSS -->
    <link href="../../assets/css/pages/stylish-tooltip.css" rel="stylesheet">
    <link href="../../assets/node_modules/Magnific-Popup-master/magnific-popup.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../assets/node_modules/datatables.net-bs4/dataTables.bootstrap4.css">
    <!-- Page CSS -->
    <link href="../../assets/css/pages/chat-app-page.css" rel="stylesheet">
    <link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="../../assets/node_modules/dropify/dropify.min.css">
    <link href="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
    <link href="../../assets/css/style.min.css" rel="stylesheet">
    <link href="../../assets/css/custom.css" rel="stylesheet">
    <link href="../../assets/css/pages/consultant.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<script src="../../assets/js/env.js"></script>
<script>
    var sender_pseudo = "<?= $_SESSION['pseudo'] ?>";
    var sender_avatar = "<?= $_SESSION['avatar'] ?>";
    var sender = <?= $_SESSION['id_user'] ?>;
    var consultant_id = <?= $_SESSION['id_account'] ?>;
    var id_group = <?= $_SESSION['id_company'] ?>;
    var sender_role = 3;
</script>

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
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <!-- User Profile-->
                    <div class="profile_img">
                        <div class="user-pro-body">
                            <div class="dropdown">
                                <span style="color: white"><?php echo $_SESSION['pseudo'] ?></span>
                                <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo '../uploads/consultants/' . $_SESSION['avatar'] ?>" alt="user-img" class="img-circle" style="height: 45px;width:45px"> <span class="caret"></span></a>
                                <div class="dropdown-menu animated flipInY">
                                    <!-- text-->
                                    <a href="profile.php" class="dropdown-item"><i class="mdi mdi-clipboard-account"></i> <?php echo ($trans["side_bar"]["my_profile"]) ?></a>
                                    <!-- text-->
                                    <!-- text-->
                                    <div class="dropdown-divider"></div>
                                    <!-- text-->
                                    <a href="profile.php?tab=settings" class="dropdown-item"><i class="mdi mdi-settings"></i> <?php echo ($trans["side_bar"]["settings"]) ?></a>
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
            </nav>
        </header> <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li> <a class="waves-effect waves-dark" href="index.php" aria-expanded="false"><i class="mdi mdi-speedometer-medium"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["dashboard"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="profile.php" aria-expanded="false"><i class="mdi mdi-clipboard-account"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["my_account"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="in_progress.php" aria-expanded="false"><i class="mdi mdi-wechat"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["chat_room"]) ?><span class="badge badge-pill badge-info" id="total_unread_messages"><?=$total_unreaded?></span></span></a>
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
                    <div class="col-md-12 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)"><?php echo ($trans['home']) ?></a></li>
                                <li class="breadcrumb-item m-r-10 active"><?php echo ($trans[$page_name]) ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
