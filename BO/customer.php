<?php
$page_name = "Customer";
include('header.php'); ?>
<link href="../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<link href="../assets/css/pages/chat-app-page.css" rel="stylesheet">
<link href="../assets/css/custom.css" rel="stylesheet">
<div class="row">
    <?php
    $id_account = "";
    if (isset($_GET['id'])) {
        $id_account = intval($_GET['id']);
    }
    if ($id_account == 0) {
        echo '<section id="wrapper" class="col-md-12 error-page m-t-40">
                        <div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">Oops! Something went wrong !</h3><p class="text-muted m-t-30 m-b-30">We will work on the resolution right away.</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Return to the dashboard</a></div></div></section>';
    } else {
        $stmt = $conn->prepare("SELECT a.*, b.* FROM `accounts` a LEFT JOIN `managers` b ON b.`id_account` = a.`id_account` WHERE a.`id_account` = :ID");
        $stmt->bindParam(':ID', $id_account, PDO::PARAM_INT);
        $stmt->execute();
        $total = $stmt->rowCount();
        $result = $stmt->fetchObject();
        /*         */
        if ($total == 0) {
            echo '<section id="wrapper" class="col-md-12 error-page m-t-40"><div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">Customer does not exist !</h3><p class="text-muted m-t-30 m-b-30">We could not find the page you are looking for.</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Return to the dashboard</a> </div></div></section>';
        } else {
    ?>
            <div class="col-md-12 col-md-6">
                <div class="card">
                    <div class="d-flex flex-row">
                        <?php
                        if ($result->status == 0) {
                            echo '<div class="p-10 bg-danger"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-danger">Declined</h3></div>';
                        } elseif ($result->status == 1) {
                            echo '<div class="p-10 bg-success"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-success">New</h3></div><div class="col text-right align-self-center"><button type="button" class="btn btn-outline-info" id="approve"><i class="fa fa-check"></i> Approve</button></div>';
                        } elseif ($result->status == 2) {
                            echo '<div class="p-10 bg-info"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-info">Approved</h3></div>';
                        } elseif ($result->status == 3) {
                            echo '<div class="p-10 bg-warning"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-warning">Checking</h3></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-body">
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) &&  $_GET['tab'] == 'general_information')) {
                                                                        echo "active";
                                                                    } ?>" data-toggle="tab" href="#general_information" role="tab"><?php echo ($trans["account_informations"]) ?></a> </li>
                        <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Manager') {
                                                                        echo "active";
                                                                    } ?>" data-toggle="tab" href="#Manager" role="tab">Manager</a> </li>
                        <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Contacts') {
                                                                        echo "active";
                                                                    } ?>" data-toggle="tab" href="#Contacts" role="tab">Contacts</a> </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="general_information" role="tabpanel">
                            <div class="card card-body">
                                <h3 class="box-title m-b-0">Customer #<?php echo $id_account; ?></h3>
                                <p class="text-muted m-b-30 font-13"> Edit your account informations.</p>
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">
                                        <?php
                                        if (isset($_POST['update1'])) {
                                            $business_name = (isset($_POST['business_name']) && $_POST['business_name'] != '') ? htmlspecialchars($_POST['business_name']) : NULL;
                                            $registration = (isset($_POST['registration']) && $_POST['registration'] != '') ? htmlspecialchars($_POST['registration']) : NULL;
                                            $taxid = (isset($_POST['taxid']) && $_POST['taxid'] != '') ? htmlspecialchars($_POST['taxid']) : NULL;
                                            $address = (isset($_POST['address']) && $_POST['address'] != '') ? htmlspecialchars($_POST['address']) : NULL;
                                            $pcode = (isset($_POST['pcode']) && $_POST['pcode'] != '') ? htmlspecialchars($_POST['pcode']) : NULL;
                                            $city = (isset($_POST['city']) && $_POST['city'] != '') ? htmlspecialchars($_POST['city']) : NULL;
                                            $country = (isset($_POST['country']) && $_POST['country'] != '') ? $_POST['country'] : NULL;
                                            $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;
                                            $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
                                            $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                                            $stmt2 = $conn->prepare("UPDATE `accounts` SET `business_name`=:bus,`registration`=:reg,`taxid`=:tax,`address`=:adr,`code_postal`=:cp,`city`=:ct,`country`=:cnt,`phone`=:ph,`emailc`=:em,`website`=:web WHERE `id_account`=:ID");
                                            $stmt2->bindParam(':bus', $business_name, PDO::PARAM_STR);
                                            $stmt2->bindParam(':reg', $registration, PDO::PARAM_STR);
                                            $stmt2->bindParam(':tax', $taxid, PDO::PARAM_STR);
                                            $stmt2->bindParam(':adr', $address, PDO::PARAM_STR);
                                            $stmt2->bindParam(':cp', $pcode, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ct', $city, PDO::PARAM_STR);
                                            $stmt2->bindParam(':cnt', $country, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ph', $phone, PDO::PARAM_STR);
                                            $stmt2->bindParam(':em', $emailc, PDO::PARAM_STR);
                                            $stmt2->bindParam(':web', $website, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                            $stmt2->execute();
                                            $affected_rows = $stmt2->rowCount();

                                            if ($affected_rows != 0) {
                                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The account informations has been updated successfully </div>";
                                                $stmt->execute();
                                                $result = $stmt->fetchObject();
                                            } else {
                                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The account informations has not been updated successfully </div>";
                                            }
                                            unset($_POST);
                                        }
                                        ?>
                                        <form action="" method="POST">
                                            <div class="form-group">
                                                <label for="acctInput1">Business name</label>
                                                <input type="text" name="business_name" class="form-control" id="acctInput1" value="<?php echo $result->business_name; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="acctInput2">Registration number</label>
                                                <input type="text" name="registration" class="form-control" id="acctInput2" value="<?php echo $result->registration; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="acctInput22">Tax ID</label>
                                                <input type="text" name="taxid" class="form-control" id="acctInput22" value="<?php echo $result->taxid; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="acctInput3">Address</label>
                                                <input type="text" name="address" class="form-control" id="acctInput3" value="<?php echo $result->address; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="acctInput4">Postal code</label>
                                                <input type="text" name="pcode" class="form-control" id="acctInput4" value="<?php echo $result->code_postal; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="acctInput44">City</label>
                                                <input type="text" name="city" class="form-control" id="acctInput44" value="<?php echo $result->city; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="country">Country</label>
                                                <select name="country" id="country" class="form-control select-search country">
                                                    <option></option>
                                                    <?php
                                                    foreach ($countries as $key => $country) {
                                                        if ($key == $result->country) {
                                                            echo '<option value="' . $key . '" selected>' . $country . '</option>';
                                                        } else {
                                                            echo '<option value="' . $key . '">' . $country . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Phone number</label>
                                                <input type="tel" name="phone" id="phone" class="form-control phonenumber" value="<?php echo $result->phone; ?>">
                                                <span id="valid-msg" data-type="valid-msg" class="hide text-success">✓ Valid</span>
                                                <span id="error-msg" data-type="error-msg" class="hide text-danger">Invalid number</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="Email1">Email</label>
                                                <input type="email" name="emailc" class="form-control" id="Email1" value="<?php echo $result->emailc; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="acctInput7">Website</label>
                                                <input type="url" name="website" class="form-control" id="acctInput7" value="<?php echo $result->website; ?>">
                                            </div>
                                            <br>
                                            <hr>
                                            <button type="submit" name="update1" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                            <button type="submit" class="btn btn-secondary waves-effect waves-light">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane " id="Manager" role="tabpanel">
                            <div class="card card-body">
                                <h3 class="box-title m-b-0">Manager</h3>
                                <p class="text-muted m-b-30 font-13"> Edit the manager informations.</p>
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">
                                        <?php
                                        if (isset($_POST['update2'])) {
                                            $first_name = (isset($_POST['first_name']) && $_POST['first_name'] != '') ? htmlspecialchars($_POST['first_name']) : NULL;
                                            $last_name = (isset($_POST['last_name']) && $_POST['last_name'] != '') ? htmlspecialchars($_POST['last_name']) : NULL;
                                            $phone2 = (isset($_POST['phone2']) && $_POST['phone2'] != '') ? htmlspecialchars($_POST['phone2']) : NULL;
                                            $emailc2 = (isset($_POST['emailc2']) && $_POST['emailc2'] != '') ? htmlspecialchars($_POST['emailc2']) : NULL;
                                            $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;

                                            $stmt2 = $conn->prepare("UPDATE `managers` SET `gender`=:ge,`firstname`=:fn,`lastname`=:ln,`phonep`=:ph2,`emailp`=:em2 WHERE `id_account`=:ID");
                                            $stmt2->bindParam(':ge', $gender, PDO::PARAM_INT);
                                            $stmt2->bindParam(':fn', $first_name, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ln', $last_name, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ph2', $phone2, PDO::PARAM_STR);
                                            $stmt2->bindParam(':em2', $emailc2, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                            $stmt2->execute();
                                            $affected_rows = $stmt2->rowCount();

                                            if ($affected_rows != 0) {
                                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The manager informations has been updated successfully </div>";
                                                $stmt->execute();
                                                $result = $stmt->fetchObject();
                                            } else {
                                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The manager informations has not been updated successfully </div>";
                                            }
                                            unset($_POST);
                                        }
                                        ?>
                                        <form action="" method="POST">
                                            <div class="form-group">
                                                <label for="acctInput1">First name</label>
                                                <input type="text" name="first_name" class="form-control" id="acctInput1" value="<?php echo $result->firstname; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="acctInput2">Last name</label>
                                                <input type="text" name="last_name" class="form-control" id="acctInput2" value="<?php echo $result->lastname; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Gender</label>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input" <?php if ($result->gender == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?>>
                                                    <label class="custom-control-label" for="customRadio1">Male</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input" <?php if ($result->gender == 2) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?>>
                                                    <label class="custom-control-label" for="customRadio2">Female</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="phone">Personal phone number</label>
                                                <input type="tel" name="phone2" id="phone2" class="form-control phonenumber" value="<?php echo $result->phonep; ?>">
                                                <span id="valid-msg2" data-type="valid-msg" class="hide text-success">✓ Valid</span>
                                                <span id="error-msg2" data-type="error-msg" class="hide text-danger">Invalid number</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="Email1">Personal email</label>
                                                <input type="email" name="emailc2" class="form-control" id="Email1" value="<?php echo $result->emailp; ?>">
                                            </div>
                                            <br>
                                            <hr>
                                            <button type="submit" name="update2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                            <button type="reset" class="btn btn-secondary waves-effect waves-light">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane " id="Contacts" role="tabpanel">
                            <div class="m-t-30">
                                <table id="contactform" class="table table-hover dt-responsive display m-t-30" style="width:100%">
                                    <thead>
                                        <tr>
                                            <td>Subject</td>
                                            <td>Reason</td>
                                            <td>Number</td>
                                            <td>Solved</td>
                                            <td>Pending</td>
                                            <td>Percentage</td>
                                            <td>Details</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($trans['subject'] as $key => $subject) {
                                            $stmt = $conn->prepare("SELECT subject ,COUNT(*) as total,(SELECT count(*) from contact_ticket ctt,users u where ctt.status=0 and ctt.id_account=ct.id_account AND ctt.id_customer=u.id_user AND u.profile!=2 AND ctt.subject=ct.subject) as unsolved,(SELECT count(*) from contact_ticket ctt,users u where ctt.status=1 and ctt.id_account=ct.id_account AND ctt.id_customer=u.id_user AND u.profile!=2 AND ctt.subject=ct.subject) as solved FROM contact_ticket ct,users u WHERE ct.id_account=:ID AND ct.id_customer=u.id_user AND u.profile!=2 AND ct.subject=:IDS GROUP BY ct.subject");
                                            $stmt->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                            $stmt->bindParam(':IDS', intval($key), PDO::PARAM_INT);
                                            $stmt->execute();
                                            $contact = $stmt->fetchObject();

                                        ?>
                                            <tr style="background-color: #c2d2e2;">
                                                <td><?= $subject ?></td>
                                                <td></td>
                                                <td><?= intval($contact->total) ?></td>
                                                <td><?= intval($contact->solved) ?></td>
                                                <td><?= intval($contact->unsolved) ?></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <?php
                                            if (isset($trans['reasons'][$key])) {
                                                foreach ($trans['reasons'][$key] as $key1 => $reason) {
                                                    $stmt = $conn->prepare("SELECT reason ,COUNT(*) as total,(SELECT count(*) from contact_ticket ctt,users u where ctt.status=0 and ctt.id_account=ct.id_account AND ctt.id_customer=u.id_user AND u.profile!=2 AND (ctt.reason=ct.reason OR (ctt.reason IS NULL AND ct.reason IS NULL)) AND ctt.subject=ct.subject) as unsolved,(SELECT count(*) from contact_ticket ctt,users u where ctt.status=1 and ctt.id_account=ct.id_account AND ctt.id_customer=u.id_user AND u.profile!=2 AND (ctt.reason=ct.reason OR (ctt.reason IS NULL AND ct.reason IS NULL))AND ctt.subject=ct.subject) as solved FROM contact_ticket ct,users u WHERE ct.id_account=:ID AND ct.id_customer=u.id_user AND u.profile!=2 AND ct.subject=:IDS AND ct.reason=:IDR GROUP BY ct.reason");
                                                    $stmt->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                                    $stmt->bindParam(':IDS', intval($key), PDO::PARAM_INT);
                                                    $stmt->bindParam(':IDR', intval($key1), PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $reason = $stmt->fetchObject();
                                            ?>
                                                    <tr>
                                                        <td></td>
                                                        <td><?= $trans['reasons'][$key][$key1] ?></td>
                                                        <td><?= intval($reason->total) ?></td>
                                                        <td><?= intval($reason->solved) ?></td>
                                                        <td><?= intval($reason->unsolved) ?></td>
                                                        <td><?= intval($reason->total) == 0 ? 0 : (intval($reason->total) / intval($contact->total)) * 100 ?> %</td>
                                                        <td><a href="javascript:void(0)" class="details badge badge-info" data-id="<?= $key ?>" data-reason="<?= $key1 ?>">Details</a></td>
                                                    </tr>
                                        <?php }
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php }
    } ?>
</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>
<div class="modal" id="messages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header d-block">
                <h4 class="modal-title">
                    <h4 class="box-title subject"></h4><span class="text-muted reason">Contacts</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-material">
                    <input class="form-control p-2" type="text" placeholder="Search customer" id="search_bar">
                </div>
                <div class="tickets" id="tick">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
<footer class="footer">© 2019 Private chat by Diamond services</footer> <!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
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
<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<script src="../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script type="text/javascript">
    var errorMsg = $("#error-msg"),
        validMsg = $("#valid-msg");
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
        errorMsg.html("");
        errorMsg.addClass("hide");
        validMsg.addClass("hide");
    };
    $("#search_bar").keyup(function() {
            $this = $(this);
            var ticket = jQuery(".ticket");
            ticket.each(function() {
                if ($(this).find('h4').text().toLowerCase().indexOf($this.val()) >= 0) {
                    $(this).css("display", "flex");
                } else {
                    $(this).css("display", "none");
                }
            });
        });
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

    $('.details').click(function() {
        let subject = $(this).data('id');
        let reason = $(this).data('reason');
        $.ajax({
            url: 'functions_ajax.php',
            dataType: 'json',
            type: "POST",
            data: {
                type: "getSubject",
                subject: subject,
                id: <?= $id_account ?>,
                reason: reason
            },
            success: function(data) {
                $('.tickets').empty();
                $.each(data, function() {
                    $('.tickets').append(this);
                });
                $('#messages').modal('show');
            }
        });
    });
    var table = $('#contactform').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        "ordering": false
    });
    $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
    $('.dt-button').removeClass('dt-button');
    // on keyup / change flag: reset
    $("#phone").on('change', reset);
    $("#phone").on('keyup', reset);
    // listen to the address dropdown for changes

    var errorMsg2 = $("#error-msg2"),
        validMsg2 = $("#valid-msg2");

    // here, the index maps to the error code returned from getValidationError - see readme
    var errorMap2 = ["Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];

    // initialise plugin
    var iti = $("#phone2").intlTelInput({
        nationalMode: true,
        autoPlaceholder: "off",
        initialCountry: "fr",
        utilsScript: "../assets/int-phone-number/js/utils.js"
    });

    var reset2 = function() {
        $("#phone2").removeClass("error");
        errorMsg2.html("");
        errorMsg2.addClass("hide");
        validMsg2.addClass("hide");
    };

    // on blur: validate
    $("#phone2").on('blur', function() {
        reset();
        if ($("#phone2").val().trim()) {
            if ($("#phone2").intlTelInput("isValidNumber")) {
                $("#phone2").val($("#phone2").intlTelInput("getNumber"));
                validMsg2.removeClass("hide");
            } else {
                $("#phone2").addClass("error");
                var errorCode2 = $("#phone2").intlTelInput("getValidationError");
                errorMsg2.html(errorMap2[errorCode2]);
                errorMsg2.removeClass("hide");
            }
        }
    });

    // on keyup / change flag: reset
    $("#phone2").on('change', reset2);
    $("#phone2").on('keyup', reset2);


    $("#country").on('change', function() {
        $('#phone').intlTelInput('setCountry', $(this).val());
        $('#phone2').intlTelInput('setCountry', $(this).val());
    });

    $("#approve").click(function() {
        $.ajax({
            url: 'functions_ajax.php',
            dataType: 'json',
            data: {
                type: "approve",
                id_account: "<?php echo $id_account; ?>"
            },
            success: function(code_html, statut) {
                location.reload();
            },
            error: function(xhr, status, error) {
                alert("Unsuccessful request");
            }
        });
    });
</script>
</body>

</html>