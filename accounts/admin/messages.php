<?php
$page_name = "messages";
include('header.php');
$stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM messages where u.id_user = receiver and status=0) as unread_messages_count from consultants c join users u on u.id_profile=c.id_consultant where c.id_account=:ia and u.profile=3");
$stmt->bindparam(":ia",  $_SESSION['id_company']);
$stmt->execute();
$consultants = $stmt->fetchAll(PDO::FETCH_OBJ);
$stmt = $conn->prepare("SELECT * from customers c join users u on u.id_profile=c.id_customer JOIN messages m ON m.sender=u.id_user where c.id_account=:ia and u.profile=4 GROUP BY m.sender");
$stmt->bindparam(":ia",  $_SESSION['id_company']);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<link href="../../assets/css/pages/chat-app-page.css" rel="stylesheet">
<link href="../../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
<link href="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
<link href="../../assets/css/custom.css" rel="stylesheet">
<link href="../../assets/css/pages/consultant.css" rel="stylesheet">
<style>
    .menu {position: relative;display: inline-block;overflow: hidden;width: 100%;}
    td {min-width: 300px;}
    tr {max-height: 70px;}
    .table tr:first-child td {border-top: none;}
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
    .list-block{height: 75vh;}
</style>
<div class="card">
    <div class="chat-main-box">
        <div class="row">
            <div class="col-lg-3 col-md-4 ">
                <small class="total"><span class="btn waves-light btn-outline-secondary"><?= count($consultants) ?></span></small>
                <div class="card-body inbox-panel">
                    <h4 class="card-title text-center" style="width:100%"><?php echo $trans["consultants"] ?></h4>
                    <div class="">
                        <div class="list-block">
                            <div class="form-material">
                                <input class="form-control p-2" type="text" placeholder="<?php echo ($trans["chat"]["search_consultant"]) ?>" id="search_bar">
                            </div>
                            <ul class="chatonline style-none consultants p-0 list-block" style="list-style:none">
                                <?php foreach ($consultants as $consultant) { ?>
                                    <li class="consultants-li" data-full-name="<?php echo $consultant->pseudo; ?>">
                                        <a href="javascript:void(0)" data-unread-message-count="<?= $consultant->unread_messages_count ?>" data-id="<?= $consultant->id_user ?>" data-full-name="<?php echo $consultant->pseudo; ?>" data-avatar="<?= $consultant->photo != ""?$consultant->photo:'consult.png' ?>" class="chat_item consultants-items expert"><img src="<?php echo '../uploads/consultants/' . ($consultant->photo != ""?$consultant->photo:'consult.png')?>" alt="user-img" class="img-circle"> <span><?php echo $consultant->pseudo ?>
                                            </span></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 border-left border-right">
                <div class="card-body p-t-0 customer-options ">
                    <div class="card b-all shadow-none">
                        <div class="inbox-center table-responsive">
                            <div class="tab-content">
                                <div class="">
                                    <div class="card list-block">
                                        <div class="row bg-secondary m-t-5 m-b-20">
                                            <center class="m-t-5 p-4 col-md-4">
                                                <div class="avatar-wrapper m-b-5">
                                                    <img class="profile-pic" src="" style="height: 100px;width:100px" /></div>
                                                <h5 class="card-title m-t-10" id="full_name"></h5>
                                                <h6 class="card-subtitle" id="website"></h6>
                                            </center>
                                            <div class="row p-4 col-md-8">
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
                                                <div class="col-6 address">
                                                    <small class="text-muted p-t-30 db"><?php echo ($trans["address"]) ?></small>
                                                    <h6 id="address"></h6>
                                                </div>
                                                <div class="col-6 country">
                                                    <small class="text-muted p-t-30 db"><?php echo ($trans["country"]) ?></small>
                                                    <h6 id="country"></h6>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted p-t-30 db"><?php echo ($trans["language"]) ?></small>
                                                    <h6 id="language"></h6>
                                                </div>
                                            </div>
                                            <div>
                                                <hr>
                                            </div>
                                        </div>
                                        <h4 class="card-title text-center " style="width:100%"><?= $trans['in_Conversation'] ?></h4>
                                        <div class="chatonline"  style="overflow-y: auto;height: 50vh;">
                                            <table class="table table-hover no-wrap">
                                                <tbody id="converations"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 ">
                <small class="total"><span class="waves-light btn btn-outline-secondary"><?= count($customers) ?></span></small>
                <div class="card-body inbox-panel">
                    <h4 class="card-title text-center " style="width:100%"><?php echo $trans["customersP"] ?></h4>
                    <div class="">
                        <div class="list-block">
                            <div class="form-material">
                                <input class="form-control p-2" type="text" placeholder="<?php echo ($trans["chat"]["search_customer"]) ?>" id="search_bar_customers">
                            </div>
                            <ul class="chatonline style-none customers p-0 list-block" style="list-style:none;overflow-y:auto">
                                <?php foreach ($customers as $customer) { ?>
                                    <li class="customers-li" data-full-name="<?php echo $customer->firstname . ' ' . $customer->lastname; ?>">
                                        <a href="javascript:void(0)" data-avatar="<?= $customer->photo != null?$customer->photo:'../uploads/customers/img-1.png' ?>" data-id="<?= $customer->id_user ?>" data-full-name="<?php echo $customer->firstname . ' ' . $customer->lastname; ?>" class="chat_item consultants-items customer"><img src="<?php echo '../uploads/customers/' .($customer->photo != null?$customer->photo:'img-1.png' ) ?>" alt="user-img" class="img-circle">
                                            <span><?php echo $customer->firstname . ' ' . $customer->lastname ?>
                                                <small style="display: block;font-size: 10px;"><?php echo ($trans["customer_account"]["header"]["balance"]) ?> : <span><?php echo $customer->balance ?> </span></small>
                                            </span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
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
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
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
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/pages/chat.js"></script>
<script src="../../assets/js/moment.js"></script>
<script>
    var consultant_fullName = null;
    var consultant_avatar = null;
    var consultant = null;
    var customer_fullName = null;
    var customer_avatar = null;
    var customer = null;

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
                    $('.profile-pic').attr('src', '../uploads/customers/' + (data.photo != null?data.photo:'img-1.png'));
                    $('.sendOffer').data('id', data.id_user);
                    $('#converations').empty();
                    $.each(data.conv, function() {
                        $('#converations').append('<tr  class="unread menu row border-bottom"><td class="col-md-6"><span><img src="../uploads/consultants/' + (this.photo != null?this.photo:'img-1.png') + '" alt="user-img" class="img-circle " style="float:left"><span>' + this.pseudo + '</span></span><small style="display:block;color:#D5D5D5">Last Message :   ' + this.LastMSG + '</small></td><td class="col-md-6"><a class="btn btn-sm btn-color waves-effect waves-light float-right" href="javascript:void(0)" onClick="MyWindow=window.open(' + "'" + 'superConversation.php?sender=' + id + '&receiver=' + this.id + '' + "'" + ",'" + id + "-" + this.id + "','width=800,height=500'); return false;" + '"' + '>conversation</a>  </td></tr>');
                    });
                    $('.customer-options').show();
                    $('.address').show();
                    $('.country').show();
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
                    messages: true,
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
                    $('#language').text(data.lang);
                    $('.profile-pic').attr('src', '../uploads/consultants/' + (data.photo != null?data.photo:'consult.png'));
                    $('#converations').empty();
                    $.each(data.conv, function() {
                        $('#converations').append('<tr  class="unread menu row border-bottom"><td class="col-md-6"><span><img src="../uploads/customers/' + (this.photo != null?this.photo:'img-1.png') + '" alt="user-img" class="img-circle " style="float:left"><span>' + this.firstname + ' ' + this.lastname + '</span></span><small style="display:block;color:#D5D5D5"> Last Message :   ' + this.LastMSG + '</small></td><td class="col-md-6"><a class="btn btn-sm btn-color waves-effect waves-light float-right" href="javascript:void(0)" onClick="MyWindow=window.open(' + "'" + 'superConversation.php?sender=' + this.id + '&receiver=' + id + '' + "'" + ",'" + this.id + "-" + id + "','width=800,height=500'); return false;" + '"' + '>conversation</a> </td></tr>');
                    });
                    $('.address').hide();
                    $('.country').hide();
                    $('.customer-options').show();
                    $('#overlay').hide();
                }
            })
        });
        $('.back').click(function() {
            $('.conversations-options').show();
            $('.conversations-actions').show();
            $('.customer-options').hide();
            $('.customer-actions').hide();
            $('.back').hide();
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

    });
</script>
</body>
</html>