    <?php
    include('../../init.php');
    $stmt = $conn->prepare("SELECT * from customers c left join users u on u.id_profile=c.id_customer where (u.id_user=:id or c.id_customer=:id) and (u.profile=4 or u.profile is null) ");
    $stmt->bindparam(":id",  $_GET['sender']);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_OBJ);

    if ($_GET['receiver'] != 0) {
        $stmt = $conn->prepare("SELECT * from consultants c join users u on u.id_profile=c.id_consultant where u.id_user=:id and u.profile=3 ");
        $stmt->bindparam(":id",  $_GET['receiver']);
        $stmt->execute();
        $consultant = $stmt->fetch(PDO::FETCH_OBJ);
    }


    ?>

    <div class="card" style="height: 70vh; width: 100%;">
        <div class="chat-main-header">
            <div class="p-3 b-b">
                <h4 class="box-title"><span style="float: left"><?php echo  strtolower(isset($consultant->pseudo) ? $consultant->pseudo : 'Default Agent') ?></span><span style="float: right"><?php echo strtolower($customer->firstname) . ' ' . strtolower(($customer->lastname) == 'guest' ? $customer->id_customer : $customer->lastname) ?></span></h4>
            </div>
        </div>
        <div class="chat-texture" style="height: 75%">
            <span id="loading" style="display: none">chargement...</span>
            <div class="chat-rbox" style="height: 100% !important; overflow-y: scroll !important; overflow-x: hidden !important;">
                <ul class="chat-list p-3 chat_container"></ul>
            </div>
        </div>
        <div class="card-body border-top bloc-bottom response-container">
            <div class="row">
                <div class="col-6">
                    <div class="d-flex my-3">
                        <div class="custom-control custom-radio ml-2">
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
                </div>
                <div class="col-9 col-sm-9">
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
    <script>
        var sender_pseudo = "<?php echo $consultant->pseudo ?>";
        var sender_avatar = "<?= $consultant->photo != ''?$consultant->photo:'consult.png' ?>";
        var sender = <?= isset($consultant->id_user) ? $consultant->id_user : 0 ?>;
        var id_group = <?= isset($consultant->id_account) ? $consultant->id_account : $customer->id_account  ?>;
        var consultant_id = <?= isset($consultant->id_consultant) ? $consultant->id_consultant : 0 ?>;
        var sender_role = 3;
        var receiver_fullName = "<?= $customer->firstname . ' ' . $customer->lastname ?>";
        var receiver_avatar = "<?= $customer->photo != ''?$customer->photo:'img-1.png' ?>";
        var receiver = <?= isset($customer->id_user) ? $customer->id_user : (isset($customer->id_customer) ? $customer->id_customer : 0)  ?>;
        var receiver_role = <?= isset($customer->id_user) ? 4 : 7  ?>;
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
                                    if (sender_avatar != '') {
                                        $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    } else {
                                        $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/consult.png" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    }
                                    status = value.status;
                                } else {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../uploads/customers/${(receiver_avatar == " ")? 'img-1.png' : receiver_avatar}" alt="user" /></div></li>`);
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
                                    if (sender_avatar != '') {
                                        $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    } else {
                                        $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/consult.png" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    }
                                    status = value.status;
                                } else {
                                    $(".chat_container").append(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../uploads/customers/${(receiver_avatar == "") ? 'img-1.png' : receiver_avatar}" alt="user" /></div></li>`);
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
                                if (sender_avatar != '') {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                } else {
                                    $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/consult.png" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                }
                                status = value.status;
                            } else {
                                if (receiver_avatar !== '') {
                                    $(".chat_container").prepend('<li id="' + value.id_message + '" class="reverse" ><div class="chat-content" Style="width:50%"><div class="box bg-light-info" style="display:block">' + value.content + '</div><div class="chat-time" style="display:inline-block">' + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + '</div></div><div class="chat-img" ><img src="../uploads/customers/' + receiver_avatar + '" alt="user" /></div></li>');
                                } else {
                                    $(".chat_container").prepend('<li id="' + value.id_message + '" class="reverse" ><div class="chat-content" Style="width:50%"><div class="box bg-light-info" style="display:block">' + value.content + '</div><div class="chat-time" style="display:inline-block">' + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + '</div></div><div class="chat-img" ><img src="../uploads/customers/img-1.png" alt="user" /></div></li>');
                                }
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
                    if (dt.action == "newMessage") {
                        if (dt.sender == sender && dt.receiver == receiver || dt.sender == receiver && dt.receiver == sender) {
                            if (dt.sender == sender) {
                                if (dt.admin == 2) {
                                    $(".chat_container").append('<li><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5>' + business_name + '</h5><div class="admin">' + dt.message + '</div><div class="chat-time">now</div></div></li>').children(':last').fadeIn(1000);
                                } else if (dt.admin == 4) {
                                    $(".chat_container").append(`<li class="reverse"><div class="chat-content"><h5>${business_name} (Private)</h5><div class="admin">${dt.message}</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
                                } else {
                                    if (sender_avatar != '') {
                                        $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    }else{
                                        $(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/consult.png" alt="user" /></div><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`);
                                    }
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