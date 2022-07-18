<?php
$page_name = "leads";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT  l.id_lead,l.add_date,l.update_date,l.status,w.name as program,c.firstname,c.lastname,c.country  FROM leads l,websites w,customers c  WHERE l.id_contributor=:id AND w.id_website=c.id_website AND l.id_customer=c.id_customer");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$leads = $stmt->fetchAll();
?>
<div class="card">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-body">
                <h3 class="box-title m-b-0"><?php echo ($trans['leads']) ?></h3>
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
</div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/custom.min.js"></script>

<script src="../../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script>
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
                var select = $('<select><option value=""></option></select>')
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
    $(document).ready(function() {
        $('.dt-button').addClass('btn waves-effect waves-light btn-xm btn-secondary');
        $('.dt-button').removeClass('dt-button');
        $.fn.dataTable.tables({
            visible: true,
            api: true
        }).columns.adjust();
    });
</script>