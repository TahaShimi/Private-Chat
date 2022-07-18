<?php
$page_folder = "customersP";
$page_name = "customer";

include('header.php'); ?>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<link href="../../assets/css/pages/timeline-vertical-horizontal.css" rel="stylesheet">
<div class="row">
    <?php
    $id_customer = "";
    if (isset($_GET['id'])) {
        $id_customer = intval($_GET['id']);
    }
    if ($id_customer == 0) {
        echo '<section id="wrapper" class="col-md-12 error-page m-t-40"><div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">' .  ($trans["feedback_msg"]["something_went_wrong"]) . '</h3><p class="text-muted m-t-30 m-b-30">' .  ($trans["feedback_msg"]["working_on"]) . '</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">' .  ($trans["feedback_msg"]["return_to_dashboard"]) . '</a></div></div></section>';
    } else {
        $stmt = $conn->prepare("SELECT * FROM `customers` c join users u on c.id_customer=u.id_profile and u.profile=4 WHERE c.id_customer = :ID AND c.id_account = :IDA ");
        $stmt->bindParam(':ID', $id_customer, PDO::PARAM_INT);
        $stmt->bindParam(':IDA', $id_account, PDO::PARAM_INT);
        $stmt->execute();
        $total = $stmt->rowCount();
        $result = $stmt->fetchObject();
        $stmt1 = $conn->prepare("SELECT * FROM transactionsc t,packages p  WHERE t.id_customer = :ID AND p.id_package=t.id_package order by t.date_add desc");
        $stmt1->bindParam(':ID', $id_customer, PDO::PARAM_INT);
        $stmt1->execute();
        $transactions=$stmt1->fetchAll();
        if ($total == 0) {
            echo '<section id="wrapper" class="col-md-12 error-page m-t-40"><div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">' .  ($trans["feedback_msg"]["customer_not_found"]) . '</h3><p class="text-muted m-t-30 m-b-30">' .  ($trans["feedback_msg"]["page_not_found"]) . '</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">' .  ($trans["feedback_msg"]["return_to_dashboard"]) . '</a> </div></div></section>';
        } else {
    ?>
            <!-- Column -->
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card"> <img class="card-img" src="../../assets/images/background-customer.jpg" height="456" alt="Card image">
                    <div class="card-img-overlay card-inverse text-white social-profile d-flex justify-content-center">
                        <div class="align-self-center"> <img src="<?php echo '../uploads/customers/' . $result->photo ?>" class="img-circle" width="100">
                            <h4 class="card-title m-t-20"><?php echo $result->firstname . " " . $result->lastname; ?></h4>
                            <h6 class="card-subtitle"><?php echo $result->date_start; ?></h6>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted"><?php echo ($trans["email"]) ?></small>
                        <h6><?php echo $result->emailc; ?></h6>
                        <small class="text-muted p-t-30 db"><?php echo ($trans["phone"]) ?></small>
                        <h6><?php echo $result->phone; ?></h6>
                        <small class="text-muted p-t-30 db"><?php echo ($trans["email"]) ?></small>
                        <h6><?php echo $result->address; ?></h6>
                        <small class="text-muted p-t-30 db">IP Address</small>
                        <h6><?php echo $result->remote_address; ?></h6>
                        <small class="text-muted p-t-30 db"><?php echo ($trans["social_profiles"]) ?></small>
                        <br />
                        <button class="btn btn-circle btn-secondary"><i class="fab fa-facebook"></i></button>
                        <button class="btn btn-circle btn-secondary"><i class="fab fa-twitter"></i></button>
                        <button class="btn btn-circle btn-secondary"><i class="fab fa-youtube"></i></button>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" data-toggle="tab" href="#general_informations" role="tab"><?php echo ($trans["general_informations"]) ?></a> </li>
                        <li class="nav-item"> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'historique') {echo "active";} ?>" data-toggle="tab" href="#historique" role="tab">Logs</a> </li>
                        <li class="nav-item"> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'transactions') {echo "active";} ?>" data-toggle="tab" href="#transactions" role="tab">Transactions</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" id="general_informations" role="tabpanel">
                            <div class="card-body">
                                <?php
                                if (isset($_POST['update'])) {
                                    $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;
                                    $firstname = (isset($_POST['firstname']) && $_POST['firstname'] != '') ? htmlspecialchars($_POST['firstname']) : NULL;
                                    $lastname = (isset($_POST['lastname']) && $_POST['lastname'] != '') ? htmlspecialchars($_POST['lastname']) : NULL;
                                    $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
                                    $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;
                                    $address = (isset($_POST['address']) && $_POST['address'] != '') ? htmlspecialchars($_POST['address']) : NULL;
                                    $country = (isset($_POST['country']) && $_POST['country'] != '') ? htmlspecialchars($_POST['country']) : NULL;
                                    $lang = (isset($_POST['lang']) && $_POST['lang'] != '') ? htmlspecialchars($_POST['lang']) : NULL;

                                    $stmt2 = $conn->prepare("UPDATE `customers` SET `gender`=:gd,`firstname`=:fn, `lastname`=:ln, `emailc`=:em, `phone`=:ph,`address`=:adr, `country`=:cn WHERE `id_customer`=:ID");
                                    $stmt2->bindParam(':gd', $gender, PDO::PARAM_INT);
                                    $stmt2->bindParam(':fn', $firstname, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ln', $lastname, PDO::PARAM_STR);
                                    $stmt2->bindParam(':em', $emailc, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ph', $phone, PDO::PARAM_STR);
                                    $stmt2->bindParam(':adr', $address, PDO::PARAM_STR);
                                    $stmt2->bindParam(':cn', $country, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ID', $id_customer, PDO::PARAM_INT);
                                    $stmt2->execute();
                                    $affected_rows = $stmt2->rowCount();

                                    $stmt3 = $conn->prepare("UPDATE `users` SET `lang`=:lang WHERE `id_user`=:IDU");
                                    $stmt3->bindParam(':lang', $lang, PDO::PARAM_STR);
                                    $stmt3->bindParam(':IDU', $result->id_user, PDO::PARAM_INT);
                                    $stmt3->execute();
                                    $affected_rows3 = $stmt3->rowCount();

                                    if ($affected_rows + $affected_rows3 != 0) {
                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" .  ($trans["feedback_msg"]["account_info_updated"]) . " </div>";
                                        $stmt->execute();
                                        $result = $stmt->fetchObject();
                                    } else {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_failed"]) . " </div>";
                                    }
                                    unset($_POST);
                                }
                                ?>
                                <form class="form-horizontal form-material" action="" method="POST">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo ($trans["gender"]) ?></label>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input" <?php if ($result->gender == 1) {echo "checked";} ?>>
                                            <label class="custom-control-label" for="customRadio1"><?php echo ($trans["male"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input" <?php if ($result->gender == 2) {echo "checked";} ?>>
                                            <label class="custom-control-label" for="customRadio2"><?php echo ($trans["female"]) ?></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo ($trans["first_name"]) ?></label>
                                        <div class="col-md-12">
                                            <input type="text" name="firstname" value="<?php echo $result->firstname; ?>" class="form-control form-control-line">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo ($trans["last_name"]) ?></label>
                                        <div class="col-md-12">
                                            <input type="text" name="lastname" value="<?php echo $result->lastname; ?>" class="form-control form-control-line">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="example-email" class="col-md-12"><?php echo ($trans["email"]) ?></label>
                                        <div class="col-md-12">
                                            <input type="email" value="<?php echo $result->emailc; ?>" class="form-control form-control-line" name="emailc" id="example-email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo ($trans["phone"]) ?></label>
                                        <div class="col-md-12">
                                            <input type="text" value="<?php echo $result->phone; ?>" name="phone" class="form-control form-control-line">
                                            <span id="valid-msg2" data-type="valid-msg" class="hide text-success">✓ Valid</span>
                                            <span id="error-msg2" data-type="error-msg" class="hide text-danger">Invalid number</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="acctInput3" class="col-md-12"><?php echo ($trans["address"]) ?></label>
                                        <div class="col-md-12">
                                            <input type="text" name="address" class="form-control" id="acctInput3" value="<?php echo $result->address; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-12"><?php echo ($trans["country"]) ?></label>
                                        <div class="col-sm-12">
                                            <select name="country" class="form-control form-control-line">
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
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12" for="activity"><?php echo ($trans["language"]) ?></label>
                                        <div class="col-md-12">
                                            <select name="lang" id="lang" class="form-control select2 select-search">
                                                <option value="en" <?php if ($result->lang ==  "en") {echo "selected";} ?>><?php echo ($trans["english"]) ?></option>
                                                <option value="fr" <?php if ($result->lang ==  "fr") {echo "selected";} ?>><?php echo ($trans["french"]) ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" name="update" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'historique') {echo "active";} ?>" id="historique" role="tabpanel">
                            <div class="card-body">
                                <?php
                                $stmt1 = $conn->prepare("SELECT id_user from users where id_profile=:ip and profile=4");
                                $stmt1->bindParam(':ip', $id_customer, PDO::PARAM_INT);
                                $stmt1->execute();
                                $id_user = intval($stmt1->fetchObject()->id_user);

                                $stmt = $conn->prepare("SELECT * FROM logs where `id_user` = :iu order by date desc");
                                $stmt->bindParam(':iu',  $id_user, PDO::PARAM_INT);
                                $stmt->execute();
                                $logs = $stmt->fetchAll();
                                ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <ul class="timeline">
                                                    <?php
                                                    $left = true;
                                                    foreach ($logs as $log) {
                                                    ?>
                                                        <li class="<?php if ($left) {echo 'timeline-inverted';} ?>">
                                                            <?php if ($log["log_type"] == 1) {
                                                                echo '<div class="timeline-badge danger">
                                                                          <i class="fas fa-ban"></i>
                                                                        </div>';
                                                            } elseif ($log["log_type"] == 2) {
                                                                echo '<div class="timeline-badge info">
                                                                          <i class="fas fa-shopping-cart"></i>
                                                                        </div>';
                                                            } elseif ($log["log_type"] == 3) {
                                                                echo '<div class="timeline-badge success">
                                                                          <i class="mdi mdi-gift"></i>
                                                                        </div>';
                                                            } ?>
                                                            <div class="timeline-panel">
                                                                <div class="timeline-heading">
                                                                    <h4 class="timeline-title"><?= $log["description"] ?></h4>
                                                                    <p><small class="text-muted"><i class="fa fa-clock-o"></i> <?= $log["date"] ?></small> </p>
                                                                </div>
                                                                <div class="timeline-body">
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php $left = !$left;
                                                    } ?>
                                                    <li>
                                                        <div class="timeline-badge success"><img class="img-responsive" alt="user" src="<?php echo '../uploads/customers/' . $result->photo ?>" alt="img"> </div>
                                                        <div class="timeline-panel">
                                                            <div class="timeline-heading">
                                                                <h4 class="timeline-title"><?php echo $result->firstname . ' ' . $result->lastname ?></h4>
                                                                <p><small class="text-muted"><i class="fa fa-clock-o"></i> <?= $result->date_start ?></small> </p>
                                                            </div>
                                                            <div class="timeline-body">
                                                                <p>Account Creaion</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'transactions') {echo "active";} ?>" id="transactions" role="tabpanel">
                            <div class="card-body">
                                <table class="table display dt-responsive" id="trans" style="width:100%">
                                        <thead>
                                            <tr>
                                                <td>transaction id</td>
                                                <td>package</td>
                                                <td>date</td>
                                                <td>final prix</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($transactions as $transaction){ ?>
                                                <tr>
                                                <td><?= $transaction['id_transaction'] ?></td>
                                                <td><?= $transaction['title'] ?></td>
                                                <td><?= $transaction['date_add'] ?></td>
                                                <td><?= $transaction['final_price'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
    <?php }
    } ?>
</div>
</div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
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
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/node_modules/dropify/dropify.min.js"></script>
<script src="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script type="text/javascript">
 $('#trans').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true
    });
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        window.history.pushState("", "", "./customer.php?id=<?= $id_customer ?>&tab=" + e.target.hash.substr(1));
    })
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

    $('.dropify').dropify();
</script>
</body>
</html>