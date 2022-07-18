<?php
$page_name = "contributors";
ob_start();
include('header.php');

$stmt = $conn->prepare("SELECT  *  FROM contributors WHERE id_publisher=:id");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$contributors = $stmt->fetchAll();
$stmt = $conn->prepare("SELECT  *  FROM publisher_advertiser pa,accounts a WHERE pa.id_publisher=:id AND pa.id_advertiser=a.id_account AND pa.date_end IS NULL");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$advertisers = $stmt->fetchAll();
?>
<style>
    .select2 {width: 100% !important;}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <h3 class="box-title m-b-0"><?= $trans['publisher']['contributors'] ?></h3>
            <hr>
            <div class="table-responsive m-b-40 m-r-0">

                <table class="display  nowrap table table-hover table-striped" id="contributors">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pseudo </th>
                            <th>Email </th>
                            <th><?= $trans['publisher']['Creation_date'] ?></th>
                            <th><?= $trans['publisher']['Advertiser_program'] ?></th>
                            <th><?= $trans['publisher']['End_date'] ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($contributors as $contributor) {
                            $stmt = $conn->prepare("SELECT  *  FROM contributors_programs cp,websites w WHERE  w.id_website=cp.id_advertiserProgram AND cp.id_contributor=:id AND cp.status=1");
                            $stmt->bindParam(':id', intval($contributor['id_contributor']));
                            $stmt->execute();
                            $programs = $stmt->fetchAll();
                        ?>
                            <tr id="<?= $contributor['id_contributor'] ?>">
                                <td><?= $contributor['id_contributor'] ?></td>
                                <td><?= $contributor['pseudo'] ?></td>
                                <td><?= $contributor['email'] ?></td>
                                <td><?= $contributor['date_add'] ?></td>
                                <td><?= implode(',', array_column($programs, 'name')) ?></td>
                                <td><?= $contributor['date_end'] ?></td>
                                <td><?php if (!$contributor['date_end']) { ?>
                                        <a href="#" type="button" data-id="<?= $contributor['id_contributor'] ?>" class="btn btn-sm waves-effect waves-light btn-warning Add mr-1">Add program</a><a href="Contributor.php?id=<?= $contributor['id_contributor'] ?>" type="button" class="btn btn-sm waves-effect waves-light btn-info edit mr-1">Edit</a><a href="#" type="button" data-id="<?= $contributor['id_contributor'] ?>" class="btn btn-sm waves-effect waves-light btn-danger Stop">Stop</a>
                                    <?php } else { ?>
                                        <a href="#" type="button" data-id="<?= $contributor['id_contributor'] ?>" class="btn btn-sm waves-effect waves-light btn-info Continue">Continue</a>
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
</div>
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
                <div class="form-group">
                    <label for="advertiser"><?= $trans['publisher']['Advertiser'] ?></label>
                    <select name="advertiser" id="advertiser" class="form-control">
                        <option></option>
                        <?php foreach ($advertisers as $advertiser) {
                            echo '<option value="' . $advertiser['id_account'] . '">' . $advertiser['business_name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="advertiserPrograms"><?= $trans['publisher']['Advertiser_program'] ?></label>
                    <select name="advertiserPrograms" id="advertiserPrograms" class="form-control select2 ">
                    </select>
                </div>
                <div class="form-group">
                    <label for="advertiserPrograms">Packages</label>
                    <select name="Packages" id="Packages" class="form-control select2 " multiple>
                    </select>
                </div>
                <input type="hidden" class="user" value="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                <button type="button" class="btn btn-primary save"><?php echo ($trans["add"]) ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit contributor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Pseudo">Pseudo</label>
                            <input type="text" name="Pseudo" class="form-control" id="Pseudo">
                        </div>
                        <div class="form-group">
                            <label for="UserName"><?= $trans['publisher']['userName'] ?></label>
                            <input type="text" name="UserName" class="form-control" id="UserName">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" name="email" class="form-control" id="email">
                        </div>
                        <div class="form-group">
                            <label for="advertiserPrograms"><?= $trans['publisher']['Advertiser_program'] ?></label>
                            <select name="advertiserPrograms[]" id="advertiserPrograms2" class="form-control select2 " multiple>
                            </select>
                        </div>
                    </div>
                    <input type="text" name="id" class="form-control" id="id" hidden>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit" class="btn btn-primary waves-effect waves-light m-r-10 "><?php echo ($trans["save"]) ?></button>
                    <button type="reset" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/custom.min.js"></script>

<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script>
    var table2 = $('#contributors').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(document).ready(function() {
        $(".select2").select2();
        $('.dt-button').addClass('btn waves-effect waves-light btn-xm btn-secondary');
        $('.dt-button').removeClass('dt-button');

        $('#contributors').on('click', '.Add', function() {
            let id = $(this).data('id');
            $('.user').val(id);
            $('#editModal').modal('show');
        });
        $('#contributors').on('click', '.Stop', function() {
            Swal.fire({
                title: 'Stop Contributor',
                showCancelButton: true,
                html: 'This action will stop the activity of this contributor. Would you like to continue?',
            }).then((result) => {
                if (result.value) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: 'functions_ajax.php',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: 'stop_contributor',
                            id: id
                        },
                        success: function(data) {
                            table2.row($('#' + id)).data(data).draw();
                        }
                    });
                }
            })
        });
        $('#contributors').on('click', '.Continue', function() {
            Swal.fire({
                title: 'Active Contributor',
                showCancelButton: true,
                html: 'This action will active the activity of this contributor. Would you like to continue?',
            }).then((result) => {
                if (result.value) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: 'functions_ajax.php',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: 'Reprendre_contributor',
                            id: id
                        },
                        success: function(data) {
                            table2.row($('#' + id)).data(data).draw();
                        }
                    })
                }
            })
        });
        $('#contributors').on('click', '.edit', function() {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'get_contributor',
                    id: id
                },
                success: function(data) {
                    $('#Pseudo').val(data.info.pseudo);
                    $('#UserName').val(data.info.username);
                    $('#email').val(data.info.email);
                    $('#password').val(data.info.password);
                    $('#id').val(data.info.id);
                    $.each(data.programs, function() {
                        $('#advertiserPrograms2').append('<option value="' + this.id + '" selected>' + this.name + '</option>');
                    });
                    $('#form').modal('show');
                }
            })
        });
        $("#advertiserPrograms").change(function() {
            let id = $(this).find("option:selected").val();
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'getPackeges',
                    id: id
                },
                success: function(data) {
                    $('#Packages').empty();
                    $.each(data, function() {
                        if (parseInt(this.price) == 0) {
                            price = '<?= $trans['free'] ?>';
                        } else price = this.price + this.currency;
                        $('#Packages').append('<option value="' + this.id_package + '">' + this.title + ' <sup> (' + price + ')</sup></option>');
                    });
                    if (data.length != 1) {
                        $('.pckg').show();
                    }
                }
            });
        });
        $("#advertiser").change(function() {
            let id = $(this).find("option:selected").val();
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'getWebsites_contri',
                    id: id,
                    publisher: <?= $_SESSION['id_user'] ?>
                },
                success: function(data) {
                    $('#advertiserPrograms').empty();
                    $('#advertiserPrograms').append('<option></option>');
                    $.each(data, function() {
                        $('#advertiserPrograms').append('<option value="' + this.id + '">' + this.name + '</option>');
                    });
                }
            })
        });
        $('.save').click(function() {
            Swal.fire({
                title: 'Active Contributor',
                showCancelButton: true,
                html: 'Would you like to save changes?',
            }).then((result) => {
                if (result.value) {
                    let id = parseInt($('.user').val());
                    let programs = $('#advertiserPrograms').val();
                    let packages = $('#Packages').serializeArray();
                    $.ajax({
                        url: 'functions_ajax.php',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: 'setPrograms',
                            id: id,
                            programs: programs,
                            packages: packages
                        },
                        success: function(data) {
                            $('#editModal').modal('hide');
                            table2.row($('#' + id)).data(data).draw();
                        }
                    })
                }
            })
        });
    });
</script>
</body>
</html>>