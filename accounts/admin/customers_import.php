<?php
$page_name = "import_customers";
include('header.php');
$s1 = $conn->prepare("SELECT * FROM `websites` WHERE id_account = :ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->execute();
$websites = $s1->fetchAll();

$s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.*,pp.* FROM `offers` o join `packages` p on p.id_package=o.id_package JOIN packages_price pp ON  pp.id_package=p.id_package WHERE o.id_account = :ID AND p.public = 1 AND pp.primary=1");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->execute();
$offers = $s1->fetchAll();
$total = 0;
$duplicated = 0;
$existed = 0;
$count = 0;
$emailsVerif = [];
if (isset($_POST['upload-customers'])) {
    $v = 0;
    if (is_uploaded_file($_FILES['file1']['tmp_name'])) {
        $datec = date('Y-m-d', strtotime('now'));
        $websiteId = isset($_POST['website']) && $_POST['website'] != '' ? $_POST['website'] : NULL;
        $offers_ids = (isset($_POST['offers_ids']) && count($_POST['offers_ids']) > 0) ? $_POST['offers_ids'] : NULL;
        $csvFile = fopen($_FILES['file1']['tmp_name'], 'r');
        $total = count(file($_FILES['file1']['tmp_name'], FILE_SKIP_EMPTY_LINES)) - 1;
        if ($total < 5000) {
            $stmt7 = $conn->prepare("INSERT INTO log(id_account,action_date) VALUES(:ic,NOW())");
            $stmt7->bindParam(':ic', $id_account);
            $stmt7->execute();
            $importId = $conn->lastInsertId();
            $stmt0 = $conn->prepare("SELECT login FROM `users`");
            $stmt0->execute();
            $emails = $stmt0->fetchAll();
            $emailsNames = array_column($emails, 'login');
            $packages_ids = $_POST["packages_ids"];

            while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
                if ($v == 0) {
                    $v++;
                    continue;
                }
                if (!in_array($line[2], $emailsNames) && $line[2] != "email") {
                    if (!in_array($line[2], $emailsVerif)) {
                        if (isset($line[2])) {
                            $firstname = (isset($line[0]) && $line[0] != '') ? htmlentities($line[0]) : NULL;
                            $lastname = (isset($line[1]) && $line[1] != '') ? $line[1] : NULL;
                            $email = (isset($line[2]) && $line[2] != '') ? str_replace(' ', '', $line[2]) : NULL;
                            $phone = (isset($line[3]) && $line[3] != '') ? $line[3] : NULL;
                            $address = (isset($line[4]) && $line[4] != '') ? $line[4] : NULL;
                            $country = (isset($line[5]) && $line[5] != '') ? $line[5] : NULL;
                            if (strlen($country) > 2) {
                                $country = array_search($country, $countries);
                                if (!$country) {
                                    $country = null;
                                }
                            }
                            if (isset($line[6])) {
                                if ($line[6] == '') $gender = NULL;
                                else
                                if (in_array($line[6], ['m', 'M', 'male', 'Male'])) $gender = 1;
                                else
                                if (in_array($line[6], ['F', 'F', 'female', 'Female'])) $gender = 2;
                            }
                            $lang = (isset($_POST['lang']) && $_POST['lang'] != '') ? $_POST['lang'] : NULL;
                            $stmt = $conn->prepare("INSERT into customers (firstname, lastname, emailc, phone, country, address, id_website, date_start, status, id_account, gender,importation_id) values (:fn, :ln, :em, :ph, :ct, :ad, :iw, now(), 1, :ia, :ge,:im)");
                            $stmt->bindparam(":ln", $lastname, PDO::PARAM_STR);
                            $stmt->bindparam(":fn", $firstname, PDO::PARAM_STR);
                            $stmt->bindparam(":em", $email, PDO::PARAM_STR);
                            $stmt->bindparam(":ph", $phone, PDO::PARAM_STR);
                            $stmt->bindparam(":ct", $country, PDO::PARAM_STR);
                            $stmt->bindparam(":ad", $address, PDO::PARAM_STR);
                            $stmt->bindparam(":iw", $websiteId, PDO::PARAM_STR);
                            $stmt->bindparam(":ia", $id_account, PDO::PARAM_STR);
                            $stmt->bindparam(":ge", $gender, PDO::PARAM_INT);
                            $stmt->bindparam(":im", $importId, PDO::PARAM_INT);
                            $stmt->execute();
                            $last_id = $conn->lastInsertId();
                            $affected_rows = $stmt->rowCount();
                            foreach ($packages_ids as $key_of => $value_of) {
                                $stmt1 = $conn->prepare("INSERT INTO `customers_packages`(`id_customer`, `id_package`, `date_add`) VALUES (:ic,:iof,NOW())");
                                $stmt1->bindParam(':ic', $last_id, PDO::PARAM_STR);
                                $stmt1->bindParam(':iof', $value_of, PDO::PARAM_STR);
                                $stmt1->execute();
                            }
                            if ($affected_rows != 0) {
                                $profile = 4;
                                $profileId = $last_id;
                                $password = password_hash($email, PASSWORD_BCRYPT);
                                $stmt1 = $conn->prepare("INSERT INTO `users`(`login`, `password`, `profile`, `id_profile`, `date_add`, `active`, `status`, `lang`) VALUES (:em2,:pwd,:pr,:ip,:da,1,1,:lg)");
                                $stmt1->bindParam(':em2', $email, PDO::PARAM_STR);
                                $stmt1->bindParam(':pwd', $password, PDO::PARAM_STR);
                                $stmt1->bindParam(':pr', $profile, PDO::PARAM_INT);
                                $stmt1->bindParam(':ip', $profileId, PDO::PARAM_INT);
                                $stmt1->bindParam(':da', $datec, PDO::PARAM_STR);
                                $stmt1->bindparam(':lg', $lang, PDO::PARAM_STR);
                                $stmt1->execute();
                                $last_id2 = $conn->lastInsertId();
                                $affected_rows2 = $stmt1->rowCount();

                                if ($affected_rows2 != 0) {
                                    $count++;
                                    $emailsVerif[] = $email;
                                    if ($offers_ids != null) {
                                        foreach ($offers_ids as $key => $value) {
                                            $stmt2 = $conn->prepare("INSERT INTO `offers_customers`(`id_customer`, `id_offer`, `created_at`) VALUES (:ic,:iof,NOW())");
                                            $stmt2->bindParam(':ic', $last_id, PDO::PARAM_INT);
                                            $stmt2->bindParam(':iof', $value, PDO::PARAM_INT);
                                            if ($stmt2->execute()) {
                                                foreach ($offers as $offer) {
                                                    if ($offer["id_offer"] == $value) {
                                                        if ($offer["discount"] == 100) {
                                                            $stmt3 = $conn->prepare("UPDATE customers set balance=balance+:ba where id_customer=:id");
                                                            $stmt3->bindParam(':id', $last_id, PDO::PARAM_INT);
                                                            $stmt3->bindParam(':ba', $offer["messages"], PDO::PARAM_INT);
                                                            $stmt3->execute();

                                                            $description = "Free offer attached to customer";
                                                            $stmt5 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) 
                                                                        VALUES(:iu,:ds,:mt,3,NOW())");
                                                            $stmt5->bindParam(':iu', $last_id2, PDO::PARAM_INT);
                                                            $stmt5->bindParam(':ds', $description, PDO::PARAM_STR);
                                                            $stmt5->bindParam(':mt', $offer["id_offer"], PDO::PARAM_INT);
                                                            $stmt5->execute();
                                                        } else {
                                                            $description = "New offer attached to customer";
                                                            $stmt7 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) 
                                                                        VALUES(:iu,:ds,:mt,3,NOW())");
                                                            $stmt7->bindParam(':iu', $last_id2, PDO::PARAM_INT);
                                                            $stmt7->bindParam(':ds', $description, PDO::PARAM_STR);
                                                            $stmt7->bindParam(':mt', $offer["id_offer"], PDO::PARAM_INT);
                                                            $stmt7->execute();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["customer_failed"]) . " </div></div>";
                                }
                            }
                        }
                    } else {
                        $duplicated++;
                    }
                } else {
                    $existed++;
                }
            }
        } else  echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'> more than 5000 </div></div>";
    }

    $stmt7 = $conn->prepare("UPDATE log set description=:iu ,offers=:of WHERE id_action=:ic");
    $stmt7->bindParam(':iu', $count, PDO::PARAM_INT);
    $stmt7->bindParam(':of', json_encode($offers_ids));
    $stmt7->bindParam(':ic', intval($importId));
    $stmt7->execute();
    echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["customers_imported"]) . "<br>
    " . ($trans["total_contact_num"]) . ":" . $total . "<br>
    " . ($trans["duplicated_contact_num"]) . " :" . $duplicated . "<br>
    " . ($trans["existed_contact_num"]) . " :" . $existed . "<br>
    " . ($trans["imported_contact_num"]) . " :" . $count . "</div></div>";
}
?>
<style>
    .select2 {
        width: 80% !important;
    }
</style>
<div class="row">
    <div class="col-lg-6">
        <div class="card ">
            <div class="card-body">
                <form action="" id="myForm" method="POST" class="col-md-12" enctype="multipart/form-data" novalidate>
                    <div class="form-body">
                        <h5 class="m-b-30 "><i class="mdi mdi-numeric-1-box m-r-5"></i><?php echo ($trans["admin"]["customers"]["import_customers"]["first_step"]) ?> :<a href="../../assets/customers_model.csv" class=" m-2"><img src="../../assets/images/csv.png" style="width:24px"></a></h5>
                    </div>

                    <div class="row ">
                        <div class="col-12 m-b-30">
                            <h5 class=""><i class="mdi mdi-numeric-2-box"></i> <?php echo ($trans["admin"]["customers"]["import_customers"]["second_step"]) ?> :</h5>
                            <div class="card">
                                <div class="card-body m-r-0 m-l-0">
                                    <input type="file" id="input-file-now" name="file1" class="dropify" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 m-b-30">
                            <div class="form-group ">
                                <h5 class=""><i class="mdi mdi-numeric-3-box"></i> <?php echo ($trans["admin"]["customers"]["import_customers"]["third_step"]) ?> :</h5>
                                <div class='input-group mb-3'>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="mdi mdi-web"></span>
                                        </span>
                                    </div>
                                    <select name="website" id="website" class="form-control select-search form-control-line" placeholder="Choose website" required>
                                        <option disabled selected>Select Website</option>
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
                    </div>
                    <div class="row">
                        <div class="col-12 m-b-30">
                            <div class="form-group ">
                                <h5 class=""><i class="mdi mdi-numeric-4-box"></i> Packages :</h5>
                                <div class='input-group mb-3'>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="mdi mdi-package-variant-closed"></span>
                                        </span>
                                    </div>
                                    <select name="packages_ids[]" id="packages" class="select2 select2-multiple" style="width: 80%!important" multiple="multiple" data-placeholder="Choose">
                                        <?php
                                        $s1 = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title,(SELECT count(*) FROM offers o where o.id_package=p.id_package and end_date >= CURDATE()) as offers_count FROM `packages` p JOIN packages_price pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang WHERE pp.primary=1 AND p.id_account = :ID  and  p.public=0");
                                        $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                        $s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
                                        $s1->execute();
                                        $packages1 = $s1->fetchAll();
                                        foreach ($packages1 as $package) {
                                            echo '<option value="' . $package["id_package"] . '">' . $package["title"] . ' ( ' . $package["messages"] . ' messages at ' . ($package["price"]) . ' ' . $package["currency"] . ' )' . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 m-b-30">
                            <div class="form-group ">
                                <h5 class=""><i class="mdi mdi-numeric-4-box"></i> <?php echo ($trans["admin"]["customers"]["import_customers"]["fourth_step"]) ?> :</h5>
                                <div class='input-group mb-3'>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="mdi mdi-package-variant-closed"></span>
                                        </span>
                                    </div>
                                    <select name="offers_ids[]" id="offers" class="select2 select2-multiple" style="width:92%;!important" multiple="multiple" data-placeholder="Choose" required>
                                        <?php
                                        foreach ($offers as $offer) {
                                            echo '<option value="' . $offer["id_offer"] . '">' . $offer["offer_title"] . ' on ' . $offer["title"] . ' ( ' . $offer["messages"] . ' messages at ' . (((100 - $offer["discount"]) * ($offer["price"] / 100))) . ' ' . $offer["currency"] . ' )' . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="" for="activity"><?php echo ($trans["language"]) ?></label>
                            <div class="">
                                <select name="lang" id="lang" class="form-control select2 select-search">
                                    <option value="en"><?php echo ($trans["english"]) ?></option>
                                    <option value="fr"><?php echo ($trans["french"]) ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class="form-actions">
                        <button type="submit" name="upload-customers" class="btn btn-primary waves-effect waves-light m-r-10"> <i class="mdi mdi-check"></i> <?php echo ($trans["upload"]) ?> </button>
                        <button type="button" class="btn btn-inverse"><?php echo ($trans["cancel"]) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>
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
<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<!-- Date Picker Plugin JavaScript -->
<script src="../../assets/node_modules/timepicker/bootstrap-timepicker.min.js"></script>
<script src="../../assets/node_modules/wizard/jquery.validate.min.js"></script>
<script src="../../assets/node_modules/dropify/dropify.min.js"></script>
<script type="text/javascript" src="../../assets/node_modules/multiselect/js/jquery.multi-select.js"></script>
<script src="../../assets/node_modules/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".select2").select2();
    $('.dropify').dropify();
    $("#myForm").validate({
        rules: {
            messagesCount: {
                required: true,
                digits: true
            }
        }
    });

    // Used events
    var drEvent = $('#input-file-events').dropify();
    drEvent.on('dropify.beforeClear', function(event, element) {
        return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
    });
    drEvent.on('dropify.afterClear', function(event, element) {
        alert('File deleted');
    });
    drEvent.on('dropify.errors', function(event, element) {
        console.log('Has Errors');
    });
    var drDestroy = $('#input-file-to-destroy').dropify();
    drDestroy = drDestroy.data('dropify')
    $('#toggleDropify').on('click', function(e) {
        e.preventDefault();
        if (drDestroy.isDropified()) {
            drDestroy.destroy();
        } else {
            drDestroy.init();
        }
    });
    $('#s2_demo4, #input-file-now').bind("change keyup", function() {
        $('#Upload').removeClass('disabled');
        $('#s2_demo4, #input-file-now').each(function() {
            if ($(this).val() == '') {
                $('#Upload').addClass('disabled');
            }
        });
    });
    $('#packages').change(function() {
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                type: 'getFiltredoffers',
                id: $(this).val()
            },
            success: function(data) {
                console.log(data);
                $('#offers').empty();
                $.each(data, function() {
                    let pri = (100 - this.discount) * (this.price / 100);
                    $('#offers').append('<option value="' + this.id_offer + '">' + this.offer_title + ' on ' + this.title + ' ( ' + this.messages + ' messages at ' + pri.toFixed(2) + ' ' + this.currency + ' )</option>');
                })
            }
        })
    });
</script>
</body>

</html>