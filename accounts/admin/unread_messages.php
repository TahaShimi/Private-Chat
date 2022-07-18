<?php
$page_name = "messages";
include('header.php');

$st = $conn->prepare("SELECT unread_duration FROM `accounts` WHERE id_account=:IDA");
$st->bindparam(":IDA", $_SESSION['id_account']);
$st->execute();
$duration = $st->fetchObject();
$unread_duration = date('Y-m-d H:i:s', strtotime("- " . $duration->unread_duration . " seconds"));
$st = $conn->prepare("SELECT * FROM customers c left join users u on u.id_profile=c.id_customer join messages m ON m.id_message = ( SELECT id_message FROM messages mi WHERE ((mi.sender = c.id_customer or mi.sender = 0 or mi.sender = u.id_user) or mi.receiver = c.id_customer) ORDER BY mi.sender DESC, mi.id_message DESC LIMIT 1 ) where m.status =0 and seen_at is NULL and m.date_send < :x_temps and c.id_account=:IDA and (c.id_customer IN (SELECT DISTINCT sender from messages where sender_role = 7) or u.id_user IN (SELECT DISTINCT sender from messages where sender_role = 4));");
$st->bindparam(":x_temps", $unread_duration);
$st->bindparam(":IDA", $_SESSION['id_account']);
$st->execute();
$mssgs = $st->fetchAll(PDO::FETCH_OBJ);
$nbrmssgs = $st->rowCount();
$stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM messages where u.id_user = receiver and status=0) as unread_messages_count from consultants c join users u on u.id_profile=c.id_consultant where c.id_account=:ia and u.profile=3");
$stmt->bindparam(":ia",  $_SESSION['id_company']);
$stmt->execute();
$consultants = $stmt->fetchAll(PDO::FETCH_OBJ);

?>
<link href="../../assets/css/pages/chat-app-page.css" rel="stylesheet">
<link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
<link href="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
<link href="../../assets/css/custom.css" rel="stylesheet">
<link href="../../assets/css/pages/consultant.css" rel="stylesheet">
<style>
    .transp {
        background: none !important;
        box-shadow: none !important;
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

    .pricing-tab {
        margin: auto;
        padding: 0 20px;
    }

    .price-header {
        position: relative;
        z-index: 9;
    }

    

    .menu {
        position: relative;
        display: inline-block;
        overflow: hidden;
        width: 100%;
    }

    td {
        min-width: 300px;
    }

    tr {
        max-height: 70px;
    }

    .table tr:first-child td {
        border-top: none;
    }

    input {
        max-height: 37px;
    }

    .menu:hover button {
        transform: translateX(-100%);
    }

    table {
        width: 100%;
    }

    .profile-pic {
        width: 100px;
        border-radius: 100%;
    }



    .dropdown-toggle::after {
        display: none;
    }

    .liste {
        width: 100%;
        max-height: 500px;
    }

    .col-sm-3 {
        border-right: 1px solid #c7c5c5;
    }

    .comboColumn {
        border-bottom: 1px solid #c7c5c5;
    }

    .liste>li {
        list-style: none;
        margin-left: -30px;
        margin-top: 8px;
    }

    #inConversation>li:after {
        content: "";
        display: block;
        margin: 0 auto;
        width: 40%;
        padding-top: 20px;
        border-bottom: 1px solid #c7c5c5;
    }

    .w3-bar-item {
        text-transform: capitalize;
    }

    .text {
        padding-top: 7px;
    }

    

    .img-circle {
        margin-right: 10px;
        width: 40px;
        height: 40px;
    }

    .back {
        margin-left: -8px;
        margin-top: 2px;
        margin-bottom: 15px;
    }

    .row {
        margin-right: 0px;
    }

    .total {
        float: right;
        font-size: 11px;
        margin: 5px 5px 0 0;
    }

    .total span {
        background-color: #2a7b9b;
        color: white;
    }

    .btn-rounded {
        padding: 4px 14px;
    }
</style>
<link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
<link href="../../assets/css/style.min.css" rel="stylesheet">
<link href="../../assets/css/custom.css" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/jquery.emojipicker.tw.css">
<link rel="stylesheet" href="../../assets/css/jquery.emojipicker.css">
<style>
    .pricing-body {
        padding: 30px 30px;
    }

    .admin {
        background: #818182;
        color: white;
        max-width: 50%;
        display: inline-block;
        padding: 8px;
        margin-bottom: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .1);
        border-radius: .8rem .8rem .8rem 0;
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

    #footer {
        bottom: 0;
        background: #514e4e;
        color: white;
        width: 300px;
        border-radius: 7px;
        margin-left: auto;
        margin-right: auto;
    }

    .border-left {
        padding-left: 0px !important;
    }

    .chat-main-header {
        border-top: 6px solid #ff6774;
        height: 8%;
        text-transform: capitalize;
        border-bottom: 45px solid #ffffff;
        padding-top: 5px;
        box-shadow: 11px 1px 19px 0px #b9b7b7;
        z-index: 0;
    }

    title {
        text-transform: capitalize;
    }

    .chat-texture {
        background: #fff;
        background-image: url(../../assets/images/background.png);
    }

    .reverse .box {
        text-align: right !important;
        position: relative !important;
        display: block !important;
        float: right !important;
        padding: 8px 15px !important;
        clear: both !important;
        color: #fff !important;
        background-color: #fca2ab !important;
        -webkit-box-shadow: 0 5px 12px 0 rgba(62, 57, 107, 0.36) !important;
        box-shadow: 0 5px 12px 0 rgba(62, 57, 107, 0.36) !important;
        border-radius: 7px !important;
    }

    .reverse .chat-time {
        float: right !important;
        clear: both;
        text-align: right !important;
    }

    .chat-time {
        float: left !important;
        clear: both;
        display: block;
        text-align: left !important;
    }

    .chat-list li.reverse .chat-content {
        padding-right: 0px !important;
        padding-left: 0px !important;
    }

    .chat-content {
        padding-left: 0px !important;
    }

    .box {
        text-align: left;
        float: left;
        color: #6b6f80;
        clear: both !important;
        background-color: #FFFFFF;
        -webkit-box-shadow: 0 7px 12px 0 rgba(62, 57, 107, 0.16);
        box-shadow: 0 7px 12px 0 rgba(62, 57, 107, 0.16);
    }

    .box:before {
        position: absolute;
        right: -10px;
        width: 0;
        height: 0;
        bottom: 4;
        content: '';
        border: 5px solid transparent;
        border-left-color: #6b6f80;
    }

    .reverse .box:before {
        border-left-color: #fca2ab !important;
    }

    body {
        overflow: hidden;
    }

    #loading {
        position: absolute;
        width: 80%;
        text-align: center;
    }

    .custom-control {
        margin-left: 25px;
        font-size: 12px;
    }
</style>
<div class="row m-0">
    <div class="col-12 p-0">
        <div class="card m-b-0">
            <!-- .chat-row -->
            <div class="chat-main-box">
                <!-- .chat-left-panel -->
                <div class="chat-left-aside">
                    <div class="open-panel"><i class="ti-angle-right"></i></div>
                    <div class="chat-left-inner">
                        <div class="form-material">
                            <small class="total"><span class="waves-light btn btn-outline-secondary"><?= $nbrmssgs ?></span></small>
                            <input class="form-control p-2" type="text" placeholder="<?php echo ($trans["chat"]["search_customer"]) ?>" id="search_bar_customers">
                        </div>
                        <ul class="chatonline style-none customers p-0" style="list-style: none;">
                            <div class="customerslist">
                                <?php foreach ($mssgs as $mssg) { ?>
                                    <li class="customers-li d-flex align-items-center" data-full-name="<?php echo $mssg->firstname . ' ' . $mssg->lastname; ?>">
                                        <a href="javascript:void(0)" data-avatar="<?= isset($mssg->photo) ? $mssg->photo : 'img-1.png' ?>" data-id="<?= isset($mssg->id_user) ? $mssg->id_user : $mssg->id_customer ?>" data-full-name="<?php echo $mssg->firstname . ' ' . $mssg->lastname; ?>" data-avatar="<?= isset($mssg->photo) ? $mssg->photo : 'img-1.png' ?>" class="chat_item consultants-items customer"><img src="<?php echo isset($mssg->photo) ? '../uploads/customers/' . $mssg->photo : '../uploads/customers/img-1.png'; ?>" alt="user-img" class="img-circle">
                                            <span><?php echo $mssg->firstname . ' ' . (($mssg->lastname == 'guest') ?  $mssg->id_customer :  $mssg->lastname) ?>
                                                <small style="display: block;font-size: 10px;"><?php echo ($trans["customer_account"]["header"]["balance"]) ?> : <span><?php echo $mssg->balance ?> </span></small>
                                            </span></a>
                                        <button type="button" class="btn h-25 redistAgent" data-toggle="modal" data-target="#redist_agent" data-id="<?= isset($mssg->id_user) ? $mssg->id_user : $mssg->id_customer ?>">
                                            <i class="mdi mdi-shuffle" style="font-size: 25px;"></i>
                                        </button>
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
                    <div id="chat-welcome" class="fto" style="height: 580px;">
                        <div>
                            <h1 class="fto-r1">welcome to our chat App</h1>
                            <h1 class="fto-r2"> ~~</h1>
                            <h1 class="fto-r3">Please choose then select your customer to start your discussions</h1>
                        </div>
                    </div>
                    <div id="chat-block" class="hide">

                    </div>
                </div>
                <!-- .chat-right-panel -->
                <div id="overlay" class="hided" style="display: none;">
                    <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
                </div>
            </div>
            <!-- /.chat-row -->
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
<!-- Modal -->
<div class="modal fade" id="redist_agent" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form class="form-horizontal form-material" action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Redistribute To Other Agent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_cust" value="" id="id_cust">
                    <select name="id_agent" id="agent" class="form-control select2">
                        <option value="" selected disabled></option>
                        <?php
                        foreach ($consultants as $consultant) {   ?>
                            <option value="<?= $consultant->id_user ?>" class="d-none" id="agent-<?= $consultant->id_user ?>"><?= $consultant->pseudo ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="redistribution_submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
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
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script>
    var consultant_fullName = null;
    var consultant_avatar = null;
    var consultant = null;
    var customer_fullName = null;
    var customer_avatar = null;
    var customer = null;
    var id_group = <?= $_SESSION['id_account']  ?>;
    var conn = new WebSocket(wsCurEnv);
    var active_consultants = [];
    conn.onopen = function(e) {
        conn.send(JSON.stringify({
            command: "attachAccount",
            id_group: id_group,
            role: 2
        }));
    };
    $(document).ready(function() {
        $('.customer-options').hide();
        $('#overlay ').hide();
        var consultants = jQuery("a.consultants-items");
        consultants.each(function() {
            if ($(this).data("unreadMessageCount")) {
                $("#new-message-" + $(this).data("id")).css("display", "block");
                $("#flushing-message-" + $(this).data("id")).css("display", "none");
                $("#stable-message-" + $(this).data("id")).css("display", "block");

            }
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
        $('.customers').on('click', '.customer', function() {
            $('#overlay').show();
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    type: 'getInfo',
                    id: id,
                    messages: true,
                    profile: 4
                },
                success: function(data) {
                    // please provide the URL of the PHP page
                    if (data.conv[0] == undefined) {
                        var url = 'superConversation_1.php?sender=' + id + '&receiver=' + (data.id_customer = data.id_user);
                    } else {
                        var url = 'superConversation_1.php?sender=' + id + '&receiver=' + data.conv[0].id;
                    }
                    $.post(url,
                        function(data) {
                            if ('' !== data) {
                                // here data is the content of the whole php page
                                // Inner HTML for php page content
                                $('#chat-block').empty().html(data);
                                $('.fto').addClass('hide');
                                $('#chat-block').removeClass('hide');
                            }
                        }
                    );
                    /*  MyWindow = window.open('superConversation.php?sender=' + id + '&receiver=' + data.conv[0].id + '' + "'" + ",'" + id + "-" + data.conv[0].id + "','width=800,height=500'");
                     return false; */
                    $('#overlay').hide();

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

        $("#search_bar_customers").keyup(function() {
            $this = $(this);
            var customers = jQuery("li.customers-li");
            customers.each(function() {
                if ($(this).data("fullName").toLowerCase().indexOf($this.val()) >= 0) {
                    $(this).css("display", "block");
                } else {
                    $(this).css("display", "none");
                }
            });
        });
        $('.redistAgent').on('click', function() {
            $('#id_cust').val($(this).data('id'));
        });
        $('#redistribution_submit').on('click', function() {
            if (conn.readyState == WebSocket.OPEN) {
                conn.send(JSON.stringify({
                    command: "redistribute",
                    customer: $('#id_cust').val(),
                    agent: $('#agent').val(),
                    id_group: id_group
                }));
                $('#redist_agent').modal('hide');
                $('.modal-backdrop').hide();
            }
        });
        conn.onmessage = function(e) {
            var dt = jQuery.parseJSON(e.data);
            if (dt.status == 200) {
                if (dt.action == "connected") {
                    $.each(dt.agents, function() {
                        $('#agent-' + this).removeClass('d-none');
                        active_consultants.push(this);
                    });
                } else if (dt.action == "newConnection") {
                    $('#agent-' + dt.id_user).removeClass('d-none');
                    active_consultants.push(dt.id_user);
                } else if (dt.action == "closedConnection") {
                    $('#agent-' + dt.id_user).addClass('d-none');
                    active_consultants.filter(item => item != dt.id_user);
                }
            }
        }
    });
</script>
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/pages/chat.js"></script>
<script src="../../assets/js/jquery.emojipicker.js"></script>
<script src="../../assets/js/jquery.emojis.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script src="../../assets/js/env.js"></script>

<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/moment.js"></script>

</body>

</html>