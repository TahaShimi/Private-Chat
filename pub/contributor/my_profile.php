<?php
$page_name = "profile";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT u.password,u.login FROM contributors c,users u  WHERE id_contributor = u.id_profile AND u.id_user=:ID AND u.profile=6 ");
$stmt->bindParam(':ID', intval($_SESSION['id_user']), PDO::PARAM_INT);
$stmt->execute();
$contributor = $stmt->fetchObject();
?>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<div class="row">
    <div class="col-lg-12 col-xlg-9 col-md-12">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'authentication_credentials') {echo "active";} ?>" data-toggle="tab" href="#authentication_credentials" role="tab">Authentication credentials</a> </li>
            </ul>
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
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["wrong_password_confirmation"]) . "  </div>";
                                    unset($_POST);
                                } elseif (!empty($currentPassword)) {
                                    if (!password_verify($currentPassword, $contributor->password)) {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["wrong_current_password"]) . " </div>";
                                        unset($_POST);
                                    } else {
                                        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                                        $stmt2 = $conn->prepare("UPDATE `users` SET `password`=:nps, `password_updated_at`=NOW() WHERE `id_user`=:ID");
                                        $stmt2->bindParam(':nps', $newHashedPassword, PDO::PARAM_STR);
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
                                    <input type="text" disabled value="<?php echo $contributor->login ?>" class="form-control form-control-line">
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
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>