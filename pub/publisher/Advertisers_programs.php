<?php
$page_name = "Advertisers_Programs";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT  pp.id,w.id_website,w.name,w.url,a.business_name,pp.date_start,pp.date_end,pp.status FROM publishers_programs pp JOIN websites w ON w.id_website=pp.id_program,accounts a WHERE w.id_account=a.id_account AND pp.id_publisher=:id");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$ads = $stmt->fetchAll();
?>
<div class="card">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-body">
                <h3 class="box-title m-b-0"><?= $trans['publisher']['Advertisers_Programs'] ?></h3>
                <hr>
                <table class="display  nowrap table table-hover table-striped" id="advert" style="width:100%">
                    <thead>
                        <tr>
                            <th><?= $trans['publisher']['Advertiser_Name'] ?></th>
                            <th><?= $trans['publisher']['Advertiser_Program_name'] ?></th>
                            <th><?= $trans['publisher']['Advertiser_program_URL'] ?></th>
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
                                <td><?= $ad['name'] ?></td>
                                <td><?= $ad['url'] ?></td>
                                <td><?= $ad['date_start'] ?></td>
                                <td><?= $ad['date_end'] ?></td>
                                <?php if ($ad['status'] == 0) {
                                    echo '<td><a href="#" type="button" class="btn btn-sm waves-effect waves-light btn-warning pause m-r-5" data-id="' . $ad['id'] . '">Break</a><a href="#" type="button" class="btn btn-sm waves-effect waves-light btn-danger stop" data-id="' . $ad['id'] . '">Stop</a></td>';
                                } else if ($ad['status'] == 2) echo '<td><a href="#" type="button" class="btn btn-sm waves-effect waves-light btn-info Renew" data-id="' . $ad['id'] . '">Renew</a></td>';
                                else if ($ad['status'] == 1) echo '<td><a href="#" type="button" class="btn btn-sm waves-effect waves-light btn-success Continue m-r-5" data-id="' . $ad['id'] . '">Continue</a><a href="#" type="button" class="btn btn-sm waves-effect waves-light btn-danger stop" data-id="' . $ad['id'] . '">Stop</a></td>';
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

<script src="../../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
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
    $(document).ready(function() {
        $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
        $('.dt-button').removeClass('dt-button');

        $('#advert').on('click', '.stop', function() {
            Swal.fire({
                title: 'Stop program',
                showCancelButton: true,
                html: 'This action will stop the activity of this program. Would you like to continue?',
            }).then((result) => {
                if (result.value) {
                    stop($(this).data('id'));
                }
            })

        });
        $('#advert').on('click', '.pause', function() {
            Swal.fire({
                title: 'Break program',
                showCancelButton: true,
                html: 'This action will Break the activity of this program. Would you like to continue?',
            }).then((result) => {
                if (result.value) {
                    pause($(this).data('id'));
                }
            })
        });
        $('#advert').on('click', '.Continue', function() {
            Swal.fire({
                title: 'Continue program',
                showCancelButton: true,
                html: 'This action will continue the activity of this program. Would you like to continue?',
            }).then((result) => {
                if (result.value) {
                    continu($(this).data('id'));
                }
            })
        });
        $('#advert').on('click', '.Renew', function() {
            Swal.fire({
                title: 'Renew program',
                showCancelButton: true,
                html: 'This action will Renew the activity of this program. Would you like to continue?',
            }).then((result) => {
                if (result.value) {
                    renew($(this).data('id'));
                }
            })
        });
    });

    function renew(id) {
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                action: 'renew_website',
                id: id,
                publisher: <?= $_SESSION['id_user'] ?>
            },
            success: function(data) {
                let idRow = data.id;
                table2.row.add(data.table).node().id = idRow;
                table2.draw();
            }
        })
    }

    function stop(id) {
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                action: 'Stop_website',
                id: id,
                publisher: <?= $_SESSION['id_user'] ?>
            },
            success: function(data) {
                table2.row($('#' + id)).data(data).draw();
            }
        })
    }

    function pause(id) {
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                action: 'pause_website',
                id: id,
                publisher: <?= $_SESSION['id_user'] ?>
            },
            success: function(data) {
                table2.row($('#' + id)).data(data).draw();
            }
        })
    }

    function continu(id) {
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                action: 'continue_website',
                id: id,
                publisher: <?= $_SESSION['id_user'] ?>
            },
            success: function(data) {
                table2.row($('#' + id)).data(data).draw();
            }
        })
    }
</script>