<?php
$page_name = "leads";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT  l.id_lead,l.add_date,l.update_date,l.status,w.name as program,cu.firstname,cu.lastname,cu.country,(SELECT p.company_name FROM publishers p,users u1 WHERE p.id_publisher=u1.id_profile AND u1.id_user=c.id_publisher ) as company_name FROM leads l,websites w,customers cu,users u,contributors c  WHERE cu.id_customer =l.id_customer AND cu.id_account=:id AND  w.id_website = cu.id_website AND u.id_user = l.id_contributor AND c.id_contributor=u.id_profile");
$stmt->bindParam(':id', intval($_SESSION['id_account']));
$stmt->execute();
$leads = $stmt->fetchAll();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <div class="table-responsive">
                <table class="display  nowrap table table-hover table-striped " id="leads" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?= $trans['publisher']['Full_Name'] ?></th>
                            <th><?= $trans['publisher']['Country'] ?></th>
                            <th><?= $trans['publisher']['publisher'] ?></th>
                            <th><?= $trans['publisher']['Advertiser_program'] ?></th>
                            <th><?= $trans['publisher']['Result'] ?></th>
                            <th><?= $trans['publisher']['Creation_date'] ?></th>
                            <th><?= $trans['publisher']['Updated_date'] ?></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="filterhead"><?= $trans['publisher']['Country'] ?></th>
                            <th class="filterhead"><?= $trans['publisher']['publisher'] ?></th>
                            <th class="filterhead"><?= $trans['publisher']['Advertiser_program'] ?></th>
                            <th class="filterhead"><?= $trans['publisher']['Result'] ?></th>
                            <th class="filterhead"><?= $trans['publisher']['Creation_date'] ?></th>
                            <th class="filterhead"><?= $trans['publisher']['Updated_date'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($leads as $lead) {
                            switch ($lead['status']) {
                                case 0:
                                    $status = $trans['publisher']['No_visit'];
                                    break;
                                case 1:
                                    $status = $trans['publisher']['Visit_without_sale'];
                                    break;
                                case 2:
                                    $status = $trans['publisher']['1st_Sale'];
                                    break;

                                default:
                                    # code...
                                    break;
                            }
                        ?>
                            <tr>
                                <td><?= $lead['id_lead'] ?></td>
                                <td><?= $lead['firstname'] . " " . $lead['lastname'] ?></td>
                                <td><?= $trans['countries'][$lead['country']] ?></td>
                                <td><?= $lead['company_name'] ?></td>
                                <td><?= $lead['program'] ?></td>
                                <td><?= $status ?></td>
                                <td><?= $lead['add_date'] ?></td>
                                <td><?= $lead['update_date'] ?></td>
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
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
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
                var column = api.column(i+2);
                var select = $('<select class="form-control" style="min-height:30px;height:30px"><option value=""></option></select>')
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
    $.fn.dataTable.tables({
        visible: true,
        api: true
    }).columns.adjust();
</script>
</body>
</html>>