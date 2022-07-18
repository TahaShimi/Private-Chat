<?php
$page_name = "realTime_supervision";
ob_start();
include('header.php');
$s1 = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title,(SELECT count(*) FROM offers o where o.id_package=p.id_package and end_date >= CURDATE()) as offers_count FROM `packages` p JOIN packages_price pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang WHERE pp.primary=1 AND p.id_account = :ID and p.active=1");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
$s1->execute();
$packages = $s1->fetchAll();
$s2 = $conn->prepare("SELECT * FROM websites WHERE id_account=:ID");
$s2->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s2->execute();
$websites = $s2->fetchAll();
$stmt = $conn->prepare("SELECT * from accounts_messages where id_account=:id");
$stmt->bindparam(":id",  $_SESSION['id_company'], PDO::PARAM_INT);
$stmt->execute();
$cont = $stmt->fetchObject();
?>
<!-- <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="../../assets/node_modules/dropify/dropify.min.css">
<link href="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
<link href="../../assets/css/style.min.css" rel="stylesheet"> -->
<link href="../../assets/css/custom.css" rel="stylesheet">
<link href="../../assets/css/pages/consultant.css" rel="stylesheet">
<link href="../../assets/css/pages/chat-app-page.css" rel="stylesheet">
<style>
    .msg {white-space: nowrap;text-overflow: ellipsis;max-width: 200px;overflow: hidden;}
    .menu {position: relative;display: inline-block;overflow: hidden;width: 100%;}
    tr {max-height: 70px;}
    .table tr:first-child td {border-top: none;}
    .menu td,.menu button {display: inline-block;white-space: nowrap;transition: transform .5s ease-out, opacity .5s;overflow-x: hidden;}
    .menu button {position: absolute;left: 100%;width: 14%;}
    .menu button:nth-child(1) {top: 38px;}
    .menu button:nth-child(2) {top: 7px;}
    input {max-height: 37px;}
    .menu:hover button {transform: translateX(-100%);}
    table {width: 100%;}
    .profile-pic {width: 100px;border-radius: 100%;}
    .chat-main-box .chat-left-aside {position: relative;width: 100%;float: left;z-index: 9;height: 100%;top: 0px;border-right: none}
    .dropdown-toggle::after {display: none;}
    .liste {width: 100%;max-height: 500px;}
    .col-sm-3 {border-right: 1px solid #c7c5c5;}
    .comboColumn {border-bottom: 1px solid #c7c5c5;}
    .liste>li {list-style: none;margin-left: -30px;margin-top: 8px;}
    #inConversation>li:after {content: "";display: block;margin: 0 auto;width: 40%;padding-top: 20px;border-bottom: 1px solid #c7c5c5;}
    .w3-bar-item {text-transform: capitalize;}
    .text {padding-top: 7px;}
    .chat-main-box .chat-left-aside .chat-left-inner .style-none li a {padding: 1rem;}
    .img-circle {margin-right: 10px;width: 40px;height: 40px;}
    .back {margin-left: -8px;margin-top: 2px;margin-bottom: 15px;}
    .row {margin-right: 0px;}
    .total {float: right;font-size: 11px;margin: 5px 5px 0 0;}
    .total span {background-color: #2a7b9b;color: white;}
    .btn-rounded {padding: 4px 14px;}
</style>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-between align-items-center">
            <h5 class="card-title p-0 m-0"><?= $trans['Filtre'] ?></h5>
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex  align-items-center">
                    <h5 class="card-title m-0" style="min-width: 125px;"><?= $trans['websites'] ?> :</h5>
                    <select id="websites" class="form-control m-l-20 m-r-20" style="width:200px">
                        <option value="0"></option>
                        <?php foreach ($websites as $website) { ?>
                        <option value="<?= $website['id_website'] ?>"><?= $website['name'] ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="col-12 col-md-6 d-flex">
                    <h5 class="card-title my-0  m-r-20">Contacts :</h5>
                    <div class="custom-control custom-checkbox mr-sm-2">
                        <input type="checkbox" class="custom-control-input" id="customers" checked="">
                        <label class="custom-control-label" for="customers"><?= $trans['customersP'] ?></label>
                    </div>
                    <div class="custom-control custom-checkbox mr-sm-2">
                        <input type="checkbox" class="custom-control-input" id="leads" checked="">
                        <label class="custom-control-label" for="leads"><?= $trans['leads'] ?></label>
                    </div>
                    <div class="custom-control custom-checkbox mr-sm-2">
                        <input type="checkbox" class="custom-control-input" id="guests" checked="">
                        <label class="custom-control-label" for="guests"><?= $trans['guests'] ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="chat-main-box">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <small class="total"><span class="coTitle btn waves-light btn-outline-secondary">0</span></small>
                <div class="card-body inbox-panel">
                    <h4 class="card-title text-center " style="width:100%"><?php echo $trans["consultants"] ?></h4>
                    <div>
                        <div class="chat-left-inner" style="height: 386px;">
                            <div class="form-material">
                                <input class="form-control p-2" type="text" placeholder="<?php echo ($trans["chat"]["search_consultant"]) ?>" id="search_bar">
                            </div>
                            <div id="0" class="itemConsultant d-none" data-website="">
                                    <div class=" consultant_0" id="0" data-name="Default" data-id="0" data-avatar="consult.png">
                                        <a href="javascript:void(0)" data-id="0" data-type="3" class="expert" title="<?= $trans["Show_details"] ?>">  
                                            <img src="../uploads/consultants/consult.png" alt="user-img" class="img-circle" />
                                            <span>Default</span>
                                        </a>
                                    </div>
                            </div>
                            <ul class="chatonline style-none consultants p-0" style="list-style:none">
                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 border-left border-right">
                <small class="total"><span class="chatTitle btn  waves-light btn-outline-secondary">0</span></small>
                <button class="btn waves-effect waves-light btn-danger back"><i class="icon-arrow-left"></i> <?= $trans['in_Conversation'] ?></button>
                <div class="card-body conversations-actions">
                    <h4 class="card-title text-center " style="width:100%"><?= $trans['in_Conversation'] ?></h4>
                    <div class="card-body p-t-0" style="padding:0;">
                        <div class="card b-all shadow-none conversations-options  chat-left-inner">
                            <div class="inbox-center conve ">
                                <div class="form-material row">
                                    <div class="form-material col-md-6">
                                        <input class="form-control p-2" type="text" placeholder="Search By Expert" id="search_barConv1">
                                    </div>
                                    <div class="form-material col-md-6">
                                        <input class="form-control p-2" type="text" placeholder="Search By customer" id="search_barConv2">
                                    </div>
                                </div>
                                <table class="table table-hover no-wrap" style="margin-bottom: 0;">
                                    <tbody id="converations"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="customer-actions " style="padding-left: 1.25rem;">
                    <div class="btn-group m-b-10 m-r-10" role="group" aria-label="Button group with nested dropdown">
                        <button type="button" class="btn btn-secondary btn-sm sendOffer"><i class="mdi mdi-send"></i> <?= $trans['send_create_offer'] ?></button>
                    </div>
                </div>
                <div class="card-body p-t-0 customer-options ">
                    <div class="card b-all shadow-none">
                        <div class="inbox-center table-responsive">
                            <div class="tab-content">
                                <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {
                                                            echo "active";
                                                        } ?>" id="general_informations" role="tabpanel">
                                    <div class="card">
                                        <div>
                                            <center class="m-t-5">
                                                <div class="avatar-wrapper m-b-5">
                                                    <img class="profile-pic" src="" /></div>
                                                <h5 class="card-title m-t-10" id="full_name"></h5>
                                                <h6 class="card-subtitle" id="website"></h6>
                                            </center>
                                        </div>
                                        <div>
                                            <hr>
                                        </div>
                                        <div class="row p-4">
                                            <div class="col-6">
                                                <small class="text-muted"><?php echo ($trans["gender"]) ?></small>
                                                <h6 id="gender"></h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db"><?php echo ($trans["first_name"]) ?></small>
                                                <h6 id="first_name"></h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db"><?php echo ($trans["last_name"]) ?></small>
                                                <h6 id="last_name"></h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db"><?php echo ($trans["email"]) ?></small>
                                                <h6 id="email"></h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db"><?php echo ($trans["phone"]) ?></small>
                                                <h6 id="phone"></h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db"><?php echo ($trans["address"]) ?></small>
                                                <h6 id="address"></h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db"><?php echo ($trans["country"]) ?></small>
                                                <h6 id="country"></h6>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db"><?php echo ($trans["language"]) ?></small>
                                                <h6 id="language"></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 ">
                <small class="total"><span class="cuTitle waves-light btn btn-outline-secondary">0</span></small>
                <div class="card-body inbox-panel">
                    <h4 class="card-title text-center " style="width:100%"><?php echo $trans["customersP"] ?></h4>
                    <div class="">

                        <div class="chat-left-inner">
                            <div class="form-material">
                                <input class="form-control p-2" type="text" placeholder="<?php echo ($trans["chat"]["search_customer"]) ?>" id="search_bar1">
                            </div>
                            <ul class="chatonline style-none customers p-0" style="list-style: none">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div id="overlay">
    <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>

<div class="modal" id="experts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1"><?= $trans['Re-assignCustomer'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div>
                    <h4><?= $trans['ChooseExpert'] ?></h4>
                    <select class="form-control experts" required>

                    </select>
                </div>
                <input id="customer" value="" hidden>
                <input id="expert" value="" hidden>
            </div>
            <div class="modal-footer">
                <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                <button type="button" class="btn btn-primary save"><?php echo ($trans["save"]) ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="offers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#send_offers" role="tab"><span class="hidden-xs-down"><?= $trans['Sendoffers'] ?> </span></a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#create_offer" role="tab"><span class="hidden-xs-down"><?= $trans['Create_dedicated_offer'] ?></span></a> </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane p-20 active" id="send_offers" role="tabpanel">
                    <div class="modal-body">
                        <div>
                            <h4><?= $trans['ChooseExpert'] ?></h4>
                            <select class="form-control experts1" required>

                            </select>
                        </div>
                        <div>
                            <h4><?= $trans['Choosepackage'] ?></h4>
                            <select class="form-control packages select2" required multiple style="width: 100%">

                            </select>
                        </div>
                        <input id="customer1" value="" hidden>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="button" class="btn btn-primary SendOff"><?= $trans['chat']['send'] ?></button>
                    </div>
                </div>

                <div class="tab-pane p-20 " id="create_offer" role="tabpanel">
                    <form action="" id="myForm" method="POST" class="col-md-12" novalidate>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="title"><?php echo ($trans["admin"]["offers"]["add_offer"]["title_en"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-message-draw"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="title" class="form-control" id="title" required placeholder="<?php echo ($trans["admin"]["offers"]["add_offer"]["title_placeholder_en"]) ?>" required data-validation-required-message="Offer title is required">
                                            <label id="title-error" class="error col-12" for="title"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="title"><?php echo ($trans["admin"]["offers"]["add_offer"]["title_fr"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-message-draw"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="title_fr" class="form-control" id="title_fr" required placeholder="<?php echo ($trans["admin"]["offers"]["add_offer"]["title_placeholder_fr"]) ?>" required data-validation-required-message="Offer title is required">
                                            <label id="title-error" class="error col-12" for="title_fr"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 ">
                                    <div class="form-group ">
                                        <label for="discount"><?php echo ($trans["admin"]["offers"]["add_offer"]["discount"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3 '>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-percent"></span>
                                                </span>
                                            </div>
                                            <input type="number" min="0" max="100" name="discount" class="form-control" id="discount" required placeholder="<?php echo ($trans["admin"]["offers"]["add_offer"]["discount_placeholder"]) ?>" required data-validation-required-message="Offer discount is required">
                                            <label id="discount-error" class="error col-12" for="discount"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="limit"><?php echo ($trans["admin"]["offers"]["add_offer"]["limit"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-unfold-less"></span>
                                                </span>
                                            </div>
                                            <input type="number" name="limit" min="0" class="form-control" id="limit" required value="1" disabled>
                                            <label id="limit-error" class="error col-12" for="limit"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo ($trans["admin"]["offers"]["add_offer"]["date"]) ?></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio1" name="dateType" value="1" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="customRadio1"><?php echo ($trans["admin"]["offers"]["add_offer"]["periodic"]) ?></label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio2" name="dateType" value="2" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadio2"><?php echo ($trans["admin"]["offers"]["add_offer"]["always"]) ?></label>
                                </div>
                            </div>
                            <div class="row dateRangePickerContainer">
                                <div class="col-8">
                                    <label for="dateRange"><?php echo ($trans["admin"]["offers"]["add_offer"]["check_offer"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="ti-calendar"></span>
                                            </span>
                                        </div>
                                        <input type='text' id="dateRange" name="dateRange" class="form-control buttonClass" required />
                                        <label id="dateRange-error" class="error col-12" for="dateRange"></label>
                                    </div>
                                </div>
                                <div class="col-4 p-t-30">
                                    <button class="btn btn-secondary waves-effect waves-light" id="check-offer-effect" type="button">
                                        <span class="btn-label" id="check-span"><i class="mdi mdi-check"></i></span>
                                        <?php echo ($trans["admin"]["offers"]["add_offer"]["check_offer"]) ?></button>
                                </div>
                            </div>
                            <div class="row packagesTableContainer">
                                <div class="col-12">
                                    <div class="table-responsive m-b-40 m-r-0">
                                        <table class="table display dt-responsive" id="packages-dtable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="custom-control custom-checkbox checkbox-info form-check">
                                                            <input class="custom-control-input" name="select_all" value="" id="select-all-packages" type="checkbox" />
                                                            <label class="custom-control-label" for="select-all-packages"></label>
                                                        </div>
                                                    </th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["title"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["messages"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["initial_price"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["offers"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["final_price"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["start_at"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["end_at"]) ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($packages as $package) { ?>
                                                    <tr>
                                                        <td>
                                                            <div class="custom-control custom-checkbox checkbox-info form-check">
                                                                <input class="custom-control-input check-package packages-table" id="pkg-<?= $package['id_package'] ?>" type="checkbox" name="packages_ids[]" value="<?= $package['id_package'] ?>">
                                                                <label class="custom-control-label" for="pkg-<?= $package['id_package'] ?>"><?= $package['id_package'] ?></label>
                                                            </div>
                                                        </td>
                                                        <td><?= $package['title'] ?></td>
                                                        <td><?= $package['messages'] ?> <sup> Messages</sup></td>
                                                        <td><?= $package['price'] ?><sup><?= $package['currency'] ?></sup></td>
                                                        <td><?php echo $package['offers_count'] ?></td>
                                                        <td id="package-<?= $package['id_package'] ?>-price-effect">--</sup></td>
                                                        <td><?= $package['start_date'] ?></td>
                                                        <td><?= $package['end_date'] ?></td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table><label for="packages_ids[]" d="packages_ids[]-error" class="error"></label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="button" name="add-package" class="btn btn-primary addOffer"> <?php echo ($trans["save"]) ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
<footer class="footer">
    <?php echo  $trans["footer"] ?>
</footer> <!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<!-- This is data table -->
<script type="text/javascript" src="../../assets/node_modules/multiselect/js/jquery.multi-select.js"></script>
<script src="../../assets/node_modules/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/pages/chat.js"></script>
<script src="../../assets/js/moment.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="../../assets/js/connection.js"></script>
<script src="../../assets/js/verifyConnection.js"></script>
<script>
    var offers = [];
    var sender = 0;
    var receiver = 0;
	var receiver_role = 4;
    $('.select2').select2();
    $('.conversations-options').perfectScrollbar();
    $("#select-all-packages").change(function() {
        $("input:checkbox.packages-table").prop('checked', $(this).prop("checked"));
    });
    /* $('.buttonClass').daterangepicker({
        drops: "up",
        buttonClasses: "btn",
        applyClass: "btn-info",
        cancelClass: "btn-danger",
        minDate: new Date(),
    }); */
    $('input[type=radio][name=dateType]').change(function() {
        if (this.value == '1') {
            $(".dateRangePickerContainer").css("display", "block");
        } else if (this.value == '2') {
            $(".dateRangePickerContainer").css("display", "none");
        }
    });
    $('#websites').change(function() {
        $this = $(this);
        var customers = jQuery(".customers > li");
        var consultants = jQuery(".consultants > li");
        if ($this.val() == '0') {
            customers.show();
            consultants.show();
        } else {
            customers.each(function() {
                if ($(this).data("website") == $this.val()) {
                    $(this).css("display", "block");
                } else {
                    $(this).css("display", "none");
                }
            });
            consultants.each(function() {
                if ($(this).data("website") == $this.val()) {
                    $(this).css("display", "block");
                } else {
                    $(this).css("display", "none");
                }
            });
        }
    });

    $("#search_bar1").keyup(function() {
        $this = $(this);
        var customers = jQuery(".customers > li");
        customers.each(function() {
            if ($(this).children('div').data("name").toLowerCase().indexOf($this.val()) >= 0) {
                $(this).css("display", "block");
            } else {
                $(this).css("display", "none");
            }
        });
    });
    $("#search_bar").keyup(function() {
        $this = $(this);
        var consultants = jQuery("li.consultants-li");
        consultants.each(function() {
            if ($(this).data("fullName").toLowerCase().indexOf($this.val()) >= 0) {
                $(this).css("display", "block");
            } else {
                $(this).css("display", "none");
            }
        });
    });
    $("#search_barConv1").keyup(function() {
        $this = $(this);
        var consultants = jQuery("tr");
        consultants.each(function() {
            if ($(this).data("consultant").toLowerCase().indexOf($this.val()) >= 0) {
                $(this).css("display", "block");
            } else {
                $(this).css("display", "none");
            }
        });
    });
    $("#search_barConv2").keyup(function() {
        $this = $(this);
        var consultants = jQuery("tr");
        consultants.each(function() {
            if ($(this).data("customer").toLowerCase().indexOf($this.val()) >= 0) {
                $(this).css("display", "block");
            } else {
                $(this).css("display", "none");
            }
        });
    });
    $("#discount").change(function() {

        if ($(this).val() > 0 && $(this).val() <= 100) {
            $("#check-offer-effect").prop("disabled", false);
        }

    });
    $('.back').click(function() {
        $('.conversations-options').show();
        $('.conversations-actions').show();
        $('.customer-options').hide();
        $('.customer-actions').hide();
        $('.back').hide();
    });
    $('.customers').on('click', '.customer', function() {
        $('#overlay').show();
        let id = $(this).data('id');
        receiver = id;
        receiver_role = $(this).data('type');
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                type: 'getInfo',
                id: id,
                profile: 4
            },
            success: function(data) {
                $('#gender').text(data.gender);
                $('#website').text(data.name);
                $('#full_name').text(data.firstname + ' ' + data.lastname);
                $('#first_name').text(data.firstname);
                $('#last_name').text(data.lastname);
                $('#email').text(data.emailc);
                $('#phone').text(data.phone);
                $('#address').text(data.address);
                $('#country').text(data.country);
                $('#language').text(data.lang);
                $('.profile-pic').attr('src', '../uploads/customers/' + data.photo);
                $('.sendOffer').data('id', data.id_user);
                $('.conversations-options').hide();
                $('.conversations-actions').hide();
                $('.customer-options').show();
                $('.customer-actions').show();
                $('.back').show();
                $('#overlay').hide();
            }
        })
    });
    $('.consultants').on('click', '.expert', function() {
        $('#overlay').show();
        let id = $(this).data('id');
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                type: 'getInfo',
                id: id,
                profile: 3
            },
            success: function(data) {
                $('#gender').text(data.gender);
                $('#website').text(data.pseudo);
                $('#full_name').text(data.firstname + ' ' + data.lastname);
                $('#first_name').text(data.firstname);
                $('#last_name').text(data.lastname);
                $('#email').text(data.emailc);
                $('#phone').text(data.phone);
                $('#address').text(data.address);
                $('#country').text(data.country);
                $('#language').text(data.lang);
                $('.profile-pic').attr('src', '../uploads/consultants/' + data.photo);
                $('.conversations-options').hide();
                $('.conversations-actions').hide();
                $('.customer-options').show();
                $('.customer-actions').hide();
                $('.back').show();
                $('#overlay').hide();
            }
        })
    });
    $("#check-offer-effect").click(function() {
        $(this).prop("disabled", "true");
        $(this).text("Checking Offer Effect");

        var startDate = null;
        var endDate = null;
        var dateType = $('input[type=radio][name=dateType]').val();
        if (dateType == 1) {
            var dateRange = $("#dateRange").val();
            startDate = dateRange.split(' - ')[0];
            endDate = dateRange.split(' - ')[1];
        }

        var discount = $("#discount").val();
        var accountId = <?= $id_account ?>;
        $.ajax({
            url: "investigateOffer.php",
            type: "POST",
            data: {
                dateType: dateType,
                startDate: startDate,
                endDate: endDate,
                discount: discount,
                accountId: accountId
            },
            dataType: "json",
            success: function(dataResult) {
                if (dataResult.statusCode == 200) {
                    $.each(dataResult.packages, function(key, value) {
                        var extra_discount = ((value.total_discount == null) ? 0 : parseInt(value.total_discount));
                        var discounted_price = (value.price / 100) * (100 - (parseInt(discount) + extra_discount));
                        $("#package-" + value.id_package + "-price-effect").html(discounted_price.toFixed(2) + '<sup>' + value.currency + '</sup>');
                    });
                } else if (dataResult.statusCode == 201) {}
            }
        });
    });
    $(document).ready(function() {
        $('.conversations-options').show();
        $('.conversations-actions').show();
        $('.customer-options').hide();
        $('.customer-actions').hide();
        $('#overlay ').hide();
        $('.back').hide();
        $("#leads").change(function() {
            var customers = jQuery(".customers > li");
            if (this.checked) {
                $('.lead_1').show();
            } else {
                $('.lead_1').hide();
            }
        });
        $("#customers").change(function() {
            var customers = jQuery(".customers > li");
            if (this.checked) {
                $('.lead_0').show();

            } else {
                $('.lead_0').hide();
            }
        });

        $("#guests").change(function() {
            var customers = jQuery(".customers > li");
            if (this.checked) {
                $('.customer[data-type="7"]').show();

            } else {
                $('.customer[data-type="7"]').hide();
            }
        });
        addListner(conn);
        $("#search_bar").keyup(function() {
            $this = $(this);
            var consultants = jQuery(".consultants li");
            consultants.each(function() {
                let div = this.children;
                if ($(div).data("name").toLowerCase().indexOf($this.val()) >= 0) {
                    $(this).css("display", "block");
                } else {
                    $(this).css("display", "none");
                }
            });
        });
        $("#search_bar_customers").keyup(function() {
            $this = $(this);
            var customers = jQuery(".customers li");
            customers.each(function() {
                let div = this.children;
                if ($(div).data("name").toLowerCase().indexOf($this.val()) >= 0) {
                    $(this).css("display", "block");
                } else {
                    $(this).css("display", "none");
                }
            });
        });
        $("#converations").on('click', '.ressign', function() {
            $('#overlay').show();
            let customer = $(this).data('id');
            let consultant = $(this).data('expert');
            $('.experts').empty();
            $('#customer').val(customer);
            $('#expert').val(consultant);
            $.each($('.consultants .itemConsultant'), function() {
                if ($(this).children('div').data('id') != consultant) {
                    $('.experts').append('<option value="' + $(this).children('div').data('id') + '">' + $(this).children('div').data('name') + '</option>')
                }
            });
            $('#experts').modal('show');
            $('#overlay').hide();
        });
        $('.save').click(function() {
            $('#overlay').show();
            sendMessage({
                command: "reassign",
                customer: $('#customer').val(),
                oldExpert: $('#expert').val(),
                newExpert: $('.experts option:selected').val(),
                pseudo: $('.consultant_' + $('.experts option:selected').val()).data('name'),
                id_group: <?= $_SESSION['id_company'] ?>
            });
            $('#' + $('#customer').val() + '-' + $('#expert').val()).remove();
            sendMessage({
                command: "message",
                msg: "Welcome, I'm your new expert.",
                sender: $('.experts option:selected').val(),
                sender_role: 3,
                receiver: $('#customer').val(),
                receiver_role: 4,
                account: <?= $_SESSION['id_company'] ?>,
                id_group: <?= $_SESSION['id_company'] ?>
            });
            $('#experts').modal('hide');
            $('#overlay').hide();
            Swal.fire({
                type: 'success',
                title: 'Re-assigned successfully ',
                showCancelButton: true
            })
        });
        $('.addOffer').click(function() {
            $('#overlay').show();
            let form = $('#myForm').serializeArray();
            let id = $('#customer1').val();
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    type: 'SetOffer',
                    form: form,
                    id_account: <?= $id_account ?>,
                    user_id: id
                },
                success: function(data) {
                    if (data == 1) {
                        Swal.fire({
                            type: 'success',
                            title: 'Offer added successfully !'
                        })
                    } else if (data == 0) {
                        Swal.fire({
                            type: 'error',
                            title: 'Offer has not added!'
                        })
                    }
                    $('#overlay').hide();
                    $('#offers').modal('hide');
                }
            })
        });
        $('.customer-actions').on('click', '.sendOffer', function() {
            $('#overlay').show();
            $('#customer1').val($(this).data('id'));
            $('.experts1').empty();
            $.each($('.consultants .itemConsultant'), function() {
                $('.experts1').append('<option value="' + $(this).children('div').data('id') + '">' + $(this).children('div').data('name') + '</option>')
            });
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    type: 'getOffers',
				    receiver_role: receiver_role,
                    id: $(this).data('id')
                },
                success: function(data) {
                    offers = data.offers;
                    $('.packages').empty();
                    $.each(data.packages, function(k, v) {
                        $('.packages').append(this);
                    });
                    $('#offers').modal('show');
                    $('#overlay').hide();
                }
            })
        });
        $('.SendOff').click(function() {
            let msg = '';
		    let msg1 = '';
            $('#overlay').show();
            if ($('.packages').val().length > 0 && $('.experts1').val() != null) {
                $.each($('.packages').val(), function() {
                    let offer = offers[this];
                    if (offer.total_discount < 100) {
                        let finalPrice = (100 - offer.total_discount) * (offer.price / 100);
                        if (offer.total_discount > 0) {
                            msg1 += '<div class="col-lg-3 col-md-12"><div class="price-table"><div class="price-header"><div class="shape-box"><div class="shape-bg"><h5 class="disc"> -' + offer.total_discount + '%</h5></div></div><div class="price-value"><h2><span>€</span>' + finalPrice.toFixed(2) + '</h2><span>' + offer.title + '</span></div><h3 class="price-title">' + offer.messages + '<span style="font-size: 12px;">Messages</span></h3><a class="buy-cred btn-1 btn-theme btn-circle my-3 content-card" data-idagent="' + sender + '" data-id = "' + offer.id_package + '"  data-price="' + finalPrice.toFixed(2) + '" data-text="Purchase Now" ><?= ($trans["package_card"]["buy_now"]) ?></a></div></div></div>'
                        } else {
                            msg1 += '<div class="col-lg-3 col-md-12"><div class="price-table"><div class="price-header"><div class="price-value"><h2><span>€</span>' + finalPrice.toFixed(2) + '</h2><span>' + offer.title + '</span></div><h3 class="price-title">' + offer.messages + '<span style="font-size: 12px;">Messages</span></h3><a class="buy-cred btn-1 btn-theme btn-circle my-3 content-card"  data-id = "' + offer.id_package + '" data-idagent ="' + sender + '" data-price="' + finalPrice.toFixed(2) + '" data-text="Purchase Now" onclick="paiment(this)"><?= ($trans["package_card"]["buy_now"]) ?></a></div></div></div>'
                        }
                        msg = '<div class="container pricing-tab"><div class="row pricing-card">' + msg1 + '</div></div>';
                    }/* 
                    sendMessage({
                        command: "message",
                        msg: msg,
                        sender: $('.experts1 option:selected').val(),
                        sender_role: 3,
                        receiver: $('#customer1').val(),
                        receiver_role: 4,
                        account: <?= $_SESSION['id_company'] ?>,
                        id_group: <?= $_SESSION['id_company'] ?>,
                        admin: 2
                    }); */
                });
                $('#offers').modal('hide');
                sendMessage({
                    command: "message",
                    msg: msg,
                    sender: $('.experts1 option:selected').val(),
                    sender_role: 3,
                    receiver: receiver,
                    receiver_role: receiver_role,
                    account: <?= $_SESSION['id_company'] ?>,
                    id_group: <?= $_SESSION['id_company'] ?>,
                });
                sendMessage({
                    command: "message",
                    msg: '<?= isset($cont->agent_pricing) ? $cont->agent_pricing : '' ?>',
                    sender: $('.experts1 option:selected').val(),
                    sender_role: 3,
                    receiver: receiver,
                    receiver_role: receiver_role,
                    account: <?= $_SESSION['id_company'] ?>,
                    id_group: <?= $_SESSION['id_company'] ?>,
                });
                $('#overlay').hide();
                Swal.fire({
                    type: 'success',
                    title: 'Offers sended successfully '
                })
            }else{
                Swal.fire({
                    type: 'error',
                    title: 'Empty fields detected !'
                })
            }
        });
    });

    function sendMessage(message) {
        if (conn.readyState == WebSocket.OPEN) {
            conn.send(JSON.stringify(message));
        } else {
            Swal.fire({
                type: 'error',
                title: 'Reconnecting ...',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            conn = null;
            conn = new WebSocket(wsCurEnv);
            conn.onopen = function(e) {
                conn.send(JSON.stringify({
                    command: "attachAccount",
                    account: id_user,
                    id_group: id_company,
                    role: 2
                }));
            };
            addListner(conn);
        }
    }
    
    function addListner(conn) {
        conn.onmessage = async function(e) {
            var dt = jQuery.parseJSON(e.data);
            if (dt.status == 200) {
                if (dt.action == "notification") {
                    if (dt.type == 1) {
                        $('#notifications').prepend(`<a href="javascript:void(0)"> <div class="btn btn-danger btn-circle"><i class="mdi mdi-exclamation"></i></div><div class="mail-contnet"><h5><?= $trans['complaint'] ?></h5> <span class="mail-desc">${dt.receiver_name} <?= $trans['reportedBy'] ?> ${dt.sender_name}</span> <span class="time">${dt.date}</span></div><div class="notify"> <span class="point"></span> </div></a>`);
                    } else if (dt.type == 2) {
                        $('#notifications').prepend(`<a href="javascript:void(0)"> <div class="btn btn-danger btn-circle"><i class="mdi mdi-exclamation"></i></div><div class="mail-contnet"><h5>Late response</h5> <span class="mail-desc">${dt.sender_name} id wainting for ${dt.receiver_name}</span> <span class="time">${dt.date}</span></div></a><div class="notify"> <span class="point"></span> </div>`);
                        $("#notify_" + dt.sender).css("display", "block");
                        $("#point_" + dt.sender).css("display", "block");
                    }
                    $(".notify").css("display", "block");
                    $(".heartbit").css("display", "block");
                    $(".point").css("display", "block");
                }
                if (dt.action == "newConnection") {
                    await $.ajax({
                            url: 'functions_ajax.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                type: 'getId',
                                id: dt.id_user,
                                role: dt.role
                            },
                            success: function(data) {
                                if (dt.role == 3 && !$('.consultant_' + dt.id_user).length) {
                                    $('.consultants').append(data);
                                    $('.coTitle').text(parseInt($('.coTitle').text()) + 1);
                                }else if((dt.role == 4||dt.role == 7) && !$('#' + dt.id_user).length){
                                    $('.customers').append(data);
                                    $('.cuTitle').text(parseInt($('.cuTitle').text()) + 1);
                                }
                            }
                        })

                } else if (dt.action == "closedConnection") {
                    $('.customer-' + dt.id_user).remove();
                    $('.consultant-' + dt.id_user).remove();
                    $('#' + dt.id_user).remove();
                    $('.chatTitle').text($('#converations >  tr').length);
                    $('.cuTitle').text($('.customers > li').length);
                    $('.coTitle').text($('.consultants > li').length);
                } else if (dt.action == "conversationStatus" || dt.action == "writing" || dt.action == "newMessage") {
                    if (dt.action == "newMessage" && $('.balance-' + dt.sender).text() != 0) $('.balance-' + dt.sender).text(parseInt($('.balance-' + dt.sender).text()) - 1);
                    if ($('#' + dt.sender).length && $('#' + dt.receiver).length) {
                        let customer_msg = '';
                        let consultant_msg = '';
                        let text = '';
                        if (dt.action == "newMessage") {
                            text = dt.message.substring(0, 4);
                        }
                        if ($('.consultant_' + dt.sender).length) {
                            consultant_id = $('.consultant_' + dt.sender).data('id');
                            consultant_name = $('.consultant_' + dt.sender).data('name');
                            consultant_avatar = $('.consultant_' + dt.sender).data('avatar');
                            customer_id = $('.item_' + dt.receiver).data('id');
                            customer_name = $('.item_' + dt.receiver).data('name');
                            customer_avatar = $('.item_' + dt.receiver).data('avatar');
                            consultant_msg = dt.message ? dt.message : '';
                            if (text == '<div') {
                                consultant_msg = "";
                            }
                        } else {
                            consultant_id = $('.consultant_' + dt.receiver).data('id');
                            consultant_name = $('.consultant_' + dt.receiver).data('name');
                            consultant_avatar = $('.consultant_' + dt.receiver).data('avatar');
                            customer_id = $('.item_' + dt.sender).data('id');
                            customer_name = $('.item_' + dt.sender).data('name');
                            customer_avatar = $('.item_' + dt.sender).data('avatar');
                            customer_msg = dt.message ? dt.message : '';
                            if (text == '<div') {
                                customer_msg = "";
                            }
                        }
                        if ($('#' + customer_id + '-' + consultant_id).length == 1 && dt.action == "newMessage" && text != '<div') {
                            $('#' + customer_id + '-' + consultant_id).find('.last-' + consultant_id).text(consultant_msg);
                            $('#' + customer_id + '-' + consultant_id).find('.last-' + customer_id).text(customer_msg);
                        } else if ($('#' + customer_id + '-' + consultant_id).length == 0) {
                            if ($('.consultant-' + consultant_id).length > 0) {
                                $('<tr class="unread menu row customer-' + customer_id + ' consultant-' + consultant_id + '" data-customer="' + customer_name + '" data-consultant="' + consultant_name + '" id="' + customer_id + '-' + consultant_id + '"><td class="col-6"><span><img src="../uploads/consultants/' + consultant_avatar + '" alt="user-img" class="img-circle " style="float:left"><span>' + consultant_name + '<small style="display:block" class="msg last-' + consultant_id + '">' + consultant_msg + '</small></span></span></td><td class="col-6"><img src="../uploads/customers/' + customer_avatar + '" alt="user-img" class="img-circle " style="float:left"><span>' + customer_name + ':'+customer_id+'<small style="display:block" class="msg last-' + customer_id + '">' + customer_msg + '</small></span></td><td><button class="btn btn-sm btn-danger" onClick="MyWindow=window.open(' + "'" + 'superConversation.php?sender=' + customer_id + '&receiver=' + consultant_id + '' + "'" + ",'" + customer_id + "-" + consultant_id + "','width=800,height=500'); return false;" + '"' + 'href="#" class="dropdown-item"><i class="mdi mdi-eye"></i> Rejoindre</button> <button class="btn btn-sm btn-danger ressign" href="#" class="ressign dropdown-item" data-id="' + customer_id + '" data-expert="' + consultant_id + '"><i class="icon-shuffle" title="Re-assign"></i> Re-assign</button></td></tr>').insertAfter('.consultant-' + consultant_id);
                            } else {
                                $('#'+customer_id+'-0').remove();
                                $('#converations').append('<tr class="unread menu row customer-' + customer_id + ' consultant-' + consultant_id + '" data-customer="' + customer_name + '" data-consultant="' + consultant_name + '" id="' + customer_id + '-' + consultant_id + '"><td class="col-6"><span><img src="../uploads/consultants/' + consultant_avatar + '" alt="user-img" class="img-circle " style="float:left"><span>' + consultant_name + '<small style="display:block" class="msg last-' + consultant_id + '">' + consultant_msg + '</small></span></span></td><td class="col-6"><img src="../uploads/customers/' + customer_avatar + '" alt="user-img" class="img-circle " style="float:left"><span>' + customer_name + '<small style="display:block" class="msg last-' + customer_id + '">' + customer_msg + '</small></span></td><td><button class="btn btn-sm btn-danger" onClick="MyWindow=window.open(' + "'" + 'superConversation.php?sender=' + customer_id + '&receiver=' + consultant_id + '' + "'" + ",'" + customer_id + "-" + consultant_id + "','width=800,height=500'); return false;" + '"' + 'href="#" class="dropdown-item"><i class="mdi mdi-eye"></i> Rejoindre</button> <button class="btn btn-sm btn-danger ressign" href="#" class="ressign dropdown-item" data-id="' + customer_id + '" data-expert="' + consultant_id + '"><i class="icon-shuffle" title="Re-assign"></i> Re-assign</button></td></tr>');
                            }
                        }
                        $('.chatTitle').text($('#converations > tr').length);
                    }
                } else if (dt.action == "stopWriting") {} else if (dt.action == "connected") {
                    $.ajax({
                        url: 'functions_ajax.php',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            type: 'getConnected',
                            agents: dt.agents,
                            users: dt.users,
                            guests: dt.guests,
                        },
                        success: function(data) {
                            $.each(data.consultants, function(k, v) {
                                $('.consultants').append(v);
                            });
                            $.each(data.customers, function(k, v) {
                                $('.customers').append(v);
                            });
                            $('.cuTitle').text(data.customers.length);
                            $('.coTitle').text(Object.keys(data.consultants).length);
                            $('.chatTitle').text($('#converations > tr').length);
                        }
                    })
                    swal.close();
                    $("#overlay").hide();
                }else if (dt.action == "buying") {
                    if (dt.is_buying == 1) {
                        $('.item_' + dt.sender + ' .buying').removeClass('d-none');						
                    }else{
                        $('.item_' + dt.sender + ' .buying').addClass('d-none');
                    }
                } 
            }
        }
    }
</script>
</body>
</html>