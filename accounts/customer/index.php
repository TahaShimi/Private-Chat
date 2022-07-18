<?php
$page_name = "chat_room";
include('header.php');
$stmt = $conn->prepare("SELECT a.*, b.*, c.`name` AS website_name,c.rights,c.max_size,c.max_time FROM `users` a LEFT JOIN `customers` b ON a.`id_profile` = b.`id_customer` LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE a.`id_user` = :ID");
$stmt->bindParam(':ID', $id_user, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchObject();
$rights = json_decode($result->rights);
$id_website = intval($result->id_website);
$max_time = intval($result->max_time);

$stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM messages where u.id_user = sender and status=0) as unread_messages_count from consultants c join users u on u.id_profile=c.id_consultant where c.id_account=:ia and (find_in_set(:idw,c.websites) or c.websites is null) and u.profile=3");
$stmt->bindparam(":ia",  $_SESSION['id_company']);
$stmt->bindparam(":idw",  $id_website);
$stmt->execute();
$consultants = $stmt->fetchAll(PDO::FETCH_OBJ);
$stmt = $conn->prepare("SELECT a.welcome_text as en,t.content as fr FROM accounts a,translations t WHERE t.id_element=:ia AND t.table='account' AND t.column='welcome_text' AND id_account=:ia");
$stmt->bindparam(":ia",  $_SESSION['id_company']);
$stmt->execute();
$welcome = $stmt->fetch();
$packages = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount, (SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count from packages p JOIN packages_price pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang where pp.currency=:cur AND p.status=1 and (p.id_website is null or p.id_website=:IDW) ");
$packages->bindParam(':IDW', $id_website, PDO::PARAM_INT);
$packages->bindParam(':IDC', $id_account, PDO::PARAM_INT);
$packages->bindParam(':cur', $trans['devise'][$_SESSION['country']], PDO::PARAM_STR);
$packages->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
$packages->execute();
$pricings_rows = $packages->rowCount();
$pricings = $packages->fetchAll();
?>
<link href='https://fonts.googleapis.com/css?family=Merriweather:300italic' rel='stylesheet' type='text/css'>
<link href="../../assets/css/pages/user-card.css" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/jquery.emojipicker.tw.css">
<link rel="stylesheet" href="../../assets/css/jquery.emojipicker.css">
<style>
    .shape-box {
        position: relative;
        top: 0;
        right: 0;
        width: 0;
        height: 200px;
        z-index: 2;
        float: right;
        margin-right: -30px;
        margin-top: -60px;
    }

    .shape-box .shape-bg {
        background-image: url(../uploads/shape.png);
        position: relative;
        top: 0;
        right: 0;
        width: 100px;
        height: 110px;
        float: right;
    }

    .shape-box .disc {
        position: relative;
        top: 24px;
        right: -22px;
        color: #fafaff !important;
        font-size: 23px;
        line-height: 30px;
        font-weight: 600;
        transform: rotate(45deg);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pricing-tab {
        margin: auto;
        padding: 0 20px;
    }

    .col-lg-3 {
        max-width: 100% !important;
    }

    .btn-1 {
        padding: 12px 30px;
        font-weight: 500;
        background: none;
        color: #1c1d3e;
        overflow: hidden;
        border-radius: 7px;
        border: none;
        position: relative;
        z-index: 9;
    }

    .btn-1:hover {
        background: #2575fc;
        color: #fff;
        transform: translateY(-3px);
    }

    .btn-1.btn-theme {
        background: rgb(0, 91, 234);
        background: linear-gradient(90deg, rgba(0, 91, 234, 1) 0%, rgba(37, 117, 252, 1) 80%);
        color: #ffffff;
    }

    .btn-1.btn-circle {
        border-radius: 30px;
        width: auto !important;
    }

    .price-table {
        padding: 50px 30px;
        border-radius: 7px;
        overflow: hidden;
        position: relative;
        background: #ffffff;
        text-align: center;
        width: 30vh;
    }

    .price-title {
        text-transform: uppercase;
        font-weight: 700;
        color: #2575fc;
        margin-bottom: 30px;
    }

    .price-header {
        position: relative;
        z-index: 9;
    }

    .price-value {
        display: inline-block;
        width: 100%;
    }

    .price-value h2 {
        font-size: 60px;
        line-height: 40px;
        font-weight: 400;
        color: #1c1d3e;
        margin-bottom: 0;
        position: relative;
        display: inline-block;
    }

    .price-value h2 span {
        font-size: 33px;
        left: -27px;
        line-height: 24px;
        margin: 0;
        position: absolute;
        top: -7px;
        color: #5f5f5f;
        font-weight: normal;
    }

    .price-value span {
        margin: 15px 0;
        display: block;
    }

    .price-inside {
        font-size: 80px;
        line-height: 80px;
        position: absolute;
        left: 85%;
        top: 50%;
        transform: translateX(-50%) translateY(-50%) rotate(-90deg);
        font-weight: 900;
        color: rgba(0, 0, 0, 0.040);
    }

    .price-table::before {
        background: #fafaff;
        content: "";
        height: 300px;
        left: -25%;
        position: absolute;
        top: -10%;
        transform: rotate(-10deg);
        width: 150%;
    }

    .price-table.active::before {
        transform: rotate(10deg);
    }

    @media (max-width: 767px) {
        .chat-main-box .chat-left-aside {
            left: -93%;
            width: 93%;
        }

        #chat-welcome>div {
            display: none;
        }

    }

    .pricing-body {
        padding: 30px 30px;
    }

    .emojioneemoji {
        height: 20px;
        width: 20px;
    }

    #content_watcher_container {
        position: absolute;
        right: 16px;
        width: 150px;
    }

    .admin {
        background: #818182;
        color: white;
        display: inline-block;
        padding: 8px;
        margin-bottom: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .1);
        border-radius: .8rem .8rem .8rem 0;
    }

    ul#themecolors li a {
        color: white !important;
    }

    .buy-package {
        display: none;
        position: absolute;
        right: 10px;
    }

    .packages-modal-desc {
        font-weight: 500;
        font-size: 12px;
    }

    .writing_status_v2 {
        font-size: 11px;
        font-weight: 500;
        color: green;
        margin-left: 10px;
    }

    #pForm {
        display: none;
    }

    .buy-offer {
        background-color: #ff365b;
        border-color: #ff365b;
        min-width: 140px;
    }

    .info {
        margin-top: 21px;
    }

    .price-lable {
        background-color: #f8d7da !important;
        top: -50px;
        color: #721c24;
        font-weight: 500;
        opacity: 80%
    }

    ._3z_5 {
        background-color: #fa3e3e;
        border-radius: 2px;
        color: #fff;
        padding: 1px 3px;
    }

    ._51lp {
        margin-left: -21px;
        margin-right: 5px;
        background-clip: padding-box;
        display: inline-block;
        font-family: 'helvetica neue', Helvetica, Arial, sans-serif;
        font-size: 10px;
        -webkit-font-smoothing: subpixel-antialiased;
        line-height: 1.3;
        min-height: 13px;
        color:
            white !important;
    }

    #loading {
        position: absolute;
        left: 50%;
        text-align: center;
        z-index: 1000;
        width: 250px;
    }

    ul#themecolors li a {
        width: 30px !important;
        height: 30px !important;
    }

    .shw-rside {
        right: 10px;
    }

    [data-title]:hover:after {
        opacity: 1;
        visibility: visible;
    }

    [data-title]:after {
        content: attr(data-title);
        background-color: #ff7f80;
        color: white;
        font-size: 100%;
        border-radius: 10px;
        position: absolute;
        padding: 2px 10px 2px 10px;
        bottom: 24px;
        right: 73px;
        visibility: hidden;
    }

    [data-title] {
        position: relative;
    }

    .el-element-overlay {
        margin: -10px;
        padding: 10px 10px 10px 5px;
        background-color: #dee4ea;
        border-radius: .8rem .8rem .8rem 0;
    }

    .el-element-overlay .el-card-item {
        padding-bottom: 0 !important;
    }

    .el-overlay-1 {
        border-radius: 10px;

    }

    .el-overlay a {
        border-radius: 10px;
        border-color: #fff;
        color: #fff;
        padding: 12px 12px 10px;
        float: left;
        width: 100%;
    }

    .el-overlay-1>.user {
        height: 200px !important;
        width: 200px !important;
        border-radius: 10px;
    }

    .right-sidebar {
        width: 300px;
    }

    #opt {
        position: absolute;
        right: 30px;
        z-index: 1000;
        top: 100%
    }

    #opt ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #opt ul li {
        margin: 10px;
        cursor: pointer;
        background-color: #ff7f80;
        color: white;
        min-height: 50px;
        min-width: 50px;
        border-radius: 100px;
        text-align: center;
        padding-top: 15px;
    }

    .consultantslist li {
        border-bottom: 1px solid #eaeaea;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card ">
            <!-- .chat-row -->
            <div class="chat-main-box">
                <!-- .chat-left-panel -->
                <div class="chat-left-aside open-pnl">
                    <div class="open-panel"><i class="mdi mdi-chevron-right"></i></div>
                    <div class="chat-left-inner">
                        <div class="form-material">
                            <input class="form-control p-2" type="text" placeholder="<?php echo ($trans["chat"]["search_consultant"]) ?>" id="search_bar">
                        </div>
                        <ul class="chatonline style-none ">
                            <div class="consultantslist">
                                <?php
                                foreach ($consultants as $consultant) {
                                    $stmt2 =  $conn->prepare("SELECT Count(*) as total_unread_messages from messages where receiver=:id and sender=:ir and status=0");
                                    $stmt2->bindparam(":id", $_SESSION['id_user'], PDO::PARAM_INT);
                                    $stmt2->bindparam(":ir", $consultant->id_user, PDO::PARAM_INT);
                                    $stmt2->execute();
                                    $dta = $stmt2->fetch();
                                ?>
                                    <li class="consultant-li-<?= $consultant->id_user ?>" data-full-name="<?php echo $consultant->pseudo; ?>">
                                        <a href="javascript:void(0)" data-avatar="<?= $consultant->photo ?>" data-unread-message-count="<?= $consultant->unread_messages_count ?>" data-id="<?= $consultant->id_user ?>" data-full-name="<?php echo $consultant->pseudo; ?>" data-avatar="<?= $consultant->photo ?>" class="chat_item consultants-items">
                                            <img src="<?php echo '../uploads/consultants/' . $consultant->photo ?>" alt="user-img" class="img-circle">
                                            <?php if ($dta[0] != "0") { ?>
                                                <span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $consultant->id_user ?>"><?php echo $dta[0] ?></span>
                                            <?php } else { ?>
                                                <span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $consultant->id_user ?>" style="display: none">0</span>
                                            <?php } ?>
                                            <span><?php echo $consultant->pseudo ?>
                                                <small class="text-danger" id="consultant-status-<?= $consultant->id_user ?>"><?php echo ($trans["offline"]) ?></small>
                                                <div class="notify" id="new-message-<?= $consultant->id_user ?>">
                                                    <span class="heartbit" id="flushing-message-<?= $consultant->id_user ?>"></span>
                                                    <span class="point hide" id="stable-message-<?= $consultant->id_user ?>"></span>
                                                </div>
                                            </span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </div>
                            <li class="p-20"></li>
                        </ul>
                    </div>
                </div>
                <!-- .chat-left-panel -->
                <!-- .chat-right-panel -->
                <div class="chat-right-aside">
                    <div id="chat-welcome" class="fto">
                        <div>
                            <h1 class="fto-r1"><?= $welcome[$_COOKIE['lang']] ? $welcome[$_COOKIE['lang']] : ($trans["msg_welcome"]) ?></h1>
                            <h1 class="fto-r2"> ~~</h1>
                            <h1 class="fto-r3"><?php echo ($trans["msg_welcome_desc"]) ?></h1>
                        </div>
                    </div>
                    <div id="chat-block" class="hide">
                        <div class="chat-main-header">
                            <div class="p-3 b-b" style="display: inline-block;">
                                <h4 class="box-title"></h4>
                                <small class="consultant-status"></small>
                            </div>
                            <?php if ($rights[0] == 1) { ?>
                                <label for="more" style="float: right; cursor: pointer;margin:6px">
                                    <i class="mdi mdi-information-outline nav-item right-side-toggle" id="more" style="font-size: 30px;display: block;"></i>
                                </label>
                            <?php } ?>
                            <div class="col-md-2 align-self-center text-right">
                            </div>
                        </div>
                        <div class="chat-texture">
                            <span id="loading" style="display: none">chargement...</span>
                            <div style="width: 100%;background-color: white;" class="row m-0" id="searchBar">
                                <div class="col-2 d-flex" style="justify-content: flex-end;align-items: center;">
                                    <span class="m-r-5"><span class="current">0</span>/<span class="total">0</span></span>
                                    <span><i class=" icon-arrow-up m-r-5 before" style="cursor: pointer;"></i><i class="icon-arrow-down after" style="cursor: pointer;"></i></span>
                                </div>
                                <div class="col-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search" id="SearchVal">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><a href="javascript:void(0)" id="search"><i class="mdi mdi-search"></i></a></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 d-flex" style="align-items: center;">
                                    <span><i class="mdi mdi-close search-toggle"></i></span>
                                </div>
                            </div>
                            <div class="chat-rbox">
                                <div class="right-sidebar">
                                    <div class="slimscrollright">
                                        <div class="rpanel-title"> Actions <span><i class="mdi mdi-close right-side-toggle"></i></span> </div>
                                        <div class="r-panel-body">
                                            <ul id="chatonline">
                                                <li><b><?= $trans['Others'] ?></b></li>
                                                <li>
                                                    <a href="javascript:void(0)" class="searchInConv row" style="color: black;align-items: center;">
                                                        <span class="col-8"><?= $trans['search_in_conversation'] ?></span>
                                                        <span class="col-4" style="padding-right:0">
                                                            <i class="mdi mdi-search float-right" style="border-radius:100%;padding:10px;font-size:15px"></i>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="report row" style="color: black;align-items: center;" data-toggle="modal" data-target="#reportModal">
                                                        <span class="col-8"><?= $trans['ReportExpert'] ?></span>
                                                        <span class="col-4" style="padding-right:0">
                                                            <i class="mdi mdi-alert-circle-outline float-right" style="border-radius:100%;padding:0px 7px;font-size:20px"></i>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <hr>
                                            <div class="m-t-15 picCollapse" style="cursor: pointer;"><span><?= $trans['SharedPictures'] ?><i class=" icon-arrow-down switch" style="cursor: pointer;float:right"></i><i class=" icon-arrow-up switch" style="cursor: pointer;float:right;display:none"></i></span></div>
                                            <div id="themecolors" class="m-t-20 " style="flex-flow: row wrap;display:none">
                                                <div style="flex-flow: row wrap;display:flex" class="pictures">

                                                </div>
                                            </div>
                                            <hr>
                                            <div class="m-t-15  fileCollapse" style="cursor: pointer;"><span><?= $trans['SharedFiles'] ?><i class=" icon-arrow-down switch1" style="float:right"></i><i class=" icon-arrow-up switch1" style="cursor: pointer;float:right;display:none"></i></span></div>
                                            <div class="m-t-20 chatonline files" style="display:none;list-style:none">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="chat-list p-3 chat_container">
                                    <!--chat Row -->
                                </ul>
                            </div>
                            <?php foreach ($consultants as $consultant) { ?>
                                <div class="bloc-bottom conversation-status-container conversation-status-container-<?= $consultant->id_user ?>">
                                    <div class="row conversation-status-<?= $consultant->id_user ?>">

                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="card-body border-top bloc-bottom response-container">
                            <div class="row">
                                <div class="col-9 py-3">

                                    <textarea <?php if ($company->max_length > 0) {
                                                    echo 'maxlength="' . $company->max_length . '"';
                                                } ?> placeholder="<?php echo ($trans["chat"]["select_a_consultant_to_enable_typing"]) ?>" class="form-control border-0 emojiable-option" id="content"></textarea>
                                </div>
                                <div class="col-3 text-right">
                                    <?php if ($company->max_length > 0) { ?>
                                        <small id="content_watcher_container"><?php echo ($trans["chat"]["remain"]) ?> <span id="content_watcher">0</span>/ <?= $company->max_length ?> <?php echo ($trans["chat"]["characters"]) ?></small>
                                    <?php } ?>
                                    <button type="button" id="send_message" disabled class="btn btn-primary btn-sm m-t-30"><i class="mdi mdi-send"></i> <?php echo ($trans["chat"]["send"]) ?> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- .chat-right-panel -->
                <div id="overlay">
                    <h4 style="position: absolute;" id="progress"></h4>
                    <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
                </div>
            </div>
            <!-- /.chat-row -->
        </div>
    </div>
</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
<div class="modal" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title text-center"><?= $trans['ReportExpert'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" class="col-md-12">
                    <div class="form-body">
                        <div class="col-12 Reason m-b-20">
                            <label for="Reason"><?= $trans['Reason'] ?></label>
                            <select name="reason" id="Reason" class="form-control">
                                <?php foreach ($trans['reasons']['4'] as $key => $reason) {
                                    echo '<option value="' . $key . '">' . $reason . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label for="message"><?= $trans['description'] ?></label>
                            <textarea class="form-control" id="message" name="message" placeholder="Write your message here"></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary waves-effect waves-light m-r-10 reportSend"> <i class="mdi mdi-check"></i> <?php echo ($trans["chat"]["send"]) ?></button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo ($trans["chat"]["shopping_modal"]["close"]) ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- <div class="modal" id="buy-package-main-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title text-center"><?php echo ($trans["chat"]["shopping_modal"]["title"]) ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="m-t-15 m-b-25 packages-modal-desc"><?php echo ($trans["chat"]["shopping_modal"]["subtitle"]) ?> </p>
                <div class="row packagesTableContainer">
                    <div class="col-12">
                        <div class="table-responsive m-b-40 m-r-0">
                            <table class="table display " id="packages-dtable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><?php echo ($trans["chat"]["shopping_modal"]["packages_table"]["package_name"]) ?></th>
                                        <th><?php echo ($trans["chat"]["shopping_modal"]["packages_table"]["messages"]) ?></th>
                                        <th><?php echo ($trans["chat"]["shopping_modal"]["packages_table"]["discount"]) ?></th>
                                        <th><?php echo ($trans["chat"]["shopping_modal"]["packages_table"]["price"]) ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pricings as $pricing) {
                                        if ($pricing['total_discount'] == 100) {
                                            $stmt = $conn->prepare("SELECT *, (SELECT count(*) from offers_customers oc where oc.id_offer=o.id_offer and oc.id_customer=:idc) as pick_count from offers o where o.id_package=:idp and  o.discount=100");
                                            $stmt->bindParam(':idp', $pricing["id_package"], PDO::PARAM_INT);
                                            $stmt->bindParam(':idc', $id_account, PDO::PARAM_INT);
                                            $stmt->execute();
                                            $rs = $stmt->fetchObject();
                                            if ($rs->limit > $rs->pick_count) { ?>
                                                <tr class="package-modal-items" data-package-id="<?= $pricing['id_package'] ?>">
                                                    <td><?= $pricing["title"] ?></td>
                                                    <td><?= $pricing['messages'] ?> </td>
                                                    <td><?php if ($pricing['total_discount'] == 0) {
                                                            echo 0;
                                                        } else {
                                                            echo $pricing['total_discount'];
                                                        } ?>%</td>
                                                    <td><?= $pricing['price'] ?><sup><?= $pricing['currency'] ?></sup></td>
                                                    <td>
                                                        <button type="button" data-package-id="<?= $pricing['id_package'] ?>" id="buy-package-<?= $pricing['id_package'] ?>" class="btn waves-effect btn-sm waves-light btn-success buy-package"><?php echo ($trans["chat"]["shopping_modal"]["packages_table"]["use"]) ?></button>
                                                    </td>
                                                </tr>
                                            <?php }
                                        }
                                        if ($pricing['total_discount'] < 100) { ?>
                                            <tr class="package-modal-items" data-package-id="<?= $pricing['id_package'] ?>">
                                                <td><?= $pricing["title"] ?></td>
                                                <td><?= $pricing['messages'] ?> </td>
                                                <td><?php if ($pricing['total_discount'] == 0) {
                                                        echo 0;
                                                    } else {
                                                        echo $pricing['total_discount'];
                                                    } ?>%</td>
                                                <td><?= $pricing['price'] ?><sup><?= $pricing['currency'] ?></sup></td>

                                                <td>
                                                    <button type="button" data-package-id="<?= $pricing['id_package'] ?>" id="buy-package-<?= $pricing['id_package'] ?>" class="btn waves-effect btn-sm waves-light btn-success buy-package"><?php echo ($trans["chat"]["shopping_modal"]["packages_table"]["continue"]) ?></button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <label for="packages_ids[]" d="packages_ids[]-error" class="error"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo ($trans["chat"]["shopping_modal"]["close"]) ?></button>
            </div>
        </div>
    </div>
</div> -->

<form method="post" id="pForm" action="https://secure-payment.pro/index_v2.php">
    <input type="hidden" name="id_company" value="" />
    <input type="hidden" name="id_shop" value="" />
    <input type="hidden" name="amount" value="">
    <input type="hidden" name="currency" value="" />
    <input type="hidden" name="country" value="">
    <input type="hidden" name="last_name" value="">
    <input type="hidden" name="first_name" value="">
    <input type="hidden" name="email" value="">
    <input type="hidden" name="package_id" value="">

    <input type="submit" value="Validate">
</form>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
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
<script src="../../assets/js/env.js"></script>
<script>
    var conn = new WebSocket(wsCurEnv);
    conn.onopen = function(e) {
        conn.send(JSON.stringify({
            command: "attachAccount",
            account: sender,
            role: sender_role,
            name: sender_pseudo,
            sender_avatar: sender_avatar,
            id_group: id_group
        }));
    };
    conn.onerror = function(e) {
        console.log(e);
    }
</script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<script src="../../assets/js/jquery.emojipicker.js"></script>
<script src="../../assets/js/jquery.emojis.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script src="../../assets/js/pages/update_password.js"></script>
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/pages/chat.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/pages/customers.js"></script>
<script src="../../assets/js/moment.js"></script>
<script>
    var max;
    var balance = 0;
    var receiver_fullName = null;
    var receiver_avatar = null;
    var receiver = null;
    var receiver_role = 3;
    var search = [];
    $(document).ready(function() {
        $('#content').emojiPicker({
            categories: '<?= json_encode($trans['categories']) ?>'
        });
        moment.locale('<?= $_COOKIE['lang'] ?>', {
            months: 'janvier_février_mars_avril_mai_juin_juillet_août_septembre_octobre_novembre_décembre'.split('_'),
            monthsShort: 'janv._févr._mars_avr._mai_juin_juil._août_sept._oct._nov._déc.'.split('_')
        });
        $('.emojiPickerIcon').click();
        $('.picCollapse').click(function() {
            $('#themecolors').toggle();
            $('.switch').toggle();
        })
        $('.fileCollapse').click(function() {
            $('.files').toggle();
            $('.switch1').toggle();
        })
        $('.searchInConv').click(function() {
            $('#more').click()
            $('#searchBar').show();
        });
        $('.reportSend').click(function() {
            let reason = $('#Reason').val();
            let desc = $('#message').val();
            $('#overlay').show();
            $.ajax({
                url: "moveFile.php",
                type: "POST",
                data: {
                    action: "report",
                    sender: sender,
                    receiver: receiver,
                    reason: reason,
                    subject: 4,
                    desc: desc,
                    id_account: id_group
                },
                success: function(data) {
                    if (data == "1") {
                        sendMessage({
                            command: "notification",
                            action: 1,
                            sender: sender,
                            receiver: receiver,
                            consultant: receiver_fullName,
                            customer: '<?= $account->firstname . ' ' . $account->lastname ?>',
                            id_group: id_group
                        });
                        Swal.fire({
                            type: 'success',
                            title: 'Expert reported successfully',
                            text: 'We will treat the case as soon as possible.'
                        })
                    } else {
                        Swal.fire({
                            type: 'error',
                            title: 'Message not sended!',
                            text: 'please Contact the administration.'
                        })
                    }
                    $('#reportModal').modal('hide');
                    $('#overlay').hide();
                }
            });
        });
        $('.search-toggle').click(function() {
            $('#searchBar').toggle();
            $('#SearchVal').val('');
            $('.current').text(0);
            $('.total').text(0);
            $('.consultant-li-' + receiver).children('a').click();
        });
        $('.before').click(function() {
            let i = parseInt($('.current').text());
            if (i < search.length) {
                $('#overlay').show();
                $(".chat_container").empty();
                $('.chat-rbox').scrollTop($('.chat-rbox').height() / 2);
                $('.current').text(i + 1);
                $.each(search[i++], function(key, value) {
                    if (value.origin == "1") {
                        $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                    } else if (value.sender_role == sender_role) {
                        $(".chat_container").prepend(`<li  id="` + value.id_message + `"><div class="chat-img"><img src="<?php echo '../uploads/customers/' . $account->photo ?>" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`);
                        status = value.status;
                    } else {
                        $(".chat_container").prepend(`<li  id="` + value.id_message + `"class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div><div class="chat-img"><img src="../uploads/consultants/${receiver_avatar}" alt="user" /></div></li>`);
                    }
                    max = value.id_message;
                });
                $('#overlay').hide();
            }
        });
        $('.after').click(function() {

            let i = parseInt($('.current').text());
            if (i > 1) {
                $('#overlay').show();
                $(".chat_container").empty();
                $('.chat-rbox').scrollTop($('.chat-rbox').height() / 2);
                $('.current').text(i - 1);
                $.when($.each(search[i--], function(key, value) {
                    if (value.origin == "1") {
                        $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                    } else if (value.sender_role == sender_role) {
                        var d1 = new Date();
                        var d2 = new Date(d1);
                        d2.setMinutes(d1.getMinutes() + (-60));
                        $(".chat_container").prepend(`<li  id="` + value.id_message + `"><div class="chat-img"><img src="<?php echo '../uploads/customers/' . $account->photo ?>" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`);
                        status = value.status;
                    } else {
                        $(".chat_container").prepend(`<li  id="` + value.id_message + `"class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div><div class="chat-img"><img src="../uploads/consultants/${receiver_avatar}" alt="user" /></div></li>`);
                    }
                    max = value.id_message;
                })).done(function() {
                    $('#overlay').hide();
                });
            }
        });
        $('#search').click(function() {
            let word = $('#SearchVal').val();
            if (word != '') {
                $('#overlay').show();
                $.ajax({
                    url: "moveFile.php",
                    type: "POST",
                    data: {
                        action: "search",
                        sender: sender,
                        receiver: receiver,
                        word: word
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        $(".chat_container").empty();
                        $('.chat-rbox').scrollTop($('.chat-rbox').height() / 2);
                        $('.current').text('1');
                        $('.total').text(data.length);
                        search = data;
                        $.each(data[0], function(key, value) {
                            if (value.origin == "1") {
                                $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse" "><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                            } else if (value.sender_role == sender_role) {
                                $(".chat_container").prepend(`<li  id="` + value.id_message + `"><div class="chat-img"><img src="<?php echo '../uploads/customers/' . $account->photo ?>" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`);
                                status = value.status;
                            } else {
                                $(".chat_container").prepend(`<li  id="` + value.id_message + `"class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div><div class="chat-img"><img src="../uploads/consultants/${receiver_avatar}" alt="user" /></div></li>`);
                            }
                            max = value.id_message;
                        });
                        $('#overlay').hide();
                    }
                });
            }
        });
        $(".package-modal-items").hover(function() {
            $(".buy-package").hide();
            $("#buy-package-" + $(this).data("packageId")).show();
        });
        $("#content_watcher").text(<?= $company->max_length ?>);

        $('#content').on("keyup", function(e) {
            if (<?= $company->max_length ?> > 0) {
                set = <?= $company->max_length ?>;
                var tval = $('#content').val();
                tlength = tval.length;
                remain = parseInt(set - tlength);
                $("#content_watcher").text(set - tlength);
                if (remain == 0) {
                    $("#content_watcher_container").css("color", "red");
                } else {
                    $("#content_watcher_container").css("color", "black");
                }
            }

        });
        $('#content').keypress(function(e) {
            if (e.keyCode == 13) {
                var content = $("#content").val();
                if (receiver == null || receiver_role == null) {
                    return false;
                } else if (!content.replace(/\s/g, '').length) {
                    return false;
                } else {
                    $("#send_message").trigger("click");
                    $(':input[id="content"]').trigger("focusout");
                    $(':input[id="content"]').val(null);
                    $(':input[id="content"]').trigger("focusin");
                    return false;
                }
            }
        });
        $('#content').on("focusout", function(e) {
            sendMessage({
                command: "writing",
                status: 0,
                sender: sender,
                receiver: receiver,
                id_group: id_group
            });
        });
        $('#content').on("focusin", function(e) {
            sendMessage({
                command: "writing",
                status: 1,
                sender: sender,
                receiver: receiver,
                id_group: id_group
            });
            let count = parseInt($('#messagesCount_' + receiver).text());
            let total = parseInt($("#total_unread_messages").text());

            if (count != 0 && total > 0) {
                total--;
                $("#total_unread_messages").text(total);
            }
            $('#messagesCount_' + receiver).text('0');
            $('#messagesCount_' + receiver).hide();
            deleteNotif();
        });
        $(".buy-package").click(function() {
            var packageId = $(this).data("packageId");
            $.ajax({
                url: "../packageTrait.php",
                type: "POST",
                data: {
                    action: "buy_package",
                    packageId: packageId,
                    accountId: <?= $id_account ?>,
                    websiteId: <?= $id_website ?>,
                    currency: '<?= $trans['devise'][$_SESSION['country']] ?>'
                },
                dataType: "json",
                success: function(dataResult) {
                    if (dataResult.statusCode == 200) {
                        $("input[name='id_company']").val(dataResult.response.payment_requirement.id_company);
                        $("input[name='id_shop']").val(dataResult.response.payment_requirement.id_shop);
                        $("input[name='amount']").val(dataResult.response.payment_requirement.amount);
                        $("input[name='currency']").val(dataResult.response.payment_requirement.currency);
                        $("input[name='country']").val(dataResult.response.payment_requirement.country);
                        $("input[name='first_name']").val(dataResult.response.payment_requirement.first_name);
                        $("input[name='last_name']").val(dataResult.response.payment_requirement.last_name);
                        $("input[name='email']").val(dataResult.response.payment_requirement.email);
                        $("input[name='package_id']").val(packageId);
                        $("#pForm").submit();
                    }
                }
            });

        })
        $(".chat-list").on('click', '.get-offer', function() {
            var packageId = $(this).data("id");
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'mr-2 btn btn-danger'
                },
                buttonsStyling: false,
            });
            swalWithBootstrapButtons.fire({
                title: "<?php echo ($trans["package_alert"]["title"]) ?>",
                text: "<?php echo ($trans["package_alert"]["subtitle"]) ?>",
                type: 'success',
                showCancelButton: true,
                confirmButtonText: "<?php echo ($trans["package_alert"]["confirm"]) ?>",
                cancelButtonText: "<?php echo ($trans["package_alert"]["cancel"]) ?>",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "../packageTrait.php",
                        type: "POST",
                        data: {
                            action: "get_package",
                            packageId: packageId,
                            accountId: <?= $id_account ?>,
                            websiteId: <?= $id_website ?>,
                        },
                        dataType: "json",
                        success: function(dataResult) {
                            if (dataResult.statusCode == 200) {
                                window.location.href = 'index.php';
                            } else if (dataResult.statusCode == 201) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                                    'error'
                                )
                            }
                        }
                    });
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                        'error'
                    )
                }
            })
        })
        $(".chat-list").on('click', '.buy-offer', function() {
            var packageId = $(this).data("id");
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'mr-2 btn btn-danger'
                },
                buttonsStyling: false,
            });
            swalWithBootstrapButtons.fire({
                title: "<?php echo ($trans["package_alert"]["title"]) ?>",
                text: "<?php echo ($trans["package_alert"]["subtitle"]) ?>",
                type: 'success',
                showCancelButton: true,
                confirmButtonText: "<?php echo ($trans["package_alert"]["confirm"]) ?>",
                cancelButtonText: "<?php echo ($trans["package_alert"]["cancel"]) ?>",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "../packageTrait.php",
                        type: "POST",
                        data: {
                            action: "buy_package",
                            packageId: packageId,
                            accountId: <?= $id_account ?>,
                            websiteId: <?= $id_website ?>,
                            currency: '<?= $trans['devise'][$_SESSION['country']] ?>'
                        },
                        dataType: "json",
                        success: function(dataResult) {
                            if (dataResult.statusCode == 200) {
                                $("input[name='id_company']").val(dataResult.response.payment_requirement.id_company);
                                $("input[name='id_shop']").val(dataResult.response.payment_requirement.id_shop);
                                $("input[name='amount']").val(dataResult.response.payment_requirement.amount);
                                $("input[name='currency']").val(dataResult.response.payment_requirement.currency);
                                $("input[name='country']").val(dataResult.response.payment_requirement.country);
                                $("input[name='first_name']").val(dataResult.response.payment_requirement.first_name);
                                $("input[name='last_name']").val(dataResult.response.payment_requirement.last_name);
                                $("input[name='email']").val(dataResult.response.payment_requirement.email);
                                $("input[name='package_id']").val(packageId);
                                $("#pForm").submit();
                            } else if (dataResult.statusCode == 201) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                                    'error'
                                )
                            }
                        }
                    });

                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                        'error'
                    )
                }
            })

        })

        var consultants = jQuery("a.consultants-items");
        consultants.each(function() {
            if ($(this).data("unreadMessageCount")) {
                $("#new-message-" + $(this).data("id")).css("display", "block");
                $("#flushing-message-" + $(this).data("id")).css("display", "none");
                $("#stable-message-" + $(this).data("id")).css("display", "block");
            }
        });
        $("#send_message").click(function() {
            var content = $("#content").val();
            if (receiver == null || receiver_role == null) {} else if (!content.replace(/\s/g, '').length) {} else {
                if (balance > 0) {
                    sendMessage({
                        command: "message",
                        msg: content,
                        sender: sender,
                        sender_role: sender_role,
                        receiver: receiver,
                        receiver_role: receiver_role,
                        account: receiver,
                        customer_id: customer_id,
                        id_group: id_group,
                        unlimited: unlimited
                    });
                    $('#content').val('');
                    $('#content_watcher').text('12');
                }
            }
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

        $('.chat-rbox').scroll(function() {
            if ($('.chat-rbox').scrollTop() == 0) {
                $('#loading').show();
                $.when($.ajax({
                    url: "../conversationTrait.php",
                    type: "POST",
                    data: {
                        action: "getConversation",
                        sender: sender,
                        sender_role: sender_role,
                        receiver: receiver,
                        receiver_role: receiver_role,
                        last_id: max
                    },
                    dataType: "json",
                    success: function(dataResult) {
                        if (dataResult.statusCode == 200) {
                            $('.chat-rbox').scrollTop(1);
                            $.each(dataResult.conversations, function(key, value) {
                                if (value.origin == "1") {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse" "><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin">${value.content}</div><div class="chat-time">${moment(value.date_send).format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                                } else if (value.sender_role == sender_role) {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="<?php echo '../uploads/customers/' . $account->photo ?>" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment(value.date_send).format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    status = value.status;
                                } else {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div> <div class="chat-time">${moment(value.date_send).format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../uploads/consultants/${receiver_avatar}" alt="user" /></div></li>`);
                                }
                                max = value.id_message;
                            });
                        }
                    }
                })).done(function() {
                    $('#loading').hide();
                });
            }
        });
        if (unlimited == 0) {
            $.ajax({
                url: "../customerTrait.php",
                type: "POST",
                data: {
                    action: "getBalance",
                    customer_id: customer_id,
                },
                dataType: "json",
                success: function(dataResult) {
                    if (dataResult.statusCode == 200) {
                        balance = dataResult.balance;
                        $(".header-balance-text").text(balance);
                        if (balance <= 0) {
                            $(".header-balance-text").text(0);
                            $("#send_message").prop("disabled", true);
                            $("#content").prop("disabled", true);
                            $("#content").attr("placeholder", "<?= $trans["chat"]["insufficient_credits"] ?>");
                            $("#out_of_balace").css("display", "block");
                            Swal.fire({
                                type: 'error',
                                title: '<?= $trans["chat"]["insufficient_credits"] ?>',
                                showConfirmButton: true,
                                confirmButtonText: '<?= $trans["chat"]["exploreOurPacks"] ?>'
                            }).then((result) => {
                                if (result.value) {
                                    $(location).attr('href', 'packages.php');
                                }
                            })
                        } else if (receiver != null) {
                            $("#send_message").prop("disabled", false);
                            $("#content").prop("disabled", false);
                            $("#content").attr("placeholder", "<?= $trans["chat"]["select_a_consultant_to_enable_typing"] ?>");
                            $("#out_of_balace").css("display", "none");
                        }
                    }
                }
            });
        } else {
            $("#send_message").prop("disabled", false);
            $("#content").prop("disabled", false);
            $("#content").attr("placeholder", "<?= $trans["chat"]["select_a_consultant_to_enable_typing"] ?>");
            $("#out_of_balace").css("display", "none");
        }

        $(".chat_item").on('click', function() {
            switchElement(this);
        });


    });
    addListner(conn);

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
                    account: sender,
                    role: sender_role,
                    name: sender_pseudo,
                    sender_avatar: sender_avatar,
                    id_group: id_group
                }));
                conn.send(JSON.stringify({
                    command: "connected",
                    id_group: id_group
                }));
            };
            addListner(conn);
        }
    }

    function addListner(conn) {
        conn.onmessage = function(e) {
            var dt = jQuery.parseJSON(e.data);
            if (dt.status == 200) {
                if (dt.action == "newMessage") {
                    if (dt.sender == sender) {
                        $(".chat_container").append(`<li><div class="chat-img"><img src="<?php echo '../uploads/customers/' . $account->photo ?>" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${dt.message}</div><div class="chat-time">now</div></div></li>`).children(':last').fadeIn(1000);
                        $(".conversation-status-" + receiver).html('<span class="text-danger"><small><i class="mdi mdi-cancel"></i> Not seen yet</small></span>');

                    } else {
                        displayNotif(dt, receiver_avatar);
                        if (dt.sender == receiver) {
                            $(".writing_perview").hide();
                            if (dt.admin == 2) {
                                $(".chat_container").append(`<li class="reverse"><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin row">${dt.message}</div><div class="chat-time">now</div></div><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                            } else {
                                $(".chat_container").append(`<li class="reverse"><div class="chat-content"><div class="box bg-light-info row">${dt.message}</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../uploads/consultants/${receiver_avatar}" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                            }
                            sendMessage({
                                command: "openConversation",
                                sender: sender,
                                receiver: receiver,
                                sender_role: sender_role,
					            receiver_role: receiver_role,
                            });
                        } else {

                            $("#new-message-" + dt.sender).css("display", "block");
                            $("#flushing-message-" + dt.sender).css("display", "block");
                            $("#stable-message-" + dt.sender).css("display", "block");
                            setTimeout(function() {
                                $("#flushing-message-" + dt.sender).css("display", "none");
                            }, 4000);
                        }

                    }
                    $('.chat-rbox').scrollTop(1E10);
                } else if (dt.action == "newConnection") {
                    if (dt.id_user != sender) {
                        $("#consultant-status-" + dt.id_user).removeClass("text-danger");
                        $("#consultant-status-" + dt.id_user).addClass("text-success");
                        $("#consultant-status-" + dt.id_user).text("<?= $trans["online"] ?>");
                        $('.consultant-status').html($("#consultant-status-" + dt.id_user).clone());

                        var element = $(".consultant-li-" + dt.id_user);
                        $(".consultant-li-" + dt.id_user).remove();
                        $('.consultantslist').prepend(element);
                        $('.consultantslist li').on('click', function() {
                            switchElement($(this).children());
                        });
                    }
                } else if (dt.action == "closedConnection") {
                    if (dt.id_user != sender) {
                        $("#consultant-status-" + dt.id_user).removeClass("text-success");
                        $("#consultant-status-" + dt.id_user).addClass("text-danger");
                        $("#consultant-status-" + dt.id_user).text("<?= $trans["offline"] ?>");
                        $('.consultant-status').html($("#consultant-status-" + dt.id_user).clone());
                        var element = $(".consultant-li-" + dt.id_user);
                        $(".consultant-li-" + dt.id_user).remove();
                        $('.consultantslist').append(element);
                        $('.consultantslist li').on('click', function() {
                            switchElement($(this).children());
                        });

                    }
                } else if (dt.action == "balance") {
                    if (unlimited == 0) {
                        $(".header-balance-text").text(dt.balance);
                        if (dt.balance <= 0) {
                            $("#send_message").prop("disabled", true);
                            $("#content").prop("disabled", true);
                            $("#content").attr("placeholder", "<?= $trans["chat"]["insufficient_credits"] ?>");
                            $('#buy-package-main-modal').modal('show');
                            $("#out_of_balace").css("display", "block");
                        } else {
                            $("#send_message").prop("disabled", false);
                            $("#content").prop("disabled", false);
                            $("#content").attr("placeholder", "<?= $trans["chat"]["select_a_consultant_to_enable_typing"] ?>");
                            $("#out_of_balace").css("display", "none");
                        }
                    }
                } else if (dt.action == "conversationStatus") {
                    $(".conversation-status-" + receiver).html('<span class="text-success"><small><i class="mdi mdi-check"></i> <?= $trans["seen"]; ?></small></span>');

                } else if (dt.action == "reassign") {
                    $('.conversation-status-' + dt.oldExpert + ' span').html('<small>You have been Re-assigned ,you will receive a message from ' + dt.pseudo + '</small>');
                } else if (dt.action == "total_unread_messages") {
                    $("#total_unread_messages").text(dt.total_unread_messages);
                } else if (dt.action == "connected") {
                    $.each(dt.users, function() {
                        $("#consultant-status-" + this).removeClass("text-danger");
                        $("#consultant-status-" + this).addClass("text-success");
                        $("#consultant-status-" + this).text("<?= $trans["online"] ?>");
                        var element = $("#consultant-li-" + this);
                        $("#consultant-li-" + this).remove();
                        $('.consultantslist').prepend(element);
                    });

                    $('.consultantslist li').on('click', function() {
                        switchElement($(this).children());
                    });
                    $("#overlay").hide();
                } else if (dt.action == "writing") {
                    if (dt.receiver == sender && dt.sender == receiver) {
                        $(".chat_container").append(`<li class="reverse writing_perview" ><div class="chat-content"><div class="box bg-light-info"><span class="writing_status_v2"><i class="mdi mdi-grease-pencil"></i> <?= $trans["writing"]; ?> ..</span></div></div><div class="chat-img"><img src="../uploads/consultants/${receiver_avatar}" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                        $('.chat-rbox').scrollTop(1E10);
                    }
                } else if (dt.action == "stopWriting") {
                    if (dt.receiver == sender && dt.sender == receiver) {
                        var ah = $(".writing_perview").height();
                        $(".writing_perview").fadeOut(0);
                        var dh = $(".chat-rbox .ps__scrollbar-y-rail").css("top").slice(0, -2);
                        $('.chat-rbox .ps__scrollbar-y-rail').css('top', (dh - (ah + 20)) + "px");
                    }
                }
            }
        };
    }

    function switchElement(element) {
        $('.chat-rbox').scrollTop(1);
        $("#overlay").show();
        $("#searchBar").hide();
        $("#chat-welcome").addClass("hide");
        $("#chat-block").removeClass("hide");
        $(".chat_item").removeClass("chat-box-active-item");
        var $this = $(element);
        $(element).addClass("chat-box-active-item");
        if (balance > 0) {
            $("#send_message").prop("disabled", false);
            $("#content").prop("disabled", false);
            $("#content").attr("placeholder", "<?= $trans["chat"]["type_your_message"] ?>");
        }
        receiver_fullName = $(element).data("fullName");
        receiver_avatar = $(element).data("avatar");
        receiver = $(element).data("id");
        $('.consultant-status').html($("#consultant-status-" + receiver).clone());
        let count = parseInt($('#messagesCount_' + receiver).text());
        if (count != 0) {
            let total = parseInt($("#total_unread_messages").text());
            total--;
            $("#total_unread_messages").text(total);
        }
        $('#messagesCount_' + receiver).text('0');
        $('#messagesCount_' + receiver).hide();
        $(".fto").addClass("hide");
        $(".box-title").text(receiver_fullName);
        deleteNotif();
        $.ajax({
            url: "../conversationTrait.php",
            type: "POST",
            data: {
                action: "getConversation",
                sender: sender,
                sender_role: sender_role,
                receiver: receiver,
                receiver_role: receiver_role,
                last_id: 0
            },
            dataType: "json",
            success: function(dataResult) {
                if (dataResult.statusCode == 200) {
                    $("#new-message-" + receiver).css("display", "none");
                    $("#flushing-message-" + receiver).css("display", "none");
                    $("#stable-message-" + receiver).css("display", "none");
                    $("#content").val("");
                    $(".chat_container").empty();
                    $('.chat-rbox').scrollTop(0);
                    var status = 0;
                    sendMessage({
                        command: "openConversation",
                        sender: sender,
                        receiver: receiver
                    });
                    $.each(dataResult.conversations, function(key, value) {
                        if (value.origin == "1") {
                            $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse" "><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                        } else if (value.sender_role == sender_role) {
                            $(".chat_container").prepend(`<li  id="` + value.id_message + `"><div class="chat-img"><img src="<?php echo '../uploads/customers/' . $account->photo ?>" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`);
                            status = value.status;
                        } else {
                            $(".chat_container").prepend(`<li  id="` + value.id_message + `"class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div><div class="chat-img"><img src="../uploads/consultants/${receiver_avatar}" alt="user" /></div></li>`);
                        }
                        max = value.id_message;
                    });
                    if (status == 1) {
                        $(".conversation-status-container").css("display", "none");
                        $(".conversation-status-container-" + receiver).css("display", "block");
                        $(".conversation-status-" + receiver).html('<span class="text-success"><small><i class="mdi mdi-check"></i> <?= $trans["seen"] ?></small></span>');

                    } else {

                        $(".conversation-status-container").css("display", "none");
                        $(".conversation-status-container-" + receiver).css("display", "block");
                        $(".conversation-status-" + receiver).html('<span class="text-danger"><small><i class="mdi mdi-cancel"></i><?= $trans["not_seen"] ?></small></span>');
                    }
                    $('.chat-rbox').scrollTop(1E10);
                } else if (dataResult.statusCode == 201) {
                    $("#content").val("");
                    $(".chat_container").empty();
                    $('.chat-rbox').scrollTop(0);
                }
                $.ajax({
                    url: "moveFile.php",
                    type: "POST",
                    data: {
                        action: "getFiles",
                        sender: sender,
                        receiver: receiver,
                        id: id_group,
                        type: 1
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        $('.files').empty();
                        if (data.files.length > 0) {
                            $.each(data.files, function() {
                                $('.files').append(this);
                            });
                        }
                        $('.pictures').empty();
                        if (data.pictures.length > 0) {
                            $.each(data.pictures, function() {
                                $('.pictures').append(this);
                            });
                        }
                        $("#overlay").hide();

                    }
                });
                $('.open-pnl').removeClass('open-pnl');
            }
        });
    }

    function displayNotif(dt, receiver_avatar) {
        $('#messagesCount_' + dt.sender).show();
        let count = parseInt($('#messagesCount_' + dt.sender).text());
        if (count == 0) {
            let count1 = parseInt($("#total_unread_messages").text());
            count1++;
            $("#total_unread_messages").text(count1);
        }
        count++;
        $('#messagesCount_' + dt.sender).text(count);
        if ($("#audio_notification_value").val() == "1") {
            document.getElementById('play').play();
        }
        if ($("#browser_notification_value").val() == "1") {

            if (window.Notification && Notification.permission !== "granted") {

                Notification.requestPermission(function(status) {
                    if (Notification.permission !== status) {
                        Notification.permission = status;
                    }
                });
            }
            if (window.Notification && Notification.permission === "granted") {
                let img = window.location.href + '/../../uploads/consultants/' + receiver_avatar;
                Push.create("New Message From " + dt.customer_fullName, {
                    body: dt.message,
                    icon: img,
                    timeout: 4000,
                    vibrate: [200, 100, 200],
                    tag: "new-message",
                    onClick: function() {
                        window.focus();
                        this.close();
                    }
                });
            }

        }
        setTimeout(function() {
            var total_unread_messages = parseInt($("#total_unread_messages").text());
            if (total_unread_messages > 0) {
                var icon = document.getElementById("pageIcon");
                icon.href = '../../assets/images/notify.png';
                var title = document.title;
                var strArray = title.split("(");
                document.title = strArray[0] + " (" + total_unread_messages + ")";
            }
        }, 1000);
    }

    function deleteNotif() {
        setTimeout(function() {
            var icon = document.getElementById("pageIcon");
            icon.href = '../../assets/images/favicon.png';
            var title = document.title;
            var strArray = title.split("(");
            document.title = strArray[0];
        }, 2000);
    }

    function selected(input) {
        if (input.files && input.files[0]) {
            if (input.files[0].size / 1048576 < <?= $result->max_size ?>) {
                let type = input.name == 'file' ? 1 : 2;
                Swal.fire({
                    title: "Send File",
                    text: "Do you want to send this file ?",
                    type: 'question',
                    iconHtml: '?',
                    footer: 'the send of file is considered as message',
                    showCancelButton: true,
                    confirmButtonText: "Send File",
                    confirmButtonColor: '#fec107',
                    cancelButtonText: "<?php echo ($trans["package_alert"]["cancel"]) ?>",
                }).then((result) => {
                    if (result.value) {
                        var fd = new FormData();
                        var files = input.files[0];
                        fd.append('file', files);
                        fd.append('id', <?= intval($result->id_website) ?>);
                        fd.append('sender', sender);
                        fd.append('receiver', receiver);
                        fd.append('type', type);
                        fd.append('action', 'moveFile');
                        $(".conversation-status-" + receiver).html('<span class="text-danger"><small><i class="mdi mdi-cancel"></i> Not seen yet</small></span>');
                        $.ajax({
                            url: 'moveFile.php',
                            type: 'post',
                            data: fd,
                            contentType: false,
                            processData: false,
                            cache: false,
                            xhr: function() {
                                $('#overlay').show();
                                var myXhr = $.ajaxSettings.xhr();
                                if (myXhr.upload) {
                                    myXhr.upload.addEventListener('progress', function(e) {
                                        if (e.lengthComputable) {
                                            $('#progress').text(Math.round((e.loaded / e.total) * 100) + "%");
                                        }
                                    }, false);
                                }
                                return myXhr;
                            },
                            success: function(response) {
                                response = JSON.parse(response);
                                if (response.status == 200) {
                                    if (receiver == null || receiver_role == null) {} else {
                                        if (balance > 0) {
                                            sendMessage({
                                                command: "message",
                                                msg: response.content,
                                                sender: sender,
                                                sender_role: sender_role,
                                                receiver: receiver,
                                                receiver_role: receiver_role,
                                                account: receiver,
                                                customer_id: customer_id,
                                                id_group: id_group,
                                                unlimited: unlimited
                                            });
                                        }
                                    }
                                } else if (response.status == 201) {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'No more Storage!',
                                        text: 'please Contact the administration.'
                                    })
                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Something went wrong!',
                                        text: 'please try in another time.'
                                    })
                                }
                                $('#overlay').hide();
                                $(':input[id="file-input"]').val(null);
                            }
                        });

                    } else {
                        $(':input[id="file-input"]').val(null);
                    }
                })
            } else {
                Swal.fire({
                    type: 'warning',
                    title: 'File too big !!',
                    text: 'please choose a file less then ' + <?= $result->max_size ?> + ' M.'
                })
            }
        }
    }
</script>
</body>

</html>
