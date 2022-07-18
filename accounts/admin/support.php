<?php
$page_name = "support";
include('header.php');
ini_set("display_errors", 1);

if (isset($_POST['contact'])) {
    $message = $_POST['message'];
    $subject = $_POST['subject'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : NULL;

    $stmt = $conn->prepare('INSERT INTO contact_ticket(subject,reason,date,status,id_account,id_customer) VALUES (:sb,:re,NOW(),0,:IDA,:IDC)');
    $stmt->bindParam(':IDC', $_SESSION['id_user'] );
    $stmt->bindParam(':IDA', $_SESSION['id_account']);
    $stmt->bindParam(':sb', $subject);
    $stmt->bindParam(':re', $reason);
    $stmt->execute();
    $affected_rows = $stmt->rowCount();
    if ($affected_rows != 0) {
        $id = $conn->lastInsertId();
        $stmt = $conn->prepare('INSERT INTO contact_messages(message,id_ticket,date,id_sender) VALUES (:msg,:IDT,NOW(),:IDS)');
        $stmt->bindParam(':IDS', $_SESSION['id_user']);
        $stmt->bindParam(':IDT', $id);
        $stmt->bindParam(':msg', $message);
        $stmt->execute();
        $affected_rows = $stmt->rowCount();
    }
    if ($affected_rows != 0) {
        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> Your message is sended succefully </div></div>";
    } else {
        echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> Sending message failed </div></div>";
    }
    unset($_POST);
}
$stmt = $conn->prepare('SELECT ct.*,(case when (SELECT cm.id_sender from contact_messages cm WHERE cm.id_ticket=ct.id_ticket AND ct.status !=2 ORDER by cm.date DESC LIMIT 1)!=:IDC THEN 1 ELSE 0 END) as noresponde FROM  contact_ticket ct WHERE ct.id_customer=:IDC AND ct.id_account = :IDA');
$stmt->bindParam(':IDC', $_SESSION['id_user']);
$stmt->bindParam(':IDA', $_SESSION['id_account']);
$stmt->execute();
$contacts = $stmt->fetchAll();
?>
<style>
    .col-lg-6 {margin-right: auto;margin-left: auto;}
    .form-control {min-height: 30px !important;}
    #message {min-height: 300px!important;}
    #contactform_filter {width: 100%;}
    #conv {overflow-y: scroll;}
    .filterhead {padding: 5px !important;}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card ">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="contactform" class="table table-striped table-hover dt-responsive display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Date</td>
                                <td><?= $trans['Subject'] ?></td>
                                <td><?= $trans['Reason'] ?></td>
                                <td>Status</td>
                                <td>Action</td>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead">Subject</th>
                                <th class="filterhead">Reason</th>
                                <th class="filterhead">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact) { ?>
                                <tr>
                                    <td><?= $contact['id_ticket'] ?>
                                        <?php if ($contact['noresponde'] == 1) { ?>
                                            <div class="notify d-block" style="top:0;right:0">
                                                <span class="heartbit d-block"></span>
                                                <span class="point hide " style="display: block !important;"></span>
                                            </div>
                                        <?php } ?></td>
                                    <td><?= $contact['date'] ?></td>
                                    <td><?= $trans['subject'][$contact['subject']] ?></td>
                                    <td><?= $contact['reason'] ? $trans['reasons'][$contact['subject']][$contact['reason']] : '' ?></td>
                                    <td><?php if ($contact['status'] == 0) '<span class="label label-success">' . $trans['pending'] . '</span>';
                                        else if ($contact['status'] == 1) echo '<span class="label label-warning">' . $trans['processing'] . '</span>';
                                        else echo '<span class="label label-danger">' . $trans['closed'] . '</span>' ?></td>
                                    <td><a href="javascript:void(0)" class="see badge badge-info" data-id="<?= $contact['id_ticket'] ?>" data-status="<?= $contact['status'] ?>">see</a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="modal" id="contact" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title text-center">Contact</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" class="col-md-12">
                    <div class="form-body">
                        <div class="col-12 m-b-10">
                            <label for="subject"><?= $trans['Subject'] ?></label>
                            <select name="subject" id="subject" class="form-control">
                                <?php foreach ($trans['subject'] as $key => $subject) {
                                    if ($key != 4) {
                                        echo '<option value="' . $key . '">' . $subject . '</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                        <div class="col-12 Reason m-b-10" style="display: none;">
                            <label for="Reason"><?= $trans['Reason'] ?></label>
                            <select name="reason" id="Reason" class="form-control"></select>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" id="message" name="message" placeholder="<?php echo utf8_encode($trans["chat"]["type_your_message"]) ?>"></textarea>
                        </div>
                        <div class="form-actions text-center">
                            <button type="submit" name="contact" class="btn btn-primary waves-effect waves-light m-r-10"> <i class="fa fa-check"></i> <?php echo ($trans["chat"]["send"]) ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="messages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title text-center">Messages</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body chat-main-box">
                <div class="chat-right-aside" style="width: 100%;">
                    <ul class="chat-list p-3" id="conv" style="max-height: 500px;">
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row" style="width:100%">
                    <div class="col-9 ">
                        <textarea placeholder="<?php echo utf8_encode($trans["chat"]["type_your_message"]) ?>" class="form-control border-0" id="content"></textarea>
                    </div>
                    <div class="col-3 text-right">
                        <button type="button" id="send_message" class="btn btn-primary btn-sm m-t-10"><i class="fas fa-paper-plane"></i> <?php echo ($trans["chat"]["send"]) ?> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer> <!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>

<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/js/pages/update_password.js"></script>
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/pages/chat.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/moment.js"></script>
<script src="../../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup.min.js"></script>
<script src="../../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup-init.js"></script>

<script>
    var sender = <?= intval($_SESSION['id_user']) ?>;
    var ticket = 0;
    $('#contactform').DataTable({
        dom: '<"toolbar">frtip',
        responsive: true,
        "order": [
            [1, "desc"]
        ],
        orderCellsTop: true,
        fixedHeader: true,
        searching: false,
        initComplete: function() {
            var api = this.api();
            $('.filterhead', api.table().header()).each(function(i) {
                var column = api.column(i + 2);
                var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
                    .appendTo($('#contactform thead tr:eq(1) th').eq(i + 2).empty())
                    .on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function(d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }
    });


    $('.see').click(function() {
        let id = $(this).data('id');
        ticket = id;

        $.ajax({
            url: "functions_ajax.php",
            type: "POST",
            data: {
                type: "getContactConv",
                id: id
            },
            dataType: 'JSON',
            success: function(data) {
                $('#conv').empty();
                if (data.length > 0) {
                    $.each(data, function() {
                        if (this.id_sender == sender) {
                            $('#conv').append(`<li><div class="chat-content"><h5><?= $_SESSION['business_name'] ?></h5><div class="box bg-light-info">${this.message}</div><div class="chat-time">` + moment.utc(this.date).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`);
                        } else {
                            $('#conv').append(`<li class="reverse"><div class="chat-content"><h5>Private chat</h5><div class="box bg-light-info">${this.message}</div><div class="chat-time">` + moment.utc(this.date).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`);
                        }
                    });
                }
                $('#messages').modal('show');
            }
        });
    });
    $(".toolbar").append('<button type="button" class="btn btn-primary float-right m-b-10" data-toggle="modal" data-target="#contact"><?= $trans['newContact']?></button>');
    $('#subject').change(function() {
        let reasons = [];
        switch ($(this).val()) {
            case "2":
                reasons = <?php echo json_encode($trans['reasons'][2]) ?>;
                $('#Reason').empty();
                $.each(reasons, function(i, v) {
                    $('#Reason').append('<option value="' + i + '">' + v + '</option>');
                })
                $('.Reason').show();
                break;
            case "4":
                reasons = <?php echo json_encode($trans['reasons'][4]) ?>;
                $('#Reason').empty();
                $.each(reasons, function(i, v) {
                    $('#Reason').append('<option value="' + i + '">' + v + '</option>');
                })
                $('.Reason').show();
                break;
            default:
                $('.Reason').hide();
                break;
        }
    });
    $('#send_message').click(function() {
        $.ajax({
            url: "functions_ajax.php",
            type: "POST",
            data: {
                type: "sendReponse",
                id: ticket,
                sender: sender,
                message: $('#content').val()
            },
            dataType: 'JSON',
            success: function(data) {
                $('#conv').append(data);
                $('#content').val(null);
            }
        });
    });
</script>