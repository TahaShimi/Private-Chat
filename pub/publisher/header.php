<?php
include('../../init.php');

session_start();
if(!isset($_SESSION['creationTime'])){
    $_SESSION['creationTime'] = time();

}else if (time() - $_SESSION['creationTime'] > 60*60*24 ){
    session_unset();
    session_destroy();
}
if (empty($_SESSION['login']) || intval($_SESSION['login']) != 5) {
    header('Location: ../index.php');
    exit();
} elseif (isset($_GET["logout"])) {

    session_unset();
    session_destroy();

    header('Location: ../index.php');
    exit();
}
$st = $conn->prepare("SELECT * from publishers c,users u WHERE c.id_publisher=u.id_profile AND u.id_user=:IDA");
$st->bindparam(":IDA", $_SESSION['id_user']);
$st->execute();
$account = $st->fetchObject();
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
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="../../assets/node_modules/morrisjs/morris.css" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <link href="../../assets/node_modules/toast-master/jquery.toast.css" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="../../assets/css/pages/pricing-page.css" rel="stylesheet">
    <link href="../../assets/css/pages/dashboard1.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../assets/int-phone-number/css/intlTelInput.css">

    <!-- Custom CSS -->
    <link href="../../assets/css/pages/contact-app-page.css" rel="stylesheet">
    <link href="../../assets/node_modules/bootstrap-switch/bootstrap-switch.min.css" rel="stylesheet">
    <link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/pages/tab-page.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/node_modules/dropify/dropify.min.css">
    <link href="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
    <!-- Popup CSS -->
    <link href="../../assets/node_modules/Magnific-Popup-master/magnific-popup.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../assets/node_modules/datatables.net-bs4/dataTables.bootstrap4.css">
    <!-- page css -->
    <link href="../../assets/css/pages/user-card.css" rel="stylesheet">

    <link href="../../assets/css/style.min.css" rel="stylesheet">
    <link href="../../assets/css/account_custom.css" rel="stylesheet">
    <link href="../../assets/css/pages/chat-app-page.css" rel="stylesheet">
    <link href="../../assets/css/custom.css" rel="stylesheet">
    <link href="../../assets/css/pages/consultant.css" rel="stylesheet">
    <link href="../../assets/css/pages/bootstrap-switch.css" rel="stylesheet">
    <link href="../../assets/css/pages/file-upload.css" rel="stylesheet">
    <link href="../../assets/css/pages/form-icheck.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

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
                        <b>
                            <img src="../../assets/images/logo-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <span>
                            <img src="../../assets/images/logo-text.png" alt="homepage" class="light-logo" />
                        </span>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item ">
                            <h4 class="text-themecolor title_page"><?php echo ($trans[$page_name]) ?></h4>
                        </li>
                    </ul>
                    <div class="user-profile">
                        <div class="user-pro-body">
                            <div class="dropdown">
                                <span style="color: white"><?= $account->company_name?></span>
                                <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <img src="../../assets/images/users/2.jpg" alt="user-img" class="img-circle" style="height: 45px;width:45px"><span class="caret"></span></a>
                                <div class="dropdown-menu animated flipInY">
                                    <!-- text-->
                                    <a href="my_profile.php?tab=general_informations" class="dropdown-item"><i class="mdi mdi-cog"></i> <?php echo ($trans["settings"]) ?></a>
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
        </header>
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li> <a class="waves-effect waves-dark" href="index.php" aria-expanded="false"><i class="mdi mdi-speedometer-medium"></i><span class="hide-menu"><?php echo ($trans["side_bar"]["dashboard"]) ?></span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="leads.php" aria-expanded="false"><i class="mdi mdi-grid"></i><span class="hide-menu"><?= $trans['leads']?></span></a>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-layout-"></i><span class="hide-menu"><?= $trans['Advertisers']?></span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="Add_Advertiser.php"><?= $trans['add_advertiser']?></a></li>
                                <li><a href="Advertiser.php"><?= $trans['Advertisers']?></a></li>
                                <li><a href="Advertisers_programs.php"><?= $trans['Advertisers_Programs'] ?></a></li>
                            </ul>
                        </li>
                        <li class="<?php if (in_array(basename($_SERVER['PHP_SELF']), array('consultant.php'))) {echo "active";} ?>"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-badge"></i><span class="hide-menu"><?= $trans['publisher']['contributors']?></span></a>
                            <ul aria-expanded="false" class="collapse <?php if (in_array(basename($_SERVER['PHP_SELF']), array('consultant.php'))) {echo "in";} ?>">
                                <li><a href="Add_Contributor.php"><?= $trans['add_contributor']?></a></li>
                                <li><a href="Contributors.php"><?= $trans['publisher']['contributors']?></a></li>
                            </ul>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="Reportings.php?tab=byContributor" aria-expanded="false"><i class="mdi mdi-chart-areaspline"></i><span class="hide-menu"><?= $trans['reportings']?></span></a>
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
                                <li class="breadcrumb-item active"><?php echo ($trans[$page_name]) ?></li>
                            </ol>
                        </div>
                    </div>
                </div>