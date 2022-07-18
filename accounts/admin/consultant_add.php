<?php
$page_name = "add_consultant";
include('header.php');
$s0 = $conn->prepare("SELECT `id_website`, `name` FROM `websites` WHERE `id_account` = :ID");
$s0->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s0->execute();
$websites_rows = $s0->rowCount();
$websites = $s0->fetchAll();

?>
<style>
    .bootstrap-tagsinput {
        width: 100%;
    }
</style>

<div class="row">
    <?php if ($account_status != 2) {
        echo '<div class="col-md-12 msg_bloc"><h2>your account is not approved yet</h2></div>';
    }
    if (isset($_POST['add'])) {
        $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;
        $first_name = (isset($_POST['first_name']) && $_POST['first_name'] != '') ? htmlspecialchars($_POST['first_name']) : NULL;
        $last_name = (isset($_POST['last_name']) && $_POST['last_name'] != '') ? htmlspecialchars($_POST['last_name']) : NULL;
        $pseudo = (isset($_POST['pseudo']) && $_POST['pseudo'] != '') ? htmlspecialchars($_POST['pseudo']) : NULL;
        $photo = NULL;
        $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
        $websites = (isset($_POST['websites'])) ? implode(",", $_POST['websites']) : NULL;
        if (isset($_FILES["picture"]["name"]) && $_FILES["picture"]["name"] != "") {
            $dirLogo = '../uploads/consultants/';
            $uploadImg = removeAccents($_FILES["picture"]["name"]);
            $uploadImgTmp = removeAccents($_FILES["picture"]["tmp_name"]);
            $fileData1 = pathinfo(basename($uploadImg));
            $Imgnom = basename($uploadImg, "." . $fileData1['extension']);
            $photo = md5(uniqid()) . "-" . $id_account . '.' . $fileData1['extension'];
            $target_path1 = ($dirLogo . $photo);
            while (file_exists($target_path1)) {
                $photo = md5(uniqid()) . "-" . $id_account . '.' . $fileData1['extension'];
                $target_path1 = ($dirLogo . $photo);
            }
            move_uploaded_file($uploadImgTmp, $target_path1);
        }

        $stmt1 = $conn->prepare("INSERT INTO `consultants`(`gender`, `firstname`, `lastname`, `pseudo`, `photo`, `profile_url`, `status`, `websites`, `id_account`) VALUES (:gd,:fn,:ln,:ps,:ph,:ur,1,:IDW,:ID)");
        $stmt1->bindParam(':gd', $gender, PDO::PARAM_STR);
        $stmt1->bindParam(':fn', $first_name, PDO::PARAM_STR);
        $stmt1->bindParam(':ln', $last_name, PDO::PARAM_STR);
        $stmt1->bindParam(':ps', $pseudo, PDO::PARAM_STR);
        $stmt1->bindParam(':ph', $photo, PDO::PARAM_STR);
        $stmt1->bindParam(':ur', $website, PDO::PARAM_STR);
        $stmt1->bindParam(':IDW', $websites, PDO::PARAM_STR);
        $stmt1->bindParam(':ID', $id_account, PDO::PARAM_INT);
        $stmt1->execute();
        $last_id = $conn->lastInsertId();
        $affected_rows = $stmt1->rowCount();
        if ($affected_rows > 0) {
            $datec = date('Y-m-d', strtotime('now'));
            $username = $first_name . " " . $last_name;
            $login = (isset($pseudo) && $pseudo != '') ? $pseudo : $first_name . "-" . $last_id;
            $pwd = $pseudo . "-" . $last_id;
            $pwd = password_hash($pwd, PASSWORD_BCRYPT);
            $stmt3 = $conn->prepare("INSERT INTO `users`(`login`, `password`, `profile`, `id_profile`, `date_add`, `active`, `last_connect`, `lastday`, `nbr_essai`,`status`,`lang`) VALUES (:lo,:pw,3,:ID,:dt,1,NULL,NULL,NULL,1,'en')");
            $stmt3->bindParam(':lo', $login, PDO::PARAM_STR);
            $stmt3->bindParam(':pw', $pwd, PDO::PARAM_STR);
            $stmt3->bindParam(':ID', $last_id, PDO::PARAM_INT);
            $stmt3->bindParam(':dt', $datec, PDO::PARAM_STR);
            $stmt3->execute();
            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["consultant_added"]) . " <br><a href='consultant.php?id=" . $last_id . "' class='text-muted'>" . ($trans["feedback_msg"]["consultant_detail"]) . "</a></div></div>";
        } else {
            echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["consultant_failed"]) . " </div></div>";
        }
        unset($_POST);
    }
    ?>
    <form id="form" action="" method="POST" class="row col-md-12 pr-0" enctype="multipart/form-data">
        <div class="col-md-12">
            <div class="card card-body">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-group col-12 col-md-6">
                            <label for="input-file"><?php echo ($trans["avatar"]) ?></label>
                            <input type="file" name="picture" id="input-file" class="dropify" data-height="150"/>
                        </div>           
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label class="control-label  d-block"><?php echo ($trans["gender"]) ?></label>
                                <div class="custom-control custom-radio d-inline mr-3">
                                    <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadio1"><?php echo ($trans["male"]) ?></label>
                                </div>
                                <div class="custom-control custom-radio  d-inline">
                                    <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadio2"><?php echo ($trans["female"]) ?></label>
                                </div>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="acctInput3"><?php echo ($trans["pseudo"]) ?></label>
                                <input type="text" name="pseudo" class="form-control" id="acctInput3" placeholder="">
                            </div>
                        </div>             
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label for="acctInput1"><?php echo ($trans["first_name"]) ?></label>
                                <input type="text" name="first_name" class="form-control" id="acctInput1" placeholder="">
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="acctInput2"><?php echo ($trans["last_name"]) ?></label>
                                <input type="text" name="last_name" class="form-control" id="acctInput2" placeholder="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label for="acctInput4"><?php echo ($trans["website_url"]) ?></label>
                                <input type="url" name="website" class="form-control" id="acctInput4" placeholder="">
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="websites"><?php echo ($trans["website"]) ?></label>
                                <select id="websites" class="select2 m-b-10 select2-multiple" style="width: 100%" multiple="multiple" name="websites[]" data-placeholder="">
                                    <option></option>
                                    <?php if ($websites_rows > 0) {
                                        foreach ($websites as $web) {
                                            echo "<option value='" . $web['id_website'] . "'>" . $web['name'] . "</option>";
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <hr>
                <div class="row">
                    <div class="col-sm-12 col-xs-12 text-right">
                        <button type="submit" name="add" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["add"]) ?></button>
                        <button type="submit" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/dropify/dropify.min.js"></script>
<script src="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript">
    $(".select2").select2();
    $('.dropify').dropify();
    $('#tagsinput').tagsinput({
        confirmKeys: [13, 188]
    });
    $('.bootstrap-tagsinput input').on('keypress', function(e) {
        if (e.keyCode == 13) {
            e.keyCode = 188;
            e.preventDefault();
        };
    });
    $('.genKey').click(function() {
        $.ajax({
            url: 'functions_ajax.php',
            method: 'POST',
            dataType: 'JSON',
            data: {
                type: 'genkey',
                id: <?= $_SESSION['id_account'] ?>
            },
            success: function(data) {
                if (data != 0) {
                    $('.clink').text('http://45.76.121.27/chat/index.php?cod=200&id=' + data);
                }
            }
        })
    });
</script>
</body>

</html>