<?php
$page_name = "my_account";
ob_start();

include('header.php');
$stmt = $conn->prepare("SELECT a.*, b.* FROM `users` a LEFT JOIN `consultants` b ON a.`id_profile` = b.`id_consultant` WHERE a.`id_user` = :ID");
$stmt->bindParam(':ID', $_SESSION['id_user'], PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchObject();

$id_company = intval($result->id_account);
$s0 = $conn->prepare("SELECT `id_website`, `name` FROM `websites` WHERE `id_account` = :ID");
$s0->bindParam(':ID', $id_company, PDO::PARAM_INT);
$s0->execute();
$websites = $s0->fetchAll();
$websites_rows = $s0->rowCount();
$picture = "../../assets/img/account.png";
if ($result->photo != NULL) {
    $picture = "../../photos/account/" . $result->photo;
}
?>
<style>
    .bootstrap-tagsinput {
        all: unset;
    }

    .bootstrap-tagsinput input {
        width: 100%;
    }
</style>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<link href="../../assets/css/customers.css" rel="stylesheet">
<div class="row">
    <!-- Column -->
    <div class="col-lg-3 col-xlg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <?php
                if (isset($_POST["update-avatar"])) {
                    $picture = NULL;
                    if (isset($_FILES["picture"]["name"]) && $_FILES["picture"]["name"] != "") {
                        $dirLogo = '../uploads/consultants/';
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
                        $stmt2 = $conn->prepare("UPDATE `consultants` SET `photo`=:ph WHERE `id_consultant`=:ID");
                        $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
                        $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                        $stmt2->execute();
                        $affected_rows = $stmt2->rowCount();

                        if ($affected_rows != 0) {
                            echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["avatar_updated"]) . " </div>";
                            $stmt->execute();
                            $result = $stmt->fetchObject();
                        } else {
                            echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["avatar_failed"]) . " </div>";
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
                    <h5 class="card-title m-t-10"><?php echo $result->firstname . " " . $result->lastname; ?></h5>
                    <h6 class="card-subtitle"><?php echo $result->pseudo; ?></h6>
                    <div class="row text-center justify-content-md-center">

                    </div>
                </center>
            </div>
            <div>
                <hr>
            </div>
            <div class="card-body"> <small class="text-muted"><?php echo ($trans["email"]) ?> </small>
                <h6><?php echo $result->emailc; ?></h6> <small class="text-muted p-t-30 db"><?php echo ($trans["phone"]) ?></small>
                <h6><?php echo $result->phone; ?></h6>
                <small class="text-muted p-t-30 db"><?php echo ($trans["social_profiles"]) ?></small>
                <br />
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-facebook"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-twitter"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-youtube-play"></i></button>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-9 col-xlg-9 col-md-9">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) &&  $_GET['tab'] == 'general_information')) {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#general_information" role="tab"><?php echo ($trans["account_informations"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'connection_information') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#connection_information" role="tab"><?php echo ($trans["authentication_credentials"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'settings') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#settings" role="tab"><?php echo ($trans["side_bar"]["settings"]) ?></a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) &&  $_GET['tab'] == 'general_information')) {
                                            echo "active";
                                        } ?>" id="general_information" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-account'])) {
                            $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;
                            $first_name = (isset($_POST['first_name']) && $_POST['first_name'] != '') ? htmlspecialchars($_POST['first_name']) : NULL;
                            $last_name = (isset($_POST['last_name']) && $_POST['last_name'] != '') ? htmlspecialchars($_POST['last_name']) : NULL;
                            $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
                            $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;
                            $pseudo = (isset($_POST['pseudo']) && $_POST['pseudo'] != '') ? htmlspecialchars($_POST['pseudo']) : NULL;
                            $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                            $websites_ids = (isset($_POST['websites'])) ? implode(",", $_POST['websites']) : NULL;
                            $stmt2 = $conn->prepare("UPDATE `consultants` SET `gender`=:gd,`firstname`=:fn,`lastname`=:ln, `emailc`=:em, `phone`=:ph,`websites`=:wb,`profile_url`=:ur WHERE `id_consultant`=:ID");
                            $stmt2->bindParam(':gd', $gender, PDO::PARAM_INT);
                            $stmt2->bindParam(':fn', $first_name, PDO::PARAM_STR);
                            $stmt2->bindParam(':ln', $last_name, PDO::PARAM_STR);
                            $stmt2->bindParam(':em', $emailc, PDO::PARAM_STR);
                            $stmt2->bindParam(':ph', $phone, PDO::PARAM_STR);
                            $stmt2->bindParam(':ur', $website, PDO::PARAM_STR);
                            $stmt2->bindParam(':wb', $websites_ids, PDO::PARAM_STR);
                            $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                            $stmt2->execute();
                            $affected_rows = $stmt2->rowCount();
                            if ($affected_rows != 0) {
                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["account_info_updated"]) . " </div>";
                                $stmt->execute();
                                $result = $stmt->fetchObject();
                            } else {
                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>  " . ($trans["feedback_msg"]["account_info_failed"]) . " </div>";
                            }
                            unset($_POST);
                        }
                        ?>
                        <form class="form-horizontal form-material" action="" method="POST">
                            <div class="form-group m-t-20">
                                <label class="control-label"><?php echo ($trans["gender"]) ?></label>
                                <div>
                                    <div class="custom-control custom-radio d-inline m-l-10">
                                        <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input" <?php if ($result->gender == 1) {
                                                                                                                                        echo "checked";
                                                                                                                                    } ?>>
                                        <label class="custom-control-label" for="customRadio1"><?php echo ($trans["male"]) ?></label>
                                    </div>
                                    <div class="custom-control custom-radio d-inline m-l-5">
                                        <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input" <?php if ($result->gender == 2) {
                                                                                                                                        echo "checked";
                                                                                                                                    } ?>>
                                        <label class="custom-control-label" for="customRadio2"><?php echo ($trans["female"]) ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="acctInput1"><?php echo ($trans["first_name"]) ?></label>
                                <input type="text" name="first_name" class="form-control form-control-line" id="acctInput1" value="<?php echo $result->firstname; ?>">
                            </div>
                            <div class="form-group">
                                <label for="acctInput2"><?php echo ($trans["last_name"]) ?></label>
                                <input type="text" name="last_name" class="form-control form-control-line" id="acctInput2" value="<?php echo $result->lastname; ?>">
                            </div>
                            <div class="form-group">
                                <label for="acctInput3"><?php echo ($trans["pseudo"]) ?></label>
                                <input type="text" name="pseudo" class="form-control" id="acctInput3" value="<?php echo $result->pseudo; ?>">
                            </div>
                            <div class="form-group">
                                <label for="example-email"><?php echo ($trans["email"]) ?></label>
                                <input type="email" value="<?php echo $result->emailc; ?>" class="form-control form-control-line" name="emailc" id="emailc">
                            </div>
                            <div class="form-group">
                                <label for=""><?php echo ($trans["phone"]) ?></label>
                                <input type="text" value="<?php echo $result->phone; ?>" class="form-control form-control-line" name="phone" id="phonc">
                            </div>
                            <div class="form-group">
                                <label for="acctInput4"><?php echo ($trans["website_url"]) ?></label>
                                <input type="url" name="website" class="form-control" id="acctInput4" value="<?php echo $result->profile_url; ?>">
                            </div>
                            <div class="form-group">
                                <label for="websites"><?php echo ($trans["websites"]) ?></label>
                                <select id="websites" class="select2 m-b-10 select2-multiple" style="width: 100%" multiple="multiple" name="websites[]" data-placeholder="Choose websites">
                                    <option></option>
                                    <?php
                                    $arr = explode(",", $result->websites);
                                    foreach ($websites as $web) { ?>
                                        <option value="<?= $web['id_website'] ?>" <?php if (in_array($web['id_website'], $arr)) {
                                                                                        echo "selected";
                                                                                    } ?>><?= $web['name'] ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="update-account" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'connection_information') {
                                            echo "active";
                                        } ?>" id="connection_information" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-connection-credentials'])) {

                            if (isset($_POST['current-password']) && isset($_POST['new-password']) && isset($_POST['password-confirmation']) && isset($_POST['login'])) {
                                $currentPassword = htmlentities($_POST['current-password']);
                                $newPassword = htmlentities($_POST['new-password']);
                                $PasswordConfirmation = htmlentities($_POST['password-confirmation']);
                                $login = htmlentities($_POST['login']);
                                $stmt2 = $conn->prepare("SELECT * from users where login=:lg");
                                $stmt2->bindParam(':lg', $login, PDO::PARAM_STR);
                                $stmt2->execute();
                                $user = $stmt2->fetchObject();
                                if (!$user) {
                                    if ($newPassword != $PasswordConfirmation) {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["wrong_password_confirmation"]) . "  </div>";
                                        unset($_POST);
                                    } elseif (!empty($currentPassword)) {
                                        if (!password_verify($currentPassword, $result->password)) {
                                            echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["wrong_current_password"]) . " </div>";
                                            unset($_POST);
                                        } else {
                                            $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                                            $stmt2 = $conn->prepare("UPDATE `users` SET `password`=:nps,`login`=:lg, `password_updated_at`=NOW() WHERE `id_user`=:ID");
                                            $stmt2->bindParam(':nps', $newHashedPassword, PDO::PARAM_STR);
                                            $stmt2->bindParam(':lg', $login, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ID', $_SESSION['id_user'], PDO::PARAM_INT);
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
                                } else echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["user_exist"]) . " </div>";
                                unset($_POST);
                            } else {
                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["credentials_failed"]) . " </div>";
                                unset($_POST);
                            }
                        }
                        ?>
                        <form class="form-horizontal form-material" action="" method="POST">
                            <div class="form-group m-t-20">
                                <label><?php echo ($trans["login"]) ?></label>
                                <div>
                                    <input type="text" name="login" value="<?= $result->login ?>" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo ($trans["current_password"]) ?></label>
                                <div>
                                    <input type="password" class="form-control form-control-line" name="current-password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo ($trans["new_password"]) ?></label>
                                <div>
                                    <input type="password" class="form-control form-control-line" name="new-password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo ($trans["password_confirmation"]) ?></label>
                                <div>
                                    <input type="password" class="form-control form-control-line" name="password-confirmation">
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="update-connection-credentials" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'settings') {
                                            echo "active";
                                        } ?>" id="settings" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-settings'])) {
                            $audioNotification = (isset($_POST['audio_notification']) && $_POST['audio_notification'] == 'on') ? true : false;
                            $browserNotification = (isset($_POST['browser_notification']) && $_POST['browser_notification'] == 'on') ? true : false;
                            $stmt2 = $conn->prepare("UPDATE `consultants` SET `audio_notification`=:an,`browser_notification`=:bn WHERE `id_consultant`=:ID");
                            $stmt2->bindParam(':an', $audioNotification, PDO::PARAM_BOOL);
                            $stmt2->bindParam(':bn', $browserNotification, PDO::PARAM_BOOL);
                            $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                            $stmt2->execute();
                            $affected_rows = $stmt2->rowCount();
                            $stmt3 = $conn->prepare("UPDATE `users` SET `lang`=:lg WHERE `id_user`=:IDU");
                            $stmt3->bindParam(':lg', $_POST['lang'], PDO::PARAM_STR);
                            $stmt3->bindParam(':IDU', $result->id_user, PDO::PARAM_INT);
                            $stmt3->execute();
                            $affected_rows2 = $stmt3->rowCount();
                            if ($affected_rows + $affected_rows2 != 0) {
                                setcookie("lang", $_POST['lang'], time() + 2 * 24 * 60 * 60);
                                header("Refresh:0");
                            } else {
                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["settings_failed"]) . " </div>";
                            }
                            unset($_POST);
                        }
                        ?>
                        <form class="form-horizontal form-material" action="" method="POST">
                            <div class="m-t-20">
                                <div class="switchery-demo m-b-30">
                                    <label for="audio_notification" class="m-r-20"><?php echo ($trans["audio_notification"]) ?></label>
                                    <input type="checkbox" id="audio_notification" name="audio_notification" <?php if ($result->audio_notification) {
                                                                                                                    echo "checked";
                                                                                                                } ?> class="js-switch" data-color="#5ab95e" data-size="small" />
                                </div>

                                <div class="switchery-demo m-b-30">
                                    <label for="browser_notification" class="m-r-20"><?php echo ($trans["browser_notification"]) ?></label>
                                    <input type="checkbox" id="browser_notification" name="browser_notification" <?php if ($result->browser_notification) {
                                                                                                                        echo "checked";
                                                                                                                    } ?> class="js-switch" data-color="#5ab95e" data-size="small" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="lang"><?php echo ($trans["language"]) ?></label>
                                <select name="lang" id="lang" class="form-control select2 select-search">
                                    <option value="en" <?php if ($result->lang ==  "en") {
                                                            echo "selected";
                                                        } ?>><?php echo ($trans["english"]) ?></option>
                                    <option value="fr" <?php if ($result->lang ==  "fr") {
                                                            echo "selected";
                                                        } ?>><?php echo ($trans["french"]) ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="update-settings" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
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
<footer class="footer">© 2019 Private chat by Diamond services</footer>
<!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script>
    var conn = new WebSocket(wsCurEnv);
    conn.onopen = function(e) {
        conn.send(JSON.stringify({
            command: "attachAccount",
            account: sender,
            role: sender_role,
            name: sender_pseudo,
            sender_avatar: sender_avatar,
            id_group: id_group
        }));
    };
</script>

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
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="../../assets/node_modules/switchery/switchery.min.js"></script>

<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/pages/consultants.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(window).on('unload', function() {
            logout();
        });
        $('.bootstrap-tagsinput').children('input').addClass('form-control');

        function logout() {
            $.post("functionsAjax.php", {
                action: "logout",
                sender: sender,
            });
        }
        $(".select2").select2();
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            window.history.pushState("", "", "./profile.php?tab=" + e.target.hash.substr(1));
        })
        $("#audio_notification_value").val(<?= $account->audio_notification ?>)
        $("#browser_notification_value").val(<?= $account->browser_notification ?>)
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });
        $('.profile-pic').attr('src', "<?php echo '../uploads/consultants/' . $result->photo ?>");
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
        $('#tagsinput').tagsinput({
            confirmKeys: [13, 188]
        });
        $('.bootstrap-tagsinput input').on('keypress', function(e) {
            if (e.keyCode == 13) {
                e.keyCode = 188;
                e.preventDefault();
            };
        });
    });
</script>
</body>

</html>