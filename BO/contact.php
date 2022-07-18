<?php
$page_name = "contact";
include('header.php');
ini_set("display_errors", 1);
$stmt = $conn->prepare('SELECT ct.*,c.business_name,(case when (SELECT cm.id_sender from contact_messages cm WHERE cm.id_ticket=ct.id_ticket AND ct.status !=2 ORDER by cm.date DESC LIMIT 1)!=:idu THEN 1 ELSE 0 END) as noresponde FROM  contact_ticket ct,accounts c ,users u WHERE  u.id_user=ct.id_customer AND c.id_account=u.id_profile AND u.profile=2 ');
$stmt->bindParam(':idu', $_SESSION['id_user']);
$stmt->execute();
$contacts = $stmt->fetchAll();
?>
<link href="../assets/css/pages/chat-app-page.css" rel="stylesheet">
<link href="../assets/css/custom.css" rel="stylesheet">
<style>
    #conv {
        overflow-y: scroll;
    }
    .form-control {
        min-height: 30px!important;
    }
    .filterhead {
        padding: 5px !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card ">
            <div class="card-body">
                <table id="contactform" class="table table-striped table-hover dt-responsive display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Customer</td>
                            <td>Subject</td>
                            <td>Reason</td>
                            <td>Status</td>
                            <td>Date</td>
                            <td>Action</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="filterhead">customer</th>
                            <th class="filterhead">Subject</th>
                            <th class="filterhead">Reason</th>
                            <th class="filterhead">Status</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact) { ?>
                            <tr id="<?= $contact['id_ticket'] ?>">
                                <td><?= $contact['id_ticket'] ?>
                                    <?php if ($contact['noresponde'] == 1) { ?>
                                        <div class="notify d-block" style="top:0;right:0">
                                            <span class="heartbit show" id="flushing-message-<?= $contact['id_ticket'] ?>"></span>
                                            <span class="point show"  id="stable-message-<?= $contact['id_ticket'] ?>"></span>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td><?= $contact['business_name']  ?></td>
                                <td><?= $trans['subject'][$contact['subject']] ?></td>
                                <td><?= $contact['reason'] ? $trans['reasons'][$contact['subject']][$contact['reason']] : '' ?></td>
                                <td id="status-<?= $contact['id_ticket'] ?>"><?php if ($contact['status'] == 0) echo '<span class="label label-success">' . $trans['pending'] . '</span>';
                                    else if ($contact['status'] == 1) echo '<span class="label label-warning">' . $trans['processing'] . '</span>';
                                    else echo '<span class="label label-danger">' . $trans['closed'] . '</span>' ?></td>
                                <td><?= $contact['date'] ?></td>
                                <td><a href="javascript:void(0)" class="see badge badge-info" data-id="<?= $contact['id_ticket'] ?>" data-status="<?= $contact['status'] ?>" data-name="<?= $contact['business_name'] ?>" data-subject="<?= $trans['subject'][$contact['subject']] ?>" data-reason="<?= $contact['reason'] ? $trans['reasons'][$contact['subject']][$contact['reason']] : '' ?>">see</a></td>
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
<div class="modal" id="messages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">
                    <h4 class="box-title subject m-r-10"></h4> : <span class="text-muted reason m-l-10"></span>
                </h4>
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
                    <div class="col-2 border-right">
                        <button class="btn btn-success btn-sm m-t-10 solved">Solved <i class="fa fa-check"></i></button>
                    </div>
                    <div class="col-7 ">
                        <textarea placeholder="Write here your responce to customer" class="form-control border-0" id="content"></textarea>
                    </div>
                    <div class="col-3 text-right">
                        <button type="button" id="send_message" class="btn btn-primary btn-sm m-t-10"><i class="fas fa-paper-plane"></i> <?php echo ($trans["chat"]["send"]) ?> </button>
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
<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/node_modules/popper/popper.min.js"></script>
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Menu sidebar -->
<script src="../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<!--Custom JavaScript -->
<script src="../assets/js/custom.min.js"></script>
<script src="../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<!-- This is data table -->
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script src="../assets/js/moment.js"></script>
<script>
    var sender = <?= intval($_SESSION['id_user']) ?>;
    var ticket = 0;
    var table = $('#contactform').DataTable({
        dom: '<"toolbar">frtip',
        "order": [[ 5, "desc" ]],
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        searching: false,
        initComplete: function() {
            var api = this.api();
            $('.filterhead', api.table().header()).each(function(i) {
                var column = api.column(i + 1);
                var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
                .appendTo($('#contactform thead tr:eq(1) th').eq(i + 1).empty())
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
        let name = $(this).data('name');
        let subject = $(this).data('subject');
        let reason = $(this).data('reason');
        if ($(this).data('status') == 2) {
            $('#send_message').prop('disabled', true);
            $('#content').prop('disabled', true);
            $('.solved').prop('disabled', true);
        } else {
            $('#send_message').prop('disabled', false);
            $('#content').prop('disabled', false);
            $('.solved').prop('disabled', false);
        }
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
                            $('#conv').append(`<li><div class="chat-content"><h5>Private chat</h5><div class="box bg-light-info">${this.message}</div><div class="chat-time">${this.date }</div></div></li>`);
                        } else {
                            $('#conv').append(`<li class="reverse"><div class="chat-content"><h5>${name}</h5><div class="box bg-light-info">${this.message}</div><div class="chat-time">` + this.date + `</div></div></li>`);
                        }
                    });
                    $('.subject').text(subject);
                    $('.reason').text(reason);
                }
                $('#messages').modal('show');
            }
        });
    });
    $('.solved').click(function() {
        Swal.fire({
            title: "Problem Solved",
            text: "Is this problem solved ?",
            type: 'question',
            iconHtml: '?',
            showCancelButton: true,
            confirmButtonText: "Solved",
            confirmButtonColor: '#fec107',
            cancelButtonText: "<?php echo ($trans["package_alert"]["cancel"]) ?>",
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "functions_ajax.php",
                    type: "POST",
                    data: {
                        type: "Solved",
                        id: ticket
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        if (data == 1) {
                            $('#status-' + ticket).html('<span class="label label-danger"><?= $trans['closed'] ?></span>');
                            $("#new-message-" + ticket).css("display", "none");
                            $("#flushing-message-" + ticket).css("display", "none");
                            $("#stable-message-" + ticket).css("display", "none");
                        }
                        $('#messages').modal('hide');
                    }
                });
            }
        });
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
                $("#new-message-" + ticket).css("display", "none");
                $("#flushing-message-" + ticket).css("display", "none");
                $("#stable-message-" + ticket).css("display", "none");
                $('#status'+ticket).text('<span class="label label-warning"><?= $trans['processing'] ?></span>');
            }
        });
    });
</script>