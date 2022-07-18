<?php
$page_name = "edit_package";
ob_start();
include('header.php');
$msgRe = (isset($_GET["cde"])) ? $_GET["cde"] : null;
?>
<link href="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<style>
    .error {
        font-size: 11px !important;
        color: #e46a76 !important;
    }

    .custom-radio {
        margin-top: 35px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .custom-checkbox {
        margin-top: 35px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
</style>

<?php
if (isset($_GET["id"])) {
    $packageId = $_GET["id"];
} else {
    header('Location: packages.php?cde=202');
    exit();
}
if (isset($_POST["add"])) {
    $price = $_POST["price"];
    $currency = $_POST["currency"];
    $stmt1 = $conn->prepare("SELECT * FROM `packages_price` WHERE id_package=:id AND currency=:cu");
    $stmt1->bindParam(':cu', $currency, PDO::PARAM_STR);
    $stmt1->bindParam(':id', $packageId, PDO::PARAM_STR);
    $stmt1->execute();
    $pack = $stmt1->fetchObject();
    if ($pack) {
        $stmt1 = $conn->prepare("UPDATE `packages_price` SET `date_end`= NOW() WHERE id_package=:id AND currency=:cu");
        $stmt1->bindParam(':cu', $currency, PDO::PARAM_STR);
        $stmt1->bindParam(':id', $packageId, PDO::PARAM_INT);
        $stmt1->execute();
    }
    if (isset($_POST["primary"]) || intval($pack->primary) == 1) {
        $primary = 1;
        $stmt1 = $conn->prepare("UPDATE `packages_price` SET `primary`= 0 WHERE id_package=:id");
        $stmt1->bindParam(':id', $packageId, PDO::PARAM_STR);
        $stmt1->execute();
    } else  $primary = 0;
    $stmt1 = $conn->prepare("INSERT INTO `packages_price`(`id_package`, `price`, `currency`, `date_start`, `primary`) 
                                                 VALUES (:id,:pr,:cu,NOW(),:pry)");
    $stmt1->bindParam(':id', $packageId, PDO::PARAM_STR);
    $stmt1->bindParam(':pr', $price, PDO::PARAM_STR);
    $stmt1->bindParam(':cu', $currency, PDO::PARAM_STR);
    $stmt1->bindParam(':pry', $primary, PDO::PARAM_INT);
    $stmt1->execute();
}
if (isset($_POST['update'])) {
    $price = $_POST['price'];
    $currency = $_POST['currency'];
    if (isset($_POST["primary"])) {
        $primary = 1;
        $stmt1 = $conn->prepare("UPDATE `packages_price` SET `primary`= 0 WHERE id_package=:id");
        $stmt1->bindParam(':id', $packageId, PDO::PARAM_STR);
        $stmt1->execute();
    } else  $primary = 0;
    $id = intval($_POST['id']);
    $stmt1 = $conn->prepare("UPDATE `packages_price` SET `date_end`= NOW() WHERE id=:id");
    $stmt1->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt1->execute();
    $stmt1 = $conn->prepare("INSERT INTO `packages_price`(`id_package`, `price`, `currency`, `date_start`, `primary`) 
                                                 VALUES (:id,:pr,:cu,NOW(),:pry)");
    $stmt1->bindParam(':id', $packageId, PDO::PARAM_INT);
    $stmt1->bindParam(':pr', $price, PDO::PARAM_STR);
    $stmt1->bindParam(':cu', $currency, PDO::PARAM_STR);
    $stmt1->bindParam(':pry', $primary, PDO::PARAM_INT);
    $stmt1->execute();
}
$s1 = $conn->prepare("SELECT * FROM `websites` WHERE id_account = :ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->execute();
$websites = $s1->fetchAll();

$s2 = $conn->prepare("SELECT * from packages p left join translations t on t.table='packages' and t.id_element=:IDP where id_package=:IDP");
$s2->bindParam(":IDP", $packageId, PDO::PARAM_INT);
$s2->execute();
$package = $s2->fetch();

$s2 = $conn->prepare("SELECT * from packages_price pp,packages p WHERE pp.id_package=:IDP AND p.id_package =pp.id_package ORDER BY pp.date_end ASC , pp.primary DESC ");
$s2 = $conn->prepare("SELECT * from packages_price pp,packages p WHERE pp.id_package=:IDP AND p.id_package =pp.id_package ORDER BY pp.date_end ASC , pp.primary DESC ");
$s2->bindParam(":IDP", $packageId, PDO::PARAM_INT);
$s2->execute();
$oldPrices = $s2->fetchAll();
if (!$package) {
    header('Location: packages.php?cde=203');
    exit();
}
$parts = explode('-', $package["start_date"]);
$frontSideStartDate = $parts[1] . '/' . $parts[2] . '/' . $parts[0];

$s3 = $conn->prepare("SELECT count(*) as sails_count from transactionsc where id_package=:IDP");
$s3->bindParam(":IDP", $packageId, PDO::PARAM_INT);
$s3->execute();
$sailsCount = $s3->fetch();
if (intval($sailsCount["sails_count"]) > 0) {
    header('Location: packages.php?cde=201');
    exit();
} else {
    if (isset($_POST['update-package'])) {
        $title = (isset($_POST['title']) && $_POST['title'] != '') ? htmlspecialchars($_POST['title']) : $package["title"];
        $messagesCount = (isset($_POST['messagesCount']) && $_POST['messagesCount'] != '') ? htmlspecialchars($_POST['messagesCount']) : $package["messages"];
        $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : $package["id_website"];
        if ($website == 0) {
            $website = NULL;
        }
        $datec = date('Y-m-d', strtotime('now'));

        $dateStart = (isset($_POST['dateStart']) && $_POST['dateStart'] != '') ? htmlspecialchars($_POST['dateStart']) : $package["dateStart"];
        $parts = explode('/', $dateStart);
        $startDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
        $visible = isset($_POST['visible']) ? 1 : 0;
        $private = isset($_POST['private']) ? 0 : 1;

        $stmt1 = $conn->prepare("UPDATE `packages` set `title`=:ti,  `messages`=:msg,
                                                     `status`=1, `public`=:pr, `id_website`=:iw, `start_date`=:sd, `updated_at`=NOW(),`visible`=:vs where id_package=:IDP");
        $stmt1->bindParam(':ti', $title, PDO::PARAM_STR);
        $stmt1->bindParam(':msg', $messagesCount, PDO::PARAM_INT);
        $stmt1->bindParam(':iw', $website, PDO::PARAM_INT);
        $stmt1->bindParam(':sd', $startDate, PDO::PARAM_STR);
        $stmt1->bindParam(':IDP', $packageId, PDO::PARAM_INT);
        $stmt1->bindParam(':pr', $private, PDO::PARAM_INT);
        $stmt1->bindParam(':vs', $visible, PDO::PARAM_INT);
        $stmt1->execute();
        $affected_rows = $stmt1->rowCount();

        if ($affected_rows != 0) {
            if (isset($_POST['title_fr'])) {
                $titleFr = $_POST['title_fr'];
                if ($package["content"] != null) {
                    $stmtl = $conn->prepare("UPDATE translations set content=:ct where `table` like'packages' and lang like 'fr' and id_element=:IDP");
                    $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
                    $stmtl->bindParam(':IDP', $packageId, PDO::PARAM_INT);
                    $stmtl->execute();
                } else {
                    $table = "packages";
                    $column = "title";
                    $lang = "fr";
                    $stmtl = $conn->prepare("INSERT INTO `translations`(`content`, `table`, `column`, `lang`, `id_element`) VALUES (:ct,:tb,:cl,:lg,:ie)");
                    $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
                    $stmtl->bindParam(':tb', $table, PDO::PARAM_STR);
                    $stmtl->bindParam(':cl', $column, PDO::PARAM_STR);
                    $stmtl->bindParam(':lg', $lang, PDO::PARAM_STR);
                    $stmtl->bindParam(':ie', $packageId, PDO::PARAM_INT);
                    $stmtl->execute();
                }
            }
            $s2 = $conn->prepare("SELECT * from packages p left join translations t on t.table='packages' and t.id_element=:IDP where id_package=:IDP");
            $s2->bindParam(":IDP", $packageId, PDO::PARAM_INT);
            $s2->execute();
            $package = $s2->fetch();
            if (!$package) {
                header('Location: packages.php?cde=203');
                ob_end_flush();
                exit();
            }
            $parts = explode('-', $package["start_date"]);
            $frontSideStartDate = $parts[1] . '/' . $parts[2] . '/' . $parts[0];
            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["package_updated"]) . " </div></div>";
        } else {
            echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["package_update_failed"]) . "</div></div>";
        }
        unset($_POST);
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card ">
            <div class="card-body">
                <form action="" id="form" method="POST" class="col-md-12" novalidate>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="title"><?php echo ($trans["admin"]["packages"]["edit_package"]["title_en"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-message-draw"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="title" class="form-control" id="title" required placeholder="<?php echo ($trans["admin"]["packages"]["edit_package"]["title_placeholder_en"]) ?>" value="<?= $package["title"] ?>" required data-validation-required-message="Package title is required">
                                        <label id="title-error" class="error col-12" for="title"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="title"><?php echo ($trans["admin"]["packages"]["add_package"]["title_fr"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-message-draw"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="title_fr" class="form-control" id="title_fr" value="<?= $package["content"] ?>" required placeholder="<?php echo ($trans["admin"]["packages"]["add_package"]["title_placeholder_fr"]) ?>" required data-validation-required-message="<?php echo ($trans["admin"]["packages"]["add_package"]["title_placeholder_fr"]) ?>">
                                        <label id="title-error" class="error col-12" for="title_fr"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="website"><?php echo ($trans["admin"]["packages"]["edit_package"]["website"]) ?></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-web"></span>
                                            </span>
                                        </div>
                                        <select name="website" id="website" class="form-control select-search form-control-line" placeholder="<?php echo ($trans["admin"]["packages"]["edit_package"]["website_placeholder"]) ?>" value="<?= $package["id_website"] ?>">
                                            <option disabled selected>Select Website</option>
                                            <option value="0">No Website</option>
                                            <?php
                                            foreach ($websites as $website) {
                                                if ($website["id_website"] == $package["id_website"]) {
                                                    echo '<option value="' . $website["id_website"] . '" selected>' . $website["name"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $website["id_website"] . '">' . $website["name"] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 ">
                                <div class="form-group ">
                                    <label for="messagesCount"><?php echo ($trans["admin"]["packages"]["edit_package"]["messages_count"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3 '>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-message-plus"></span>
                                            </span>
                                        </div>
                                        <input type="number" min="1" name="messagesCount" value="<?= $package["messages"] ?>" class="form-control" id="messagesCount" required placeholder="<?php echo ($trans["admin"]["packages"]["edit_package"]["messages_count_placeholder"]) ?>" required data-validation-required-message="Package messages count is required">
                                        <label id="messagesCount-error" class="error col-12" for="messagesCount"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dateStartPickerContainer">
                            <div class="col-6">
                                <label for="dateRange"><?php echo ($trans["admin"]["packages"]["edit_package"]["start_at"]) ?> <span class="text-danger">*</span></label>
                                <div class='input-group mb-3'>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="mdi mdi-calendar"></span>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control singledate" id="dateStart" value="<?= $frontSideStartDate ?>" name="dateStart" required>
                                    <label id="dateRange-error" class="error col-12" for="dateStart"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="private" name="private" <?= $package['public'] == 0 ? "checked" : "" ?>>
                                <label class="custom-control-label" for="private"><?= $trans['private'] ?></label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="visible" name="visible" <?= $package['visible'] == 1 ? "checked" : "" ?>>
                                <label class="custom-control-label" for="visible"><?php echo ($trans["admin"]["packages"]["packages_table"]["displayed"]) ?></label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class="form-actions">
                        <button type="sumbit" name="update-package" class="Submit btn btn-primary waves-effect waves-light m-r-10"> <i class="mdi mdi-check"></i> <?php echo ($trans["save"]) ?></button>
                        <button type="button" class="btn btn-inverse"><?php echo ($trans["cancel"]) ?></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3 class="box-title m-b-0"><?php echo ($trans["admin"]["packages"]["edit_package"]["price"]) ?><button class="add btn btn-sm ml-2 btn-primary waves-effect waves-light m-r-10 float-right" type="button" data-toggle="modal" data-target="#Add">Add price</button></h3>
                <hr>
                <table class="table display dt-responsive" id="oldPrices" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo ($trans["admin"]["packages"]["edit_package"]["package"]) ?></th>
                            <th><?php echo ($trans["admin"]["packages"]["edit_package"]["price"]) ?></th>
                            <th><?php echo ($trans["admin"]["packages"]["edit_package"]["currency"]) ?></th>
                            <th><?php echo ($trans["admin"]["packages"]["edit_package"]["start_at"]) ?></th>
                            <th>Status</th>
                            <th>primary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($oldPrices as $price) { ?>
                            <tr>
                                <td><?= $price['id'] ?></td>
                                <td><?= $price['title'] ?></td>
                                <td><?= $price['price'] ?></td>
                                <td><?= $price['currency'] ?></td>
                                <td><?= $price['date_start'] ?></td>
                                <td><?= $price['date_end'] == '' ? '<span class="badge badge-success badge-pill">ACTIVE</span>' : 'Stopped at ' . $price['date_end'] ?></td>
                                <td class="text-center"><?= $price['primary'] == 1 ? '<i class="mdi mdi-check"></i>' : "" ?></td>
                                <?php if ($price['date_end']) {
                                    echo '<td></td>';
                                } else { ?>
                                    <td><a href="javascript:void(0)" class="edit badge badge-info badge-pill" data-id="<?= $price['id'] ?>">Edit</a></td>
                                <?php } ?>
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
<div class="modal" id="Add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?php echo ($trans["admin"]["packages"]["edit_package"]["add_price"]) ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" id="myForm" method="POST" class="col-md-12 m-t-20" novalidate>
                    <div class="row">
                        <div class="col-5">
                            <label for="price"><?php echo ($trans["admin"]["packages"]["add_package"]["price"]) ?> <span class="text-danger">*</span></label>
                            <div class='input-group mb-3'>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <span class="mdi mdi-cash-multiple"></span>
                                    </span>
                                </div>
                                <input type="text" name="price" class="form-control" id="price" required placeholder="<?php echo ($trans["admin"]["packages"]["add_package"]["price_placeholder"]) ?>">
                                <label id="price-error" class="error col-12" for="price"></label>
                            </div>
                        </div>
                        <div class="col-5">
                            <label for="currency"> <?php echo ($trans["admin"]["packages"]["add_package"]["currency"]) ?> <span class="text-danger">*</span></label>
                            <div class='input-group mb-3'>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <span class="mdi mdi-credit-card-plus"></span>
                                    </span>
                                </div>
                                <select name="currency" id="currency" required class="form-control select-search country form-control-line" placeholder="Choose Currency">
                                    <option value="EUR">Euro</option>
                                    <option value="CHF">Franc Suisse</option>
                                    <option value="GBP">Livre Sterling</option>
                                    <option value="SEK">Couronne Suédoise</option>
                                    <option value="DKK">Couronne Danoise</option>
                                    <option value="CAD">Dollar Canadien</option>
                                    <option value="USD">Dollar US</option>
                                    <option value="AUD">Dollar Australien</option>
                                    <option value="NZD">Dollar Néo-Zélandais</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="primary" name="primary">
                                <label class="custom-control-label" for="primary">Primary</label>
                            </div>
                        </div>
                        <input type="text" id="id" name="id" hidden>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="submit" name="add" class="addS btn btn-primary">Submit</button>
                        <button type="submit" name="update" class="update btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<footer class="footer"> <?php echo ($trans["footer"]) ?></footer>
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
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../../assets/js/moment.js"></script>
<!-- Date Picker Plugin JavaScript -->
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<!-- Date range Plugin JavaScript -->
<script src="../../assets/node_modules/timepicker/bootstrap-timepicker.min.js"></script>
<script src="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="../../assets/node_modules/wizard/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $('#oldPrices').DataTable({
            dom: 'Bfrtip',
            responsive: true
        });
    });
    $('.add').click(function() {
        $('.addS').show();
        $('.update').hide();
        $('#price').val('');
        $('#id').val('');
        $('#currency option[value=""]').prop('selected', true);
        $("#primary").prop("checked", false);
        $('#exampleModalLabel0').text('Add price');
        $("#currency").prop("disabled", false);

    });
    $('.update').click(function() {
        $("#currency").prop("disabled", false);
    });
    $('.edit').click(function() {
        let id = $(this).data('id');
        $('#exampleModalLabel0').text('Edit price');
        $('.addS').hide();
        $('.update').show();
        $("#currency").prop("disabled", true);

        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            data: {
                id: id,
                type: 'getPrice',
            },
            dataType: 'json',
            success: function(data) {
                $('#price').val(data.price);
                $('#id').val(id);
                $('#currency option[value="' + data.currency + '"]').prop('selected', true);
                if (data.primary == 1) {
                    $("#primary").prop("checked", true);
                } else $("#primary").prop("checked", false);
                $('.modal').modal('show');
            }
        });
    });
    $('.singledate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minDate: new Date()
    });

    $("#myForm").validate({
        rules: {
            messagesCount: {
                required: true,
                digits: true
            }
        }
    });
</script>
</body>

</html>