<?php
$page_name = "publishers";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT p.id_publisher,p.company_name,pa.date_add,pa.date_end,u.id_user FROM publisher_advertiser pa,users u,publishers p WHERE pa.id_advertiser=:id AND  p.id_publisher=u.id_profile AND u.profile = 5");
$stmt->bindParam(':id', intval($_SESSION['id_company']));
$stmt->execute();
$publishers = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT business_name,id_account FROM accounts  WHERE id_account=:id");
$stmt->bindParam(':id', intval($_SESSION['id_company']));
$stmt->execute();
$admin = $stmt->fetchObject();
?>
<style>
    .form-control {min-height: 30px;}
</style>
<div class='card'>
    <div class='card-body'>
        <h4 class="box-title m-b-0"><?= $trans['personel_code'] ?> : <strong><?= substr($admin->business_name, 0, 3) . '-' . $admin->id_account ?></strong></h4>
        <h6 class="text-muted m-b-0 font-13"><?= $trans['you_can_share'] ?>.</h6>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <div class="table-responsive">
                <table class="display  nowrap table table-hover table-striped " id="publishers" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?= $trans['publishers'] ?></th>
                            <th><?= $trans['total_Leads'] ?></th>
                            <th><?= $trans['total_no_visit'] ?></th>
                            <th><?= $trans['total_with_visit'] ?></th>
                            <th><?= $trans['total_with_sale'] ?></th>
                            <th><?= $trans['publisher']['Creation_date'] ?></th>
                            <th><?= $trans['end_date'] ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($publishers as $publisher) {
                            $end = $publisher['date_end'] == null ? date('Y-m-d') : $publisher['date_end'];
                            $stmt = $conn->prepare("SELECT  l.status FROM leads l,websites w,contributors c,users u,accounts a,customers cu WHERE w.id_account=a.id_account AND l.id_contributor=u.id_user AND u.id_profile=c.id_contributor  AND w.id_website=cu.id_website AND c.id_publisher=:id AND cu.id_customer=l.id_customer AND l.add_date BETWEEN :dt1 AND :dt2");
                            $stmt->bindParam(':id', intval($publisher['id_user']));
                            $stmt->bindParam(':dt1', $publisher['date_add']);
                            $stmt->bindParam(':dt2', $end);
                            $stmt->execute();
                            $leads = $stmt->fetchAll();
                            $values = array_count_values(array_column($leads, 'status'));
                        ?>
                            <tr>
                                <td><?= $publisher['id_publisher'] ?></td>
                                <td><?= $publisher['company_name'] ?></td>
                                <td><?= count($leads) ?></td>
                                <td><?= isset($values['0']) ? $values['0'] : 0 ?></td>
                                <td><?= isset($values['1']) ? $values['1'] : 0 ?></td>
                                <td><?= isset($values['2']) ? $values['2'] : 0 ?></td>
                                <td><?= $publisher['date_add'] ?></td>
                                <td><?= $publisher['date_end'] ?></td>
                                <td><a href="javascript:void(0)" class="btn btn-sm waves-effect waves-light btn-color mr-2 programs" data-begin="<?= $publisher['date_add'] ?>" data-end="<?= $end ?>" data-id="<?= $publisher['id_user'] ?>"><i class="mdi mdi-format-list-text m-r-5"></i><?= $trans['programs'] ?></a><a href="javascript:void(0)" class="btn btn-sm waves-effect waves-light btn-color details" data-begin="<?= $publisher['date_add'] ?>" data-end="<?= $end ?>" data-id="<?= $publisher['id_user'] ?>"><i class="mdi mdi-account-group m-r-5"></i><?= $trans['leads'] ?></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>
<div class="modal" id="leadspop" tabindex="-1" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel2"><?= $trans['leads'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped dt-responsive" id="leads" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= $trans['publisher']['Full_Name'] ?></th>
                                <th><?= $trans['publisher']['Country'] ?></th>
                                <th><?= $trans['publisher']['Advertiser_program'] ?></th>
                                <th><?= $trans['publisher']['Result'] ?></th>
                                <th><?= $trans['publisher']['Creation_date'] ?></th>
                                <th><?= $trans['publisher']['Updated_date'] ?></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead"><?= $trans['publisher']['Country'] ?></th>
                                <th class="filterhead"><?= $trans['publisher']['Advertiser_program'] ?></th>
                                <th class="filterhead"><?= $trans['publisher']['Result'] ?></th>
                                <th class="filterhead"><?= $trans['publisher']['Creation_date'] ?></th>
                                <th class="filterhead"><?= $trans['publisher']['Updated_date'] ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal" id="programspop" tabindex="-1" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel3"><?= $trans['programs'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">

                    <table class="display  nowrap table table-hover table-striped dt-responsive" id="programs" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= $trans['publisher']['Full_Name'] ?></th>
                                <th>Status</th>
                                <th><?= $trans['total_Leads'] ?></th>
                                <th><?= $trans['publisher']['Creation_date'] ?></th>
                                <th><?= $trans['publisher']['End_date'] ?></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead">Result</th>
                                <th class="filterhead">Total leads</th>
                                <th class="filterhead"><?= $trans['publisher']['Creation_date'] ?></th>
                                <th class="filterhead"><?= $trans['publisher']['End_date'] ?></th>
                            </tr>
                        </thead>
                    </table>
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
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
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
        $('#overlay').hide();
    });

    var table1 = $('#publishers').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        initComplete: function() {
            var api = this.api();
            $('.filterhead', api.table().header()).each(function(i) {
                var column = api.column(i + 2);
                var select = $('<select class="form-control" style="height: 30px;"><option value=""></option></select>')
                    .appendTo($(this).empty())
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
    var table2 = $('#leads').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        initComplete: function() {
            var api = this.api();
            $('.filterhead', api.table().header()).each(function(i) {
                var column = api.column(i + 2);
                var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
                    .appendTo($(this).empty())
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
    $('.details').click(function() {
        $('#overlay').show();
        let id = $(this).data('id');
        let begin = $(this).data('begin');
        let end = $(this).data('end');
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                type: 'getLeads',
                id: id,
                begin: begin,
                end: end
            },
            success: function(data) {
                $("#leads").dataTable().fnDestroy();
                $('#leads').DataTable({
                    orderCellsTop: true,
                    dom: 'Bfrtip',
                    scrollX: true,
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    data: data,
                    initComplete: function() {
                        var api = this.api();
                        $('.filterhead', api.table().header()).each(function(i) {
                            var column = api.column(i + 2);
                            var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
                                .appendTo($(this).empty())
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
                $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
                $('.dt-button').removeClass('dt-button');
                $('#leadspop').modal('show');
                $('#leads').DataTable().columns.adjust().draw();
                $('#overlay').hide();
            }
        })
    });
    $('.programs').click(function() {
        $('#overlay').show();
        let id = $(this).data('id');
        let begin = $(this).data('begin');
        let end = $(this).data('end');
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                type: 'getPrograms',
                id: id,
                begin: begin,
                end: end
            },
            success: function(data) {
                $("#programs").dataTable().fnDestroy();
                $('#programs').DataTable({
                    orderCellsTop: true,
                    dom: 'Bfrtip',
                    scrollX: true,
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    data: data,
                    initComplete: function() {
                        var api = this.api();
                        $('.filterhead', api.table().header()).each(function(i) {
                            var column = api.column(i + 2);
                            var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
                                .appendTo($(this).empty())
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
                $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
                $('.dt-button').removeClass('dt-button');
                $('#programspop').modal('show');
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
                $('#overlay').hide();
            }
        })
    });
</script>
</body>
</html>>