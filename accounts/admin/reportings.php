<?php
$page_name = "reportings";
ob_start();
include('header.php');
?>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<style>
    .form-control {
        min-height: 30px;
    }

    .imp {
        border-top: 1px solid #dddddd;
        border-bottom: 1px solid #dddddd;
        border-right: 1px solid #dddddd;
        background-color: #f8f9fa
    }

    .imp:first-child {
        border-left: 1px solid #dddddd;
    }

    .pair {
        border-top: 1px solid #dddddd;
        border-bottom: 1px solid #dddddd;
        border-right: 1px solid #dddddd;
    }

    .pair:first-child {
        border-left: 1px solid #dddddd;
    }

    .bg-info {
        background-color: #ff3f5f !important;
    }

    .text-info {
        color: #ff3f5f !important;
    }

    .container-fluid {
        padding: 0 10px;
    }

    #content {
        padding-left: 10px;
    }

    .radio-toolbar input[type="radio"] {
        opacity: 0;
        position: fixed;
        width: 0;
    }

    .radio-toolbar label {
        display: inline-block;
        background-color: #E6EAEE;
        margin-bottom: 0px;
        cursor: pointer;
        border-radius: 5%;
    }

    .radio-toolbar label:hover {
        background-color: #ff6774;
    }

    .radio-toolbar input[type="radio"]:checked+label {
        background-color: #ff6774;
    }

    .radio-toolbar a {
        color: black;
    }

    .customtab2 li a.nav-link.active {
        background: #dddddd;
        color: black;
    }

    .card-title {
        margin-top: auto;
        margin-bottom: auto;
    }

    .m-l-10 {
        width: 80%;
    }

    .round.round-danger .progress-bar {
        background-color: #ff6774;
    }

    .danger {
        color: #ff6774;
    }

    table.dataTable {
        margin: 0 !important
    }

    .DTFC_ScrollWrapper {
        height: auto !important
    }

    .even {
        background-color: white;
    }

    thead {
        background-color: white;
    }

    .radio-toolbar a .active {
        background-color: #ff6774;
        color: white;
    }

    .bg-secondary {
        min-height: 140px;
        padding: 20px;
    }

    .card-group .card {
        border-right: 0;
    }

    .bg-secondary {
        background-color: #fafcfd !important;
    }

    .box {
        max-width: 100%;
    }

    .col-10 {
        background-image: linear-gradient(to right, #ff6472 0%, #ffb199 150%);
        border-radius: 5%
    }

    .toolbar {
        float: left;
        margin-top: 10px;
    }
</style>
<div class="card">
    <div class="card-body">
        <div style="display: flex">
            <div>
                <h5 class="card-title m-b-10 float-left m-r-10"><?= $trans['reportingsPage']['Periode'] ?></h5>
                <h6 class="currentDate float-left m-b-0" style="color:#6c757d;line-height: initial;"></h6>
            </div>
            <div class="text-right ml-auto" style="margin-top: auto;margin-bottom: auto;">
                <div class="radio-toolbar">
                    <a href="#" id="1" class="periode"><label for="Today" class="btn-sm"><?= $trans['reportingsPage']['Today'] ?></label></a>
                    <a href="#" id="2" class="periode"><label for="Previousday" class="btn-sm"><?= $trans['reportingsPage']['Previous_day'] ?></label></a>
                    <a href="#" id="3" class="periode"><label for="Thisweek" class="btn-sm"><?= $trans['reportingsPage']['This_week'] ?></label></a>
                    <a href="#" id="4" class="periode"><label for="Previousweek" class="btn-sm"><?= $trans['reportingsPage']['Previous_week'] ?></label></a>
                    <a href="#" id="5" class="periode"><label for="Thismonth" class="btn-sm"><?= $trans['reportingsPage']['This_month'] ?></label></a>
                    <a href="#" id="6" class="periode"><label for="Previousmonth" class="btn-sm"><?= $trans['reportingsPage']['Previous_month'] ?></label></a>
                    <a href="#" id="7" class="custom"><label for="Customperiod" class="btn-sm"><?= $trans['reportingsPage']['Custom_period'] ?></label></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card">
        <ul class="nav nav-tabs profile-tab reportingTabs" role="tablist">
            <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'visits')) {
                                                            echo "active";
                                                        } ?>" data-toggle="tab" href="#visits" role="tab"><?= $trans['reportingsPage']['Visits'] ?></a> </li>
            <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'chat_activity') {
                                                            echo "active";
                                                        } ?>" data-toggle="tab" href="#chat_activity" role="tab"><?= $trans['reportingsPage']['Chat_activity'] ?></a></li>
            <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'chat_activity_per_consultant') {
                                                            echo "active";
                                                        } ?>" data-toggle="tab" href="#chat_activity_per_consultant" role="tab"><?= $trans['reportingsPage']['Chat_Activity_per_consultant'] ?></a> </li>
            <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'customers') {
                                                            echo "active";
                                                        } ?>" data-toggle="tab" href="#customers" role="tab"><?= $trans['reportingsPage']['customers'] ?></a> </li>
            <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'sales') {
                                                            echo "active";
                                                        } ?>" data-toggle="tab" href="#sales" role="tab"><?= $trans['reportingsPage']['sales'] ?></a> </li>
        </ul>
        <div class="tab-content" id="content">
            <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'visits')) {
                                        echo "active";
                                    } ?>" id="visits" role="tabpanel">
                <div id="container"></div>
            </div>
            <div class="tab-pane " id="chat_activity" role="tabpanel">
                <div class="card-group">
                    <div class="card bg-secondary m-1">
                        <div class="card-body p-b-0">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="mdi mdi-send"></i></h3>
                                            <p class="text-muted"><?= $trans['reportingsPage']['customers_send_messages'] ?></p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-info send"></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-secondary m-1">
                        <div class="card-body p-b-0">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="mdi mdi-call-made"></i></h3>
                                            <p class="text-muted"><?= $trans['reportingsPage']['consultant_replied_messages'] ?></p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-info receive"></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-secondary m-1">
                        <div class="card-body p-b-0">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="mdi mdi-timer"></i></h3>
                                            <p class="text-muted"><?= $trans['consultant_account']['dashboard']['average_response_time'] ?></p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-info Total"></h2>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <small>Min : <span class="min text-muted"></span></small>
                                        <small>Max : <span class="max text-muted"></span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-secondary m-1">
                        <div class="card-body p-b-0">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h3><i class="mdi mdi-phone"></i></h3>
                                            <p class="text-muted"><?= $trans['reportingsPage']['Pick_up_time'] ?></p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-info pick"></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h3 class="box-title m-t-30 m-l-10"><?= $trans['reportingsPage']['Unreaded_messages'] ?></h3>
                <table class="display nowrap table table-hover table-striped m-t-10" id="UnreadMessages" style="width:100%">
                    <thead>
                        <tr>
                            <th><?= $trans['fullname'] ?></th>
                            <th><?= $trans['reportingsPage']['Total_Messages'] ?></th>
                            <th><?= $trans['reportingsPage']['Last_Messages'] ?></th>
                            <th>Date</th>
                            <th><?= $trans['consultant'] ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>

            </div>
            <div class="tab-pane" id="chat_activity_per_consultant" role="tabpanel">
                <div class="table-responsive m-b-40 m-t-10">
                    <table class="display  nowrap table table-hover table-striped m-t-10" id="activity" style="width:100%">
                        <thead>
                            <tr>
                                <th><?= $trans['consultant'] ?></th>
                                <th><?= $trans['consultant_account']['dashboard']['received_messages'] ?></th>
                                <th><?= $trans['consultant_account']['dashboard']['replied_messages'] ?></th>
                                <th><?= $trans['consultant_account']['dashboard']['average_response_time'] ?></th>
                                <th><?= $trans['reportingsPage']['Pick_up_time'] ?></th>
                                <th><?= $trans['reportingsPage']['Unreaded_messages'] ?></th>
                                <th><?= $trans['reportingsPage']['sales'] ?></th>
                                <th><?= $trans['reportingsPage']['Total_amount'] ?></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane " id="customers" role="tabpanel">
                <div class="card-group">
                    <div class="card bg-secondary m-1">
                        <div class="card-body p-b-0">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h1><i class="mdi mdi-account-multiple-outline"></i></h1>
                                            <p class="text-muted"><?= $trans['reportingsPage']['Total_Contacts'] ?></p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-info contacts"></h2>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm waves-effect waves-light btn-secondary" data-toggle="modal" data-target="#contacts"><?= $trans['details'] ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-secondary m-1">
                        <div class="card-body p-b-0">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h1><i class="mdi mdi-account-multiple"></i></h1>
                                            <p class="text-muted"><?= $trans['reportingsPage']['Total_Customers'] ?></p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-info customers"></h2>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm waves-effect waves-light btn-secondary" data-toggle="modal" data-target="#customersM"><?= $trans['details'] ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-secondary m-1">
                        <div class="card-body p-b-0">
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <h1><i class="mdi mdi-account-remove"></i></h1>
                                            <p class="text-muted"><?= $trans['reportingsPage']['contacts_without_purchase'] ?></p>
                                        </div>
                                        <div class="ml-auto">
                                            <h2 class="counter text-info purchase"></h2>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm waves-effect waves-light btn-secondary" data-toggle="modal" data-target="#purchase"><?= $trans['details'] ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="container1"></div>
            </div>
            <div class="tab-pane " id="sales" role="tabpanel">
                <div>
                    <div class="card-group">
                        <div class="card bg-secondary m-1">
                            <div class="card-body p-b-0">
                                <div class="row">
                                    <div class="col-md-12 p-0">
                                        <div class="d-flex no-block align-items-center">
                                            <div>
                                                <h1><i class="mdi mdi-credit-card"></i></h1>
                                                <p class="text-muted"><?= $trans['reportingsPage']['Total_sales'] ?></p>
                                            </div>
                                            <div class="ml-auto">
                                                <h2 class="counter text-info totalSales"></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-secondary m-1">
                            <div class="card-body p-b-0">
                                <div class="row">
                                    <div class="col-md-12 p-0">
                                        <div class="d-flex no-block align-items-center">
                                            <div>
                                                <h1><i class="mdi mdi-cash"></i></h1>
                                                <p class="text-muted"><?= $trans['reportingsPage']['Total_amount'] ?></p>
                                            </div>
                                            <div class="ml-auto">
                                                <h2 class="counter text-info totalamount"></h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#totalincomes"><?= $trans['details'] ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="nav  profile-tab " role="tablist">
                    <li class="nav-item" style="padding: 12px 20px;">
                        <h4><?= $trans['viewby'] ?></h4>
                    </li>
                    <li class="nav-item nav-tabs"> <a class="nav-link active" data-toggle="tab" href="#VBwebsites" role="tab">Website</a></li>
                    <li class="nav-item nav-tabs"> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'VBexperts') {
                                                                            echo "active";
                                                                        } ?>" data-toggle="tab" href="#VBexperts" role="tab">Expert</a> </li>
                    <li class="nav-item nav-tabs"> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'VBend_user') {
                                                                            echo "active";
                                                                        } ?>" data-toggle="tab" href="#VBend_user" role="tab">End user</a> </li>
                </ul>
                <div class="tab-content" id="content">
                    <div class="tab-pane active" id="VBwebsites" role="tabpanel">
                        <div class="table-responsive m-b-40 m-t-10">
                            <table class="display  nowrap table table-hover table-striped" id="VBwebsitesT" style="width:100%">
                                <thead>
                                    <th><?= $trans['admin']['websites_list']['websitestable']['name'] ?></th>
                                    <th><?= $trans['admin']['websites_list']['websitestable']['activity'] ?></th>
                                    <th><?= $trans['reportingsPage']['Total_sales'] ?></th>
                                    <th><?= $trans['reportingsPage']['Total_amount'] ?></th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane " id="VBexperts" role="tabpanel">
                        <div class="table-responsive m-b-40 m-t-10">
                            <table class="display  nowrap table table-hover table-striped" id="VBexpertsT" style="width:100%">
                                <thead>
                                    <th><?= $trans['pseudo'] ?></th>
                                    <th><?= $trans['reportingsPage']['Total_sales'] ?></th>
                                    <th><?= $trans['reportingsPage']['Total_amount'] ?></th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane " id="VBend_user" role="tabpanel">
                        <div class="table-responsive m-b-40 m-t-10">
                            <table class="display  nowrap table table-hover table-striped" id="VBcustomersT" style="width:100%">
                                <thead>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                                    <th><?= $trans['reportingsPage']['Total_sales'] ?></th>
                                    <th><?= $trans['reportingsPage']['Total_amount'] ?></th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="modal fade" id="with" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel0"><?= $trans['reportingsPage']['known_visitors_with_chat'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="withTb" style="width:100%">
                        <thead>
                            <th>ID</th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["email"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["country"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["phone"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["created_at"]) ?></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="without" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['reportingsPage']['known_visitors_without_chat'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="withoutTb" style="width:100%">
                        <thead>
                            <th>ID</th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["email"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["country"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["phone"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["created_at"]) ?></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="expertDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['consultant'] ?> Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="expertDetails" style="width:100%">
                        <thead>
                            <th>ID</th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                            <th><?= $trans['reportingsPage']['Pick_up_time'] ?></th>
                            <th><?= $trans['reportingsPage']['Send_date'] ?></th>
                            <th><?= $trans['reportingsPage']['Reply_date'] ?></th>
                            <th><?= $trans['consultant_account']['dashboard']['average_response_time'] ?></th>
                            <th>Actions</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="contacts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['reportingsPage']['contacts'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="contactsT" style="width:100%">
                        <thead>
                            <th>ID</th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["email"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["country"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["phone"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["buys_count"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["created_at"] ?></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="customersM" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['customers'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="customersT" style="width:100%">
                        <thead>
                            <th>ID</th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["email"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["country"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["phone"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["buys_count"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["created_at"] ?></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="purchase" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['reportingsPage']['contacts_without_purchase'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="purchaseT" style="width:100%">
                        <thead>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["email"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["country"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["phone"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["buys_count"] ?></th>
                            <th><?= $trans["admin"]["customers"]["customers_table"]["created_at"] ?></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="totalincomes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['Totalincomes'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="display  nowrap table table-hover table-striped" id="incomesTable" style="width:100%">
                    <thead>
                        <th>Currency</th>
                        <th>Income</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel0"><?= $trans['reportingsPage']['select_periode'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" id="myForm" method="POST" class="col-md-12" novalidate>
                    <div class="row">
                        <div class="col-6">
                            <label for="from"><?= $trans['from'] ?></label>
                            <input type="date" name="from" class="form-control from" required>
                        </div>
                        <div class="col-6">
                            <label for="to"><?= $trans['to'] ?></label>
                            <input type="date" name="to" class="form-control to" required>
                        </div>
                    </div>
                    <div class="m-t-40 modal-footer p-b-0 p-r-0">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="button" id="7" name="submit" class="periode btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
</div>
<div id="overlay">
    <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer> <!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/highcharts.js"></script>
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {
        $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
        $('.dt-button').removeClass('dt-button');
        $('#overlay').show();
        $('.custom').click(function() {
            $('#myModal').modal('show');
        });
        $('#1').click();
    });
    $('.nav-tabs a').on('shown.bs.tab', function(event) {
        $.fn.dataTable.tables({
            visible: true,
            api: true
        }).columns.adjust();
    });
    $('.modal').on('shown.bs.modal', function(event) {
        $.fn.dataTable.tables({
            visible: true,
            api: true
        }).columns.adjust();
    });
    var table = $('#activity').DataTable({
        orderCellsTop: true,
        dom: '<"toolbar">Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table1 = $('#UnreadMessages').DataTable({
        orderCellsTop: true,
        dom: '<"toolbar">frtip',
        scrollX: true
    });
    var table2 = $('#withoutTb').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table3 = $('#withTb').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    var table4 = $('#expertDetails').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table5 = $('#contactsT').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table6 = $('#customersT').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table7 = $('#purchaseT').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    var table8 = $('#VBwebsitesT').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        "order": [
            [3, "desc"]
        ],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    var table9 = $('#VBexpertsT').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        "order": [
            [2, "desc"]
        ],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table10 = $('#VBcustomersT').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        "order": [
            [3, "desc"]
        ],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table11 = $('#incomesTable').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.reportingTabs .nav-link').on('shown.bs.tab', function(e) {
        $('label.active').parents('.periode').click();
    });
    $('.periode').click(function() {
        $('.radio-toolbar').find('label').removeClass("active");
        $(this).children('label').addClass("active");
        $('#overlay').show();
        let id = $(this).attr('id');
        let text = $(this).text();
        let from = $('.from').val();
        let to = $('.to').val();
        if (id == 7) {
            $('.periode').children('label').removeClass("active");
            $('.custom').children('label').addClass("active");
        }
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            data: {
                id: id,
                type: 'getStat',
                tab: $('.nav-link.active').attr('href'),
                id_account: <?= intval($_SESSION['id_account']) ?>,
                from: from,
                to: to,
                currency: '<?= $_SESSION['currency'] ?>',
            },
            dataType: 'json',
            success: function(data) {
                $('#overlay').hide();
                switch ($('.nav-link.active').attr('href')) {
                    case '#visits':
                        table2.clear();
                        table2.rows.add(data.table2).draw();
                        table3.clear();
                        table3.rows.add(data.table3).draw();
                        Highcharts.chart('container', {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                type: 'pie'
                            },
                            title: {
                                text: 'Visits:' + (data.table2.length + data.table3.length)
                            },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            credits: false,
                            accessibility: {
                                point: {
                                    valueSuffix: '%'
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)'
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Brands',
                                colorByPoint: true,
                                data: [{
                                    name: '<?= $trans['reportingsPage']['known_visitors_without_chat'] ?>',
                                    y: data.table2.length,
                                    events: {
                                        legendItemClick: function() {
                                            $('#without').modal('show');
                                            return false
                                        }
                                    }
                                }, {
                                    name: '<?= $trans['reportingsPage']['known_visitors_with_chat'] ?>',
                                    y: data.table3.length,
                                    events: {
                                        legendItemClick: function() {
                                            $('#with').modal('show');
                                            return false
                                        }
                                    }
                                }]
                            }]
                        }, function(chart) {
                            chart.renderer.text('<strong>(Details)</strong><br><span style="color:#9BA0A2">Click for more details</span>', 640, 330)
                                .attr({
                                    zIndex: 5
                                })
                                .add();
                        });
                        break;
                    case '#chat_activity':
                        table1.clear();
                        table1.rows.add(data.table1).draw();
                        let total = 0;
                        $.each(data.table1, function() {
                            total += parseInt(this[1]);
                        })
                        $("#UnreadMessages_wrapper > div.toolbar").html('<h4 style="display:inline">Total : <span class="counter text-info ">' + total + '</span></h4>');
                        $('.Total').text(data.moy);
                        $('.max').text(data.max);
                        $('.min').text(data.min);
                        $('.send').text(data.customerSend);
                        $('.receive').text(data.consultantSend);
                        $('.pick').text(data.pick);
                        break;
                    case '#chat_activity_per_consultant':
                        table.clear();
                        table.rows.add(data.table).draw();
                        break;
                    case '#customers':
                        table7.clear();
                        table7.rows.add(data.table7).draw();
                        table5.clear();
                        table5.rows.add(data.table5).draw();
                        table6.clear();
                        table6.rows.add(data.table6).draw();
                        $('.contacts').text(data.table5.length);
                        $('.customers').text(data.table7.length);
                        $('.purchase').text(data.table6.length);
                        Highcharts.chart('container1', {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                type: 'pie'
                            },
                            title: {
                                text: 'Contacts'
                            },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            credits: false,
                            accessibility: {
                                point: {
                                    valueSuffix: '%'
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Brands',
                                colorByPoint: true,
                                data: [{
                                    name: '<?= $trans['reportingsPage']['contacts'] ?>',
                                    y: data.table5.length,
                                    events: {
                                        legendItemClick: function() {
                                            return false
                                        }
                                    }
                                }, {
                                    name: '<?= $trans['reportingsPage']['customers'] ?>',
                                    y: data.table7.length,
                                    events: {
                                        legendItemClick: function() {
                                            return false
                                        }
                                    }
                                }]
                            }]
                        });
                        break;
                    case '#sales':
                        $('.totalSales').text(data.sales.total);
                        $('.totalamount').html(data.sales.amount);
                        table8.clear();
                        table8.rows.add(data.VBwebsites).draw();
                        table8
                            .column('3:visible')
                            .order('asc')
                            .draw();
                        table9.clear();
                        table9.rows.add(data.VBexpert).draw();
                        table9
                            .column('2:visible')
                            .order('asc')
                            .draw();
                        table10.clear();
                        table10.rows.add(data.VBEnd_user).draw();
                        table10
                            .column('3:visible')
                            .order('asc')
                            .draw();
                        table11.clear();
                        table11.rows.add(data.incomes).draw();        
                        break;
                    default:
                        break;
                }
                $('.currentDate').html(data.currentDate);
                $('#overlay').hide();
                $('#myModal').modal('hide');
            }
        });
    });
    $('#activity').on('click', '.expertDetails', function() {
        $('#overlay').show();
        let id = $(this).data('id');
        let periode = $('.active').parent('a').attr('id');
        let from = $('.from').val();
        let to = $('.to').val();
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            data: {
                id: id,
                type: 'getPick',
                periode: periode,
                from: from,
                to: to,
            },
            dataType: 'json',
            success: function(data) {
                table4.clear();
                table4.rows.add(data).draw();
                $('#expertDetail').modal('show');

                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
                $('#overlay').hide();

            }
        });
    })
</script>