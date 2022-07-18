<?php
$page_name = "contributor";
ob_start();
include('header.php');
?>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<div class="row">
    <?php
    $id_contributor = intval($_GET['id']);

    if (isset($_POST['save1'])) {
        $Pseudo = $_POST['Pseudo'];
        $UserName = $_POST['UserName'];
        $email = $_POST['email'];
        $stmt1 = $conn->prepare("UPDATE contributors SET `pseudo`=:tt ,`username`=:urd,`email`=:em WHERE id_contributor=:id");
        $stmt1->bindParam(':tt', $Pseudo);
        $stmt1->bindParam(':urd', $UserName);
        $stmt1->bindParam(':em', $email);
        $stmt1->bindParam(':id', $id_contributor);
        $stmt1->execute();
    }
    $stmt = $conn->prepare("SELECT * FROM contributors  WHERE id_contributor = :ID");
    $stmt->bindParam(':ID', $id_contributor, PDO::PARAM_INT);
    $stmt->execute();
    $contributor = $stmt->fetchObject();
    $stmt2 = $conn->prepare("SELECT w.name,cp.id_advertiserProgram ,cp.id,cp.status,cp.date_add,cp.date_end FROM contributors_programs cp,websites w WHERE cp.id_contributor=:id AND w.id_website=cp.id_advertiserProgram");
    $stmt2->bindParam(':id', $id_contributor, PDO::PARAM_INT);
    $stmt2->execute();
    $programs = $stmt2->fetchAll();
    ?>
    <div class="col-lg-12 col-xlg-9 col-md-12">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" data-toggle="tab" href="#general_informations" role="tab"><?php echo ($trans["general_informations"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Programs') {echo "active";} ?>" data-toggle="tab" href="#Programs" role="tab"><?= $trans['programs'] ?></a> </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" id="general_informations" role="tabpanel">
                    <div class="card-body">
                        <form action="" id="contributorForm" method="POST">
                            <div class="form-group">
                                <label for="Pseudo">Pseudo</label>
                                <input type="text" name="Pseudo" class="form-control" id="Pseudo" value="<?= $contributor->pseudo ?>">
                            </div>
                            <div class="form-group">
                                <label for="UserName"><?= $trans['publisher']['userName'] ?></label>
                                <input type="text" name="UserName" class="form-control" id="UserName" value="<?= $contributor->username ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" name="email" class="form-control" id="email" value="<?= $contributor->email ?>">
                            </div>
                            <button type="submit" name="save1" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["save"]) ?></button>
                            <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Programs') {echo "active";} ?>" id="Programs" role="tabpanel">
                    <div class="card-body">
                        <div class="table-responsive m-b-40 m-r-0">
                            <table class="display  nowrap table table-hover table-striped" id="ProgramsTab">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?= $trans['programs'] ?></th>
                                        <th><?= $trans['publisher']['status'] ?> </th>
                                        <th><?= $trans['publisher']['Added_date'] ?></th>
                                        <th><?= $trans['publisher']['End_date'] ?></th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($programs) {
                                        foreach ($programs as $program) {
                                            switch ($program['status']) {
                                                case 0:
                                                    $result = 'Ended';
                                                    break;
                                                case 1:
                                                    $result = 'Active';
                                                    break;
                                                case 2:
                                                    $result = 'Paused';
                                                    break;
                                            }
                                    ?>
                                            <tr id="<?= $program['id'] ?>">
                                                <td><?= $program['id_advertiserProgram'] ?></td>
                                                <td><?= $program['name'] ?></td>
                                                <td><?= $result ?></td>
                                                <td><?= $program['date_add'] ?></td>
                                                <td><?= $program['date_end'] ?></td>
                                                <?php if ($program['status'] == 1) { ?>
                                                    <td><a href="#" class="break badge badge-pill badge-info"  data-id="<?= $program['id'] ?>">break</a><a href="#" class="stop badge badge-pill badge-danger"  data-id="<?= $program['id'] ?>">Stop</a></td>
                                                <?php } else if ($program['status'] == 2) { ?>
                                                    <td><a href="#" class="continue badge badge-pill badge-info"  data-id="<?= $program['id'] ?>">Continue</a><a href="#" class="stop badge badge-pill badge-danger"  data-id="<?= $program['id'] ?>">Stop</a></td>
                                                <?php }
                                                if ($program['status'] == 0) echo '<td></td>' ?>
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
    </div>
</div>
</div>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<script>
    var table2 = $('#ProgramsTab').DataTable({
        dom: '<"toolbar d-inline bank">frtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('#ProgramsTab').on('click', '.break', function() {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'pause_program',
                    id: id                    
                },
                success: function(data) {
                    table2.row($('#' + id)).data(data).draw();
                }
            })
        });
        $('#ProgramsTab').on('click', '.continue', function() {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'continue_program',
                    id: id                    
                },
                success: function(data) {
                    table2.row($('#' + id)).data(data).draw();
                }
            })
        });
        $('#ProgramsTab').on('click', '.stop', function() {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'stop_program',
                    id: id                    
                },
                success: function(data) {
                    table2.row($('#' + id)).data(data).draw();
                }
            })
        });
</script>