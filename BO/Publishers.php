<?php
$page_name = "publishers";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT  *  FROM publishers");
$stmt->execute();
$publishers = $stmt->fetchAll();

?>
<style>
    .select2 {
        width: 100% !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <h3 class="box-title m-b-0"><?= $trans['publishers'] ?></h3>
            <hr>
            <table class="display  nowrap table table-hover table-striped" id="publishers">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= $trans['publisher']['Company_name'] ?></th>
                        <th>Email </th>
                        <th><?= $trans['publisher']['Country'] ?> </th>
                        <th><?= $trans['publisher']['Creation_date'] ?></th>
                        <th><?= $trans['publisher']['End_date'] ?></th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($publishers as $publisher) {
                    ?>
                        <tr id="<?= $publisher['id_publisher'] ?>">
                            <td><?= $publisher['id_publisher'] ?></td>
                            <td><?= $publisher['company_name'] ?></td>
                            <td><?= $publisher['contact_email'] ?></td>
                            <td><?= $trans['countries'][$publisher['country']] ?></td>
                            <td><?= $publisher['date_add'] ?></td>
                            <td><?= $publisher['date_end'] ?></td>
                            <td>
                                <?php if ($publisher['active'] == 1) { ?>
                                    <span class="badge badge-pill badge-success">Approuved</span>
                                <?php } else { ?>
                                    <span class="badge badge-pill badge-danger">Not Approuved</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if (!$publisher['date_end']) {
                                    if ($publisher['active'] == 0) {
                                ?>
                                        <a href="javascript:void(0)" class=" approve badge badge-pill badge-info" data-id="<?= $publisher['id_publisher'] ?>">Approve</a>
                                    <?php } ?>
                                    <a class="badge badge-pill badge-info" href="publisher.php?id=<?= $publisher['id_publisher'] ?>">Edit</a>
                                    <a class="badge badge-pill badge-danger Stop" href="#" data-id="<?= $publisher['id_publisher'] ?>">Stop</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= $trans['publisher']['Add_program'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="firstname"> <?php echo ($trans["first_name"]) ?> : <span class="danger">*</span> </label>
                            <input type="text" class="form-control required" id="firstname" name="firstname" placeholder="Enter First Name"> </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lastname"><?php echo ($trans["last_name"]) ?> : </label>
                            <input type="text" class="form-control " id="lastname" name="lastname" placeholder="Enter Last Name"> </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label text-right"><?php echo ($trans["phone"]) ?> :<span class="danger">*</span> </label>
                        <div class="">
                            <input name="phone" type="tel" id="phone" class="form-control" style="width:100%" placeholder="Enter Phone Number">
                            <span id="valid-msg" class="hide text-success">� Valid</span>
                            <span id="error-msg" class="hide text-danger">✗ Invalid number</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="country"> <?php echo ($trans["country"]) ?> : <span class="danger">*</span> </label>
                            <select name="country" id="country" class="form-control select-search country form-control-line">
                                <?php
                                foreach ($countries as $key => $country) {
                                    echo '<option value="' . $key . '">' . $country . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email"><?php echo ($trans["email"]) ?> : <span class="danger">*</span></label>
                            <input type="text" class="form-control " id="email" name="email" placeholder="Enter Your Email"> </div>
                    </div>
                </div>
                <input type="hidden" class="user" value="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                <button type="button" class="btn btn-primary save"><?php echo ($trans["save"]) ?></button>
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
<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/custom.min.js"></script>
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>

<script src="../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script>
    var table2 = $('#publishers').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(document).ready(function() {
        $('.edit').click(function() {
            let id = $(this).data('id');
            $('.user').val(id);
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'getInfo',
                    id: id
                },
                success: function(data) {
                    $('#firstname').val(data.first_name);
                    $('#lastname').val(data.last_name);
                    $('#country option[value=' + data.country + ']').attr('selected', 'selected');
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                }
            })
            $('#editModal').modal('show');

        });

        $('.Stop').click(function() {
            let id = $(this).data('id');
            if (confirm('this action will stop all activity of the publisher. you want to continue?')) {
                $.ajax({
                    url: 'functions_ajax.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(data) {
                        if (data == 1) {
                            table2.rows($('#' + id)).remove().draw();
                        }
                    }
                })
            }
        });
        $('.approve').click(function() {
            let id = $(this).data('id');
            if (confirm('You really want to approve this publisher ?')) {
                $.ajax({
                    url: 'functions_ajax.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'approve_publisher',
                        id: id
                    },
                    success: function(data) {
                        if (data == 1) {
                            location.reload();

                        }
                    }
                })
            }
        });
        $('.save').click(function() {
            let id = parseInt($('.user').val());
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'setChanges',
                    id: id,
                    firstname: $('#firstname').val(),
                    lastname: $('#lastname').val(),
                    country: $('#country option:selected').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                },
                success: function(data) {
                    $('#editModal').modal('hide');
                    table2.row($('#' + id)).data(data).draw();
                }
            })
        })
    })
</script>
</body>

</html>