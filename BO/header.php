<?php
include('../init.php');

session_start();
if (empty($_SESSION['login']) || intval($_SESSION['login']) != 1) {
    header('Location: login.php');
    exit();
} elseif (isset($_GET["logout"])) {
    $stmt2 = $conn->prepare("UPDATE `users` SET `status` = 0 WHERE `id_user` = :ID");
    $stmt2->bindParam(':ID', $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt2->execute();

    session_unset();
    session_destroy();

    header('Location: login.php');
    exit();
}
$id_user = $_SESSION['id_user'];

$r1 = $conn->prepare("SELECT `id_account` FROM `accounts` WHERE `status` = 1");
$r1->execute();
$new_customers = $r1->rowCount();

$r2 = $conn->prepare("SELECT `id_account` FROM `accounts` WHERE `status` = 1");
$r2->execute();
$new_websites = $r2->rowCount();
$contact = $conn->prepare("SELECT count(*) as unread FROM contact_ticket ct,users u WHERE   (ct.status IN (1,0) AND (SELECT cm.id_sender from contact_messages cm WHERE cm.id_ticket=ct.id_ticket ORDER by cm.date DESC LIMIT 1)!=:idu) AND u.id_user= ct.id_customer AND u.profile=2");
$contact->bindParam(':idu',  $id_user, PDO::PARAM_INT);
$contact->execute();
$unresolved = $contact->fetchObject();
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Private chat</title>
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="../assets/node_modules/morrisjs/morris.css" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <link href="../assets/node_modules/toast-master/jquery.toast.css" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="../assets/css/pages/pricing-page.css" rel="stylesheet">
    <link href="../assets/css/pages/dashboard1.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../assets/node_modules/bootstrap-switch/bootstrap-switch.min.css" rel="stylesheet">
    <link href="../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/pages/tab-page.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/node_modules/dropify/dropify.min.css">
    <link href="../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
    <!-- Popup CSS -->
    <link href="../assets/node_modules/Magnific-Popup-master/magnific-popup.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/node_modules/datatables.net-bs4/dataTables.bootstrap4.css">
    <!-- page css -->
    <link href="../assets/css/pages/user-card.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/int-phone-number/css/intlTelInput.css">

    <link href="../assets/css/style.min.css" rel="stylesheet">
    <link href="../assets/css/pages/bootstrap-switch.css" rel="stylesheet">
    <link href="../assets/css/pages/file-upload.css" rel="stylesheet">
    <link href="../assets/css/pages/form-icheck.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
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
                        <b>
                            <img src="../assets/images/logo-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <span>
                            <img src="../assets/images/logo-text.png" alt="homepage" class="light-logo" />
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
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
                        <li class="nav-item ">
                            <h4 class="text-themecolor title_page"><?php echo $trans[$page_name]; ?></h4>
                        </li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <!-- User Profile-->
                    <div class="user-profile d-none d-md-block" style="width: 200px ! important;">
                        <div class="user-pro-body">
                            <div class="dropdown">
                                <span style="color:white">ADMIN</span>
                                <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="../assets/images/users/2.jpg" alt="user-img" class="img-circle" style="height:45px;width:45px"> <span class="caret"></span></a>
                                <div class="dropdown-menu animated flipInY">
                                    <!-- text-->
                                    <a href="my_profile.php" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                                    <!-- text-->
                                    <a href="my_profile.php?tab=balance" class="dropdown-item"><i class="ti-wallet"></i> My Balance</a>
                                    <!-- text-->
                                    <div class="dropdown-divider"></div>
                                    <!-- text-->
                                    <a href="my_profile.php?tab=settings" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
                                    <!-- text-->
                                    <div class="dropdown-divider"></div>
                                    <!-- text-->
                                    <a href="?logout" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
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
                        <li> <a class="waves-effect waves-dark" href="index.php" aria-expanded="false"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard</span></a>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-id-badge"></i><span class="hide-menu">Customers <?php if ($new_customers > 0) {
                                                                                                                                                                                            echo "<span class='badge badge-pill badge-success'>" . $new_customers . "</span>";
                                                                                                                                                                                        } ?></span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="customers.php">Customers list</a></li>
                                <li><a href="customer_add.php">Add customer</a></li>
                                <li><a href="websites.php">Websites</a></li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-id-badge"></i><span class="hide-menu">Publishers </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="Publishers.php">Publishers list</a></li>
                                <li><a href="Publisher_add.php">Add Publisher</a></li>
                                <li><a href="contracts.php">Contracts</a></li>
                            </ul>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="payments.php" aria-expanded="false"><i class="ti-credit-card"></i><span class="hide-menu">Payments</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="contact.php" aria-expanded="false"><i class="ti-credit-card"></i><span class="hide-menu">Contacts<span class="badge badge-pill badge-info" id="total_unread_messages"><?= $unresolved->unread ?></span></span></a>
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
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                <li class="breadcrumb-item active"><?php echo $trans[$page_name]; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->