<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$page_name = "add_customer";
include('header.php'); ?>
<style>
    .iti {
        width: 100% !important;
    }

    .text-danger {
        font-size: 11px !important;
        color: #e46a76 !important;
    }
</style>
<link href="../../assets/node_modules/wizard/steps.css" rel="stylesheet">
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">
<div class="row">
    <?php
    $s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.*, pp.price as price FROM `offers` o join `packages` p on p.id_package=o.id_package AND p.public=1 AND p.status=1 JOIN `packages_price` pp ON  pp.id_package=p.id_package AND  pp.primary=1 WHERE o.id_account = :ID ");
    $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
    $s1->execute();
    $offers = $s1->fetchAll();
    $s1 = $conn->prepare("SELECT * FROM `websites` WHERE id_account = :ID");
    $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
    $s1->execute();
    $websites = $s1->fetchAll();
    if (isset($_POST['email'])) {
        $s1 = $conn->prepare("SELECT * FROM `users` WHERE login=:lg");
        $s1->bindParam(':lg', $_POST['email'], PDO::PARAM_STR);
        $s1->execute();
        $user = $s1->fetchObject();

        if (!$user) {
            if (isset($_POST['firstname']) && isset($_POST['lastname'])) {
                /*   create customer */
                $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;
                $firstName = (isset($_POST['firstname']) && $_POST['firstname'] != '') ? htmlspecialchars($_POST['firstname']) : NULL;
                $lastName = (isset($_POST['lastname']) && $_POST['lastname'] != '') ? htmlspecialchars($_POST['lastname']) : NULL;
                $emailAddress = (isset($_POST['email']) && $_POST['email'] != '') ? htmlspecialchars($_POST['email']) : NULL;
                $address = (isset($_POST['address']) && $_POST['address'] != '') ? htmlspecialchars($_POST['address']) : NULL;
                $country = (isset($_POST['country']) && $_POST['country'] != '') ? $_POST['country'] : NULL;
                $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;
                $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                $datec = date('Y-m-d', strtotime('now'));

                $stmt = $conn->prepare("INSERT into customers (firstname, lastname, emailc, phone, country, address, date_start, status, id_account, gender,id_website) values (:fn, :ln, :em, :ph, :ct, :ad, now(), 1, :ia, :ge,:we)");
                $stmt->bindparam(":fn", $firstName, PDO::PARAM_STR);
                $stmt->bindparam(":ln", $lastName, PDO::PARAM_STR);
                $stmt->bindparam(":em", $emailAddress, PDO::PARAM_STR);
                $stmt->bindparam(":ph", $phone, PDO::PARAM_STR);
                $stmt->bindparam(":ct", $country, PDO::PARAM_STR);
                $stmt->bindparam(":ad", $address, PDO::PARAM_STR);
                $stmt->bindparam(":ia", $id_account, PDO::PARAM_INT);
                $stmt->bindparam(":ge", $gender, PDO::PARAM_INT);
                $stmt->bindparam(":we", $website, PDO::PARAM_INT);
                $stmt->execute();
                $last_id = $conn->lastInsertId();
                $affected_rows = $stmt->rowCount();
                if ($affected_rows != 0) {
                    $lang = $_POST['lang'];
                    $profile = 4;
                    $profileId = $last_id;
                    $password = password_hash($emailAddress, PASSWORD_BCRYPT);
                    $stmt1 = $conn->prepare("INSERT INTO `users`(`login`, `password`, `profile`, `id_profile`, `date_add`, `active`, `status`,lang) VALUES (:lg,:pwd,:pr,:ip,:da,1,1,:lang)");
                    $stmt1->bindParam(':lg', $emailAddress, PDO::PARAM_STR);
                    $stmt1->bindParam(':pwd', $password, PDO::PARAM_STR);
                    $stmt1->bindParam(':pr', $profile, PDO::PARAM_INT);
                    $stmt1->bindParam(':ip', $profileId, PDO::PARAM_INT);
                    $stmt1->bindParam(':da', $datec, PDO::PARAM_STR);
                    $stmt1->bindparam(":lang", $lang, PDO::PARAM_STR);
                    $stmt1->execute();
                    $affected_rows = $stmt1->rowCount();

                    if ($affected_rows != 0) {
                        if (isset($_POST["packages_ids"]) && count($_POST["packages_ids"]) > 0) {
                            $packages_ids = $_POST["packages_ids"];
                            $offers_ids = $_POST["offers_ids"];
                            foreach ($packages_ids as $key_of => $value_of) {
                                $stmt1 = $conn->prepare("INSERT INTO `customers_packages`(`id_customer`, `id_package`, `date_add`) VALUES (:ic,:iof,NOW())");
                                $stmt1->bindParam(':ic', $last_id, PDO::PARAM_STR);
                                $stmt1->bindParam(':iof', $value_of, PDO::PARAM_STR);
                                $stmt1->execute();
                            }
                            if ($offers_ids != null) {
                                foreach ($offers_ids as $key => $value) {
                                    $stmt1 = $conn->prepare("INSERT INTO `offers_customers`(`id_customer`, `id_offer`, `created_at`) VALUES (:ic,:iof,NOW())");
                                    $stmt1->bindParam(':ic', $last_id, PDO::PARAM_INT);
                                    $stmt1->bindParam(':iof', $value, PDO::PARAM_INT);
                                    if ($stmt1->execute()) {
                                        foreach ($offers as $offer) {
                                            if ($offer["id_offer"] == $value) {
                                                if ($offer["discount"] == 100) {
                                                    $stmt1 = $conn->prepare("UPDATE customers set balance=balance+:ba where id_customer=:id");
                                                    $stmt1->bindParam(':id', $last_id, PDO::PARAM_INT);
                                                    $stmt1->bindParam(':ba', $offer["messages"], PDO::PARAM_INT);
                                                    $stmt1->execute();
                                                    $stmt = $conn->prepare("SELECT id_user from users where id_profile=:ip and profile=4");
                                                    $stmt->bindParam(':ip', $last_id, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $id_user = intval($stmt->fetchObject()->id_user);
                                                    $description = "Free offer attached to customer";
                                                    $stmt2 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) VALUES(:iu,:ds,:mt,3,NOW())");
                                                    $stmt2->bindParam(':iu', $id_user, PDO::PARAM_INT);
                                                    $stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
                                                    $stmt2->bindParam(':mt', $offer["id_offer"], PDO::PARAM_INT);
                                                    $stmt2->execute();
                                                } else {
                                                    $stmt = $conn->prepare("SELECT id_user from users where id_profile=:ip and profile=4");
                                                    $stmt->bindParam(':ip', $last_id, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $id_user = intval($stmt->fetchObject()->id_user);
                                                    $description = "Free offer attached to customer";
                                                    $stmt2 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) VALUES(:iu,:ds,:mt,3,NOW())");
                                                    $stmt2->bindParam(':iu', $id_user, PDO::PARAM_INT);
                                                    $stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
                                                    $stmt2->bindParam(':mt', $offer["id_offer"], PDO::PARAM_INT);
                                                    $stmt2->execute();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["customer_added"]) . "</div></div>";
                        
                    } else {
                        echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["customer_failed"]) . "</div></div>";
                        unset($_POST);
                    }
                }
            } else {
                unset($_POST);
            }
        } else echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Email already used !!</div></div>";
        unset($_POST);
    }
    ?>

    <!-- Validation wizard -->
    <div class="row" id="validation">
        <div class="col-12">
            <div class="card wizard-content">
                <div class="card-body">
                    <h6 class="card-subtitle"></h6>
                    <form id="myForm" action="" method="POST" class="validation-wizard wizard-circle" name="add-customer">
                        <!-- Step 1 -->
                        <h6 class="col-md-3 col-sm-12 col-xs-12"><?php echo ($trans["personal_informations"]) ?></h6>
                        <section>
                            <div class="form-group">
                                <label class="control-label"><?php echo ($trans["gender"]) ?></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio1" name="gender" value="1" data-text="Male" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="customRadio1"><?php echo ($trans["male"]) ?></label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio2" name="gender" value="2" data-text="Female" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadio2"><?php echo ($trans["female"]) ?></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname"> <?php echo ($trans["first_name"]) ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="firstname" name="firstname" placeholder="Enter First Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname"><?php echo ($trans["last_name"]) ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="lastname" name="lastname" placeholder="Enter Last Name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"> <?php echo ($trans["email"]) ?> : </label>
                                        <input type="email" class="form-control required" id="email" name="email" placeholder="Enter Email Address">
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label class="control-label text-right"><?php echo ($trans["phone"]) ?></label>
                                    <div class="">
                                        <input name="phone" type="tel" id="phone" class="form-control" style="width:100%" placeholder="Enter Phone Number">
                                        <span id="valid-msg" class="hide text-success">� Valid</span>
                                        <span id="error-msg" class="hide text-danger">✗ Invalid number</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="activity"><?php echo ($trans["language"]) ?></label>
                                    <div>
                                        <select name="lang" id="lang" class="form-control select2 select-search">
                                            <option value="en"><?php echo ($trans["english"]) ?></option>
                                            <option value="fr"><?php echo ($trans["french"]) ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country"> <?php echo ($trans["country"]) ?> : </label>
                                        <select name="country" id="country" class="form-control select-search country form-control-line">
                                            <option></option>
                                            <?php
                                            foreach ($countries as $key => $country) {
                                                if ($key == "TN") {
                                                    echo '<option value="' . $key . '" selected>' . $country . '</option>';
                                                } else {
                                                    echo '<option value="' . $key . '">' . $country . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="website"> <?php echo ($trans["website"]) ?> : </label>
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
                                <div class="form-group col-md-6">
                                    <label for="address"> <?php echo ($trans["address"]) ?> : </label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address">
                                </div>
                            </div>

                        </section>
                        <!-- Step 3 -->
                        <h6 class="col-md-3 col-sm-12 col-xs-12"><?php echo ($trans["add_offer"]) ?>/Packages</h6>
                        <section>
                            <div class="row ">
                                <div class="col-6">
                                    <div class="form-group ">
                                        <h5 class="m-t-60">Packages</small> :</h5>
                                        <select name="packages_ids[]" id="packages" class="select2 select2-multiple" style="width: 80%" multiple="multiple" data-placeholder="Choose">
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
                                <div class="col-6">
                                    <div class="form-group ">
                                        <h5 class="m-t-60"><?php echo ($trans["admin"]["customers"]["add_offers"]) ?></small> :</h5>
                                        <select name="offers_ids[]" id="offers" class="select2 select2-multiple" style="width:92%;" multiple="multiple" data-placeholder="Choose">
                                            <?php
                                            foreach ($offers as $offer) {
                                                echo '<option value="' . $offer["id_offer"] . '">' . $offer["offer_title"] . ' on ' . $offer["title"] . ' ( ' . $offer["messages"] . ' messages at ' . (((100 - $offer["discount"]) * ($offer["price"] / 100))) . ' ' . $offer["currency"] . ' )' . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <!-- Step 4 -->
                        <h6 class="col-md-3 col-sm-12 col-xs-12"><?php echo ($trans["confirmation"]) ?></h6>
                        <section>
                            <div class="form-body">
                                <h3 class="box-title"><?php echo ($trans["personal_informations"]) ?></h3>
                                <hr class="m-t-0 m-b-40">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["first_name"]) ?>:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_first_name"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["last_name"]) ?>:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_last_name"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!--/row-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["gender"]) ?>:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_gender"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["email"]) ?>:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_email"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!--/row-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["phone"]) ?>:</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_email"> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/row-->
                                <h3 class="box-title"><?php echo ($trans["authentication_credentials"]) ?> </h3>
                                <hr class="m-t-0 m-b-40">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["login"]) ?> :</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_login"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["password"]) ?> :</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_password"> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/row-->
                                <h3 class="box-title"><?php echo ($trans["address"]) ?> </h3>
                                <hr class="m-t-0 m-b-40">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["address"]) ?> :</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_address"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["country"]) ?> :</label>
                                            <div class="col-md-9">
                                                <p class="form-control-static" id="summary_country"> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="box-title"><?php echo ($trans["offers"]) ?> </h3>
                                <hr class="m-t-0 m-b-40">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3"><?php echo ($trans["offers"]) ?> :</label>
                                            <div class="col-md-9" id="summary_offers">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<footer class="footer"><?php echo ($trans["footer"]) ?></footer>
</div>
<script src="../../assets/js/notification.js"></script>
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
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../../assets/node_modules/wizard/jquery.steps.min.js"></script>
<script src="../../assets/node_modules/wizard/jquery.validate.min.js"></script>
<!-- <script type="text/javascript" src="../../assets/node_modules/multiselect/js/jquery.multi-select.js"></script>
<script src="../../assets/node_modules/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script> -->
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var offers_array = [];
    var form = $("#myForm").show();
    $(document).ready(function() {
        $(".select2").select2();
    });
    $("#myForm").steps({
        headerTag: "h6",
        bodyTag: "section",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: {
            finish: "Submit"
        },
        onStepChanging: function(event, currentIndex, newIndex) {
            if (currentIndex == 0) {
                $("#summary_first_name").text($('#firstname').val());
                $("#summary_last_name").text($('#lastname').val());
                $("#summary_email").text($('#email').val());
                $("#summary_login").text($('#email').val());
                $("#summary_password").text($('#email').val());
                $("#summary_phone").text($('#phone').text());
                $("#summary_address").text($('#address').val());
                $("#summary_country").text($("#country option:selected").text());
                $("#summary_gender").text($("input[name='gender']:checked").data('text'));
            } else if (currentIndex == 1) {
                var offers_selected = $("#offers option:selected").map(function() {
                    $("#summary_offers").append('<p class="form-control-static">' + $(this).text() + '</p>')
                }).get();
            }
            return currentIndex > newIndex || !(3 === newIndex && Number($("#age-2").val()) < 18) && (currentIndex < newIndex && (form.find(".body:eq(" + newIndex + ") label.error").remove(), form.find(".body:eq(" + newIndex + ") .error").removeClass("error")), form.validate().settings.ignore = ":disabled,:hidden", form.valid())
        },
        onFinishing: function(event, currentIndex) {
            /////////////////////////////////////////$("a[href='#finish']").attr('name', 'add-customer');
            return form.validate().settings.ignore = ":disabled", form.valid()
        },
        onFinished: function(event, currentIndex) {
            //swal.fire("Form Submitted!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat eleifend ex semper, lobortis purus sed.");
            $("#myForm").submit();
        }
    }), $("#myForm").validate({
        ignore: "input[type=hidden]",
        errorClass: "text-danger",
        successClass: "text-success",
        highlight: function(element, errorClass) {
            $(element).removeClass(errorClass)
        },
        unhighlight: function(element, errorClass) {
            $(element).removeClass(errorClass)
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element)
        },
        rules: {
            email: {
                email: !0
            }
        }
    });
    var errorMsg = $("#error-msg"),
        validMsg = $("#valid-msg");
    // here, the index maps to the error code returned from getValidationError - see readme
    var errorMap = ["Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];
    // initialise plugin
    var iti = $("#phone").intlTelInput({
        nationalMode: true,
        autoPlaceholder: "off",
        initialCountry: "fr",
        utilsScript: "../../assets/int-phone-number/js/utils.js"
    });
    var reset = function() {
        $("#phone").removeClass("error");
        errorMsg.html("");
        errorMsg.addClass("hide");
        validMsg.addClass("hide");
    };
    // on blur: validate
    $("#phone").on('blur', function() {
        reset();
        if ($("#phone").val().trim()) {
            if ($("#phone").intlTelInput("isValidNumber")) {
                $("#phone").val($("#phone").intlTelInput("getNumber"));
                validMsg.removeClass("hide");
            } else {
                $("#phone").addClass("error");
                var errorCode = $("#phone").intlTelInput("getValidationError");
                errorMsg.html(errorMap[errorCode]);
                errorMsg.removeClass("hide");
            }
        }
    });
    // on keyup / change flag: reset
    $("#phone").on('change', reset);
    $("#phone").on('keyup', reset);
    // listen to the address dropdown for changes
    $("#select-all-offers").change(function() {
        $("input:checkbox.offers-table").prop('checked', $(this).prop("checked"));
    });
    $('#offers-dtable').DataTable({
        responsive: true,
        "pageLength": 5
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