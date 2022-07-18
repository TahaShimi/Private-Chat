<?php
$page_name = "my_account";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT a.*, b.*, c.`name` AS website_name FROM `users` a LEFT JOIN `customers` b ON a.`id_profile` = b.`id_customer` LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE a.`id_user` = :ID");
$stmt->bindParam(':ID', $id_user, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchObject();

$st = $conn->prepare("SELECT (SELECT count(*) from messages m where m.sender=:IDU) as sent_messages, (SELECT sum(final_price) from transactionsc tc where tc.id_customer=:IDC) as total_spent, (SELECT count(*) from offers_customers oc where oc.id_customer=:IDC) as offers_count");
$st->bindParam(':IDU', $id_user, PDO::PARAM_INT);
$st->bindParam(':IDC', $id_account, PDO::PARAM_INT);
$st->execute();
$stats = $st->fetch();

$id_website = intval($result->id_website);
?>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
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
                        $dirLogo = '../uploads/customers/';
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
                        $stmt2 = $conn->prepare("UPDATE `customers` SET `photo`=:ph WHERE `id_customer`=:ID");
                        $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
                        $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                        $stmt2->execute();
                        $affected_rows = $stmt2->rowCount();

                        if ($affected_rows != 0) {
                            echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["avatar_updated"]) . " </div>";
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
                    <h6 class="card-subtitle"><?php echo $result->website_name; ?></h6>
                    <div class="row text-center justify-content-md-center">
                        <div class="col-4">
                            <div><?= $trans["total_buys"] ?></div>
                            <a href="javascript:void(0)" class="link">
                                <font class="font-medium"><?= $stats["total_spent"] ?> <sup>$</sup></font>
                            </a>
                        </div>
                        <div class="col-4">
                            <div><?= $trans["total_offers"] ?></div>
                            <a href="javascript:void(0)" class="link">
                                <font class="font-medium"><?= $stats["offers_count"] ?> <sup><?php echo ($trans["offers"]) ?></sup></font>
                            </a>
                        </div>
                        <div class="col-4">
                            <div><?= $trans["total_sendMsg"] ?></div>
                            <a href="javascript:void(0)" class="link">
                                <font class="font-medium"><?= $stats["sent_messages"] ?> <sup>Msg</sup></font>
                            </a>
                        </div>
                    </div>
                </center>
            </div>
            <div>
                <hr>
            </div>
            <div class="card-body"> <small class="text-muted"><?php echo ($trans["email"]) ?></small>
                <h6><?php echo $result->emailc; ?></h6> <small class="text-muted p-t-30 db"><?php echo ($trans["phone"]) ?></small>
                <h6><?php echo $result->phone; ?></h6> <small class="text-muted p-t-30 db"><?php echo ($trans["address"]) ?></small>
                <h6><?php echo $result->address; ?></h6>
                <small class="text-muted p-t-30 db"><?php echo ($trans["social_profiles"]) ?></small>
                <br />
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-facebook"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-twitter"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="mdi mdi-youtube-play"></i></button>
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="card">
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) &&  $_GET['tab'] == 'account')) {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#account" role="tab"><?php echo ($trans["account_informations"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'settings') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#settings" role="tab"><?php echo ($trans["authentication_credentials"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'socials_links') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#socials_links" role="tab"><?php echo ($trans["socials_links"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'application_settings') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#application_settings" role="tab"><?php echo ($trans["application_settings"]) ?></a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) &&  $_GET['tab'] == 'account')) {
                                            echo "active";
                                        } ?>" id="account" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-account'])) {
                            $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;
                            $first_name = (isset($_POST['first_name']) && $_POST['first_name'] != '') ? htmlspecialchars($_POST['first_name']) : NULL;
                            $last_name = (isset($_POST['last_name']) && $_POST['last_name'] != '') ? htmlspecialchars($_POST['last_name']) : NULL;
                            $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
                            $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;
                            $address = (isset($_POST['address']) && $_POST['address'] != '') ? htmlspecialchars($_POST['address']) : NULL;
                            $country = (isset($_POST['country']) && $_POST['country'] != '') ? htmlspecialchars($_POST['country']) : NULL;

                            $stmt2 = $conn->prepare("UPDATE `customers` SET `gender`=:gd,`firstname`=:fn,`lastname`=:ln, `emailc`=:em, `phone`=:ph0, `address`=:adr,`country`=:cn WHERE `id_customer`=:ID");
                            $stmt2->bindParam(':gd', $gender, PDO::PARAM_INT);
                            $stmt2->bindParam(':fn', $first_name, PDO::PARAM_STR);
                            $stmt2->bindParam(':ln', $last_name, PDO::PARAM_STR);
                            $stmt2->bindParam(':em', $emailc, PDO::PARAM_STR);
                            $stmt2->bindParam(':ph0', $phone, PDO::PARAM_STR);
                            $stmt2->bindParam(':adr', $address, PDO::PARAM_STR);
                            $stmt2->bindParam(':cn', $country, PDO::PARAM_STR);
                            $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                            $stmt2->execute();
                            $affected_rows = $stmt2->rowCount();

                            if ($affected_rows != 0) {
                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["account_info_updated"]) . " </div>";
                                $stmt->execute();
                                $result = $stmt->fetchObject();
                            } else {
                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["account_info_failed"]) . " </div>";
                            }
                            unset($_POST);
                        }
                        ?>
                        <form class="form-horizontal form-material" action="" method="POST">
                            <div class="form-group m-t-20">
                                <label class="control-label"><?php echo ($trans["gender"]) ?></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input" <?php if ($result->gender == 1) {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>
                                    <label class="custom-control-label" for="customRadio1"><?php echo ($trans["male"]) ?></label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input" <?php if ($result->gender == 2) {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>
                                    <label class="custom-control-label" for="customRadio2"><?php echo ($trans["female"]) ?></label>
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
                                <label for="example-email"><?php echo ($trans["email"]) ?></label>
                                <input type="email" value="<?php echo $result->emailc; ?>" class="form-control form-control-line" name="emailc" id="emailc">
                            </div>
                            <div class="form-group">
                                <label for=""><?php echo ($trans["phone"]) ?></label>
                                <input type="text" value="<?php echo $result->phone; ?>" class="form-control form-control-line" name="phone" id="phonc">
                            </div>
                            <div class="form-group">
                                <label for="acctInput3"><?php echo ($trans["address"]) ?></label>
                                <input type="text" name="address" class="form-control form-control-line" id="acctInput3" value="<?php echo $result->address; ?>">
                            </div>
                            <div class="form-group">
                                <label for="country"><?php echo ($trans["country"]) ?></label>
                                <select name="country" id="country" class="form-control select-search country form-control-line">
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
                                <button type="submit" name="update-account" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'settings') {
                                            echo "active";
                                        } ?>" id="settings" role="tabpanel">
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
                            <div class="form-group m-t-20">
                                <label><?php echo ($trans["login"]) ?></label>
                                <div>
                                    <input type="text" value="<?php echo $result->login ?>" class="form-control form-control-line">
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
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'socials_links') {
                                            echo "active";
                                        } ?>" id="socials_links" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-socials-links'])) {
                            $facebook = (isset($_POST['facebook']) && $_POST['facebook'] != '') ? htmlspecialchars($_POST['facebook']) : NULL;
                            $twitter = (isset($_POST['twitter']) && $_POST['twitter'] != '') ? htmlspecialchars($_POST['twitter']) : NULL;
                            $instagram = (isset($_POST['instagram']) && $_POST['instagram'] != '') ? htmlspecialchars($_POST['instagram']) : NULL;

                            $stmt2 = $conn->prepare("UPDATE `customers` SET `facebook`=:fb,`twitter`=:tw,`instagram`=:ins WHERE `id_customer`=:ID");
                            $stmt2->bindParam(':fb', $facebook, PDO::PARAM_STR);
                            $stmt2->bindParam(':tw', $twitter, PDO::PARAM_STR);
                            $stmt2->bindParam(':ins', $instagram, PDO::PARAM_STR);
                            $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                            $stmt2->execute();
                            $affected_rows = $stmt2->rowCount();

                            if ($affected_rows != 0) {
                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["socials_links_updated"]) . " </div>";
                                $stmt->execute();
                                $result = $stmt->fetchObject();
                            } else {
                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["socials_links_failed"]) . " </div>";
                            }
                            unset($_POST);
                        }
                        ?>
                        <form class="form-horizontal form-material" action="" method="POST">
                            <div class="form-group m-t-20">
                                <label for="fcbAccount">Facebook</label>
                                <input type="text" name="facebook" class="form-control" id="fcbAccount" value="<?php echo $result->facebook; ?>">
                            </div>
                            <div class="form-group">
                                <label for="twAccount">Twitter</label>
                                <input type="text" name="twitter" class="form-control" id="twAccount" value="<?php echo $result->twitter; ?>">
                            </div>
                            <div class="form-group">
                                <label for="instAcount">Instagram</label>
                                <input type="text" name="instagram" class="form-control" id="instAcount" value="<?php echo $result->instagram; ?>">
                            </div>
                            <div class="form-group">
                                <button type="submit" name="update-socials-links" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'application_settings') {
                                            echo "active";
                                        } ?>" id="application_settings" role="tabpanel">
                    <div class="card-body">
                        <?php
                        if (isset($_POST['update-app-settings'])) {
                            if (isset($_POST['lang'])) {
                                $audioNotification = (isset($_POST['audio_notification']) && $_POST['audio_notification'] == 'on') ? true : false;
                                $browserNotification = (isset($_POST['browser_notification']) && $_POST['browser_notification'] == 'on') ? true : false;
                                $stmt2 = $conn->prepare("UPDATE `customers` SET `audio_notification`=:an,`browser_notification`=:bn WHERE `id_customer`=:ID");
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
                                    unset($_POST);
                                }
                            }
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
                                <label class="col-md-12" for="lang"><?php echo ($trans["language"]) ?></label>
                                <div class="col-md-12">
                                    <select name="lang" id="lang" class="form-control select2 select-search">
                                        <option value="en" <?php if ($result->lang ==  "en") {
                                                                echo "selected";
                                                            } ?>><?php echo ($trans["english"]) ?></option>
                                        <option value="fr" <?php if ($result->lang ==  "fr") {
                                                                echo "selected";
                                                            } ?>><?php echo ($trans["french"]) ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
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
</div>
</div>
</div>
<footer class="footer"><?php echo ($trans["footer"]) ?></footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/js/env.js"></script>
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
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/pages/customers.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/node_modules/switchery/switchery.min.js"></script>
<script src="../../assets/js/pages/update_password.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script type="text/javascript">
    var balance = 0;
    $(document).ready(function() {
        $("#audio_notification_value").val(<?= $account->audio_notification ?>)
        $("#browser_notification_value").val(<?= $account->browser_notification ?>)
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            window.history.pushState("", "", "./profile.php?tab=" + e.target.hash.substr(1));
        })
        $('.profile-pic').attr('src', "<?php echo '../uploads/customers/' . $result->photo ?>");
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
        if (unlimited == 0) {
            $.ajax({
                url: "../customerTrait.php",
                type: "POST",
                data: {
                    action: "getBalance",
                    customer_id: customer_id,
                },
                dataType: "json",
                success: function(dataResult) {
                    if (dataResult.statusCode == 200) {
                        balance = dataResult.balance;
                        $(".header-balance-text").text(balance);
                        if (balance == 0) {
                            $("#out_of_balace").css("display", "block");
                        }
                    }
                }
            });
        }
    });
</script>
</body>

</html>