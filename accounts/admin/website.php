<?php
$page_folder = "websites";
$page_name = "website";
include('header.php');
?>
<div class="row">
    <?php
    $id_website = "";
    if (isset($_GET['id'])) {
        $id_website = intval($_GET['id']);
    }
    if ($id_website == 0) {
        echo '<section id="wrapper" class="col-md-12 error-page m-t-40">
                        <div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">Oops! Something went wrong !</h3><p class="text-muted m-t-30 m-b-30">We will work on the resolution right away.</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Return to the dashboard</a></div></div></section>';
    } else {
        $stmt = $conn->prepare("SELECT a.*, b.*, c.`business_name`, c.`emailc` FROM `websites` a LEFT JOIN `websites_landing` b ON a.`id_website` = b.`id_website` LEFT JOIN `accounts` c ON a.`id_account` = c.`id_account` WHERE a.`id_website` = :ID AND a.`id_account` = :IDA");
        $stmt->bindParam(':ID', $id_website, PDO::PARAM_INT);
        $stmt->bindParam(':IDA', $id_account, PDO::PARAM_INT);
        $stmt->execute();
        $total = $stmt->rowCount();
        $result = $stmt->fetchObject();
        if ($total == 0) {
            echo '<section id="wrapper" class="col-md-12 error-page m-t-40"><div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">Wesbite does not exist !</h3><p class="text-muted m-t-30 m-b-30">We could not find the page you are looking for.</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Return to the dashboard</a> </div></div></section>';
        } else {
            $s0 = $conn->prepare("SELECT * FROM `testimonials` WHERE `id_website` = :ID");
            $s0->bindParam(':ID', $id_website, PDO::PARAM_INT);
            $s0->execute();
            $testimonials_rows = $s0->rowCount();
            $testimonials = $s0->fetchAll();

            $s0 = $conn->prepare("SELECT * FROM `pricing` WHERE `id_website` = :ID");
            $s0->bindParam(':ID', $id_website, PDO::PARAM_INT);
            $s0->execute();
            $pricings_rows = $s0->rowCount();
            $pricings = $s0->fetchAll();
    ?>
            <div class="col-md-12 col-md-6">
                <div class="card">
                    <h3 style="position: absolute;line-height: 50px;left: 10px;margin-bottom: 0;padding-bottom: 0;">Wesbite #<?php echo $id_website; ?></h3>
                    <div class="d-flex flex-row align-self-end">
                        <?php
                        if ($result->status == 0) {
                            echo '<div class="align-self-center m-r-20"><h3 class="m-b-0 text-danger">' .  ($trans["declined"]) . '</h3></div><div class="bg-danger"><h3 class="text-white box m-b-0"><i class="mdi mdi-cancel"></i></h3></div>';
                        } elseif ($result->status == 1) {
                            echo '<div class="align-self-center m-r-20"><h3 class="m-b-0 text-info">' . ($trans["new"]) . '</h3></div><div class="bg-info"><h3 class="text-white box m-b-0"><i class="mdi mdi-bookmark-plus-outline"></i></h3></div>';
                        } elseif ($result->status == 2) {
                            echo '<div class="align-self-center m-r-20"><h3 class="m-b-0 text-success">' . ($trans["approved"]) . '</h3></div><div class="bg-success"><h3 class="text-white box m-b-0"><i class="mdi mdi-check"></i></h3></div>';
                        } elseif ($result->status == 3) {
                            echo '<div class="align-self-center m-r-20"><h3 class="m-b-0 text-warning">' . ($trans["checking"]) . '</h3></div><div class="bg-warning"><h3 class="text-white box m-b-0"><i class="mdi mdi-progress-wrench"></i></h3></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-body p-0">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs customtab" role="tablist">
                                <li class="nav-item"> <a class="nav-link <?php if (!isset($_POST['update2']) && !isset($_POST['update3']) && !isset($_POST['update4']) && !isset($_GET['tab'])) {echo "active";} ?>" data-toggle="tab" href="#general" role="tab"><span class="hidden-xs-down"><?php echo ($trans["admin"]["edit_website"]["general_informations"]) ?></span></a> </li>
                                <li class="nav-item"> <a class="nav-link <?php if (isset($_POST['update2'])) {echo "active";} ?>" data-toggle="tab" href="#gopaid" role="tab"><span class="hidden-xs-down"><?php echo ($trans["admin"]["edit_website"]["payment_gateway"]) ?> (GoPaid)</span></a> </li>
                                <li class="nav-item"> <a class="nav-link <?php if (isset($_POST['update3']) || isset($_POST['update4']) || (isset($_GET['tab']) && $_GET['tab'] == "landing")) {echo "active";} ?>" data-toggle="tab" href="#landing" role="tab"><span class="hidden-xs-down"><?php echo ($trans["admin"]["edit_website"]["landing_page"]) ?></span></a> </li>
                                <li class="nav-item"> <a class="nav-link <?php if (isset($_POST['add_pricing']) || (isset($_GET['tab']) && $_GET['tab'] == "pricing")) {echo "active";} ?>" data-toggle="tab" href="#pricing" role="tab"><span class="hidden-xs-down"><?php echo ($trans["admin"]["edit_website"]["pricing"]) ?></span></a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#options" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-equalizer"></i></span> <span class="hidden-xs-down">Options</span></a> </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane <?php if (!isset($_POST['update2']) && !isset($_POST['update3']) && !isset($_POST['update4']) && !isset($_GET['tab'])) {echo "active";} ?>" id="general" role="tabpanel">
                                    <div class="p-20">
                                        <?php
                                        if (isset($_POST['update1'])) {
                                            $title = (isset($_POST['title']) && $_POST['title'] != '') ? htmlspecialchars($_POST['title']) : NULL;
                                            $url_directory = (isset($_POST['url_directory']) && $_POST['url_directory'] != '') ? htmlspecialchars($_POST['url_directory']) : NULL;
                                            $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                                            $activity = (isset($_POST['activity']) && $_POST['activity'] != '') ? $_POST['activity'] : NULL;
                                            $return_url = (isset($_POST['return_url']) && $_POST['return_url'] != '') ? htmlspecialchars($_POST['return_url']) : NULL;

                                            $stmt2 = $conn->prepare("UPDATE `websites` SET `name`=:tt,`url_directory`=:urd,`url`=:ur,`activity`=:ac,`return_url`=:ret WHERE `id_website`=:ID");
                                            $stmt2->bindParam(':tt', $title, PDO::PARAM_STR);
                                            $stmt2->bindParam(':urd', $url_directory, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ur', $website, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ac', $activity, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ret', $return_url, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                            $stmt2->execute();
                                            $affected_rows = $stmt2->rowCount();

                                            if ($affected_rows != 0) {
                                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website informations has been updated successfully </div>";
                                                $stmt->execute();
                                                $result = $stmt->fetchObject();
                                            } else {
                                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website informations has not been updated successfully </div>";
                                            }
                                            unset($_POST);
                                        }
                                        ?>
                                        <form action="" method="POST">
                                            <div class="form-group">
                                                <label for="webInput1">Name</label>
                                                <input type="text" name="title" class="form-control" id="webInput1" value="<?php echo $result->name; ?>" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="webInput2">Wesbite</label>
                                                <input type="url" name="website" class="form-control" id="webInput2" value="<?php echo $result->url; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="activity">Activity</label>
                                                <select name="activity" id="activity" class="form-control select2 select-search" style="width: 100%;" disabled>
                                                    <option></option>
                                                    <option value="ecommerce" <?php if ($result->activity == 'ecommerce') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["ecommerce"]) ?></option>
                                                    <option value="studies_advice" <?php if ($result->activity == 'studies_advice') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["studies_advice"]) ?></option>
                                                    <option value="it_com" <?php if ($result->activity == 'it_com') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["it_com"]) ?></option>
                                                    <option value="business_services" <?php if ($result->activity == 'business_services') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["business_services"]) ?></option>
                                                    <option value="administration" <?php if ($result->activity == 'administration') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["administration"]) ?></option>
                                                    <option value="maintenance_spport" <?php if ($result->activity == 'maintenance_spport') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["maintenance_spport"]) ?></option>
                                                    <option value="legal" <?php if ($result->activity == 'legal') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["legal"]) ?></option>
                                                    <option value="medical_service" <?php if ($result->activity == 'medical_service') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["medical_service"]) ?></option>
                                                    <option value="other" <?php if ($result->activity == 'other') {echo "selected";} ?>><?php echo ($trans["admin"]["activities"]["other"]) ?></option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="webInput22"><?php echo ($trans["admin"]["edit_website"]["pc_folder_name"]) ?></label>
                                                <input type="text" name="url_directory" class="form-control" id="webInput22" value="<?php echo $result->url_directory; ?>">
                                                <small id="webInput22Help" class="form-text text-muted"><?php echo ($trans["admin"]["edit_website"]["pc_folder_name_note"]) ?> (<b>https://private-chat.pro/landing-page/<span><?php echo $result->url_directory; ?></span>/</b>)</small>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row text-center">
                                                        <label class="control-label col-md-12"><?php echo ($trans["admin"]["edit_website"]["login_page"]) ?></label>
                                                        <p class="form-control-static col-md-12"><?php if ($result->url_directory != NULL) {echo "<a href='https://private-chat.pro/accounts/login.php'>https://private-chat.pro/accounts/login.php</a>";} ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row text-center">
                                                        <label class="control-label col-md-12"><?php echo ($trans["admin"]["edit_website"]["landing_page"]) ?></label>
                                                        <p class="form-control-static col-md-12"><?php if ($result->url_directory != NULL) {echo "<a href='https://private-chat.pro/landing-page/" . $result->url_directory . "/'>https://private-chat.pro/landing-page/" . $result->url_directory . "/</a>";} ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="webInput3"><?php echo ($trans["admin"]["edit_website"]["return_url"]) ?></label>
                                                <input type="url" name="return_url" class="form-control" id="webInput3" value="<?php echo $result->return_url; ?>">
                                                <small id="webInput3Help" class="form-text text-muted"><?php echo ($trans["admin"]["edit_website"]["return_url_note"]) ?></small>
                                            </div>
                                            <br>
                                            <hr>
                                            <button type="submit" name="update1" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                            <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane <?php if (isset($_POST['update2'])) {echo "active";} ?>" id="gopaid" role="tabpanel">
                                    <div class="p-20">
                                        <?php
                                        if (isset($_POST['update2'])) {
                                            $payment = (isset($_POST['payment']) && $_POST['payment'] != NULL) ? intval($_POST['payment']) : 0;
                                            $payment_url = (isset($_POST['payment_url']) && $_POST['payment_url'] != '') ? htmlspecialchars($_POST['payment_url']) : NULL;
                                            $payment_receipt = (isset($_POST['payment_receipt']) && $_POST['payment_receipt'] == 'on') ? 1 : 0;
                                            $payment_notification = (isset($_POST['payment_notification']) && $_POST['payment_notification'] == 'on') ? 1 : 0;
                                            $languages = (isset($_POST['languages'])) ? implode(",", $_POST['languages']) : NULL;
                                            $default_language = (isset($_POST['default_language'])) ? $_POST['default_language'] : NULL;

                                            $stmt2 = $conn->prepare("UPDATE `websites` SET `payment`=:pt,`payment_url`=:pru,`payment_receipt`=:pr,`payment_notification`=:pn,`languages`=:ln,`default_language`=:ld WHERE `id_website`=:ID");
                                            $stmt2->bindParam(':pt', $payment, PDO::PARAM_INT);
                                            $stmt2->bindParam(':pru', $payment_url, PDO::PARAM_STR);
                                            $stmt2->bindParam(':pr', $payment_receipt, PDO::PARAM_INT);
                                            $stmt2->bindParam(':pn', $payment_notification, PDO::PARAM_INT);
                                            $stmt2->bindParam(':ln', $languages, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ld', $default_language, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                            $stmt2->execute();
                                            $affected_rows = $stmt2->rowCount();

                                            if ($affected_rows != 0) {
                                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The payment informations has been updated successfully </div>";
                                                $stmt->execute();
                                                $result = $stmt->fetchObject();
                                            } else {
                                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The payment informations has not been updated successfully </div>";
                                            }
                                            unset($_POST);
                                        }
                                        ?>
                                        <form action="" method="POST">
                                            <div class="form-group bt-switch">
                                                <label for="acctInput1"><?php echo ($trans["admin"]["edit_website"]["get_payment_service"]) ?></label>
                                                <input type="checkbox" name="payment" value="0" data-size="small" data-on-text="<?php echo ($trans["yes"]) ?>" data-off-text="<?php echo ($trans["no"]) ?>" data-on-color="success" <?php if ($result->payment == 1) {echo "checked";} ?> />
                                            </div>
                                            <br>
                                            <div id="payment_bloc0" class="<?php if (intval($result->payment) == 1) {echo "hide";} ?>">
                                                <div class="form-group">
                                                    <label for="webInput3">Own Payment gateway page</label>
                                                    <input type="url" name="payment_url" class="form-control" id="webInput3" value="<?php echo $result->payment_url; ?>">
                                                </div>
                                            </div>
                                            <div id="payment_bloc" class="<?php if (intval($result->payment) != 1) {echo "hide";} ?>">
                                                <div class="col-md-12 row">
                                                    <div class="col-md-6">
                                                        <div class="form-group bt-switch">
                                                            <label for="webInput4"><?php echo ($trans["admin"]["edit_website"]["send_receipt"]) ?> : </label>
                                                            <input type="checkbox" name="payment_receipt" id="webInput4" data-on-text="<?php echo ($trans["on"]) ?>" data-off-text="<?php echo ($trans["off"]) ?>" data-size="mini" <?php if ($result->payment_receipt == 1) {echo "checked";} ?> />
                                                        </div>
                                                        <div class="form-group bt-switch">
                                                            <label for="webInput5"><?php echo ($trans["admin"]["edit_website"]["send_payment_notification"]) ?> : </label>
                                                            <input type="checkbox" name="payment_notification" id="webInput5" data-size="mini" <?php if ($result->payment_notification == 1) {echo "checked";} ?> />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for=""><?php echo ($trans["admin"]["edit_website"]["languages_payment"]) ?> :</label>
                                                            <select id="languages" class="select2 m-b-10 select2-multiple" style="width: 100%" multiple="multiple" name="languages[]" data-placeholder="<?php echo ($trans["admin"]["edit_website"]["languages_payment_placeholder"]) ?>">
                                                                <option value=""></option>
                                                                <option value="fr" <?php if (in_array('fr', explode(",", $result->languages))) {echo 'selected="selected"';} ?>>French</option>
                                                                <option value="en" <?php if (in_array('en', explode(",", $result->languages))) {echo 'selected="selected"';} ?>>English</option>
                                                                <option value="it" <?php if (in_array('it', explode(",", $result->languages))) {echo 'selected="selected"';} ?>>Italian</option>
                                                                <option value="es" <?php if (in_array('es', explode(",", $result->languages))) {echo 'selected="selected"';} ?>>Spanish</option>
                                                                <option value="de" <?php if (in_array('de', explode(",", $result->languages))) {echo 'selected="selected"';} ?>>German</option>
                                                                <option value="pt" <?php if (in_array('pt', explode(",", $result->languages))) {echo 'selected="selected"';} ?>>Portuguese</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for=""><?php echo ($trans["admin"]["edit_website"]["default_language_payment"]) ?> :</label>
                                                            <select id="default_language" class="form-control select2" name="default_language" style="width: 100%" data-placeholder="<?php echo ($trans["admin"]["edit_website"]["default_language_payment_placeholder"]) ?>">
                                                                <option value=""></option>
                                                                <option value="fr" <?php if ($result->default_language == 'fr') {echo 'selected="selected"';} ?>>French</option>
                                                                <option value="en" <?php if ($result->default_language == 'en') {echo 'selected="selected"';} ?>>English</option>
                                                                <option value="it" <?php if ($result->default_language == 'it') {echo 'selected="selected"';} ?>>Italian</option>
                                                                <option value="es" <?php if ($result->default_language == 'es') {echo 'selected="selected"';} ?>>Spanish</option>
                                                                <option value="de" <?php if ($result->default_language == 'de') {echo 'selected="selected"';} ?>>German</option>
                                                                <option value="pt" <?php if ($result->default_language == 'pt') {echo 'selected="selected"';} ?>>Portuguese</option>
                                                            </select>
                                                        </div>
                                                        <br>
                                                        <hr>
                                                        <button type="submit" name="update2" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                                        <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                                                    </div>
                                                    <div class="col-md-6 pr-0 pl-0">
                                                        <div class="ribbon-wrapper bg-light col-md-10 float-right">
                                                            <div class="ribbon ribbon-default"><?php echo ($trans["admin"]["edit_website"]["gopaid_account"]) ?></div>
                                                            <p class="ribbon-content">
                                                                <?php if ($result->id_shop == NULL) { ?>
                                                                    <div id="add_account_gopaid">
                                                                        <p><?php echo ($trans["admin"]["edit_website"]["gopaid_note"]) ?></p>
                                                                        <table class="table">
                                                                            <tr>
                                                                                <th><?php echo ($trans["admin"]["edit_website"]["account_name"]) ?></th>
                                                                                <td class="account_name"><?php echo $result->business_name; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th><?php echo ($trans["admin"]["edit_website"]["shop_name"]) ?></th>
                                                                                <td class="shop_name"><?php echo $result->name; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th><?php echo ($trans["admin"]["edit_website"]["email_address"]) ?></th>
                                                                                <td class="email_addr"><?php echo $result->emailc; ?></td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <div id="account_gopaid">
                                                                        <p><?php echo ($trans["admin"]["edit_website"]["account_informations"]) ?></p>
                                                                        <table class="table">
                                                                            <tr>
                                                                                <th><?php echo ($trans["admin"]["edit_website"]["account_name"]) ?></th>
                                                                                <td><?php echo $result->business_name; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th><?php echo ($trans["admin"]["edit_website"]["shop_name"]) ?></th>
                                                                                <td><?php echo $result->name; ?> [ID: <?php echo $id_shop; ?>]</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                <?php } ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane <?php if (isset($_POST['update3']) || isset($_POST['update4']) || ((isset($_GET['tab']) && $_GET['tab'] == "landing"))) {echo "active";} ?>" id="landing" role="tabpanel">
                                    <div class="p-20">
                                        <?php
                                        if (isset($_POST['update3'])) {
                                            $lp_name = (isset($_POST['lp_name']) && $_POST['lp_name'] != '') ? htmlspecialchars($_POST['lp_name']) : NULL;
                                            $lp_description = (isset($_POST['lp_description']) && $_POST['lp_description'] != '') ? htmlspecialchars($_POST['lp_description']) : NULL;
                                            $backg = (isset($_POST['backg']) && $_POST['backg'] != '') ? $_POST['backg'] : NULL;
                                            $background_img = NULL;
                                            $logo = NULL;
                                            if (isset($_FILES["lp_background"]["name"]) && $_FILES["lp_background"]["name"] != "") {
                                                $dirLogo = '../uploads/';
                                                $uploadImg = removeAccents($_FILES["lp_background"]["name"]);
                                                $uploadImgTmp = removeAccents($_FILES["lp_background"]["tmp_name"]);
                                                $fileData1 = pathinfo(basename($uploadImg));
                                                $Imgnom = basename($uploadImg, "." . $fileData1['extension']);
                                                $background_img = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                $target_path1 = ($dirLogo . $background_img);
                                                while (file_exists($target_path1)) {
                                                    $background_img = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                    $target_path1 = ($dirLogo . $background_img);
                                                }
                                                move_uploaded_file($uploadImgTmp, $target_path1);
                                            } else {
                                                $background_img = $backg;
                                            }
                                            if (isset($_FILES["lp_logo"]["name"]) && $_FILES["lp_logo"]["name"] != "") {
                                                $dirLogo = '../uploads/';
                                                $uploadImg = removeAccents($_FILES["lp_logo"]["name"]);
                                                $uploadImgTmp = removeAccents($_FILES["lp_logo"]["tmp_name"]);
                                                $fileData1 = pathinfo(basename($uploadImg));
                                                $Imgnom = basename($uploadImg, "." . $fileData1['extension']);
                                                $logo = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                $target_path1 = ($dirLogo . $logo);
                                                while (file_exists($target_path1)) {
                                                    $logo = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                    $target_path1 = ($dirLogo . $logo);
                                                }
                                                move_uploaded_file($uploadImgTmp, $target_path1);
                                            }

                                            $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `title`=:tt, `description`=:ds, `background`=:bc, `logo`=:lo WHERE `id_website`=:ID");
                                            $stmt2->bindParam(':tt', $lp_name, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ds', $lp_description, PDO::PARAM_STR);
                                            $stmt2->bindParam(':bc', $background_img, PDO::PARAM_STR);
                                            $stmt2->bindParam(':lo', $logo, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                            $stmt2->execute();
                                            $affected_rows = $stmt2->rowCount();

                                            if ($affected_rows != 0) {
                                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website informations has been updated successfully </div>";
                                                $stmt->execute();
                                                $result = $stmt->fetchObject();
                                            } else {
                                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website informations has not been updated </div>";
                                            }
                                            unset($_POST);
                                        } elseif (isset($_POST['update4'])) {
                                            $lp_name = (isset($_POST['lp_name']) && $_POST['lp_name'] != '') ? htmlspecialchars($_POST['lp_name']) : NULL;
                                            $lp_description = (isset($_POST['lp_description']) && $_POST['lp_description'] != '') ? htmlspecialchars($_POST['lp_description']) : NULL;
                                            $backg = (isset($_POST['backg']) && $_POST['backg'] != '') ? $_POST['backg'] : NULL;
                                            $background_img = NULL;
                                            $logo = NULL;
                                            if (isset($_FILES["lp_background"]["name"]) && $_FILES["lp_background"]["name"] != "") {
                                                $dirLogo = '../uploads/';
                                                $uploadImg = removeAccents($_FILES["lp_background"]["name"]);
                                                $uploadImgTmp = removeAccents($_FILES["lp_background"]["tmp_name"]);
                                                $fileData1 = pathinfo(basename($uploadImg));
                                                $Imgnom = basename($uploadImg, "." . $fileData1['extension']);
                                                $background_img = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                $target_path1 = ($dirLogo . $background_img);
                                                while (file_exists($target_path1)) {
                                                    $background_img = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                    $target_path1 = ($dirLogo . $background_img);
                                                }
                                                move_uploaded_file($uploadImgTmp, $target_path1);
                                            } else {
                                                $background_img = $backg;
                                            }
                                            if (isset($_FILES["lp_logo"]["name"]) && $_FILES["lp_logo"]["name"] != "") {
                                                $dirLogo = '../uploads/';
                                                $uploadImg = removeAccents($_FILES["lp_logo"]["name"]);
                                                $uploadImgTmp = removeAccents($_FILES["lp_logo"]["tmp_name"]);
                                                $fileData1 = pathinfo(basename($uploadImg));
                                                $Imgnom = basename($uploadImg, "." . $fileData1['extension']);
                                                $logo = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                $target_path1 = ($dirLogo . $logo);
                                                while (file_exists($target_path1)) {
                                                    $logo = substr($Imgnom, 0, 25) . "-" . $id_website . '.' . $fileData1['extension'];
                                                    $target_path1 = ($dirLogo . $logo);
                                                }
                                                move_uploaded_file($uploadImgTmp, $target_path1);
                                            }

                                            $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `title`=:tt, `description`=:ds, `background`=:bc, `logo`=:lo WHERE `id_website`=:ID");
                                            $stmt2->bindParam(':tt', $lp_name, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ds', $lp_description, PDO::PARAM_STR);
                                            $stmt2->bindParam(':bc', $background_img, PDO::PARAM_STR);
                                            $stmt2->bindParam(':lo', $logo, PDO::PARAM_STR);
                                            $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                            $stmt2->execute();
                                            $affected_rows = $stmt2->rowCount();

                                            if ($affected_rows != 0) {
                                                $stmt->execute();
                                                $result = $stmt->fetchObject();
                                                echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website informations has been updated successfully </div>";

                                                include('regenerate_landing.php');
                                                $response = regenerate_landing($id_website);
                                                if ($response == true) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                }
                                            } else {
                                                echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website informations has not been updated </div>";
                                            }
                                            unset($_POST);
                                        }
                                        ?>
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="webInput7">Title</label>
                                                <input type="text" name="lp_name" class="form-control" id="webInput7" value="<?php echo $result->title; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="sub">Subtitle</label>
                                                <textarea name="subtitle" class="form-control" id="sub" rows="3"><?php echo $result->subtitle; ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="desc">Description</label>
                                                <textarea name="lp_description" class="form-control" id="desc" rows="5"><?php echo $result->description; ?></textarea>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-10">
                                                    <div class="col-md-8 pl-0 pr-0 float-left">
                                                        <div class="form-group">
                                                            <label>Background image</label>
                                                            <div class="input-group">
                                                                <input type="hidden" name="backg" value="">
                                                                <ul class="icolors">
                                                                    <?php
                                                                    $all_files = glob("../landing-page/assets/images/backgrounds/*.*");
                                                                    for ($i = 0; $i < count($all_files); $i++) {
                                                                        $image_name = $all_files[$i];
                                                                        $supported_format = array('gif', 'jpg', 'jpeg', 'png');
                                                                        $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                                                                        if (in_array($ext, $supported_format)) {
                                                                            $active = ($result->background == pathinfo($image_name, PATHINFO_FILENAME)) ? "active" : "";
                                                                            echo '<li class="' . $active . '" data-name="' . pathinfo($image_name, PATHINFO_FILENAME) . '"><img src="' . $image_name . '" alt="' . pathinfo($image_name, PATHINFO_FILENAME) . '" /></li>';
                                                                        } else {
                                                                            continue;
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 pl-0 pr-0 float-left">
                                                        <div class="form-group">
                                                            <label for="webInput8">Background image (Custom)</label>
                                                            <input type="file" name="lp_background" id="input-file-1" class="dropify" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="webInput9">Logo</label>
                                                    <input type="file" name="lp_logo" id="input-file-2" class="dropify" />
                                                </div>
                                            </div>
                                            <h4 class="box-title m-t-40">Social media</h4>
                                            <hr class="m-t-0 m-b-40">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="webInput12">Facebook page</label>
                                                        <input type="text" name="facebook" class="form-control" id="webInput12" value="<?php echo $result->facebook; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="webInput13">Twitter page</label>
                                                        <input type="text" name="twitter" class="form-control" id="webInput13" value="<?php echo $result->twitter; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="webInput14">Instagram page</label>
                                                        <input type="text" name="instagram" class="form-control" id="webInput14" value="<?php echo $result->instagram; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="webInput15">Pinterest page</label>
                                                        <input type="text" name="pinterest" class="form-control" id="webInput15" value="<?php echo $result->pinterest; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="webInput16">App Store <i class="mdi mdi-apple text-info"></i></label>
                                                        <input type="text" name="appstore" class="form-control" id="webInput16" value="<?php echo $result->appstore; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="webInput17">Google Play <i class="mdi mdi-google-play text-info"></i></label>
                                                        <input type="text" name="googleplay" class="form-control" id="webInput17" value="<?php echo $result->googleplay; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <hr>
                                            <div class="text-right">
                                                <button type="submit" name="update4" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                <button type="submit" name="update3" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                <button type="reset" class="btn btn-secondary waves-effect waves-light">Cancel</button>
                                            </div>
                                        </form>
                                        <div>
                                            <h3 class="box-title m-t-40">Displayed Sections</h3>
                                            <hr class="m-t-0 m-b-40">
                                            <?php
                                            if (isset($_POST['update1.1'])) {
                                                $section_features = (isset($_POST['section_features']) && $_POST['section_features'] != NULL) ? intval($_POST['section_features']) : 0;
                                                $section_features_title = (isset($_POST['section_features_title']) && $_POST['section_features_title'] != '') ? htmlspecialchars($_POST['section_features_title']) : NULL;
                                                $section_features_desc = (isset($_POST['section_features_desc']) && $_POST['section_features_desc'] != '') ? htmlspecialchars($_POST['section_features_desc']) : NULL;
                                                $section_features_block1_name = (isset($_POST['section_features_block1_name']) && $_POST['section_features_block1_name'] != '') ? htmlspecialchars($_POST['section_features_block1_name']) : NULL;
                                                $section_features_block1_detail = (isset($_POST['section_features_block1_detail']) && $_POST['section_features_block1_detail'] != '') ? htmlspecialchars($_POST['section_features_block1_detail']) : NULL;
                                                $section_features_block2_name = (isset($_POST['section_features_block2_name']) && $_POST['section_features_block2_name'] != '') ? htmlspecialchars($_POST['section_features_block2_name']) : NULL;
                                                $section_features_block2_detail = (isset($_POST['section_features_block2_detail']) && $_POST['section_features_block2_detail'] != '') ? htmlspecialchars($_POST['section_features_block2_detail']) : NULL;
                                                $section_features_block3_name = (isset($_POST['section_features_block3_name']) && $_POST['section_features_block3_name'] != '') ? htmlspecialchars($_POST['section_features_block3_name']) : NULL;
                                                $section_features_block3_detail = (isset($_POST['section_features_block3_detail']) && $_POST['section_features_block3_detail'] != '') ? htmlspecialchars($_POST['section_features_block3_detail']) : NULL;
                                                $section_features_block4_name = (isset($_POST['section_features_block4_name']) && $_POST['section_features_block4_name'] != '') ? htmlspecialchars($_POST['section_features_block4_name']) : NULL;
                                                $section_features_block4_detail = (isset($_POST['section_features_block4_detail']) && $_POST['section_features_block4_detail'] != '') ? htmlspecialchars($_POST['section_features_block4_detail']) : NULL;
                                                $section_features_block5_name = (isset($_POST['section_features_block5_name']) && $_POST['section_features_block5_name'] != '') ? htmlspecialchars($_POST['section_features_block5_name']) : NULL;
                                                $section_features_block5_detail = (isset($_POST['section_features_block5_detail']) && $_POST['section_features_block5_detail'] != '') ? htmlspecialchars($_POST['section_features_block5_detail']) : NULL;
                                                $section_features_block6_name = (isset($_POST['section_features_block6_name']) && $_POST['section_features_block6_name'] != '') ? htmlspecialchars($_POST['section_features_block6_name']) : NULL;
                                                $section_features_block6_detail = (isset($_POST['section_features_block6_detail']) && $_POST['section_features_block6_detail'] != '') ? htmlspecialchars($_POST['section_features_block6_detail']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_features`=:sec, `section_features_title`=:tt,`section_features_desc`=:ds,`section_features_block1_name`=:bn1,`section_features_block1_detail`=:bd1,`section_features_block2_name`=:bn2,`section_features_block2_detail`=:bd2,`section_features_block3_name`=:bn3,`section_features_block3_detail`=:bd3,`section_features_block4_name`=:bn4,`section_features_block4_detail`=:bd4,`section_features_block5_name`=:bn5,`section_features_block5_detail`=:bd5,`section_features_block6_name`=:bn6,`section_features_block6_detail`=:bd6 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_features, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_features_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ds', $section_features_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_features_block1_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd1', $section_features_block1_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_features_block2_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd2', $section_features_block2_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn3', $section_features_block3_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd3', $section_features_block3_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn4', $section_features_block4_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd4', $section_features_block4_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn5', $section_features_block5_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd5', $section_features_block5_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn6', $section_features_block6_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd6', $section_features_block6_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section features has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section features has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update1.2'])) {
                                                $section_features = (isset($_POST['section_features']) && $_POST['section_features'] != NULL) ? intval($_POST['section_features']) : 0;
                                                $section_features_title = (isset($_POST['section_features_title']) && $_POST['section_features_title'] != '') ? htmlspecialchars($_POST['section_features_title']) : NULL;
                                                $section_features_desc = (isset($_POST['section_features_desc']) && $_POST['section_features_desc'] != '') ? htmlspecialchars($_POST['section_features_desc']) : NULL;
                                                $section_features_block1_name = (isset($_POST['section_features_block1_name']) && $_POST['section_features_block1_name'] != '') ? htmlspecialchars($_POST['section_features_block1_name']) : NULL;
                                                $section_features_block1_detail = (isset($_POST['section_features_block1_detail']) && $_POST['section_features_block1_detail'] != '') ? htmlspecialchars($_POST['section_features_block1_detail']) : NULL;
                                                $section_features_block2_name = (isset($_POST['section_features_block2_name']) && $_POST['section_features_block2_name'] != '') ? htmlspecialchars($_POST['section_features_block2_name']) : NULL;
                                                $section_features_block2_detail = (isset($_POST['section_features_block2_detail']) && $_POST['section_features_block2_detail'] != '') ? htmlspecialchars($_POST['section_features_block2_detail']) : NULL;
                                                $section_features_block3_name = (isset($_POST['section_features_block3_name']) && $_POST['section_features_block3_name'] != '') ? htmlspecialchars($_POST['section_features_block3_name']) : NULL;
                                                $section_features_block3_detail = (isset($_POST['section_features_block3_detail']) && $_POST['section_features_block3_detail'] != '') ? htmlspecialchars($_POST['section_features_block3_detail']) : NULL;
                                                $section_features_block4_name = (isset($_POST['section_features_block4_name']) && $_POST['section_features_block4_name'] != '') ? htmlspecialchars($_POST['section_features_block4_name']) : NULL;
                                                $section_features_block4_detail = (isset($_POST['section_features_block4_detail']) && $_POST['section_features_block4_detail'] != '') ? htmlspecialchars($_POST['section_features_block4_detail']) : NULL;
                                                $section_features_block5_name = (isset($_POST['section_features_block5_name']) && $_POST['section_features_block5_name'] != '') ? htmlspecialchars($_POST['section_features_block5_name']) : NULL;
                                                $section_features_block5_detail = (isset($_POST['section_features_block5_detail']) && $_POST['section_features_block5_detail'] != '') ? htmlspecialchars($_POST['section_features_block5_detail']) : NULL;
                                                $section_features_block6_name = (isset($_POST['section_features_block6_name']) && $_POST['section_features_block6_name'] != '') ? htmlspecialchars($_POST['section_features_block6_name']) : NULL;
                                                $section_features_block6_detail = (isset($_POST['section_features_block6_detail']) && $_POST['section_features_block6_detail'] != '') ? htmlspecialchars($_POST['section_features_block6_detail']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_features`=:sec, `section_features_title`=:tt,`section_features_desc`=:ds,`section_features_block1_name`=:bn1,`section_features_block1_detail`=:bd1,`section_features_block2_name`=:bn2,`section_features_block2_detail`=:bd2,`section_features_block3_name`=:bn3,`section_features_block3_detail`=:bd3,`section_features_block4_name`=:bn4,`section_features_block4_detail`=:bd4,`section_features_block5_name`=:bn5,`section_features_block5_detail`=:bd5,`section_features_block6_name`=:bn6,`section_features_block6_detail`=:bd6 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_features, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_features_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ds', $section_features_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_features_block1_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd1', $section_features_block1_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_features_block2_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd2', $section_features_block2_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn3', $section_features_block3_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd3', $section_features_block3_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn4', $section_features_block4_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd4', $section_features_block4_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn5', $section_features_block5_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd5', $section_features_block5_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn6', $section_features_block6_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd6', $section_features_block6_detail, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section features has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section features has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update2.1'])) {
                                                $section_qualities = (isset($_POST['section_qualities']) && $_POST['section_qualities'] != NULL) ? intval($_POST['section_qualities']) : 0;
                                                $section_qualities_title = (isset($_POST['section_qualities_title']) && $_POST['section_qualities_title'] != '') ? htmlspecialchars($_POST['section_qualities_title']) : NULL;
                                                $section_qualities_line1 = (isset($_POST['section_qualities_line1']) && $_POST['section_qualities_line1'] != '') ? htmlspecialchars($_POST['section_qualities_line1']) : NULL;
                                                $section_qualities_line2 = (isset($_POST['section_qualities_line2']) && $_POST['section_qualities_line2'] != '') ? htmlspecialchars($_POST['section_qualities_line2']) : NULL;
                                                $section_qualities_line3 = (isset($_POST['section_qualities_line3']) && $_POST['section_qualities_line3'] != '') ? htmlspecialchars($_POST['section_qualities_line3']) : NULL;
                                                $section_qualities_line4 = (isset($_POST['section_qualities_line4']) && $_POST['section_qualities_line4'] != '') ? htmlspecialchars($_POST['section_qualities_line4']) : NULL;
                                                $section_qualities_url = (isset($_POST['section_qualities_url']) && $_POST['section_qualities_url'] != '') ? htmlspecialchars($_POST['section_qualities_url']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_qualities`=:sec, `section_qualities_title`=:tt,`section_qualities_line1`=:bn1,`section_qualities_line2`=:bn2,`section_qualities_line3`=:bn3,`section_qualities_line4`=:bn4,`section_qualities_url`=:bd4 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_qualities, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_qualities_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_qualities_line1, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_qualities_line2, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn3', $section_qualities_line3, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn4', $section_qualities_line4, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd4', $section_qualities_url, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section qualities has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section qualities has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update2.2'])) {
                                                $section_qualities = (isset($_POST['section_qualities']) && $_POST['section_qualities'] != NULL) ? intval($_POST['section_qualities']) : 0;
                                                $section_qualities_title = (isset($_POST['section_qualities_title']) && $_POST['section_qualities_title'] != '') ? htmlspecialchars($_POST['section_qualities_title']) : NULL;
                                                $section_qualities_line1 = (isset($_POST['section_qualities_line1']) && $_POST['section_qualities_line1'] != '') ? htmlspecialchars($_POST['section_qualities_line1']) : NULL;
                                                $section_qualities_line2 = (isset($_POST['section_qualities_line2']) && $_POST['section_qualities_line2'] != '') ? htmlspecialchars($_POST['section_qualities_line2']) : NULL;
                                                $section_qualities_line3 = (isset($_POST['section_qualities_line3']) && $_POST['section_qualities_line3'] != '') ? htmlspecialchars($_POST['section_qualities_line3']) : NULL;
                                                $section_qualities_line4 = (isset($_POST['section_qualities_line4']) && $_POST['section_qualities_line4'] != '') ? htmlspecialchars($_POST['section_qualities_line4']) : NULL;
                                                $section_qualities_url = (isset($_POST['section_qualities_url']) && $_POST['section_qualities_url'] != '') ? htmlspecialchars($_POST['section_qualities_url']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_qualities`=:sec, `section_qualities_title`=:tt,`section_qualities_line1`=:bn1,`section_qualities_line2`=:bn2,`section_qualities_line3`=:bn3,`section_qualities_line4`=:bn4,`section_qualities_url`=:bd4 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_qualities, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_qualities_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_qualities_line1, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_qualities_line2, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn3', $section_qualities_line3, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn4', $section_qualities_line4, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd4', $section_qualities_url, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section qualities has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section qualities has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update3.1'])) {
                                                $section_video = (isset($_POST['section_video']) && $_POST['section_video'] != NULL) ? intval($_POST['section_video']) : 0;
                                                $section_video_title = (isset($_POST['section_video_title']) && $_POST['section_video_title'] != '') ? htmlspecialchars($_POST['section_video_title']) : NULL;
                                                $section_video_desc = (isset($_POST['section_video_desc']) && $_POST['section_video_desc'] != '') ? htmlspecialchars($_POST['section_video_desc']) : NULL;
                                                $section_video_url = (isset($_POST['section_video_url']) && $_POST['section_video_url'] != '') ? htmlspecialchars($_POST['section_video_url']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_video`=:sec, `section_video_title`=:tt,`section_video_desc`=:bn1,`section_video_url`=:bn2 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_video, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_video_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_video_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_video_url, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section video has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section video has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update3.2'])) {
                                                $section_video = (isset($_POST['section_video']) && $_POST['section_video'] != NULL) ? intval($_POST['section_video']) : 0;
                                                $section_video_title = (isset($_POST['section_video_title']) && $_POST['section_video_title'] != '') ? htmlspecialchars($_POST['section_video_title']) : NULL;
                                                $section_video_desc = (isset($_POST['section_video_desc']) && $_POST['section_video_desc'] != '') ? htmlspecialchars($_POST['section_video_desc']) : NULL;
                                                $section_video_url = (isset($_POST['section_video_url']) && $_POST['section_video_url'] != '') ? htmlspecialchars($_POST['section_video_url']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_video`=:sec, `section_video_title`=:tt,`section_video_desc`=:bn1,`section_video_url`=:bn2 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_video, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_video_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_video_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_video_url, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section video has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section video has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update4.1'])) {
                                                $section_statistics = (isset($_POST['section_statistics']) && $_POST['section_statistics'] != NULL) ? intval($_POST['section_statistics']) : 0;
                                                $section_statistics_title = (isset($_POST['section_statistics_title']) && $_POST['section_statistics_title'] != '') ? htmlspecialchars($_POST['section_statistics_title']) : NULL;
                                                $section_statistics_desc = (isset($_POST['section_statistics_desc']) && $_POST['section_statistics_desc'] != '') ? htmlspecialchars($_POST['section_statistics_desc']) : NULL;
                                                $section_statistics_data1_name = (isset($_POST['section_statistics_data1_name']) && $_POST['section_statistics_data1_name'] != '') ? htmlspecialchars($_POST['section_statistics_data1_name']) : NULL;
                                                $section_statistics_data1_number = (isset($_POST['section_statistics_data1_number']) && $_POST['section_statistics_data1_number'] != '') ? htmlspecialchars($_POST['section_statistics_data1_number']) : NULL;
                                                $section_statistics_data2_name = (isset($_POST['section_statistics_data2_name']) && $_POST['section_statistics_data2_name'] != '') ? htmlspecialchars($_POST['section_statistics_data2_name']) : NULL;
                                                $section_statistics_data2_number = (isset($_POST['section_statistics_data2_number']) && $_POST['section_statistics_data2_number'] != '') ? htmlspecialchars($_POST['section_statistics_data2_number']) : NULL;
                                                $section_statistics_data3_name = (isset($_POST['section_statistics_data3_name']) && $_POST['section_statistics_data3_name'] != '') ? htmlspecialchars($_POST['section_statistics_data3_name']) : NULL;
                                                $section_statistics_data3_number = (isset($_POST['section_statistics_data3_number']) && $_POST['section_statistics_data3_number'] != '') ? htmlspecialchars($_POST['section_statistics_data3_number']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_statistics`=:sec, `section_statistics_title`=:tt,`section_statistics_desc`=:ds,`section_statistics_data1_name`=:bn1,`section_statistics_data1_number`=:bd1,`section_statistics_data2_name`=:bn2,`section_statistics_data2_number`=:bd2,`section_statistics_data3_name`=:bn3,`section_statistics_data3_number`=:bd3 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_statistics, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_statistics_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ds', $section_statistics_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_statistics_data1_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd1', $section_statistics_data1_number, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_statistics_data2_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd2', $section_statistics_data2_number, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn3', $section_statistics_data3_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd3', $section_statistics_data3_number, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section features has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section features has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update4.2'])) {
                                                $section_statistics = (isset($_POST['section_statistics']) && $_POST['section_statistics'] != NULL) ? intval($_POST['section_statistics']) : 0;
                                                $section_statistics_title = (isset($_POST['section_statistics_title']) && $_POST['section_statistics_title'] != '') ? htmlspecialchars($_POST['section_statistics_title']) : NULL;
                                                $section_statistics_desc = (isset($_POST['section_statistics_desc']) && $_POST['section_statistics_desc'] != '') ? htmlspecialchars($_POST['section_statistics_desc']) : NULL;
                                                $section_statistics_data1_name = (isset($_POST['section_statistics_data1_name']) && $_POST['section_statistics_data1_name'] != '') ? htmlspecialchars($_POST['section_statistics_data1_name']) : NULL;
                                                $section_statistics_data1_number = (isset($_POST['section_statistics_data1_number']) && $_POST['section_statistics_data1_number'] != '') ? htmlspecialchars($_POST['section_statistics_data1_number']) : NULL;
                                                $section_statistics_data2_name = (isset($_POST['section_statistics_data2_name']) && $_POST['section_statistics_data2_name'] != '') ? htmlspecialchars($_POST['section_statistics_data2_name']) : NULL;
                                                $section_statistics_data2_number = (isset($_POST['section_statistics_data2_number']) && $_POST['section_statistics_data2_number'] != '') ? htmlspecialchars($_POST['section_statistics_data2_number']) : NULL;
                                                $section_statistics_data3_name = (isset($_POST['section_statistics_data3_name']) && $_POST['section_statistics_data3_name'] != '') ? htmlspecialchars($_POST['section_statistics_data3_name']) : NULL;
                                                $section_statistics_data3_number = (isset($_POST['section_statistics_data3_number']) && $_POST['section_statistics_data3_number'] != '') ? htmlspecialchars($_POST['section_statistics_data3_number']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_statistics`=:sec, `section_statistics_title`=:tt,`section_statistics_desc`=:ds,`section_statistics_data1_name`=:bn1,`section_statistics_data1_number`=:bd1,`section_statistics_data2_name`=:bn2,`section_statistics_data2_number`=:bd2,`section_statistics_data3_name`=:bn3,`section_statistics_data3_number`=:bd3 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_statistics, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_statistics_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ds', $section_statistics_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_statistics_data1_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd1', $section_statistics_data1_number, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_statistics_data2_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd2', $section_statistics_data2_number, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn3', $section_statistics_data3_name, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bd3', $section_statistics_data3_number, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section statistics has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section statistics has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update5.1'])) {
                                                $section_pricing = (isset($_POST['section_pricing']) && $_POST['section_pricing'] != NULL) ? intval($_POST['section_pricing']) : 0;
                                                $section_pricing_title = (isset($_POST['section_pricing_title']) && $_POST['section_pricing_title'] != '') ? htmlspecialchars($_POST['section_pricing_title']) : NULL;
                                                $section_pricing_desc = (isset($_POST['section_pricing_desc']) && $_POST['section_pricing_desc'] != '') ? htmlspecialchars($_POST['section_pricing_desc']) : NULL;
                                                $section_pricing_url = (isset($_POST['section_pricing_url']) && $_POST['section_pricing_url'] != '') ? htmlspecialchars($_POST['section_pricing_url']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_pricing`=:sec, `section_pricing_title`=:tt,`section_pricing_desc`=:bn1,`section_pricing_url`=:bn2 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_pricing, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_pricing_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_pricing_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_pricing_url, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section pricing has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section pricing has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update5.2'])) {
                                                $section_pricing = (isset($_POST['section_pricing']) && $_POST['section_pricing'] != NULL) ? intval($_POST['section_pricing']) : 0;
                                                $section_pricing_title = (isset($_POST['section_pricing_title']) && $_POST['section_pricing_title'] != '') ? htmlspecialchars($_POST['section_pricing_title']) : NULL;
                                                $section_pricing_desc = (isset($_POST['section_pricing_desc']) && $_POST['section_pricing_desc'] != '') ? htmlspecialchars($_POST['section_pricing_desc']) : NULL;
                                                $section_pricing_url = (isset($_POST['section_pricing_url']) && $_POST['section_pricing_url'] != '') ? htmlspecialchars($_POST['section_pricing_url']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_pricing`=:sec, `section_pricing_title`=:tt,`section_pricing_desc`=:bn1,`section_pricing_url`=:bn2 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_pricing, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_pricing_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_pricing_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn2', $section_pricing_url, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section pricing has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section pricing has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update6.1'])) {
                                                $section_mobileapp = (isset($_POST['section_mobileapp']) && $_POST['section_mobileapp'] != NULL) ? intval($_POST['section_mobileapp']) : 0;
                                                $section_mobileapp_title = (isset($_POST['section_mobileapp_title']) && $_POST['section_mobileapp_title'] != '') ? htmlspecialchars($_POST['section_mobileapp_title']) : NULL;
                                                $section_mobileapp_desc = (isset($_POST['section_mobileapp_desc']) && $_POST['section_mobileapp_desc'] != '') ? htmlspecialchars($_POST['section_mobileapp_desc']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_mobileapp`=:sec, `section_mobileapp_title`=:tt,`section_mobileapp_desc`=:bn1 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_mobileapp, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_mobileapp_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_mobileapp_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section mobile/App has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section mobile/App has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update6.2'])) {
                                                $section_mobileapp = (isset($_POST['section_mobileapp']) && $_POST['section_mobileapp'] != NULL) ? intval($_POST['section_mobileapp']) : 0;
                                                $section_mobileapp_title = (isset($_POST['section_mobileapp_title']) && $_POST['section_mobileapp_title'] != '') ? htmlspecialchars($_POST['section_mobileapp_title']) : NULL;
                                                $section_mobileapp_desc = (isset($_POST['section_mobileapp_desc']) && $_POST['section_mobileapp_desc'] != '') ? htmlspecialchars($_POST['section_mobileapp_desc']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_mobileapp`=:sec, `section_mobileapp_title`=:tt,`section_mobileapp_desc`=:bn1 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_mobileapp, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_mobileapp_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_mobileapp_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section mobile/App has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section mobile/App has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update7.1'])) {
                                                $section_testimonials = (isset($_POST['section_testimonials']) && $_POST['section_testimonials'] != NULL) ? intval($_POST['section_testimonials']) : 0;
                                                $section_testimonials_title = (isset($_POST['section_testimonials_title']) && $_POST['section_testimonials_title'] != '') ? htmlspecialchars($_POST['section_testimonials_title']) : NULL;
                                                $section_testimonials_desc = (isset($_POST['section_testimonials_desc']) && $_POST['section_testimonials_desc'] != '') ? htmlspecialchars($_POST['section_testimonials_desc']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_testimonials`=:sec, `section_testimonials_title`=:tt,`section_testimonials_desc`=:bn1 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_testimonials, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_testimonials_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_testimonials_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section testimonials has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section testimonials has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update7.2'])) {
                                                $section_testimonials = (isset($_POST['section_testimonials']) && $_POST['section_testimonials'] != NULL) ? intval($_POST['section_testimonials']) : 0;
                                                $section_testimonials_title = (isset($_POST['section_testimonials_title']) && $_POST['section_testimonials_title'] != '') ? htmlspecialchars($_POST['section_testimonials_title']) : NULL;
                                                $section_testimonials_desc = (isset($_POST['section_testimonials_desc']) && $_POST['section_testimonials_desc'] != '') ? htmlspecialchars($_POST['section_testimonials_desc']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_testimonials`=:sec, `section_testimonials_title`=:tt,`section_testimonials_desc`=:bn1 WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_testimonials, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_testimonials_title, PDO::PARAM_STR);
                                                $stmt2->bindParam(':bn1', $section_testimonials_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section testimonials has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section testimonials has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update8.1'])) {
                                                $section_form = (isset($_POST['section_form']) && $_POST['section_form'] != NULL) ? intval($_POST['section_form']) : 0;
                                                $section_form_desc = (isset($_POST['section_form_desc']) && $_POST['section_form_desc'] != '') ? htmlspecialchars($_POST['section_form_desc']) : NULL;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_form`=:tt, `section_form_desc`=:ds WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_form, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ds', $section_form_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section form has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section form has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update8.2'])) {
                                                $section_form = (isset($_POST['section_form']) && $_POST['section_form'] != NULL) ? intval($_POST['section_form']) : 0;
                                                $section_form_desc = (isset($_POST['section_form_desc']) && $_POST['section_form_desc'] != '') ? htmlspecialchars($_POST['section_form_desc']) : NULL;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_form`=:tt, `section_form_desc`=:ds WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_form, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ds', $section_form_desc, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section form has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section form has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update9.1'])) {
                                                $section_payments = (isset($_POST['section_payments']) && $_POST['section_payments'] != NULL) ? intval($_POST['section_payments']) : 0;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_payments`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_payments, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section payments has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section payments has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update9.2'])) {
                                                $section_payments = (isset($_POST['section_payments']) && $_POST['section_payments'] != NULL) ? intval($_POST['section_payments']) : 0;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_payments`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_payments, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section payments has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section payments has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update10.1'])) {
                                                $section_process = (isset($_POST['section_process']) && $_POST['section_process'] != NULL) ? intval($_POST['section_process']) : 0;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_process`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_process, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section process has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section process has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update10.2'])) {
                                                $section_process = (isset($_POST['section_process']) && $_POST['section_process'] != NULL) ? intval($_POST['section_process']) : 0;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_process`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_process, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section process has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section process has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update11.1'])) {
                                                $section_faqs = (isset($_POST['section_faqs']) && $_POST['section_faqs'] != NULL) ? intval($_POST['section_faqs']) : 0;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_faqs`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_faqs, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section FAQs has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section FAQs has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update11.2'])) {
                                                $section_faqs = (isset($_POST['section_faqs']) && $_POST['section_faqs'] != NULL) ? intval($_POST['section_faqs']) : 0;
                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_faqs`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':tt', $section_faqs, PDO::PARAM_INT);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section FAQs has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section FAQs has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update12.1'])) {
                                                $section_support = (isset($_POST['section_support']) && $_POST['section_support'] != NULL) ? intval($_POST['section_support']) : 0;
                                                $section_support_email = (isset($_POST['section_support_email']) && $_POST['section_support_email'] != '') ? htmlspecialchars($_POST['section_support_email']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_support`=:sec, `section_support_email`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_support, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_support_email, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section support has been updated successfully </div>";
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section support has not been updated </div>";
                                                }
                                                unset($_POST);
                                            } elseif (isset($_POST['update12.2'])) {
                                                $section_support = (isset($_POST['section_support']) && $_POST['section_support'] != NULL) ? intval($_POST['section_support']) : 0;
                                                $section_support_email = (isset($_POST['section_support_email']) && $_POST['section_support_email'] != '') ? htmlspecialchars($_POST['section_support_email']) : NULL;

                                                $stmt2 = $conn->prepare("UPDATE `websites_landing` SET `section_support`=:sec, `section_support_email`=:tt WHERE `id_website`=:ID");
                                                $stmt2->bindParam(':sec', $section_support, PDO::PARAM_INT);
                                                $stmt2->bindParam(':tt', $section_support_email, PDO::PARAM_STR);
                                                $stmt2->bindParam(':ID', $id_website, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                $affected_rows = $stmt2->rowCount();

                                                if ($affected_rows != 0) {
                                                    $stmt->execute();
                                                    $result = $stmt->fetchObject();
                                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section support has been updated successfully </div>";

                                                    include('regenerate_landing.php');
                                                    $response = regenerate_landing($id_website);
                                                    if ($response == true) {
                                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The landing page has been regenerated successfully </div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The section support has not been updated </div>";
                                                }
                                                unset($_POST);
                                            }
                                            ?>
                                            <div id="accordian-3">
                                                <div class="card">
                                                    <a class="card-header" id="heading8">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                                            <h5 class="mb-0">Sign up form</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse8" class="collapse" aria-labelledby="heading8" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput21">Display : </label>
                                                                    <input type="checkbox" name="section_form" id="webInput21" data-size="mini" <?php if ($result->section_form == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update8.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update8.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading1">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                                            <h5 class="mb-0">Section Features</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput21">Display : </label>
                                                                    <input type="checkbox" name="section_features" id="webInput21" data-size="mini" <?php if ($result->section_features == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Title</label>
                                                                    <input type="text" name="section_features_title" class="form-control" id="webInput18" value="<?php echo $result->section_features_title; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Description</label>
                                                                    <textarea name="section_features_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_features_desc; ?></textarea>
                                                                </div>
                                                                <div class="form-row m-b-30">
                                                                    <label for="webInput18" class="col-md-12 mb-0">block 1</label>
                                                                    <div class="col-md-4">
                                                                        <label class="col-md-12"><small>Name</small></label>
                                                                        <input type="text" name="section_features_block1_name" class="form-control" id="webInput18" value="<?php echo $result->section_features_block1_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label class="col-md-12"><small>Detail</small></label>
                                                                        <input type="text" name="section_features_block1_detail" class="form-control" id="webInput18" value="<?php echo $result->section_features_block1_detail; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row m-b-30">
                                                                    <label for="webInput18" class="col-md-12 mb-0">block 2</label>
                                                                    <div class="col-md-4">
                                                                        <label><small>Name</small></label>
                                                                        <input type="text" name="section_features_block2_name" class="form-control" id="webInput18" value="<?php echo $result->section_features_block2_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label><small>Detail</small></label>
                                                                        <input type="text" name="section_features_block2_detail" class="form-control" id="webInput18" value="<?php echo $result->section_features_block2_detail; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row m-b-30">
                                                                    <label for="webInput18" class="col-md-12 mb-0">block 3</label>
                                                                    <div class="col-md-4">
                                                                        <label><small>Name</small></label>
                                                                        <input type="text" name="section_features_block3_name" class="form-control" id="webInput18" value="<?php echo $result->section_features_block3_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label><small>Detail</small></label>
                                                                        <input type="text" name="section_features_block3_detail" class="form-control" id="webInput18" value="<?php echo $result->section_features_block3_detail; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row m-b-30">
                                                                    <label for="webInput18" class="col-md-12 mb-0">block 4</label>
                                                                    <div class="col-md-4">
                                                                        <label><small>Name</small></label>
                                                                        <input type="text" name="section_features_block4_name" class="form-control" id="webInput18" value="<?php echo $result->section_features_block4_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label><small>Detail</small></label>
                                                                        <input type="text" name="section_features_block4_detail" class="form-control" id="webInput18" value="<?php echo $result->section_features_block4_detail; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row m-b-30">
                                                                    <label for="webInput18" class="col-md-12  mb-0">block 5</label>
                                                                    <div class="col-md-4">
                                                                        <label><small>Name</small></label>
                                                                        <input type="text" name="section_features_block5_name" class="form-control" id="webInput18" value="<?php echo $result->section_features_block5_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label><small>Detail</small></label>
                                                                        <input type="text" name="section_features_block5_detail" class="form-control" id="webInput18" value="<?php echo $result->section_features_block5_detail; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row m-b-30">
                                                                    <label for="webInput18" class="col-md-12 mb-0">block 6</label>
                                                                    <div class="col-md-4">
                                                                        <label><small>Name</small></label>
                                                                        <input type="text" name="section_features_block6_name" class="form-control" id="webInput18" value="<?php echo $result->section_features_block6_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label><small>Detail</small></label>
                                                                        <input type="text" name="section_features_block6_detail" class="form-control" id="webInput18" value="<?php echo $result->section_features_block6_detail; ?>">
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update1.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update1.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading9">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
                                                            <h5 class="mb-0">Payments</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse9" class="collapse" aria-labelledby="heading9" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput21">Display : </label>
                                                                    <input type="checkbox" name="section_payments" id="webInput21" data-size="mini" <?php if ($result->section_payments == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update9.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update9.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading2">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                                            <h5 class="mb-0">Section Qualities</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput23">Display : </label>
                                                                    <input type="checkbox" name="section_qualities" id="webInput23" data-size="mini" <?php if ($result->section_qualities == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Title</label>
                                                                    <input type="text" name="section_qualities_title" class="form-control" id="webInput18" value="<?php echo $result->section_qualities_title; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">line 1</label>
                                                                    <input type="text" name="section_qualities_line1" class="form-control" id="webInput18" value="<?php echo $result->section_qualities_line1; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">line 2</label>
                                                                    <input type="text" name="section_qualities_line2" class="form-control" id="webInput18" value="<?php echo $result->section_qualities_line2; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">line 3</label>
                                                                    <input type="text" name="section_qualities_line3" class="form-control" id="webInput18" value="<?php echo $result->section_qualities_line3; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">line 4</label>
                                                                    <input type="text" name="section_qualities_line4" class="form-control" id="webInput18" value="<?php echo $result->section_qualities_line4; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Bouton Url</label>
                                                                    <input type="text" name="section_qualities_url" class="form-control" id="webInput18" value="<?php echo $result->section_qualities_url; ?>">
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update2.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update2.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading3">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                                            <h5 class="mb-0">Section Video</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput26">Display : </label>
                                                                    <input type="checkbox" name="section_video" id="webInput26" data-size="mini" <?php if ($result->section_video == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Title</label>
                                                                    <input type="text" name="section_video_title" class="form-control" id="webInput18" value="<?php echo $result->section_video_title; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Description</label>
                                                                    <textarea name="section_video_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_video_desc; ?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Video url</label>
                                                                    <input type="text" name="section_video_url" class="form-control" id="webInput18" value="<?php echo $result->section_video_url; ?>">
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update3.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update3.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading10">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse10" aria-expanded="false" aria-controls="collapse10">
                                                            <h5 class="mb-0">Process</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse10" class="collapse" aria-labelledby="heading10" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput21">Display : </label>
                                                                    <input type="checkbox" name="section_process" id="webInput21" data-size="mini" <?php if ($result->section_process == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update10.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update10.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading4">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                                            <h5 class="mb-0">Section Statistics</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput244">Statistics</label>
                                                                    <input type="checkbox" name="section_statistics" id="webInput244" data-size="mini" <?php if ($result->section_statistics == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Title</label>
                                                                    <input type="text" name="section_statistics_title" class="form-control" id="webInput18" value="<?php echo $result->section_statistics_title; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Description</label>
                                                                    <textarea name="section_statistics_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_statistics_desc; ?></textarea>
                                                                </div>

                                                                <div class="form-row m-b-20">
                                                                    <div class="col-md-2" style="line-height: 67px;">
                                                                        <label for="webInput18">Data 1</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="">Name</label>
                                                                        <input type="text" name="section_statistics_data1_name" class="form-control" id="webInput18" value="<?php echo $result->section_statistics_data1_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label>Number</label>
                                                                        <input type="text" name="section_statistics_data1_number" class="form-control" id="webInput18" value="<?php echo $result->section_statistics_data1_number; ?>">
                                                                        <div style="position: absolute;right: -10px;top: 38px;">%</div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-row m-b-20">
                                                                    <div class="col-md-2" style="line-height: 67px;">
                                                                        <label for="webInput18">Data 2</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="">Name</label>
                                                                        <input type="text" name="section_statistics_data2_name" class="form-control" id="webInput18" value="<?php echo $result->section_statistics_data2_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label>Number</label>
                                                                        <input type="text" name="section_statistics_data2_number" class="form-control" id="webInput18" value="<?php echo $result->section_statistics_data2_number; ?>">
                                                                        <div style="position: absolute;right: -10px;top: 38px;">%</div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-row m-b-20">
                                                                    <div class="col-md-2" style="line-height: 67px;">
                                                                        <label for="webInput18">Data 3</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="">Name</label>
                                                                        <input type="text" name="section_statistics_data3_name" class="form-control" id="webInput18" value="<?php echo $result->section_statistics_data3_name; ?>">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label for="">Number</label>
                                                                        <input type="text" name="section_statistics_data3_number" class="form-control" id="webInput18" value="<?php echo $result->section_statistics_data3_number; ?>">
                                                                        <div style="position: absolute;right: -10px;top: 38px;">%</div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update4.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update4.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading5">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                                            <h5 class="mb-0">Section Pricing</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse5" class="collapse" aria-labelledby="heading5" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput27">Display : </label>
                                                                    <input type="checkbox" name="section_pricing" id="webInput27" data-size="mini" <?php if ($result->section_pricing == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Title</label>
                                                                    <input type="text" name="section_pricing_title" class="form-control" id="webInput18" value="<?php echo $result->section_pricing_title; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Description</label>
                                                                    <textarea name="section_pricing_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_pricing_desc; ?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Bouton Url</label>
                                                                    <input type="text" name="section_pricing_url" class="form-control" id="webInput18" value="<?php echo $result->section_pricing_url; ?>">
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update5.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update5.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading6">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                                            <h5 class="mb-0">Section Mobile</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse6" class="collapse" aria-labelledby="heading6" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput29">Display : </label>
                                                                    <input type="checkbox" name="section_mobileapp" id="webInput29" data-size="mini" <?php if ($result->section_mobileapp == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Title</label>
                                                                    <input type="text" name="section_mobileapp_title" class="form-control" id="webInput18" value="<?php echo $result->section_mobileapp_title; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Description</label>
                                                                    <textarea name="section_mobileapp_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_mobileapp_desc; ?></textarea>
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update6.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update6.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading7">
                                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                                            <h5 class="mb-0">Section Testimonials</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse7" class="collapse" aria-labelledby="heading7" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput25">Testimonials</label>
                                                                    <input type="checkbox" name="section_testimonials" id="webInput25" data-size="mini" <?php if ($result->section_testimonials == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Title</label>
                                                                    <input type="text" name="section_testimonials_title" class="form-control" id="webInput18" value="<?php echo $result->section_testimonials_title; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Description</label>
                                                                    <textarea name="section_testimonials_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_testimonials_desc; ?></textarea>
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update7.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update7.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading11">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse11" aria-expanded="false" aria-controls="collapse11">
                                                            <h5 class="mb-0">FAQs</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse11" class="collapse" aria-labelledby="heading11" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput21">Display : </label>
                                                                    <input type="checkbox" name="section_faqs" id="webInput21" data-size="mini" <?php if ($result->section_faqs == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update11.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update11.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <a class="card-header" id="heading12">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse12" aria-expanded="false" aria-controls="collapse12">
                                                            <h5 class="mb-0">Support</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse12" class="collapse" aria-labelledby="heading12" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <div class="form-group bt-switch">
                                                                    <label for="webInput21">Display : </label>
                                                                    <input type="checkbox" name="section_support" id="webInput21" data-size="mini" <?php if ($result->section_support == 1) {echo "checked";} ?> />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="webInput18">Email for messages</label>
                                                                    <input type="text" name="section_support_email" class="form-control" id="webInput18" value="<?php echo $result->section_support_email; ?>">
                                                                </div>
                                                                <br>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" name="update12.1" class="btn btn-info waves-effect waves-light m-r-10">Update & Regenerate landing page</button>
                                                                    <button type="submit" name="update12.2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                            <h3 class="box-title m-t-40">Testimonials</h3>
                                            <hr class="m-t-0 m-b-40">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-secondary m-t-10 m-b-10 float-right" data-toggle="modal" data-target="#add-testimonial">
                                                    <span class="btn-label"><i class="mdi mdi-plus"></i></span> Add New
                                                </button>
                                                <!-- Add Contact Popup Model -->
                                                <div id="add-testimonial" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title text-primary" id="myModalLabel">Add New testimonial</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <from class="form-horizontal form-material">
                                                                    <div class="form-group">
                                                                        <div class="col-md-12 m-b-20">
                                                                            <input type="text" name="username" class="form-control" placeholder="Username">
                                                                        </div>
                                                                        <div class="col-md-12 m-b-20">
                                                                            <input type="number" name="rating" class="form-control" placeholder="Rating" min="0" max="5" step="1">
                                                                        </div>
                                                                        <div class="col-md-12 m-b-20">
                                                                            <input type="text" name="title0" class="form-control" placeholder="Title">
                                                                        </div>
                                                                        <div class="col-md-12 m-b-20">
                                                                            <textarea name="content" class="form-control" placeholder="Text" rows="3"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </from>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" name="add_test" id="add_test" class="btn btn-primary waves-effect" data-dismiss="modal">Save</button>
                                                                <button type="reset" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="testimonial-detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myLargeModalLabel">Edit testimonial #<span></span></h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" name="update_test" id="btn-update" class="btn btn-primary waves-effect" data-dismiss="modal">Update</button>
                                                                <button type="reset" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive nopadding">
                                                    <table id="myTable" class="table table-bordered table-striped" style="width: 100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Username</th>
                                                                <th>Title</th>
                                                                <th>Rating</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            if ($testimonials_rows > 0) {
                                                                foreach ($testimonials as $tes) {
                                                                    echo "<tr>
                                                                    <td>" . $tes['id_testimonial'] . "</td>
                                                                    <td>" . $tes['username'] . "</td>
                                                                    <td>" . $tes['title'] . "</td>
                                                                    <td>" . $tes['rating'] . "</td>
                                                                    <td class='text-center' data-id='" . $tes['id_testimonial'] . "'>
                                                                    <button class='jsgrid-button jsgrid-edit-button' type='button' title='Edit'></button>
                                                                    <button class='jsgrid-button jsgrid-delete-button' type='button' title='Delete'></button>
                                                                    </td>
                                                                    </tr>";
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane <?php if (isset($_POST['add_pricing']) || (isset($_GET['tab']) && $_GET['tab'] == "pricing")) {echo "active";} ?>" id="pricing" role="tabpanel">
                                    <div class="p-20">
                                        <div class="col-md-12 text-right">
                                            <div id="add-new" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog text-left">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel">Add New pricing</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <from class="form-horizontal form-material">
                                                                <div class="form-group">
                                                                    <div class="col-md-12 m-b-20">
                                                                        <input type="text" name="title" class="form-control" placeholder="Title">
                                                                    </div>
                                                                    <div class="col-md-12 m-b-20">
                                                                        <input type="text" name="price" class="form-control" placeholder="Price">
                                                                    </div>
                                                                    <div class="col-md-12 m-b-20">
                                                                        <select class="form-control" name="currency">
                                                                            <option>Currency</option>
                                                                            <option value="EUR">EUR</option>
                                                                            <option value="CHF">CHF</option>
                                                                            <option value="GBP">GBP</option>
                                                                            <option value="SEK">SEK</option>
                                                                            <option value="DKK">DKK</option>
                                                                            <option value="CAD">CAD</option>
                                                                            <option value="USD">USD</option>
                                                                            <option value="AUD">AUD</option>
                                                                            <option value="NZD">NZD</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-12 m-b-20">
                                                                        <input type="number" name="msg_nb" class="form-control" placeholder="Messages number" min="0" max="100" step="1">
                                                                    </div>
                                                                    <div class="col-md-12 m-b-20">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox" name="status" value="1" class="custom-control-input" id="customCheck1">
                                                                            <label class="custom-control-label" for="customCheck1">Status POPULAR </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </from>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" id="add_pricing" class="btn btn-info waves-effect" data-dismiss="modal">Save</button>
                                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#add-new">
                                                <span class="btn-label"><i class="mdi mdi-plus"></i></span><?php echo ($trans["admin"]["edit_website"]["add_new"]) ?>
                                            </button>
                                        </div>
                                        <div class="row m-t-20">
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="row pricing-plan">
                                                    <?php
                                                    if ($pricings_rows > 0) {
                                                        foreach ($pricings as $pri) {
                                                            if ($pri['status'] == 1) {
                                                                echo '<div class="col-md-3 col-xs-12 col-sm-6 no-padding"><div class="pricing-box featured-plan"><div class="pricing-body b-l"><div class="pricing-header"><h4 class="price-lable text-white bg-warning"> Popular</h4><h4 class="text-center">' . $pri["title"] . '</h4><h2 class="text-center"><span class="price-sign">' . Currency($pri["currency"]) . '</span>' . floatval($pri["price"]) . '</h2><p class="uppercase">per messages</p></div><div class="price-table-content"><div class="price-row"><i class="mdi mdi-comment-text-multiple"></i> ' . $pri["messages"] . ' messages</div><div class="price-row"><div id="edit-' . $pri["id_pricing"] . '" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog text-left"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">Edit pricing #' . $pri["id_pricing"] . '</h4><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div><div class="modal-body"><from class="form-horizontal form-material"><div class="form-group"><div class="col-md-12 m-b-20"><input type="text" name="title" class="form-control" value="' . $pri["title"] . '"></div><div class="col-md-12 m-b-20"><input type="text" name="price" class="form-control" value="' . $pri["price"] . '"></div><div class="col-md-12 m-b-20"><select name="currency" class="form-control" placeholder="Currency"><option value="">Currency</option><option value="EUR"';
                                                                if ($pri["currency"] == "EUR") {echo "selected";}
                                                                echo '>EUR</option><option value="CHF" ';
                                                                if ($pri["currency"] == "CHF") {echo "selected";}
                                                                echo '>CHF</option><option value="GBP" ';
                                                                if ($pri["currency"] == "GBP") {echo "selected";}
                                                                echo '>GBP</option><option value="SEK" ';
                                                                if ($pri["currency"] == "SEK") {echo "selected";}
                                                                echo '>SEK</option><option value="DKK" ';
                                                                if ($pri["currency"] == "DKK") {echo "selected";}
                                                                echo '>DKK</option><option value="CAD" ';
                                                                if ($pri["currency"] == "CAD") {echo "selected";}
                                                                echo '>CAD</option><option value="USD" ';
                                                                if ($pri["currency"] == "USD") {echo "selected";}
                                                                echo '>USD</option><option value="AUD" ';
                                                                if ($pri["currency"] == "AUD") {echo "selected";}
                                                                echo '>AUD</option><option value="NZD" ';
                                                                if ($pri["currency"] == "NZD") {echo "selected";}
                                                                echo '>NZD</option></select></div><div class="col-md-12 m-b-20"><input type="number" name="msg_nb" class="form-control" value="' . $pri["messages"] . '" min="0" max="100" step="1"></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="status" value="1" class="custom-control-input" id="customCheck1" ';
                                                                if ($pri["status"] == 1) {echo "checked";}
                                                                echo '><label class="custom-control-label" for="customCheck1">Status POPULAR </label></div></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="active" value="1" class="custom-control-input" id="customCheck2" ';
                                                                if ($pri["active"] == 1) {echo "checked";}
                                                                echo '><label class="custom-control-label" for="customCheck2">Active </label></div></div></div></from></div><div class="modal-footer"><button type="button" class="btn btn-info waves-effect save" data-dismiss="modal" data-id="' . $pri["id_pricing"] . '">Save</button><button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button></div></div></div></div><button class="btn btn-success waves-effect waves-light"  data-toggle="modal" data-target="#edit-' . $pri["id_pricing"] . '">Edit</button></div></div></div></div></div>';
                                                            } else {
                                                                echo '<div class="col-md-3 col-xs-12 col-sm-6 no-padding"><div class="pricing-box"><div class="pricing-body b-l"><div class="pricing-header"><h4 class="text-center">' . $pri["title"] . '</h4><h2 class="text-center"><span class="price-sign">' . Currency($pri["currency"]) . '</span>' . floatval($pri["price"]) . '</h2><p class="uppercase">per messages</p></div><div class="price-table-content"><div class="price-row"><i class="mdi mdi-comment-text-multiple"></i> ' . $pri["messages"] . ' messages</div><div class="price-row"><div id="edit-' . $pri["id_pricing"] . '" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog text-left"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">Edit pricing #' . $pri["id_pricing"] . '</h4><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div><div class="modal-body"><from class="form-horizontal form-material"><div class="form-group"><div class="col-md-12 m-b-20"><input type="text" name="title" class="form-control" value="' . $pri["title"] . '"></div><div class="col-md-12 m-b-20"><input type="text" name="price" class="form-control" value="' . $pri["price"] . '"></div><div class="col-md-12 m-b-20"><select name="currency" class="form-control" placeholder="' . ($trans["admin"]["edit_website"]["currency"]) . '"><option value="">' . ($trans["admin"]["edit_website"]["currency"]) . '</option><option value="EUR"';
                                                                if ($pri["currency"] == "EUR") {echo "selected";}
                                                                echo '>EUR</option><option value="CHF" ';
                                                                if ($pri["currency"] == "CHF") {echo "selected";}
                                                                echo '>CHF</option><option value="GBP" ';
                                                                if ($pri["currency"] == "GBP") {echo "selected";}
                                                                echo '>GBP</option><option value="SEK" ';
                                                                if ($pri["currency"] == "SEK") {echo "selected";}
                                                                echo '>SEK</option><option value="DKK" ';
                                                                if ($pri["currency"] == "DKK") {echo "selected";}
                                                                echo '>DKK</option><option value="CAD" ';
                                                                if ($pri["currency"] == "CAD") {echo "selected";}
                                                                echo '>CAD</option><option value="USD" ';
                                                                if ($pri["currency"] == "USD") {echo "selected";}
                                                                echo '>USD</option><option value="AUD" ';
                                                                if ($pri["currency"] == "AUD") {echo "selected";}
                                                                echo '>AUD</option><option value="NZD" ';
                                                                if ($pri["currency"] == "NZD") {echo "selected";}
                                                                echo '>NZD</option></select></div><div class="col-md-12 m-b-20"><input type="number" name="msg_nb" class="form-control" value="' . $pri["messages"] . '" min="0" max="100" step="1"></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="status" value="1" class="custom-control-input" id="customCheck1" ';
                                                                if ($pri["status"] == 1) {echo "checked";}
                                                                echo '><label class="custom-control-label" for="customCheck1">Status POPULAR </label></div></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="active" value="1" class="custom-control-input" id="customCheck2" ';
                                                                if ($pri["active"] == 1) {echo "checked";}
                                                                echo '><label class="custom-control-label" for="customCheck2">Active </label></div></div></div></from></div><div class="modal-footer"><button type="button" class="btn btn-info waves-effect save" data-dismiss="modal" data-id="' . $pri["id_pricing"] . '">Save</button><button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button></div></div></div></div><button class="btn btn-success waves-effect waves-light"  data-toggle="modal" data-target="#edit-' . $pri["id_pricing"] . '">Edit</button></div></div></div></div></div>';
                                                            }
                                                        }
                                                    } else {
                                                        echo '<div class="col-md-12 text-center m-t-40 m-b-40"><i class="mdi mdi-calculator" style="font-size: 120px;color: #dea5aa;margin: 20px 0;display: block;"></i><h3 style="color: #797979;">No pricing yet!</h3></div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane " id="options" role="tabpanel">
                                    <div class="card card-body">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <?php
                                                if (isset($_POST['update5'])) {

                                                    $stmt2 = $conn->prepare("UPDATE `websites` SET `max_size`=:st , `max_time`=:ti WHERE `id_website`=:ID");
                                                    $stmt2->bindParam(':st', $_POST['max_size'], PDO::PARAM_INT);
                                                    $stmt2->bindParam(':ti', $_POST['max_time'], PDO::PARAM_INT);
                                                    $stmt2->bindParam(':ID', intval($_GET['id']), PDO::PARAM_INT);
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
                                                        <label for="max_size">Uploaded file Max size(MB)</label>
                                                        <select name="max_size" class="form-control" id="max_size" required>
                                                            <option value="1" <?= $result->max_size==1?"Selected": "" ?>>1</option>
                                                            <option value="2" <?= $result->max_size==2?"Selected": ""  ?>>2</option>
                                                            <option value="3" <?= $result->max_size==3?"Selected": ""  ?>>3 </option>
                                                            <option value="4" <?= $result->max_size==4?"Selected": ""  ?>>4</option>
                                                            <option value="5" <?= $result->max_size==5?"Selected": ""  ?>>5</option>
                                                            <option value="6" <?= $result->max_size==6?"Selected": ""  ?>>6</option>
                                                            <option value="7" <?= $result->max_size==7?"Selected": ""  ?>>7</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_time">Max waiting time(Seconde)</label>
                                                        <select name="max_time" class="form-control" id="max_time" required>
                                                            <option value="5" <?= $result->max_time==5?"Selected": "" ?>>5</option>
                                                            <option value="10" <?= $result->max_time==10?"Selected": ""  ?>>10</option>
                                                            <option value="30" <?= $result->max_time==30?"Selected": ""  ?>>30</option>
                                                            <option value="60" <?= $result->max_time==60?"Selected": ""  ?>>60</option>
                                                            <option value="90" <?= $result->max_time==90?"Selected": ""  ?>>90</option>
                                                            <option value="120" <?= $result->max_time==120?"Selected": ""  ?>>120</option>
                                                            <option value="180" <?= $result->max_time==180?"Selected": ""  ?>>180</option>
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <hr>
                                                    <button type="submit" name="update5" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                                    <button type="reset" class="btn btn-secondary waves-effect waves-light">Cancel</button>
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
    <?php }
    } ?>
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
<footer class="footer">
    <?php echo ($trans["footer"]) ?>

</footer> <!-- ============================================================== -->
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
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/node_modules/bootstrap-switch/bootstrap-switch.min.js"></script>
<script src="../../assets/node_modules/dropify/dropify.min.js"></script>
<script src="../../assets/node_modules/icheck/icheck.min.js"></script>
<script src="../../assets/node_modules/icheck/icheck.init.js"></script>
<script src="../../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup.min.js"></script>
<script src="../../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup-init.js"></script>
<script type="text/javascript">
    background_img = "<?php echo $result->background; ?>";
    logo = "<?php echo $result->logo; ?>";
    if (background_img != null && background_img != "") {
        var ext = background_img.substr(background_img.lastIndexOf('.') + 1);
        var exts = ['gif', 'jpg', 'jpeg', 'png'];
        if (exts.indexOf("Apple") >= 0) {
            $("#input-file-1").attr("data-default-file", "../uploads/" + background_img);
        }
    }
    if (logo != null && logo != "") {
        $("#input-file-2").attr("data-default-file", "../uploads/" + logo);
    }
    $('.dropify').dropify();
    $(".select2").select2();
    $('#myTable').DataTable({
        "lengthMenu": [
            [5, 10, 20, 50, -1],
            [5, 10, 20, 50, "All"]
        ]
    });
    $(".bt-switch input[type='checkbox'], .bt-switch input[type='radio']").bootstrapSwitch();
    var radioswitch = function() {
        var bt = function() {
            $(".radio-switch").on("switchChange.bootstrapSwitch", function() {
                $(".radio-switch").bootstrapSwitch("toggleRadioState")
            }), $(".radio-switch").on("switchChange.bootstrapSwitch", function() {
                $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck")
            }), $(".radio-switch").on("switchChange.bootstrapSwitch", function() {
                $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck", !1)
            })
        };
        return {
            init: function() {
                bt()
            }
        }
    }();
    $(document).ready(function() {
        radioswitch.init()
    });
    $('input[name=payment]').on('switchChange.bootstrapSwitch', function(event, state) {
        if (state) {
            $('input[name=payment]').val(1);
            $('#payment_bloc').removeClass('hide');
            $('#payment_bloc0').addClass('hide');
        } else {
            $('input[name=payment]').val(0);
            $('#payment_bloc').addClass('hide');
            $('#payment_bloc0').removeClass('hide');
        }
    });

    $('#languages').change(function() {
        langs = ["fr", "en", "it", "es", "de", "pt"];
        ln = $(this).val();

        $.each(langs, function(key, lang) {
            if ($.inArray(lang, ln) == -1) {
                $("#default_language option[value=" + lang + "]").attr('disabled', true);
            } else {
                console.log('exist ' + lang);
                $("#default_language option[value=" + lang + "]").attr('disabled', false);
            }
        });
        $('#default_language').select2();
    });

    $("#add_test").click(function() {
        $.ajax({
            url: 'functions_ajax.php',
            dataType: 'json',
            data: {
                type: "add_testimonial",
                username: $("#add-testimonial input[name=username]").val(),
                rating: $("#add-testimonial input[name=rating]").val(),
                title0: $("#add-testimonial input[name=title0]").val(),
                content: $("#add-testimonial input[name=content]").val(),
                id_website: "<?php echo $id_website; ?>"
            },
            success: function(code_html, statut) {
                location.reload();
            },
            error: function(xhr, status, error) {
                alert("Unsuccessful request");
            }
        });
    });
    $(".jsgrid-edit-button").click(function() {
        var id = $(this).parent().attr('data-id');
        $('#testimonial-detail .modal-body').load('getTestimonial.php?id_testimonial=' + id, function() {
            $("#testimonial-detail .modal-title span").text(id);
            $('#testimonial-detail').modal({
                show: true
            });
            $("#btn-update").click(function() {
                $.ajax({
                    url: 'functions_ajax.php',
                    dataType: 'json',
                    data: {
                        type: "edit_testimonial",
                        username: $("#testimonial-detail input[name=username]").val(),
                        rating: $("#testimonial-detail input[name=rating]").val(),
                        title0: $("#testimonial-detail input[name=title0]").val(),
                        content: $("#testimonial-detail input[name=content]").val(),
                        id_testimonial: id
                    },
                    success: function(code_html, statut) {
                        location.reload();
                    },
                    error: function(statut) {
                        alert("Unsuccessful request");
                    }
                });
            });
        });
    });
    $(".jsgrid-delete-button").click(function() {
        if (!confirm('Are you sure you want to delete this testimonial?')) {
            return false;
        }
        var id = $(this).parent().attr('data-id');
        var obj = $(this);
        $.ajax({
            url: 'functions_ajax.php',
            dataType: "json",
            data: {
                type: 'remove_testimonial',
                id: id
            },
            success: function(code_html, statut) {
                location.reload();
            },
            error: function(statut) {
                alert("Unsuccessful request");
            }
        });
    });
    $("#add_pricing").click(function() {
        $.ajax({
            url: 'functions_ajax.php',
            dataType: 'json',
            data: {
                type: "add_pricing",
                title: $("#add-new input[name=title]").val(),
                price: $("#add-new input[name=price]").val(),
                currency: $("#add-new select[name=currency]").val(),
                msg_nb: $("#add-new input[name=msg_nb]").val(),
                status: $("#add-new input[name=status]").val(),
                id_website: "<?php echo $id_website; ?>"
            },
            success: function(code_html, statut) {
                location.reload();
            },
            error: function(xhr, status, error) {
                console.log(xhr);
                alert("Unsuccessful request");
            }
        });
    });
    $(".save").click(function() {
        bloc = $(this).parent().parent();
        id = $(this).attr('data-id');
        $.ajax({
            url: 'functions_ajax.php',
            dataType: 'json',
            data: {
                type: "edit_pricing",
                title: bloc.find("input[name=title]").val(),
                price: bloc.find("input[name=price]").val(),
                currency: bloc.find("select[name=currency]").val(),
                msg_nb: bloc.find("input[name=msg_nb]").val(),
                status: bloc.find("input[name=status]").val(),
                active: bloc.find("input[name=active]").val(),
                id: id
            },
            success: function(code_html, statut) {
                location.href('website.php?id=' + id + '&tab=pricing');
            },
            error: function(xhr, status, error) {
                console.log(xhr);
                alert("Unsuccessful request");
            }
        });
    });

    $(".icolors li").click(function(e) {
        var file = $(this).find('img').attr("src");
        var name = $(this).data('name');
        $.magnificPopup.open({
            items: {
                src: $('<img src="' + file + '"/><button class="choose" type="button" data-name="' + name + '">Choose</button>'),
                type: 'inline'
            },
            closeBtnInside: false,
        });
        $(".choose").click(function() {
            id = $(this).data('name');
            $('input[name=backg]').val(id);
        });
        e.preventDefault();
    });
</script>
</body>
</html>