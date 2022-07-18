<?php
ini_set("display_errors", 1);
$page_name = "Contracts";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT * FROM  publishers p,accounts a,publisher_advertiser pa,users u WHERE p.id_publisher=u.id_profile AND u.id_user=pa.id_publisher AND a.id_account=pa.id_advertiser");
$stmt->execute();
$contrats = $stmt->fetchAll();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-body">
                <h3 class="box-title m-b-0">Contracts</h3>
                <hr>
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="contracts">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= $trans['publisher']['Advertiser'] ?></th>
                                <th><?= $trans['publisher']['publisher'] ?> </th>
                                <th><?= $trans['publisher']['Added_date'] ?></th>
                                <th><?= $trans['publisher']['End_date'] ?></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($contrats) {
                                foreach ($contrats as $contrat) {
                            ?>
                                    <tr>
                                        <td><?= $contrat['id'] ?></td>
                                        <td><?= $contrat['business_name'] ?></td>
                                        <td><?= $contrat['company_name'] ?></td>
                                        <td><?= $contrat['date_add'] ?></td>
                                        <td><?= $contrat['date_end'] ?></td>
                                        <td><a href="javascript:void(0)" class="programs" data-publisher="<?= $contrat['id_publisher'] ?>" data-advertiser="<?= $contrat['id_advertiser'] ?>">See programs</a></td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="prg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Programs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="programs">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= $trans['publisher']['Program'] ?></th>
                                <th><?= $trans['publisher']['Added_date'] ?></th>
                                <th><?= $trans['publisher']['End_date'] ?></th>
                                <th>status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <input type="text" class="form-control " name="id" id="idAff" hidden>

            <div class="modal-footer"><button type="reset" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../assets/js/custom.min.js"></script>
<script>
    var table2 = $('#programs').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table1 = $('#contracts').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.programs').click(function() {
        let publisher = $(this).data('publisher');
        let advertiser = $(this).data('advertiser');
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                action: 'get_Programs',
                publisher: publisher,
                advertiser: advertiser
            },
            success: function(data) {
                table2.clear();
                table2.rows.add(data).draw();
                $('#prg').modal('show');

            }
        })
    });
</script>