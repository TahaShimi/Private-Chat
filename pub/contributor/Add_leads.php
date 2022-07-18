<?php
$page_name = "Add_leads";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT w.id_website,w.name FROM contributors_programs cp,websites w,users u WHERE cp.id_advertiserProgram = w.id_website AND  cp.id_contributor=u.id_profile AND  u.id_user=:id AND cp.status = 1");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$accounts = $stmt->fetchAll();
?>
<style>
    .iti {width: 100%;}
    .custom-radio {display: inline-block;margin-top: 10px;}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <h3 class="box-title m-b-0"><?= $trans['Add_leads'] ?></h3>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="home2" role="tabpanel">
                            <div class="p-20">
                                <?php
                                if (isset($_POST['add'])) {
                                    $firstname = $_POST['firstname'];
                                    $lastname = $_POST['lastname'];
                                    $phone = $_POST['phone'];
                                    $country = $_POST['country'];
                                    $websites = $_POST['websites'];
                                    $email = $_POST['email'];
                                    $packages = $_POST['Packages'];
                                    $gender = $_POST['gender'];
                                    $st = $conn->prepare("SELECT * from packages  where  id_package=:IDP");
                                    $st->bindParam(':IDP', $packages, PDO::PARAM_INT);
                                    $st->execute();
                                    $pckg = $st->fetch();
                                    $stmt = $conn->prepare("SELECT * fROM `users` WHERE login=:tt");
                                    $stmt->bindParam(':tt', $email);
                                    $stmt->execute();
                                    $user = $stmt->fetch();
                                    if (!$user) {
                                        $stmt = $conn->prepare("SELECT id_account fROM `websites` WHERE id_website=:id");
                                        $stmt->bindParam(':id', intval($websites));
                                        $stmt->execute();
                                        $id_account = $stmt->fetch();
                                        $st = $conn->prepare("SELECT * from offers  where  id_package=:id AND discount=100");
                                        $st->bindParam(':id', $pckg['id_package'], PDO::PARAM_INT);
                                        $st->execute();
                                        $offre = $st->fetch();
                                        if ($offre) {
                                            $stmt1 = $conn->prepare("INSERT INTO `offers_customers`(`id_customer`, `id_offer`, `created_at`) 
                                                    VALUES (:ic,:iof,NOW())");
                                            $stmt1->bindParam(':ic', $customerId, PDO::PARAM_INT);
                                            $stmt1->bindParam(':iof', $value, PDO::PARAM_INT);
                                            $stmt1->execute();
                                            $messages = $pckg['messages'];
                                        } else $messages = 0;
                                        $stmt1 = $conn->prepare("INSERT INTO `customers`(`firstname`,`lastname`,`gender`, `emailc`, `phone`,`balance`, `country`, `date_start`,id_website,id_account) VALUES (:fn,:ln,:ge,:em,:ph,:bl,:co,NOW(),:pg,:id)");
                                        $stmt1->bindParam(':fn', $firstname);
                                        $stmt1->bindParam(':ln', $lastname);
                                        $stmt1->bindParam(':ph', $phone);
                                        $stmt1->bindParam(':bl', $messages, PDO::PARAM_INT);
                                        $stmt1->bindParam(':co', $country);
                                        $stmt1->bindParam(':em', $email);
                                        $stmt1->bindParam(':ge', $gender);
                                        $stmt1->bindParam(':pg', intval($websites));
                                        $stmt1->bindParam(':id', intval($id_account));
                                        $stmt1->execute();
                                        $customerId = $conn->lastInsertId();

                                        $stmt1 = $conn->prepare("INSERT INTO `leads`(`id_customer`,`id_contributor`,`add_date`, `status`) VALUES (:cu,:co,NOW(),0)");
                                        $stmt1->bindParam(':cu', $customerId);
                                        $stmt1->bindParam(':co', intval($_SESSION['id_user']));
                                        $stmt1->execute();
                                        $pwd = password_hash($firstname, PASSWORD_BCRYPT);

                                        $stmt = $conn->prepare("INSERT INTO `users`(`login`,`password`, `profile`,`id_profile`,`date_add`,`active`,`status`) VALUES (:tt,:ur,4,:id,NOW(),1,1)");
                                        $stmt->bindParam(':tt', $email);
                                        $stmt->bindParam(':ur', $pwd);
                                        $stmt->bindParam(':id', intval($customerId));
                                        $stmt->execute();
                                        unset($_POST);
                                        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>  Lead personal code: " . substr($_SESSION['pseudo'], 0, 3) . '-' . $conn->lastInsertId() . "</div></div>";
                                    } else {
                                        echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> user already exist </div></div>";
                                    }
                                }
                                ?>
                                <form action="" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="firstname"> <?php echo ($trans["first_name"]) ?> : <span class="danger">*</span> </label>
                                                <input type="text" class="form-control " id="firstname" name="firstname" placeholder="Enter First Name" required> </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastname"><?php echo ($trans["last_name"]) ?> : </label>
                                                <input type="text" class="form-control " id="lastname" name="lastname" placeholder="Enter Last Name"> </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label text-right"><?php echo ($trans["phone"]) ?> :<span class="danger">*</span> </label>
                                            <div class="">
                                                <input name="phone" type="tel" id="phone" class="form-control" style="width:100%" placeholder="Enter Phone Number" required>
                                                <span id="valid-msg" class="hide text-success">� Valid</span>
                                                <span id="error-msg" class="hide text-danger">✗ Invalid number</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="country"> <?php echo ($trans["country"]) ?> : <span class="danger">*</span> </label>
                                                <select name="country" id="country" class="form-control select-search country form-control-line" required>
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
                                                <label for="email"><?php echo ($trans["email"]) ?> : <span class="danger">*</span></label>
                                                <input type="text" class="form-control " id="email" name="email" placeholder="Enter Your Email" required> </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div>
                                                <label class="control-label"><?= $trans['gender']?></label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio1" name="gender" value="1" data-text="Male" class="custom-control-input">
                                                <label class="custom-control-label" for="customRadio1"><?= $trans['male']?></label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio2" name="gender" value="2" data-text="Female" class="custom-control-input">
                                                <label class="custom-control-label" for="customRadio2"><?= $trans['female']?></label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="websites"><?php echo ($trans["programs"]) ?> :<span class="danger">*</span> </label>
                                            <select name="websites" id="websites" class="form-control" required>
                                                <option></option>
                                                <?php foreach ($accounts as $account) {
                                                    echo '<option value="' . $account['id_website'] . '">' . $account['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group pckg" style="display: none;">
                                                <label for="Packages">Packages :<span class="danger">*</span> </label>
                                                <select name="Packages" id="Packages" class="form-control" required>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" name="add" class="add btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["add"]) ?></button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
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
</div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script>
    $(document).ready(function() {
        $("#websites").change();
    });
    $("#websites").change(function() {
        let id = $(this).find("option:selected").val();
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                action: 'getPackeges',
                id: id,
                contributor: <?= $_SESSION['id_user'] ?>
            },
            success: function(data) {
                $('#Packages').empty();
                $.each(data, function() {
                    if (parseInt(this.price) == 0) {
                        price = '<?= $trans['free'] ?>';
                    } else price = this.price + this.currency;
                    $('#Packages').append('<option value="' + this.id_package + '">' + this.title + ' <sup> (' + price + ')</sup></option>');
                });
                if (data.length > 1) {
                    $('.pckg').show();
                }
            }
        })
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
        utilsScript: "../../assets/int-phone-number/js/utils.js"
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
                debugger;
            } else {
                $("#phone").addClass("error");
                var errorCode = $("#phone").intlTelInput("getValidationError");
                $("#error-msg").html(errorMap[errorCode]);
                $("#error-msg").removeClass("hide");
                debugger;
            }
        }
    });
    // on keyup / change flag: reset
    $("#phone").on('change', reset);
    $("#phone").on('keyup', reset);
</script>