<?php
ini_set("display_errors", 1);
$page_name = "Add_publisher";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT w.id_website,w.name FROM contributors_programs cp,websites w,users u WHERE cp.id_advertiserProgram = w.id_website AND  cp.id_contributor=u.id_profile AND  u.id_user=:id AND cp.status = 1");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$accounts = $stmt->fetchAll();
?>
<link href="../assets/node_modules/wizard/steps.css" rel="stylesheet">

<style>
    .iti {
        width: 100%;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card  wizard-content">
            <div class="card-body">

                <?php
                if (isset($_POST['Companyname'])) {
                    $stmt = $conn->prepare("SELECT * FROM `users` u WHERE u.`login`=:lg AND u.profile=5 ");
                    $stmt->bindParam(':lg', $_POST['Contact_email']);
                    $stmt->execute();
                    if ($stmt->fetchObject()) {
                        echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Email already exist</div></div>";
                    } else {
                        $Companyname = $_POST['Companyname'];
                        $Businessname = $_POST['Businessname'];
                        $Registration_number = $_POST['Registration_number'];

                        $VAT_number = $_POST['VAT_number'];
                        $Address1 = $_POST['Address1'];
                        $Address2 = $_POST['Address2'];

                        $City = $_POST['City'];
                        $Province = $_POST['Province'];
                        $Zip_code = $_POST['Zip_code'];

                        $country = $_POST['country'];
                        $Contact_email = $_POST['Contact_email'];
                        $Billing_email = $_POST['Billing_email'];

                        $stmt1 = $conn->prepare("INSERT INTO `publishers`(`country`, `date_add`, `company_name`, `business_name`, `registration_number`, `VAT_number`, `address`, `address2`, `city`, `province`, `Zip_code`, `contact_email`, `billing_email`) VALUES (:a,NOW(),:b,:c,:d,:e,:f,:g,:h,:i,:j,:k,:l)");
                        $stmt1->bindParam(':a', $country);
                        $stmt1->bindParam(':b', $Companyname);
                        $stmt1->bindParam(':c', $Businessname);
                        $stmt1->bindParam(':d', intval($Registration_number));
                        $stmt1->bindParam(':e', intval($VAT_number));
                        $stmt1->bindParam(':f', $Address1);

                        $stmt1->bindParam(':g', $Address2);
                        $stmt1->bindParam(':h', $City);
                        $stmt1->bindParam(':i', $Province);

                        $stmt1->bindParam(':j', intval($Zip_code));
                        $stmt1->bindParam(':k', $Contact_email);
                        $stmt1->bindParam(':l', $Billing_email);
                        $stmt1->execute();
                        $publisher_id = $conn->lastInsertId();

                        $firstname = $_POST['firstname'];
                        $lastname = $_POST['lastname'];
                        $email = $_POST['email'];
                        $phone = $_POST['phone'];

                        $stmt2 = $conn->prepare("INSERT INTO `publishers_management`(`firstname`,`lastname`, `email`, `phone`, `id_publisher`) VALUES (:fn,:ln,:em,:ph,:id)");
                        $stmt2->bindParam(':fn', $firstname);
                        $stmt2->bindParam(':ln', $lastname);
                        $stmt2->bindParam(':em', $email);
                        $stmt2->bindParam(':ph', $phone);
                        $stmt2->bindParam(':id', intval($publisher_id));
                        $stmt2->execute();

                        $pwd = password_hash($firstname, PASSWORD_BCRYPT);
                        if ($publisher_id != 0) {
                            $stmt = $conn->prepare("INSERT INTO `users`(`login`,`password`, `profile`,`id_profile`,`date_add`,`active`,`status`) VALUES (:tt,:ur,5,:id,NOW(),1,1)");
                            $stmt->bindParam(':tt', $email);
                            $stmt->bindParam(':ur', $pwd);
                            $stmt->bindParam(':id', intval($publisher_id));
                            $stmt->execute();
                        }
                        unset($_POST);
                        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Publisher Added successfully </div></div>";
                    }
                }
                ?>
                <form action="" id="myForm" method="POST" class="validation-wizard wizard-circle">
                    <h6 class="box-title m-b-0">General Info</h6>
                    <section>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Companyname">Company name : <span class="danger">*</span> </label>
                                    <input type="text" class="form-control required" id="Companyname" name="Companyname" placeholder="Enter Company name" required> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Businessname">Business name (optional) : </label>
                                    <input type="text" class="form-control " id="Businessname" name="Businessname" placeholder="Enter Business name"> </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Registration_number">Registration number : <span class="danger">*</span> </label>
                                    <input type="text" class="form-control required" id="Registration_number" name="Registration_number" placeholder="Enter Registration number" required> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="VAT_number">VAT number (optional) : </label>
                                    <input type="text" class="form-control " id="VAT_number" name="VAT_number" placeholder="Enter VAT number"> </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Address1">Address <span class="danger">*</span> </label>
                                    <input type="text" class="form-control " id="Address1" name="Address1" placeholder="Enter Address" required> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Address2">Address (optional) : </label>
                                    <input type="text" class="form-control " id="Address2" name="Address2" placeholder="Enter Address2"> </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="City">City <span class="danger">*</span> </label>
                                    <input type="text" class="form-control " id="City" name="City" placeholder="Enter City Name" required> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Province">Province (optional) : </label>
                                    <input type="text" class="form-control " id="Province" name="Province" placeholder="Enter Province"> </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Zip_code">Zip code <span class="danger">*</span> </label>
                                    <input type="text" class="form-control " id="Zip_code" name="Zip_code" placeholder="Enter Zip code" required> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country"> <?php echo ($trans["country"]) ?> : <span class="danger">*</span> </label>
                                    <select name="country" id="country" class="form-control select-search country form-control-line">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Contact_email">Contact e-mail <span class="danger">*</span> </label>
                                    <input type="text" class="form-control " id="Contact_email" name="Contact_email" placeholder="Enter Contact e-mail" required> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Billing_email">Billing e-mail : </label>
                                    <input type="text" class="form-control " id="Billing_email" name="Billing_email" placeholder="Enter Billing e-mail" required> </div>
                            </div>
                        </div>
                    </section>
                    <h6 class="box-title m-t-40">Management</h6>
                    <section>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname"> <?php echo ($trans["first_name"]) ?> : <span class="danger">*</span> </label>
                                    <input type="text" class="form-control " id="firstname" name="firstname" placeholder="Enter First Name" required> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname"><?php echo ($trans["last_name"]) ?> : </label>
                                    <input type="text" class="form-control " id="lastname" name="lastname" placeholder="Enter Last Name" required> </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">email : <span class="danger">*</span> </label>
                                    <input type="text" class="form-control " id="email" name="email" placeholder="Enter First Name" required> </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-right"><?php echo ($trans["phone"]) ?> :<span class="danger">*</span> </label>
                                <div class="">
                                    <input name="phone" type="tel" id="phone" class="form-control" style="width:100%" placeholder="Enter Phone Number" required>
                                    <span id="valid-msg" class="hide text-success">� Valid</span>
                                    <span id="error-msg" class="hide text-danger">✗ Invalid number</span>
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
</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
<footer class="footer"><?php echo ($trans["footer"]) ?></footer> <!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/node_modules/popper/popper.min.js"></script>
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../assets/js/custom.min.js"></script>
<script src="../assets/node_modules/wizard/jquery.steps.min.js"></script>

<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<script src="../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../assets/node_modules/wizard/jquery.steps.min.js"></script>
<script src="../assets/node_modules/wizard/jquery.validate.min.js"></script>
<script type="text/javascript" src="../assets/node_modules/multiselect/js/jquery.multi-select.js"></script>
<script src="../assets/node_modules/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script src="../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>

<script>
    var form = $("#myForm").show();
    $(document).ready(function() {
        $("#country").change(function() {
            $('#phone').intlTelInput('setCountry', $(this).val());
        });
    })

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
                var offers_selected = $("input:checkbox.offers-table:checked").map(function() {
                    $("#summary_offers").append('<p class="form-control-static">' + $(this).data('getString') + '</p>')
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
    var errorMsg = $("#error-msg");
    var validMsg = $("#valid-msg");
    // here, the index maps to the error code returned from getValidationError - see readme
    var errorMap = ["Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];

    // initialise plugin
    var iti = $("#phone").intlTelInput({
        nationalMode: true,
        autoPlaceholder: "off",
        initialCountry: "fr",
        utilsScript: "../assets/int-phone-number/js/utils.js"
    });

    var reset = function() {
        $("#phone").removeClass("error");
        $("#error-msg").html("");
        $("#error-msg").addClass("hide");
        $("#valid-msg").addClass("hide");
    };

    // on blur: validate
    $("#phone").on('blur', function() {
        reset();
        if ($("#phone").val().trim()) {
            if ($("#phone").intlTelInput("isValidNumber")) {
                $("#phone").val($("#phone").intlTelInput("getNumber"));
                $("#valid-msg").removeClass("hide");
            } else {
                $("#phone").addClass("error");
                var errorCode = $("#phone").intlTelInput("getValidationError");
                $("#error-msg").html(errorMap[errorCode]);
                $("#error-msg").removeClass("hide");
            }
        }
    });


    // on keyup / change flag: reset
    $("#phone").on('change', reset);
    $("#phone").on('keyup', reset);
</script>