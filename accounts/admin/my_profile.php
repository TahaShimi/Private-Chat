<?php
$page_name = "my_account";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT a.*, b.*, c.* FROM `users` a LEFT JOIN `accounts` b ON a.`id_profile` = b.`id_account` LEFT JOIN `managers` c ON c.`id_account` = b.`id_account` WHERE a.`id_user` = :ID");
$stmt->bindParam(':ID', $id_user, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchObject();
?>
<link href="../../assets/css/customers.css" rel="stylesheet">
<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card">
            <div class="card-body">
                <?php
                if (isset($_POST["update-avatar"])) {
                    $picture = NULL;
                    if (isset($_FILES["picture"]["name"]) && $_FILES["picture"]["name"] != "") {
                        $dirLogo = '../uploads/accounts/';
                        $uploadImg = removeAccents($_FILES["picture"]["name"]);
                        $uploadImgTmp = removeAccents($_FILES["picture"]["tmp_name"]);
                        $fileData1 = pathinfo(basename($uploadImg));
                        $Imgnom = basename($uploadImg, "." . $fileData1['extension']);
                        $photo = substr($Imgnom, 0, 25) . "-" . $id_account . '.' . $fileData1['extension'];
                        $target_path1 = ($dirLogo . $photo);
                        while (file_exists($target_path1)) {
                            $photo = substr($Imgnom, 0, 25) . "-" . $id_account . '.' . $fileData1['extension'];
                            $target_path1 = ($dirLogo . $photo);
                        }
                        move_uploaded_file($uploadImgTmp, $target_path1);
                        $stmt2 = $conn->prepare("UPDATE `accounts` SET `photo`=:ph WHERE `id_account`=:ID");
                        $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
                        $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                        $stmt2->execute();
                        $affected_rows = $stmt2->rowCount();

                        if ($affected_rows != 0) {
                            echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["avatar_updated"]) . " </div>";
                            $stmt->execute();
                            $result = $stmt->fetchObject();
                        } else {
                            echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["avatar_failed"]) . " </div>";
                        }
                        unset($_POST);
                    }
                }
                ?>
                <center class="m-t-30">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="avatar-wrapper m-b-10">
                            <img class="profile-pic" src="" />
                            <div class="upload-button">
                                <i class="mdi mdi-arrow-up-circle" aria-hidden="true"></i>
                            </div>
                            <input class="file-upload" name="picture" type="file" accept="image/*" />
                        </div>
                        <button type="submit" name="update-avatar" id="update-avatar" class="btn btn-success btn-rounded m-t-0"><i class="mdi mdi-check"></i> <?php echo ($trans["update_avatar"]) ?></button>
                    </form>
                    <h5 class="card-title m-t-10"><?php echo $result->business_name; ?></h5>
                    <div class="row text-center justify-content-md-center">
                        <div class="col-4"><a href="javascript:void(0)" class="link"><i class="mdi mdi-account-group"></i>
                                <font class="font-medium">254</font>
                            </a></div>
                        <div class="col-4"><a href="javascript:void(0)" class="link"><i class="mdi mdi-basket"></i>
                                <font class="font-medium">54</font>
                            </a></div>
                    </div>
                </center>
            </div>
            <div>
                <hr>
            </div>
            <div class="card-body"> <small class="text-muted">Email address </small>
                <h6><?php echo $result->emailc; ?></h6> <small class="text-muted p-t-30 db">Phone</small>
                <h6><?php echo $result->phone; ?></h6> <small class="text-muted p-t-30 db">Address</small>
                <h6><?php echo $result->address; ?></h6>
                <small class="text-muted p-t-30 db">Social Profile</small>
                <br />
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-facebook"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-twitter"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-youtube-play"></i></button>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'authentication_credentials') {echo "active";} ?>" data-toggle="tab" href="#authentication_credentials" role="tab"><?php echo ($trans["authentication_credentials"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'application_settings') {echo "active";} ?>" data-toggle="tab" href="#application_settings" role="tab"><?php echo ($trans["application_settings"]) ?></a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'authentication_credentials') {echo "active";} ?>" id="authentication_credentials" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-connection-credentials'])) {
                            if (isset($_POST['current-password']) && isset($_POST['new-password']) && isset($_POST['password-confirmation'])) {
                                $currentPassword = htmlentities($_POST['current-password']);
                                $newPassword = htmlentities($_POST['new-password']);
                                $PasswordConfirmation = htmlentities($_POST['password-confirmation']);
                                if ($newPassword != $PasswordConfirmation) {
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["wrong_password_confirmation"]) . " </div>";
                                    unset($_POST);
                                } elseif (!empty($currentPassword)) {
                                    if (!password_verify($currentPassword, $result->password)) {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["wrong_current_password"]) . " </div>";
                                        unset($_POST);
                                    } else {
                                        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                                        $stmt2 = $conn->prepare("UPDATE `users` SET `password`=:nps, `password_updated_at`=NOW() WHERE `id_user`=:ID");
                                        $stmt2->bindParam(':nps', $newHashedPassword, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ID', $id_user, PDO::PARAM_INT);
                                        $stmt2->execute();
                                        $affected_rows = $stmt2->rowCount();
                                        if ($affected_rows != 0) {
                                            echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["credentials_updated"]) . " </div>";
                                            $stmt->execute();
                                            $result = $stmt->fetchObject();
                                        } else {
                                            echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["credentials_failed"]) . " </div>";
                                        }
                                        unset($_POST);
                                    }
                                }
                            } else {
                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["credentials_failed"]) . " </div>";
                                unset($_POST);
                            }
                        }
                        ?>
                        <form class="form-horizontal form-material" action="" method="POST">
                            <div class="form-group">
                                <label class="col-md-12"><?php echo ($trans["login"]) ?></label>
                                <div class="col-md-12">
                                    <input type="text" value="<?php echo $result->login ?>" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><?php echo ($trans["current_password"]) ?></label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control form-control-line" name="current-password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><?php echo ($trans["new_password"]) ?></label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control form-control-line" name="new-password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12"><?php echo ($trans["password_confirmation"]) ?></label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control form-control-line" name="password-confirmation">
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="form-group m-b-0">
                                <div class="col-sm-12">
                                    <button type="submit" name="update-connection-credentials" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'application_settings') {echo "active";} ?>" id="application_settings" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-app-settings'])) {
                            if (isset($_POST['lang'])) {
                                $stmt3 = $conn->prepare("UPDATE `users` SET `lang`=:lg WHERE `id_user`=:IDU");
                                $stmt3->bindParam(':lg', $_POST['lang'], PDO::PARAM_STR);
                                $stmt3->bindParam(':IDU', $id_user, PDO::PARAM_INT);
                                $stmt3->execute();
                                $affected_rows = $stmt3->rowCount();
                                if ($affected_rows) {
                                    setcookie("lang", $_POST['lang'], time() + 2 * 24 * 60 * 60);
                                    header("Refresh:0");
                                } else {
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["settings_failed"]) . " </div>";
                                    unset($_POST);
                                }
                            }
                        }
                        ?>
                        <form class="form-horizontal form-material" action="" method="POST">
                            <div class="form-group">
                                <label class="col-md-12" for="activity"><?php echo ($trans["language"]) ?></label>
                                <div class="col-md-12">
                                    <select name="lang" id="lang" class="form-control select2 select-search">
                                        <option value="en" <?php if ($result->lang ==  "en") {echo "selected";} ?>><?php echo ($trans["english"]) ?></option>
                                        <option value="fr" <?php if ($result->lang ==  "fr") {echo "selected";} ?>><?php echo ($trans["french"]) ?></option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="form-group m-b-0">
                                <div class="col-sm-12">
                                    <button type="submit" name="update-app-settings" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
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
<script src="../../assets/node_modules/dropify/dropify.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<script type="text/javascript">
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        window.history.pushState("", "", "./my_profile.php?tab=" + e.target.hash.substr(1));
    })
    $('.profile-pic').attr('src', "<?php echo '../uploads/accounts/' . $result->photo ?>");
    var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.profile-pic').attr('src', e.target.result);
                $('#avatar').val(e.target.result);
                $('#update-avatar').css("display", 'block');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".file-upload").on('change', function() {
        readURL(this);
    });

    $(".upload-button").on('click', function() {
        $(".file-upload").click();
    });
</script>
</body>
</html>