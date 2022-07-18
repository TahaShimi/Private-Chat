<?php
$page_name = "add_package";
include('header.php'); ?>
<link href="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<style>
    .error {
        font-size: 11px !important;
        color: #e46a76 !important;
    }

    .custom-checkbox {
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
</style>
<?php
$s1 = $conn->prepare("SELECT * FROM `websites` WHERE id_account = :ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->execute();
$websites = $s1->fetchAll();

if (isset($_POST['add-package'])) {
    $title = (isset($_POST['title']) && $_POST['title'] != '') ? htmlspecialchars($_POST['title']) : NULL;
    $price = (isset($_POST['price']) && $_POST['price'] != '') ? htmlspecialchars($_POST['price']) : NULL;
    $messagesCount = (isset($_POST['messagesCount']) && $_POST['messagesCount'] != '') ? htmlspecialchars($_POST['messagesCount']) : NULL;
    $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
    $dateType = (isset($_POST['dateType']) && $_POST['dateType'] != '') ? htmlspecialchars($_POST['dateType']) : NULL;
    if ($website == 0) {
        $website = NULL;
    }
    $visible = isset($_POST['visible']) ? 1 : 0;
    $private = isset($_POST['private']) ? 0 : 1;
    $currency = (isset($_POST['currency']) && $_POST['currency'] != '') ? htmlspecialchars($_POST['currency']) : NULL;
    $datec = date('Y-m-d', strtotime('now'));

    $dateStart = (isset($_POST['dateStart']) && $_POST['dateStart'] != '') ? htmlspecialchars($_POST['dateStart']) : NULL;
    $Periode = (isset($_POST['Periode']) && $_POST['Periode'] != '') ? htmlspecialchars($_POST['Periode']) : NULL;
    $parts = explode('/', $dateStart);
    $startDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];

    $stmt1 = $conn->prepare("INSERT INTO `packages`(`title`, `messages`, `status`, `public`, `id_website`, `start_date`, `id_account`, `created_at`,`visible`,`period`) VALUES (:ti,:msg,1,:pu,:iw,:sd,:ia,NOW(),:vs,:pr)");
    $stmt1->bindParam(':ti', $title, PDO::PARAM_STR);
    $stmt1->bindValue(':msg', $messagesCount, PDO::PARAM_INT);
    $stmt1->bindParam(':iw', $website, PDO::PARAM_INT);
    $stmt1->bindParam(':sd', $startDate, PDO::PARAM_STR);
    $stmt1->bindParam(':ia', $id_account, PDO::PARAM_INT);
    $stmt1->bindParam(':vs', $visible, PDO::PARAM_INT);
    $stmt1->bindParam(':pu', $private, PDO::PARAM_INT);
    $stmt1->bindParam(':pr', $Periode, PDO::PARAM_INT);
    $stmt1->execute();
    $last_id = $conn->lastInsertId();
    $affected_rows = $stmt1->rowCount();

    $stmt1 = $conn->prepare("INSERT INTO `packages_price`(`id_package`, `price`, `currency`, `date_start`, `primary`) VALUES (:id,:pr,:cu,NOW(),1)");
    $stmt1->bindParam(':id', $last_id, PDO::PARAM_STR);
    $stmt1->bindParam(':pr', $price, PDO::PARAM_INT);
    $stmt1->bindParam(':cu', $currency, PDO::PARAM_STR);
    $stmt1->execute();

    if ($affected_rows != 0) {
        if (isset($_POST['title_fr'])) {
            $titleFr = $_POST['title_fr'];
            $table = "packages";
            $column = "title";
            $lang = "fr";
            $idElement = $last_id;
            $stmtl = $conn->prepare("INSERT INTO `translations`(`content`, `table`, `column`, `lang`, `id_element`) VALUES (:ct,:tb,:cl,:lg,:ie)");
            $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
            $stmtl->bindParam(':tb', $table, PDO::PARAM_STR);
            $stmtl->bindParam(':cl', $column, PDO::PARAM_STR);
            $stmtl->bindParam(':lg', $lang, PDO::PARAM_STR);
            $stmtl->bindParam(':ie', $idElement, PDO::PARAM_INT);
            $stmtl->execute();
        }

        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["package_created"]) . " </div></div>";
    } else {
        echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["package_failed"]) . " </div></div>";
    }
    unset($_POST);
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card ">
            <div class="card-body">
                <form action="" id="myForm" method="POST" class="col-md-12" novalidate>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="title"><?php echo ($trans["admin"]["packages"]["add_package"]["title_en"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-message-draw"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="title" class="form-control" id="title" required placeholder="<?php echo ($trans["admin"]["packages"]["add_package"]["title_placeholder_en"]) ?>" required data-validation-required-message="<?php echo ($trans["admin"]["packages"]["add_package"]["title_placeholder_en"]) ?>">
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
                                        <input type="text" name="title_fr" class="form-control" id="title_fr" required placeholder="<?php echo ($trans["admin"]["packages"]["add_package"]["title_placeholder_fr"]) ?>" required data-validation-required-message="<?php echo ($trans["admin"]["packages"]["add_package"]["title_placeholder_fr"]) ?>">
                                        <label id="title-error" class="error col-12" for="title_fr"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="website"><?php echo ($trans["admin"]["packages"]["add_package"]["website"]) ?></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-web"></span>
                                            </span>
                                        </div>
                                        <select name="website" id="website" class="form-control select-search form-control-line" placeholder="<?php echo ($trans["admin"]["packages"]["add_package"]["website_placeholder"]) ?>">
                                            <option disabled selected><?php echo ($trans["admin"]["packages"]["add_package"]["website_placeholder"]) ?></option>
                                            <option value="0">No Website</option>
                                            <?php
                                            foreach ($websites as $website) {
                                                echo '<option value="' . $website["id_website"] . '">' . $website["name"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 values">
                                <div class="form-group ">
                                    <label for="messagesCount"><?php echo ($trans["admin"]["packages"]["add_package"]["messages_count"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3 '>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-message-plus"></span>
                                            </span>
                                        </div>
                                        <input type="number" min="1" name="messagesCount" class="form-control" id="messagesCount" required placeholder="<?php echo ($trans["admin"]["packages"]["add_package"]["messages_count_placeholder"]) ?>" required data-validation-required-message="Package messages count is required">
                                        <label id="messagesCount-error" class="error col-12" for="messagesCount"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-6">
                                <div class="form-group">
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
                            </div>
                            <div class="col-6">
                                <div class="form-group">

                                    <label for="currency"> <?php echo ($trans["admin"]["packages"]["add_package"]["currency"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-credit-card-plus"></span>
                                            </span>
                                        </div>
                                        <select name="currency" id="currency" required class="form-control select-search country form-control-line" placeholder="Choose Currency">
                                            <option value=""></option>
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
                            </div>
                        </div>
                        <div class="row dateStartPickerContainer">
                            <div class="col-6">
                                <label for="dateRange"><?php echo ($trans["admin"]["packages"]["add_package"]["start_at"]) ?> <span class="text-danger">*</span></label>
                                <div class='input-group mb-3'>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="ti-calendar"></span>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control singledate" id="dateStart" name="dateStart" required>
                                    <label id="dateRange-error" class="error col-12" for="dateStart"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="visible" name="visible">
                                <label class="custom-control-label" for="visible"><?php echo ($trans["admin"]["packages"]["packages_table"]["displayed"]) ?></label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="private" name="private">
                                <label class="custom-control-label" for="private"><?= $trans['private']?></label>
                            </div>
                            <div class="custom-control custom-checkbox unlimit">
                                <input type="checkbox" class="custom-control-input" id="unlimited " name="unlimited " onclick="show()">
                                <label class="custom-control-label" for="unlimited "><?= $trans['unlimited']?></label>
                            </div>
                        </div>
                        <div class="row dateStartPickerContainer end">
                            <div class="col-6">
                                <div class="form-group ">
                                    <label for="messagesCount"><?= $trans['Periode'] ?> (<?= $trans['days'] ?>) <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3 '>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="mdi mdi-message-plus"></span>
                                            </span>
                                        </div>
                                        <input type="number" min="1" name="Periode" class="form-control" id="Periode" required placeholder="Days of period" required data-validation-required-message="Period count is required">
                                        <label id="messagesCount-error" class="error col-12" for="Periode"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <hr>
                    <div class="form-actions">
                        <button type="submit" name="add-package" class="btn btn-primary waves-effect waves-light m-r-10"> <i class="mdi mdi-check"></i> <?php echo ($trans["save"]) ?></button>
                        <button type="button" class="btn btn-inverse"><?php echo ($trans["cancel"]) ?></button>
                    </div>
                </form>
            </div>
        </div>
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
<script src="../../assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<!-- Date range Plugin JavaScript -->
<script src="../../assets/node_modules/timepicker/bootstrap-timepicker.min.js"></script>
<script src="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="../../assets/node_modules/wizard/jquery.validate.min.js"></script>
<script type="text/javascript">
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
    $('.end').hide();

    $('input[type=radio][name=dateType]').change(function() {
        if (this.value == '1') {
            $(".dateRangePickerContainer").css("display", "block");
        } else if (this.value == '2') {
            $(".dateRangePickerContainer").css("display", "none");
        }
    });

    function show() {
        // If the checkbox is checked, display the output text
        if ($('.unlimit > input:checked').prop('checked') == true) {
            $('.values').hide();
            $('.end').show();

        } else {
            $('.values').show();
            $('.end').hide();
        }
    }
</script>
</body>

</html>