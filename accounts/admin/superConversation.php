    <?php
    include('../../init.php');
    $stmt = $conn->prepare("SELECT * from customers c join users u on u.id_profile=c.id_customer where u.id_user=:id and u.profile=4 ");
    $stmt->bindparam(":id",  $_GET['sender']);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_OBJ);
    $role = 4;
    if (!$customer) {
        $stmt = $conn->prepare("SELECT c.id_customer as id_user,c.* from customers c where c.id_customer =:id");
        $stmt->bindparam(":id",  $_GET['sender']);
        $stmt->execute();
        $customer = $stmt->fetch(PDO::FETCH_OBJ);
        $role = 7;
    }
    $stmt = $conn->prepare("SELECT * from consultants c join users u on u.id_profile=c.id_consultant where u.id_user=:id and u.profile=3 ");
    $stmt->bindparam(":id",  $_GET['receiver']);
    $stmt->execute();
    $consultant = $stmt->fetch(PDO::FETCH_OBJ);
    if ((!$consultant &&  $_GET['receiver']!= 0)|| !$customer) {
        http_response_code(404);
        die();
    }
    ?>
    <link href="../../assets/css/pages/pricing-page.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <link href="../../assets/css/style.min.css" rel="stylesheet">
    <link href="../../assets/css/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/jquery.emojipicker.tw.css">
    <link rel="stylesheet" href="../../assets/css/jquery.emojipicker.css">
    <style>
        .pricing-body {padding: 30px 30px;}
        .pricing-tab {margin: auto;padding: 0 20px;}
        .pricing-body {padding: 30px 30px;}
        .admin {background: #818182;color: white;max-width: 50%;display: inline-block;padding: 8px;margin-bottom: 10px;box-shadow: 0 5px 20px rgba(0, 0, 0, .1);border-radius: .8rem .8rem .8rem 0;}
        .buy-offer {background-color: #ff365b;border-color: #ff365b;min-width: 140px;}
        .info {margin-top: 21px;}
        .price-lable {background-color: #f8d7da !important;top: -50px;color: #721c24;font-weight: 500;opacity: 80%}
        #footer {bottom: 0;background: #514e4e;color: white;width: 300px;border-radius: 7px;margin-left: auto;margin-right: auto;}
        .chat-main-header {background-image: linear-gradient(to right, #ff1d4e, #fca2ab);height: 8%;text-transform: capitalize;}
        title {text-transform: capitalize;}
        .chat-texture {background: #fff;background-image: url(../../assets/images/background.png);}
        .reverse .box {text-align: right !important;position: relative !important;display: block !important;float: right !important;padding: 8px 15px !important;clear: both !important;color: #fff !important;background-color: #fca2ab !important;-webkit-box-shadow: 0 5px 12px 0 rgba(62, 57, 107, 0.36) !important;box-shadow: 0 5px 12px 0 rgba(62, 57, 107, 0.36) !important;border-radius: 7px !important;}
        .reverse .chat-time {float: right !important;clear: both;text-align: right !important;}
        .chat-time {float: left !important;clear: both;display: block;text-align: left !important;}
        .chat-list li.reverse .chat-content {padding-right: 0px !important;padding-left: 0px !important;}
        .box {text-align: left;float: left;color: #6b6f80;clear: both !important;background-color: #FFFFFF;-webkit-box-shadow: 0 7px 12px 0 rgba(62, 57, 107, 0.16);box-shadow: 0 7px 12px 0 rgba(62, 57, 107, 0.16);}
        .box:before {position: absolute;right: -10px;width: 0;height: 0;bottom: 4;content: '';border: 5px solid transparent;border-left-color: #6b6f80;}
        .reverse .box:before {border-left-color: #fca2ab !important;}
        body {overflow: hidden;}
        #loading {position: absolute;width: 80%;text-align: center;}
        .custom-control {margin-left: 25px;font-size: 12px;}
        
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
    .transp {
		background: none !important;
		box-shadow: none !important;
	}
    </style>
    <title><?php echo  $_GET['receiver'] ==0?'Default':ucfirst($consultant->pseudo) . ' ~ ' . ucfirst($customer->firstname) . ' ' . ucfirst($customer->lastname) ?> | RealTime chat supervision</title>
    <title></title>
    <div class="card" style="height: 100%">
        <div class="chat-main-header">
            <div class="p-3 b-b">
                <h4 class="box-title"><span style="float: left"><?php echo  $_GET['receiver'] ==0?'Default':strtolower($consultant->pseudo) ?></span><span style="float: right"><?php echo strtolower($customer->firstname) . ' ' . strtolower($customer->lastname) ?></span></h4>
            </div>
        </div>
        <div class="chat-texture" style="height: 80%">
            <span id="loading" style="display: none">chargement...</span>
            <div class="chat-rbox" style="height: 92% !important;overflow-y:scroll !important ">
                <ul class="chat-list p-3 chat_container"></ul>
            </div>
        </div>
        <div class="card-body border-top bloc-bottom response-container">
            <div class="row">
                <div class="col-2" style="border-right: 1px solid #ddd">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="customRadio1" value="2" name="role" class="custom-control-input" checked>
                        <label class="custom-control-label" for="customRadio1">Admin</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="customRadio2" value="3" name="role" class="custom-control-input">
                        <label class="custom-control-label" for="customRadio2">Expert</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="customRadio3" value="4" name="role" class="custom-control-input">
                        <label class="custom-control-label" for="customRadio3">Private</label>
                    </div>
                </div>
                <div class="col-8">
                    <textarea placeholder="<?php echo ($trans["chat"]["type_your_message"]) ?>" class="form-control border-0 " id="content"></textarea>
                </div>
                <div class="col-2 text-right">
                    <button type="button" id="send_message" class="btn btn-primary  btn-sm m-t-10"><i class="fas fa-paper-plane"></i> <?php echo ($trans["chat"]["send"]) ?> </button>
                </div>
            </div>
        </div>
        <div id="overlay">
            <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
        </div>
    </div>
    <script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
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
    <script>
        var sender_pseudo = "<?= $_GET['receiver'] ==0?'Default':$consultant->pseudo ?>";
        var sender_avatar = "<?= $_GET['receiver'] ==0?'consult.png':$consultant->photo ?>";
        var sender = <?= $_GET['receiver'] ?>;
        var id_group = <?= $customer->id_account ?>;
        var consultant_id = <?= $_GET['receiver']==0?0:$consultant->id_consultant ?>;
        var sender_role = 3;
        var receiver_fullName = "<?= $customer->firstname . ' ' . $customer->lastname ?>";
        var receiver_avatar = "<?= $customer->photo!=""?$customer->photo:'img-1.png' ?>";
        var receiver = <?= $customer->id_user ?>;
        var receiver_role = <?= $role ?>;
        var business_name = '<?= $_SESSION['business_name'] ?>';
        var inversed = '<?= isset($_GET['form']) ? $_GET['form'] : 0 ?>';
        var from = '<?= $_GET['from']  ?>';
        var to = '<?= $_GET['to'] ?>';
        var conn = new WebSocket(wsCurEnv);
        conn.onopen = function(e) {
            conn.send(JSON.stringify({
                command: "attachAccount",
                id_group: id_group,
                role: 2
            }));
        };
    </script>
    <script src="../../assets/js/custom.min.js"></script>
    <script src="../../assets/js/moment.js"></script>
    <script>
        $('#footer').hide();
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
        $("#send_message").click(function() {
            var content = $("#content").val();
            var role = $("input[name='role']:checked").val();
            if (receiver == null || receiver_role == null) {} else if (!content.replace(/\s/g, '').length) {} else {
                sendMessage({
                    command: "openConversation",
                    sender: sender,
                    receiver: receiver
                });
                sendMessage({
                    command: "message",
                    msg: content,
                    sender: sender,
                    sender_role: sender_role,
                    receiver: receiver,
                    receiver_role: receiver_role,
                    account: receiver,
                    consultant_id: consultant_id,
                    id_group: id_group,
                    admin: role
                });
                $(':input[id="content"]').val(null);
            }
        });
        var max = 0;
        var min = 0;
        var periode = [];
        $('.chat-rbox').scroll(function() {
            if ($('.chat-rbox').scrollTop() == 0 && inversed != 1) {
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
                        last_id: min,
                        inversed: inversed
                    },
                    dataType: "json",
                    success: function(dataResult) {
                        if (dataResult.statusCode == 200) {
                            $('.chat-rbox').scrollTop(1);
                            $.each(dataResult.conversations, function(key, value) {
                                if (value.id_message == from) {
                                    periode[0] = moment(value.date_send).format("MMMM Do YYYY");
                                } else if (value.id_message == to) {
                                    periode[1] = moment(value.date_send).format("MMMM Do YYYY");
                                }
                                if (value.origin == "1") {
                                    $(".chat_container").prepend('<li id="' + value.id_message + '"><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5>' + business_name + '</h5><div class="admin">' + value.content + '</div><div class="chat-time">' + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + '</div></div></li>').children(':last').fadeIn(1000);
                                } else if (value.origin == "2") {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><h5>${business_name} (Private)</h5><div class="admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`);
                                } else if (value.sender_role == sender_role) {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    status = value.status;
                                } else {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../uploads/customers/${receiver_avatar}" alt="user" /></div></li>`);
                                }
                                min = value.id_message;
                            });
                            if ($('#from').length == 0 && inversed == 1) {
                                $(`<hr><li id="from" style="display: flex;flex-direction: row;justify-content: center""><div style="border-radius: 7.5px;box-shadow: 0 1px .5px rgba(var(--shadow-rgb),.13);padding: 5px 12px 6px;text-align: center;text-shadow: 0 1px 0 rgba(var(--inverse-rgb),.4);background-color: #e1f3fb;"><span dir="auto" class="_3Whw5">${periode[0]}</span></div></li>`).insertBefore($("#" + from));
                            } else if ($('#to').length == 0 && inversed == 1) {
                                $(`<li id="to" style="display: flex;flex-direction: row;justify-content: center""><div style="border-radius: 7.5px;box-shadow: 0 1px .5px rgba(var(--shadow-rgb),.13);padding: 5px 12px 6px;text-align: center;text-shadow: 0 1px 0 rgba(var(--inverse-rgb),.4);background-color: #e1f3fb;"><span dir="auto" class="_3Whw5">${periode[1]}</span></div></li><hr>`).insertAfter($("#" + to));
                            }
                        }
                    }
                })).done(function() {
                    $('#loading').hide();
                });
            } else if (Math.round($('.chat-list').height() - $('.chat-rbox').height() + 10) == $('.chat-rbox').scrollTop() && inversed == 1) {
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
                        last_id: max,
                        inversed: inversed
                    },
                    dataType: "json",
                    success: function(dataResult) {
                        if (dataResult.statusCode == 200) {
                            $('.chat-rbox').scrollTop($('.chat-rbox').scrollTop() - 10);
                            $.each(dataResult.conversations, function(key, value) {
                                if (value.id_message == from) {
                                    periode[0] = moment(value.date_send).format("MMMM Do YYYY");
                                } else if (value.id_message == to) {
                                    periode[1] = moment(value.date_send).format("MMMM Do YYYY");
                                }
                                if (value.origin == "1") {
                                    $(".chat_container").append('<li id="' + value.id_message + '"><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5>' + business_name + '</h5><div class="admin">' + value.content + '</div><div class="chat-time">' + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + '</div></div></li>').children(':last').fadeIn(1000);
                                } else if (value.origin == "2") {
                                    $(".chat_container").append(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><h5>${business_name} (Private)</h5><div class="admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`);
                                } else if (value.sender_role == sender_role) {
                                    $(".chat_container").append(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    status = value.status;
                                } else {
                                    $(".chat_container").append(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../uploads/customers/${receiver_avatar}" alt="user" /></div></li>`);
                                }
                                max = Math.max(value.id_message, max);
                            });
                            if ($('#from').length == 0 && inversed == 1) {
                                $(`<hr><li id="from" style="display: flex;flex-direction: row;justify-content: center""><div style="border-radius: 7.5px;box-shadow: 0 1px .5px rgba(var(--shadow-rgb),.13);padding: 5px 12px 6px;text-align: center;text-shadow: 0 1px 0 rgba(var(--inverse-rgb),.4);background-color: #e1f3fb;"><span dir="auto" class="_3Whw5">${periode[0]}</span></div></li>`).insertBefore($("#" + from));
                            } else if ($('#to').length == 0 && inversed == 1) {
                                $(`<li id="to" style="display: flex;flex-direction: row;justify-content: center""><div style="border-radius: 7.5px;box-shadow: 0 1px .5px rgba(var(--shadow-rgb),.13);padding: 5px 12px 6px;text-align: center;text-shadow: 0 1px 0 rgba(var(--inverse-rgb),.4);background-color: #e1f3fb;"><span dir="auto" class="_3Whw5">${periode[1]}</span></div></li><hr>`).insertAfter($("#" + to));
                            }
                        }
                    }
                })).done(function() {
                    $('#loading').hide();
                });
            }
        });
        addListner(conn);
        $(document).ready(function() {
            $('#content').emojiPicker({
                categories: '<?= json_encode($trans['categories']) ?>'
            });
            $('.sections').css("height", (($('.chat-rbox').height() / 2) + 10) + "px");

            $.ajax({
                url: "../conversationTrait.php",
                type: "POST",
                data: {
                    action: "getConversation",
                    sender: sender,
                    sender_role: 3,
                    receiver: receiver,
                    receiver_role: receiver_role,
                    last_id: 0,
                    inversed: inversed
                },
                dataType: "json",
                success: function(dataResult) {
                    if (dataResult.statusCode == 200) {
                        $("#send_message").prop("disabled", false);
                        $("#content").prop("disabled", false);
                        $("#content").attr("placeholder", "<?php echo utf8_encode($trans["chat"]["type_your_message"]) ?>");
                        $("#content").val("");
                        $(".chat_container").empty();
                        $('.chat-rbox').scrollTop(0);
                        $.each(dataResult.conversations, function(key, value) {
                            if (value.id_message == from) {
                                periode[0] = moment(value.date_send).format("MMMM Do YYYY");
                            } else if (value.id_message == to) {
                                periode[1] = moment(value.date_send).format("MMMM Do YYYY");
                            }
                            if (value.origin == "1") {
                                $(".chat_container").prepend('<li id="' + value.id_message + '"><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5>' + business_name + '</h5><div class="admin">' + value.content + '</div><div class="chat-time">' + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + '</div></div></li>').children(':last').fadeIn(1000);
                            } else if (value.origin == "2") {
                                $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><h5>${business_name} (Private)</h5><div class="admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`);
                            } else if (value.sender_role == sender_role) {
                                $(".chat_container").prepend('<li id="' + value.id_message + '"><div class="chat-img"><img src="../uploads/consultants/' + sender_avatar + '" alt="user" /></div><div class="chat-content"><div class="box bg-light-info row">' + value.content + '</div><div class="chat-time" style="display:inline-block">' + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + '</div></div></li>');
                                status = value.status;
                            } else {
                                $(".chat_container").prepend('<li id="' + value.id_message + '" class="reverse" ><div class="chat-content"><div class="box bg-light-info" style="display:block">' + value.content + '</div><div class="chat-time" style="display:inline-block">' + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + '</div></div><div class="chat-img" ><img src="../uploads/customers/' + receiver_avatar + '" alt="user" /></div></li>');
                            }
                            max = Math.max(value.id_message, max);
                            min = value.id_message;
                        });
                        if ($('#from').length == 0 && inversed == 1) {
                            $(`<hr><li id="from" style="display: flex;flex-direction: row;justify-content: center""><div style="border-radius: 7.5px;box-shadow: 0 1px .5px rgba(var(--shadow-rgb),.13);padding: 5px 12px 6px;text-align: center;text-shadow: 0 1px 0 rgba(var(--inverse-rgb),.4);background-color: #e1f3fb;"><span dir="auto" class="_3Whw5">${periode[0]}</span></div></li>`).insertBefore($("#" + from));
                        } else if ($('#to').length == 0 && inversed == 1) {
                            $(`<li id="to" style="display: flex;flex-direction: row;justify-content: center""><div style="border-radius: 7.5px;box-shadow: 0 1px .5px rgba(var(--shadow-rgb),.13);padding: 5px 12px 6px;text-align: center;text-shadow: 0 1px 0 rgba(var(--inverse-rgb),.4);background-color: #e1f3fb;"><span dir="auto" class="_3Whw5">${periode[1]}</span></div></li><hr>`).insertAfter($("#" + to));
                        }
                        if (inversed == 1) {
                            $('.chat-rbox').scrollTop(0);
                        } else {
                            $('.chat-rbox').scrollTop(1E10);
                        }
                    }
                    $("#overlay").hide();
				$('.pricing-tab').parent().addClass('transp');

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
                        id_group: id_group,
                        role: 2
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
                    if(dt.action == "affect_guest"){
                        if (dt.id_guest==receiver) {
                            location.href = 'superConversation.php?sender=' + dt.id_guest + '&receiver=' + dt.id_agent;
                        }
                    }
                    if (dt.action == "newMessage") {
                        if (dt.sender == sender && dt.receiver == receiver || dt.sender == receiver && dt.receiver == sender) {
                            if (dt.sender == sender) {
                                if (dt.admin == 2) {
                                    $(".chat_container").append('<li><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5>' + business_name + '</h5><div class="admin">' + dt.message + '</div><div class="chat-time">now</div></div></li>').children(':last').fadeIn(1000);
                                } else if (dt.admin == 4) {
                                    $(".chat_container").append(`<li class="reverse"><div class="chat-content"><h5>${business_name} (Private)</h5><div class="admin">${dt.message}</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                                } else {
                                    $(".chat_container").append('<li><div class="chat-img"><img src="../uploads/consultants/' + sender_avatar + '" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">' + dt.message + '</div><div class="chat-time">now</div></div></li>').children(':last').fadeIn(1000);
                                }
                                $(".conversation-status-" + receiver).html('<span class="text-danger"><small><i class="fa fa-ban"></i><?php echo utf8_encode($trans["not_seen"]) ?></small></span>');
                                $('.chat-rbox').scrollTop(1E10);

                            } else {
                                if (dt.sender == receiver) {
                                    $(".writing_perview").hide();
                                    $(".chat_container").append('<li class="reverse"><div class="chat-content"><div class="box bg-light-info">' + dt.message + '</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../uploads/customers/' + receiver_avatar + '" alt="user" /></div></li>').children(':last').fadeIn(1000);
                                }
                            }
                            $('.chat-rbox').scrollTop(1E10);
				            $('.pricing-tab').parent().addClass('transp');
                        }
                    } else if (dt.action == "closedConnection") {
                        if (dt.id_user == sender) {
                            $('.card').append('<div id="alert_' + sender + '" class="alert alert-danger fade show"><strong><?php echo $trans['side_bar']['logout'] ?>!</strong> ' + sender_pseudo + ' <?php echo $trans['has_deconnected'] ?></div>');
                        }
                        if (dt.id_user == receiver) {
                            $('.card').append('<div id="alert_' + receiver + '" class="alert alert-danger fade show"><strong><?php echo $trans['side_bar']['logout'] ?>!</strong> ' + receiver_fullName + ' <?php echo $trans['has_deconnected'] ?></div>');
                        }
                    } else if (dt.action == "newConnection") {
                        if (dt.id_user == sender) {
                            $('#alert_' + sender).remove();
                        }
                        if (dt.id_user == receiver) {
                            $('#alert_' + receiver).remove();
                        }
                    } else if (dt.action == "connected") {
                        swal.close();
                    }
                }
            }
        }
    </script>