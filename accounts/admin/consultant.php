<?php
$page_folder = "consultants";
$page_name = "consultant";
include('header.php');
?>
<style>
    .bootstrap-tagsinput {width: 100%;}
    .toggle-password {background: transparent;border: transparent;}
    .toggle-password:hover {cursor: pointer;}
    .stat-cards {background: aliceblue;}
    .custom-control {display: inline;}
</style>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<link href="../../assets/css/customers.css" rel="stylesheet">
<div class="row">
    <?php
    $id_consultant = "";
    if (isset($_GET['id'])) {
        $id_consultant = intval($_GET['id']);
    }
    if ($id_consultant == 0) {
        echo '<section id="wrapper" class="col-md-12 error-page m-t-40">
        <div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">' .  ($trans["feedback_msg"]["something_went_wrong"]) . '</h3><p class="text-muted m-t-30 m-b-30">' .  ($trans["feedback_msg"]["working_on"]) . '</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">' .  ($trans["feedback_msg"]["return_to_dashboard"]) . '</a></div></div></section>';
    } else {
        $cst = $conn->prepare("SELECT c.*,a.landing_page,u.status as conStatus,u.lang FROM consultants c join users u on c.id_consultant=u.id_profile join accounts a on a.id_account=c.id_account  and u.profile=3 WHERE c.id_consultant = :ID AND c.id_account = :IDA");
        $cst->bindParam(':ID', $id_consultant, PDO::PARAM_INT);
        $cst->bindParam(':IDA', $id_account, PDO::PARAM_INT);
        $cst->execute();
        $total = $cst->rowCount();
        $result = $cst->fetchObject();
        if ($total == 0) {
            echo '<section id="wrapper" class="col-md-12 error-page m-t-40"><div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">' .  ($trans["feedback_msg"]["consultant_not_found"]) . '</h3><p class="text-muted m-t-30 m-b-30">' .  ($trans["feedback_msg"]["page_not_found"]) . '</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">' .  ($trans["feedback_msg"]["return_to_dashboard"]) . '</a> </div></div></section>';
        } else {
            $s0 = $conn->prepare("SELECT `id_website`, `name` FROM `websites` WHERE `id_account` = :ID");
            $s0->bindParam(':ID', $id_account, PDO::PARAM_INT);
            $s0->execute();
            $websites_rows = $s0->rowCount();
            $websites = $s0->fetchAll();
            $readRights = json_decode($result->read_rights);

            $picture = "../../assets/img/account.png";
            if ($result->photo != NULL) {
                $picture = "../uploads/consultants/" . $result->photo;
            }
            ?>
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
                                $photo = substr($Imgnom, 0, 25) . "-" . $id_consultant . '.' . $fileData1['extension'];
                                $target_path1 = ($dirLogo . $photo);
                                while (file_exists($target_path1)) {
                                    $photo = substr($Imgnom, 0, 25) . "-" . $id_consultant . '.' . $fileData1['extension'];
                                    $target_path1 = ($dirLogo . $photo);
                                }
                                move_uploaded_file($uploadImgTmp, $target_path1);
                                $stmt2 = $conn->prepare("UPDATE `consultants` SET `photo`=:ph WHERE `id_consultant`=:ID");
                                $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
                                $stmt2->bindParam(':ID', $id_consultant, PDO::PARAM_INT);
                                $stmt2->execute();
                                $affected_rows = $stmt2->rowCount();

                                if ($affected_rows != 0) {
                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" .  ($trans["feedback_msg"]["avatar_updated"]) . "</div>";
                                    $cst->execute();
                                    $result = $cst->fetchObject();
                                } else {
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["avatar_failed"]) . "</div>";
                                }
                                unset($_POST);
                            }
                        }
                        ?>
                        <button type="button" class="btn btn-success status" data-status="0" <?=$result->conStatus==1?'':'style="display:none"'?>>
                            <i class="mdi mdi-check text " aria-hidden="true"></i>
                            <span class="text ">Active</span>
                        </button>
                        <button type="button" class="btn btn-danger status" data-status="1" <?=$result->conStatus==0?'':'style="display:none"' ?>>
                            <i class="mdi mdi-close " aria-hidden="true"></i>
                            <span class="text">Desactivé</span>
                        </button>
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
                        <h6><?php echo $result->emailc; ?></h6> <small class="text-muted p-t-30 db"><?php echo ($trans["phone"]) ?> </small>
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
                        <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" data-toggle="tab" href="#general_informations" role="tab"><?php echo ($trans["general_informations"]) ?></a> </li>
                        <?php if ($result->landing_page == 1) { ?>
                            <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'contact_informations') {echo "active";} ?>" data-toggle="tab" href="#contact_informations" role="tab"><?php echo ($trans["contact_informations"]) ?></a> </li>
                        <?php } ?>
                        <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'credentials') {echo "active";} ?>" data-toggle="tab" href="#credentials" role="tab"><?php echo ($trans["authentication_credentials"]) ?></a> </li>
                        <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'stats') {echo "active";} ?>" data-toggle="tab" href="#stats" role="tab"><?php echo ($trans["statistics"]) ?></a> </li>
                        <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'read') {echo "active";} ?>" data-toggle="tab" href="#read" role="tab"><?php echo ($trans["read_rights"]) ?></a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" id="general_informations" role="tabpanel">
                            <div class="card-body">
                                <?php
                                if (isset($_POST['update1'])) {
                                    $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;
                                    $first_name = (isset($_POST['first_name']) && $_POST['first_name'] != '') ? htmlspecialchars($_POST['first_name']) : NULL;
                                    $last_name = (isset($_POST['last_name']) && $_POST['last_name'] != '') ? htmlspecialchars($_POST['last_name']) : NULL;
                                    $pseudo = (isset($_POST['pseudo']) && $_POST['pseudo'] != '') ? htmlspecialchars($_POST['pseudo']) : NULL;
                                    $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
                                    $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;
                                    $photo = NULL;
                                    $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                                    $websites_ids = (isset($_POST['websites'])) ? implode(",", $_POST['websites']) : NULL;
                                    $lang = (isset($_POST['lang']) && $_POST['lang'] != '') ? htmlspecialchars($_POST['lang']) : NULL;

                                    $stmt2 = $conn->prepare("UPDATE `consultants` SET `gender`=:gd,`firstname`=:fn,`lastname`=:ln,`pseudo`=:pd, `emailc`=:em, `phone`=:ph0,`profile_url`=:ur,`websites`=:wb  WHERE `id_consultant`=:ID");
                                    $stmt2->bindParam(':gd', $gender, PDO::PARAM_INT);
                                    $stmt2->bindParam(':fn', $first_name, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ln', $last_name, PDO::PARAM_STR);
                                    $stmt2->bindParam(':pd', $pseudo, PDO::PARAM_STR);
                                    $stmt2->bindParam(':em', $emailc, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ph0', $phone, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ur', $website, PDO::PARAM_STR);
                                    $stmt2->bindParam(':wb', $websites_ids, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ID', $id_consultant, PDO::PARAM_INT);
                                    $stmt2->execute();
                                    $affected_rows = $stmt2->rowCount();

                                    $stmt3 = $conn->prepare("UPDATE `users` SET `lang`=:lang WHERE `id_user`=:IDU");
                                    $stmt3->bindParam(':lang', $lang, PDO::PARAM_STR);
                                    $stmt3->bindParam(':IDU', $result->id_user, PDO::PARAM_INT);
                                    $stmt3->execute();
                                    $affected_rows3 = $stmt3->rowCount();

                                    if ($affected_rows + $affected_rows3 != 0) {
                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_updated"]) . "</div>";
                                        $cst->execute();
                                        $result = $cst->fetchObject();
                                    } else {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_failed"]) . " </div>";
                                    }
                                    unset($_POST);
                                }
                                if (isset($_POST['updateRights'])) {
                                    $rights = array();
                                    for ($i = 0; $i < 10; $i++) {
                                        $rights[$i] = isset($_POST[$i]) ? 1 : 0;
                                    }
                                    $stmt2 = $conn->prepare("UPDATE `consultants` SET read_rights=:ri WHERE `id_consultant`=:ID");
                                    $stmt2->bindParam(':ri', json_encode($rights), PDO::PARAM_STR);
                                    $stmt2->bindParam(':ID', $id_consultant, PDO::PARAM_INT);
                                    $stmt2->execute();
                                    $affected_rows = $stmt2->rowCount();
                                    if ($affected_rows  != 0) {
                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_updated"]) . "</div>";
                                        $cst->execute();
                                        $result = $cst->fetchObject();
                                        $readRights = json_decode($result->read_rights);
                                    } else {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_failed"]) . " </div>";
                                    }
                                }
                                ?>
                                <form action="" class="form-horizontal form-material" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo ($trans["gender"]) ?></label>
                                        <div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input" <?php if ($result->gender == 1) {echo "checked";} ?>>
                                                <label class="custom-control-label" for="customRadio1"><?php echo ($trans["male"]) ?></label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input" <?php if ($result->gender == 2) {echo "checked";} ?>>
                                                <label class="custom-control-label" for="customRadio2"><?php echo ($trans["female"]) ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="acctInput1"><?php echo ($trans["first_name"]) ?></label>
                                        <input type="text" name="first_name" class="form-control" id="acctInput1" value="<?php echo $result->firstname; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="acctInput2"><?php echo ($trans["last_name"]) ?></label>
                                        <input type="text" name="last_name" class="form-control" id="acctInput2" value="<?php echo $result->lastname; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="acctInput3"><?php echo ($trans["pseudo"]) ?></label>
                                        <input type="text" name="pseudo" class="form-control" id="acctInput3" value="<?php echo $result->pseudo; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="example-email"><?php echo ($trans["email"]) ?></label>
                                        <input type="email" name="emailc" value="<?php echo $result->emailc; ?>" class="form-control form-control-line" name="example-email" id="example-email">
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo ($trans["phone"]) ?></label>
                                        <input type="text" name="phone" value="<?php echo $result->phone; ?>" class="form-control form-control-line">
                                    </div>
                                    <div class="form-group">
                                        <label for="acctInput4"><?php echo ($trans["website_url"]) ?></label>
                                        <input type="url" name="website" class="form-control" id="acctInput4" value="<?php echo $result->profile_url; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="websites"><?php echo ($trans["websites"]) ?></label>
                                        <select id="websites" class="select2 m-b-10 select2-multiple" style="width: 100%" multiple="multiple" name="websites[]" data-placeholder="Choose websites">
                                            <option></option>
                                            <?php if ($websites_rows > 0) {
                                                $arr = explode(",", $result->websites);
                                                foreach ($websites as $web) {
                                                    if (in_array($web['id_website'], $arr)) {
                                                        echo "<option value='" . $web['id_website'] . "' selected>" . $web['name'] . "</option>";
                                                    } else {
                                                        echo "<option value='" . $web['id_website'] . "'>" . $web['name'] . "</option>";
                                                    }
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="" for="lang"><?php echo ($trans["language"]) ?></label>
                                        <select name="lang" id="lang" class="form-control select2">
                                            <option value="en" <?php if ($result->lang ==  "en") {echo "selected";} ?>><?php echo ($trans["english"]) ?></option>
                                            <option value="fr" <?php if ($result->lang ==  "fr") {echo "selected";} ?>><?php echo ($trans["french"]) ?></option>
                                        </select>
                                    </div>
                                    <br>
                                    <hr>
                                    <button type="submit" name="update1" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                                </form>
                            </div>
                        </div>
                        <?php if ($result->landing_page == 1) { ?>
                            <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'contact_informations') {echo "active";} ?>" id="contact_informations" role="tabpanel">
                                <div class="card-body">
                                    <?php
                                    $stmt3 = $conn->prepare("SELECT * FROM logs where `id_user` = :iu order by date desc");
                                    $stmt3->bindParam(':iu',  $result->id_user, PDO::PARAM_INT);
                                    $stmt3->execute();
                                    $logs = $stmt3->fetchAll();
                                    ?>
                                    <?php
                                    if (isset($_POST['update2'])) {
                                        $title = (isset($_POST['title']) && $_POST['title'] != '') ? htmlspecialchars($_POST['title']) : NULL;
                                        $description = (isset($_POST['description']) && $_POST['description'] != '') ? htmlspecialchars($_POST['description']) : NULL;
                                        $expertise = $_POST['expertise'];
                                        $rating = (isset($_POST['rating']) && $_POST['rating'] != '') ? floatval($_POST['rating']) : NULL;
                                        $real_rating = (isset($_POST['real_rating']) && $_POST['real_rating'] == 'on') ? 1 : 0;
                                        $facebook_url = (isset($_POST['facebook_url']) && $_POST['facebook_url'] != '') ? htmlspecialchars($_POST['facebook_url']) : NULL;
                                        $instagram_url = (isset($_POST['instagram_url']) && $_POST['instagram_url'] != '') ? htmlspecialchars($_POST['instagram_url']) : NULL;

                                        $stmt2 = $conn->prepare("UPDATE `consultants` SET `contact_title`=:tt,`contact_desc`=:ds,`contact_expertise`=:ex,`contact_rating`=:ra,`real_rating`=:rr,`facebook`=:fb,`instagram`=:ins WHERE `id_consultant`=:ID");
                                        $stmt2->bindParam(':tt', $title, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ex', $expertise, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ra', $rating, PDO::PARAM_STR);
                                        $stmt2->bindParam(':rr', $real_rating, PDO::PARAM_INT);
                                        $stmt2->bindParam(':fb', $facebook_url, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ins', $instagram_url, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ID', $id_consultant, PDO::PARAM_INT);
                                        $stmt2->execute();
                                        $affected_rows = $stmt2->rowCount();

                                        if ($affected_rows != 0) {
                                            echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_updated"]) . " </div>";
                                            $cst->execute();
                                            $result = $cst->fetchObject();
                                        } else {
                                            echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_failed"]) . "</div>";
                                        }
                                        unset($_POST);
                                    }
                                    ?>
                                    <form action="" class="form-horizontal form-material" method="POST">
                                        <div class="form-group">
                                            <label for="contInput1"><?php echo ($trans["title"]) ?></label>
                                            <input type="text" name="title" class="form-control" id="contInput1" value="<?php echo $result->contact_title; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="contInput2"><?php echo ($trans["description"]) ?></label>
                                            <textarea name="description" class="form-control" id="contInput2"><?php echo $result->contact_desc; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"><?php echo ($trans["expertise"]) ?></label>
                                            <div class="tags-default">
                                                <input id="tagsinput" type="text" name="expertise" data-role="tagsinput" placeholder="add tags" value="<?php echo $result->contact_expertise; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="contInput3"><?php echo ($trans["rating"]) ?></label>
                                            <input type="number" name="rating" id="contInput3" class="form-control" min="0" max="5" step="0.5" placeholder="from 0 to 5" value="<?php echo $result->contact_rating; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="custom-control custom-checkbox m-b-0">
                                                <input type="checkbox" class="custom-control-input" name="rating" <?php if ($result->real_rating == 1) {echo "checked";} ?>>
                                                <span class="custom-control-label"><?php echo ($trans["real_rating"]) ?> ?</span>
                                            </label>
                                        </div>
                                        <br>
                                        <h5 class=""><?php echo ($trans["social_profiles"]) ?></h5>
                                        <hr>
                                        <br>
                                        <div class="form-group">
                                            <label for="contInput4">Facebook </label>
                                            <input type="url" name="facebook_url" class="form-control" id="contInput4" value="<?php echo $result->facebook; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="contInput6">Instagram </label>
                                            <input type="url" name="instagram_url" class="form-control" id="contInput6" value="<?php echo $result->instagram; ?>">
                                        </div>
                                        <br>
                                        <hr>
                                        <button type="submit" name="update2" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                                    </form>

                                </div>
                            </div>
                        <?php } ?>
                        <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'credentials') {echo "active";} ?>" id="credentials" role="tabpanel">
                            <div class="card-body">
                                <?php
                                $stmt1 = $conn->prepare("SELECT * from users where id_profile=:ip and profile=3");
                                $stmt1->bindParam(':ip', $id_consultant, PDO::PARAM_INT);
                                $stmt1->execute();
                                $user = $stmt1->fetchObject();

                                if (isset($_POST['update-connection-credentials'])) {
                                    $newPassword = htmlentities($_POST['password']);
                                    $newHashedPassword = $newPassword != '' ? password_hash($newPassword, PASSWORD_BCRYPT) : $user->password;
                                    $stmt2 = $conn->prepare("UPDATE `users` SET `password`=:nps, `password_updated_at`=NOW(),`login`=:lg WHERE `id_user`=:ID");
                                    $stmt2->bindParam(':nps', $newHashedPassword, PDO::PARAM_STR);
                                    $stmt2->bindParam(':ID', intval($_POST['id']), PDO::PARAM_INT);
                                    $stmt2->bindParam(':lg', $_POST['login'], PDO::PARAM_STR);
                                    $stmt2->execute();
                                    $affected_rows = $stmt2->rowCount();
                                    if ($affected_rows != 0) {
                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["credentials_updated"]) . " </div>";
                                        $stmt1->execute();
                                        $user = $stmt1->fetchObject();
                                    } else {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["credentials_failed"]) . " </div>";
                                    }
                                    unset($_POST);
                                }
                                ?>
                                <form action="" class="form-horizontal form-material" method="POST">
                                    <div class="form-group">
                                        <label for="contInput1"><?php echo ($trans["consultant_id"]) ?></label>
                                        <input type="text" name="id" disabled class="form-control" id="id" value="<?php echo $user->id_user; ?>">
                                        <input type="text" name="id" hidden value="<?php echo $user->id_user; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="contInput1"><?php echo ($trans["login"]) ?></label>
                                        <input type="text" name="login" class="form-control " id="login" value="<?php echo $user->login; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="contInput1"><?php echo ($trans["new_password"]) ?></label>
                                        <?php if ($user->password_updated_at == null) { ?>
                                            <div class="input-group mb-3">
                                                <input type="password" name="password" class="form-control " id="password" value="<?php echo $result->pseudo . '-' . $id_consultant; ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password"><i class="mdi mdi-eye toggle-password-icon"></i></span>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <input type="password" name="password" class="form-control " id="password">
                                        <?php } ?>
                                    </div>
                                    <br>
                                    <hr>
                                    <div class="form-group">
                                        <button type="submit" name="update-connection-credentials" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'stats') {echo "active";} ?>" id="stats" role="tabpanel">
                            <div class="card-body">
                                <?php 
                                $st = $conn->prepare("SELECT 
                                (SELECT count(*) from messages m where m.sender=:IDU) as sent_messages,
                                 (SELECT count(*) from messages m where m.receiver=:IDU) as received_messages,
                                (SELECT count(*) from customers c where  c.id_account=:IDA) as customers_count,
                                (SELECT count(*) from transactionsc tc join customers c on tc.id_customer=c.id_customer where c.id_account=:IDA) as transactions,
                                (SELECT AVG(TIME_TO_SEC(TIMEDIFF(CASE when (SELECT ms.date_send from messages ms where ms.sender=:IDU and ms.receiver=m.sender and ms.date_send>m.date_send order by ms.id_message LIMIT 1) is not null then (SELECT ms.date_send from messages ms where ms.sender=:IDU and ms.receiver=m.sender and ms.date_send>m.date_send order by ms.id_message  LIMIT 1)else now() end,m.date_send )))  from messages m where m.receiver=:IDU order by m.id_message) as latency");
                                $st->bindparam(":IDU", $user->id_user);
                                $st->bindparam(":IDA", $_SESSION['id_account']);
                                $st->execute();
                                $stat = $st->fetchObject();
                                ?>
                                <div class="row cards">
                                    <div class="card col-md-12 col-lg-6 col-sm-12 col-xs-12">
                                        <div class="card-body stat-cards">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h3><i class="mdi mdi-send"></i></h3>
                                                            <p class="text-muted"><?php echo ($trans["consultant_account"]["dashboard"]["sent_messages"]) ?></p>
                                                        </div>
                                                        <div class="ml-auto">
                                                            <h2 class="counter text-primary"><?= $stat->sent_messages ?></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card col-md-12 col-lg-6 col-sm-12 col-xs-12">
                                        <div class="card-body stat-cards">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h3><i class="mdi mdi-call-received"></i></h3>
                                                            <p class="text-muted"><?php echo ($trans["consultant_account"]["dashboard"]["received_messages"]) ?></p>
                                                        </div>
                                                        <div class="ml-auto">
                                                            <h2 class="counter text-cyan"><?= $stat->received_messages ?></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card col-md-12 col-lg-6 col-sm-12 col-xs-12">
                                        <div class="card-body stat-cards">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h3><i class="mdi mdi-shopping"></i></h3>
                                                            <p class="text-muted"><?php echo ($trans["consultant_account"]["dashboard"]["transactions_by_client"]) ?></p>
                                                        </div>
                                                        <div class="ml-auto">
                                                            <h2 class="counter text-purple"><?php if ($stat->customers_count > 0) {echo number_format((float) $stat->transactions / $stat->customers_count, 2, '.', '');} else {echo 0;}  ?></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card col-md-12 col-lg-6 col-sm-12 col-xs-12">
                                        <div class="card-body stat-cards">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex no-block align-items-center">
                                                        <div>
                                                            <h3><i class="mdi mdi-timer"></i></h3>
                                                            <p class="text-muted"><?php echo ($trans["consultant_account"]["dashboard"]["average_response_time"]) ?></p>
                                                        </div>
                                                        <div class="ml-auto">
                                                            <h2 class="counter text-success"><?php echo gmdate("H:i:s", $stat->latency); ?></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'read') {echo "active";} ?>" id="read" role="tabpanel">
                            <div class="card-body">
                                <form action="" class="form-horizontal form-material" method="POST" enctype="multipart/form-data">
                                    <div class="table-responsive m-b-40 m-r-0">
                                        <table class="table display " id="packages-dtable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <td><?= $trans['field'] ?></td>
                                                    <td><?= $trans['admin']['account']['gender'] ?></td>
                                                    <td><?= $trans['admin']['account']['first_name'] ?></td>
                                                    <td><?= $trans['admin']['account']['last_name'] ?></td>
                                                    <td><?= $trans['admin']['account']['email'] ?></td>
                                                    <td><?= $trans['admin']['account']['phone'] ?></td>
                                                    <td><?= $trans['admin']['account']['address'] ?></td>
                                                    <td><?= $trans['language'] ?></td>
                                                    <td><?= $trans['admin']['account']['country'] ?></td>
                                                    <td><?= $trans['ip_address'] ?></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <?= $trans['visibility']?>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field1" name="0" class="custom-control-input" <?= $readRights[0] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field1"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field2" name="1" class="custom-control-input" <?= $readRights[1] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field2"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field3" name="2" class="custom-control-input" <?= $readRights[2] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field3"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field4" name="3" class="custom-control-input" <?= $readRights[3] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field4"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field5" name="4" class="custom-control-input" <?= $readRights[4] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field5"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field6" name="5" class="custom-control-input" <?= $readRights[5] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field6"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field7" name="6" class="custom-control-input" <?= $readRights[6] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field7"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field8" name="7" class="custom-control-input" <?= $readRights[7] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field8"></label></div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field10" name="9" class="custom-control-input" <?= $readRights[9] == 1 ? 'checked' : '' ?>><label class="custom-control-label" for="field9"></label></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <hr>
                                    <button type="submit" name="updateRights" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                                </form>

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
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/node_modules/dropify/dropify.min.js"></script>
<script src="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../../assets/js/dashboard1.js"></script>
<script src="../../assets/node_modules/toast-master/jquery.toast.js"></script>
<script type="text/javascript">
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
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        window.history.pushState("", "", "./consultant.php?id=<?= $id_consultant ?>&tab=" + e.target.hash.substr(1));
    })
    var errorMsg = $("#error-msg"),
        validMsg = $("#valid-msg");
    var errorMap = ["Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];
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
    $('.status').on('click',function(){
        $.ajax({
            url:'functions_ajax.php',
            type:"POST",
            data:{
                id:<?= $user->id_user?>,
                status:$(this).data('status'),
                type:"update_status"
            },
            success:function(data){
                if (data==1) {
                    $('.status').toggle();
                }
            }
        })
    });
    $("#phone").on('change', reset);
    $("#phone").on('keyup', reset);
    $('.dropify').dropify();
    $(".select2").select2();
    $(".toggle-password").click(function() {
        var inp = document.getElementById("password");
        if (inp.type == "text") {
            inp.type = "password";
            $(".toggle-password-icon").removeClass("mdi-eye-off");
            $(".toggle-password-icon").addClass("mdi-eye");
        } else {
            inp.type = "text";
            $(".toggle-password-icon").removeClass("mdi-eye");
            $(".toggle-password-icon").addClass("mdi-eye-off");
        }

    });
</script>
</body>
</html>