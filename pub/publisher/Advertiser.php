<?php
$page_name = "Advertisers";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT pp.id, a.id_account,a.business_name,pp.date_add,pp.date_end  FROM publisher_advertiser pp ,accounts a WHERE pp.id_advertiser=a.id_account AND pp.id_publisher=:id");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$ads = $stmt->fetchAll();
?>
<div class="card">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-body">
                <h3 class="box-title m-b-0"><?= $trans['publisher']['Advertisers'] ?></h3>
                <table class="display  nowrap table table-hover table-striped" id="advert">
                    <thead>
                        <tr>
                            <th><?= $trans['publisher']['Advertiser_Name'] ?></th>
                            <th><?= $trans['publisher']['Added_date'] ?></th>
                            <th><?= $trans['publisher']['End_date'] ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($ads as $ad) {
                        ?>
                            <tr id="<?= $ad['id']  ?>">
                                <td><?= $ad['business_name'] ?></td>
                                <td><?= $ad['date_add'] ?></td>
                                <td><?= $ad['date_end'] ?></td>
                                <?php if (!$ad['date_end']) {
                                    echo '<td><a href="#" class="btn btn-sm waves-effect waves-light btn-danger stop" data-id="' . $ad['id'] . '">Stop</a></td>';
                                } else  echo '<td><a href="#" class="btn btn-sm waves-effect waves-light btn-info Renew" data-id="' . $ad['id'] . '">Renew</a></td>';
                                ?>
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
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script>
    var table2 = $('#advert').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('#advert').on('click', '.Renew', function() {
        if (confirm('would you like to renew this advertiser?')) {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'renew_advertiser',
                    id: id,
                    publisher: <?= $_SESSION['id_user'] ?>
                },
                success: function(data) {
                    debugger;
                    let idRow = data.id;
                    table2.row.add(data.table).node().id = idRow;
                    table2.draw();
                }
            })
        }
    });
    $('#advert').on('click', '.stop', function() {
        if (confirm('This action will stop the activity for this advertiser. would you like to continue ?')) {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'Stop_advertiser',
                    id: id,
                    publisher: <?= $_SESSION['id_user'] ?>
                },
                success: function(data) {
                    table2.row($('#' + id)).data(data).draw();
                }
            })
        }
    })
</script>