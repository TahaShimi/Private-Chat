<?php
$page_name = "import_history";
include('header.php');

$s1 = $conn->prepare("SELECT * FROM `log` where id_account=:id");
$s1->bindParam(':id', $id_account, PDO::PARAM_INT);
$s1->execute();
$logs = $s1->fetchAll();
$s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.* FROM `offers` o join `packages` p on p.id_package=o.id_package WHERE o.id_account = :ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->execute();
$offers = $s1->fetchAll();
$ids = array_column($offers, 'id_offer');
$ids = array_combine($ids, array_column($offers, 'title'));
?>
<div class="card " id="table">
    <div class="card-body">
        <div class="table-responsive m-b-40 m-r-0">
            <table class="display  nowrap table table-hover table-striped dt-responsive" id="logs-dtable" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo ($trans["customers_number"]) ?></th>
                        <th><?php echo ($trans["side_bar"]["offers"]) ?></th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log) {
                        $offr = json_decode($log['offers']);
                        $msg = '';
                        if (!empty($offr)) {
                            foreach ($offr as $key => $item) {
                                if (array_key_exists($item, $ids)) {
                                    if ($key != 0) $msg .= ',';
                                    $msg .= $ids[$item] . ' ';
                                }
                            }
                        } else {
                            $msg = $trans["no_offers"];
                        }
                    ?>
                        <tr>
                            <td><?php echo $log['id_action']  ?></td>
                            <td><?= $log['description'] != '' ? $log['description'] : 0 ?></td>
                            <td><?php echo $msg ?></td>
                            <td><?php echo $log['action_date'] ?></td>
                            <td><a href="javascript:void(0)" type="button " class="seeModal btn btn-sm btn-color waves-effect waves-light" id="<?php echo $log['id_action']  ?>"><i class=" mdi mdi-download m-r-5"></i> <?php echo $trans['download']  ?></a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="overlay" style="display: none">
        <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
    </div>
</div>
</div>
</div>
<div id="myModal" class="hide">
    <div class="modal-dialog" role="document" style="min-width: 80%;max-height: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><?php echo ($trans["import_history"]) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="display  nowrap table table-hover table-striped dt-responsive" id="customers-dtable" style="width:100%">
                    <thead>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox checkbox-info form-check">
                                    <input class="custom-control-input" name="select_all" value="" id="select-all-customers" type="checkbox" />
                                    <label class="custom-control-label" for="select-all-customers"></label>
                                </div>
                            </th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["email"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["country"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["phone"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?></th>
                            <th><?php echo ($trans["admin"]["customers"]["customers_table"]["created_at"]) ?></th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

</div>
<!-- footer -->
<!-- ============================================================== -->
<footer class="footer">© 2019 Private chat by Diamond services</footer>
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
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {
        $('.seeModal').on('click', function() {
            if (confirm('<?php echo $trans['confirm_download'] ?>')) {
                $('#overlay').show();
                $.when($.ajax({
                    url: 'functions_ajax.php',
                    type: 'post',
                    data: {
                        type: 'getImported',
                        id: $(this).attr('id')
                    },
                    dataType: 'json',
                    success: function(data) {

                        table.clear();
                        table.rows.add(data).draw();
                        $('.buttons-excel').click();
                    }

                })).done(function() {
                    $('#overlay').hide();
                });
            }
        });
        $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
        $('.dt-button').removeClass('dt-button');
    });
    var table = $('#logs-dtable').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
</script>