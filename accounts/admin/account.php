<?php
$page_name = "my_account";
include('header.php'); ?>
<style>
    .iti {width: 100%;}
</style>
<div class="">
    <?php
    if ($id_account == 0) {
        echo '<section id="wrapper" class="col-md-12 error-page m-t-40">
        <div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">' .  ($trans["feedback_msg"]["something_went_wrong"]) . '</h3><p class="text-muted m-t-30 m-b-30">' .  ($trans["feedback_msg"]["working_on"]) . '</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">' .  ($trans["feedback_msg"]["return_to_dashboard"]) . '</a></div></div></section>';
    } else {
        $stmt = $conn->prepare("SELECT a.*, b.*, c.*, d.`content`, am.* FROM `accounts` a LEFT JOIN `accounts_messages` am on am.id_account = a.id_account LEFT JOIN `managers` b ON b.`id_account` = a.`id_account` LEFT JOIN `accounts_settings` c ON c.`id_account` = a.`id_account` LEFT JOIN `translations` d ON d.`id_element` = a.`id_account` AND d.`table` = 'account' AND `column` = 'welcome_text' WHERE a.`id_account` = :ID");
        $stmt->bindParam(':ID', $id_account, PDO::PARAM_INT);
        $stmt->execute();
        $total = $stmt->rowCount();
        $result = $stmt->fetchObject();
        if ($total == 0) {
            echo '<section id="wrapper" class="col-md-12 error-page m-t-40"><div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">' .  ($trans["feedback_msg"]["account_not_found"]) . '</h3><p class="text-muted m-t-30 m-b-30">' .  ($trans["feedback_msg"]["page_not_found"]) . '</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">' .  ($trans["feedback_msg"]["return_to_dashboard"]) . '</a> </div></div></section>';
        } else { ?>
            <div class="card">
                <div class="d-flex flex-row">
                    <?php
                    if ($result->status == 0) {
                        echo '<div class="p-10 bg-danger"><h3 class="text-white box m-b-0"><i class="mdi mdi-cancel"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-danger">' . ($trans["declined"]) . '</h3></div>';
                    } elseif ($result->status == 1) {
                        echo '<div class="p-10 bg-success"><h3 class="text-white box m-b-0"><i class="mdi mdi-alarm-plus"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-success">' . ($trans["new"]) . '</h3></div>';
                    } elseif ($result->status == 2) {
                        echo '<div class="p-10 bg-info"><h3 class="text-white box m-b-0"><i class="mdi mdi-checkbox-marked-circle-outline"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-info">' . ($trans["approved"]) . '</h3></div>';
                    } elseif ($result->status == 3) {
                        echo '<div class="p-10 bg-warning"><h3 class="text-white box m-b-0"><i class="mdi mdi-progress-wrench"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-warning">' . ($trans["checking"]) . '</h3></div>';
                    }
                    ?>
                </div>
            </div>
            <div class="card card-body p-l-0">
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) &&  $_GET['tab'] == 'account')) {echo "active";} ?>" data-toggle="tab" href="#account" role="tab"><?php echo ($trans["account_informations"]) ?></a> </li>
                    <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'chat_settings') {echo "active";} ?>" data-toggle="tab" href="#chat_settings" role="tab"><?php echo ($trans["admin"]["account"]["chat_settings"]) ?></a> </li>
                    <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'manager') {echo "active";} ?>" data-toggle="tab" href="#manager" role="tab"><?php echo ($trans["admin"]["account"]["manager"]) ?></a> </li>
                    <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'predefined_messages') {echo "active";} ?>" data-toggle="tab" href="#predefined_messages" role="tab"><?php echo ($trans["admin"]["account"]["predefined_messages"]) ?></a> </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) &&  $_GET['tab'] == 'account')) {echo "active";} ?>" id="account" role="tabpanel">
                        <div class="card-body">
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
                                $currency = (isset($_POST['currency']) && $_POST['currency'] != '') ? htmlspecialchars($_POST['currency']) : NULL;

                                $stmt2 = $conn->prepare("UPDATE `accounts` SET `business_name`=:bus,`registration`=:reg,`taxid`=:tax,`address`=:adr,`code_postal`=:cp,`city`=:ct,`country`=:cnt,`phone`=:ph,`emailc`=:em,`website`=:web,`currency`=:cur WHERE `id_account`=:ID");
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
                                $stmt2->bindParam(':cur', $currency, PDO::PARAM_STR);
                                $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                $stmt2->execute();
                                $affected_rows = $stmt2->rowCount();

                                if ($affected_rows != 0) {
                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_updated"]) . " </div>";
                                    $stmt->execute();
                                    $result = $stmt->fetchObject();
                                } else {
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["account_info_failed"]) . " </div>";
                                }
                                unset($_POST);
                            }
                            ?>
                            <form action="" method="POST">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="acctInput1"><?php echo ($trans["admin"]["account"]["business_name"]) ?></label>
                                        <input type="text" name="business_name" class="form-control" id="acctInput1" value="<?php echo $result->business_name; ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="acctInput2"><?php echo ($trans["admin"]["account"]["registration_number"]) ?></label>
                                        <input type="text" name="registration" class="form-control" id="acctInput2" value="<?php echo $result->registration; ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="acctInput22"><?php echo ($trans["admin"]["account"]["tax_id"]) ?></label>
                                        <input type="text" name="taxid" class="form-control" id="acctInput22" value="<?php echo $result->taxid; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="acctInput3"><?php echo ($trans["admin"]["account"]["address"]) ?></label>
                                        <input type="text" name="address" class="form-control" id="acctInput3" value="<?php echo $result->address; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="acctInput4"><?php echo ($trans["admin"]["account"]["postal_code"]) ?></label>
                                        <input type="text" name="pcode" class="form-control" id="acctInput4" value="<?php echo $result->code_postal; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="acctInput44"><?php echo ($trans["admin"]["account"]["city"]) ?></label>
                                        <input type="text" name="city" class="form-control" id="acctInput44" value="<?php echo $result->city; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="country"><?php echo ($trans["admin"]["account"]["country"]) ?></label>
                                        <select name="country" id="country" class="form-control select-search country select2">
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
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="phone"><?php echo ($trans["admin"]["account"]["phone"]) ?></label>
                                        <div class="">
                                            <input type="tel" name="phone" id="phone" class="form-control phonenumber" style="width:100%;" value="<?php echo $result->phone; ?>">

                                        </div>
                                        <span id="valid-msg" data-type="valid-msg" class="hide text-success">✓ Valid</span>
                                        <span id="error-msg" data-type="error-msg" class="hide text-danger">Invalid number</span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="Email1"><?php echo ($trans["admin"]["account"]["email"]) ?></label>
                                        <input type="email" name="emailc" class="form-control" id="Email1" value="<?php echo $result->emailc; ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="acctInput7"><?php echo ($trans["admin"]["account"]["website"]) ?></label>
                                        <input type="url" name="website" class="form-control" id="acctInput7" value="<?php echo $result->website; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="Currency"><?php echo ($trans["Currency"]) ?></label>
                                        <select name="currency" id="Currency" class="form-control select2">
                                            <?php foreach (array_unique($currencies) as $devise) {
                                                if ($result->currency == $devise) {
                                                    echo '<option value="' . $devise . '" SELECTED>' . $devise . '</option>';
                                                } else echo '<option value="' . $devise . '">' . $devise . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <hr>
                                <button type="submit" name="update1" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                <button type="submit" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane <?php if ((isset($_GET['tab']) &&  $_GET['tab'] == 'chat_settings')) {echo "active";} ?>" id="chat_settings" role="tabpanel">
                        <div class="card-body">
                            <?php
                            if (isset($_POST['update-app-settings'])) {
                                $max_length = (isset($_POST['message-max-length']) && $_POST['message-max-length'] != '') ? htmlspecialchars($_POST['message-max-length']) : NULL;
                                $unread_duration = (isset($_POST['unread-duration']) && $_POST['unread-duration'] != '') ? htmlspecialchars($_POST['unread-duration']) * 60 : NULL;
                                $welcome_text = (isset($_POST['welcome_text']) && $_POST['welcome_text'] != '') ? htmlspecialchars($_POST['welcome_text']) : NULL;

                                $stmt2 = $conn->prepare("UPDATE `accounts` SET `unread_duration`=:ud, `max_length`=:ml, `welcome_text`=:wl WHERE `id_account`=:ID");
                                $stmt2->bindParam(':ml', $max_length, PDO::PARAM_STR);
                                $stmt2->bindParam(':ud', $unread_duration, PDO::PARAM_STR);
                                $stmt2->bindParam(':wl', $welcome_text, PDO::PARAM_STR);
                                $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                $stmt2->execute();
                                $affected_rows = $stmt2->rowCount();

                                if (isset($_POST['welcome_text_fr'])) {
                                    $titleFr = $_POST['welcome_text_fr'];
                                    if ($result->content != null) {
                                        $stmtl = $conn->prepare("UPDATE translations set content=:ct where `table` like 'account' and lang like 'fr' and id_element=:ID");
                                        $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
                                        $stmtl->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                        $stmtl->execute();
                                    } else {
                                        $stmtl = $conn->prepare("INSERT INTO `translations`(`content`, `table`, `column`, `lang`, `id_element`) VALUES (:ct,'account','welcome_text','fr',:ie)");
                                        $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
                                        $stmtl->bindParam(':ie', $id_account, PDO::PARAM_INT);
                                        $stmtl->execute();
                                    }
                                }

                                if ($affected_rows != 0) {
                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["settings_updated"]) . " </div>";
                                    $stmt->execute();
                                    $result = $stmt->fetchObject();
                                } else {
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["settings_failed"]) . " </div>";
                                }
                                unset($_POST);
                            }
                            ?>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label class="col-md-12"><?php echo ($trans["chat_length"]) ?></label>
                                    <div class="col-md-12">
                                        <input type="number" min="0" name="message-max-length" id="message-max-length" value="<?= $result->max_length ?>" class="form-control form-control-line">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12"><?php echo ($trans["unread_duration"]) ?></label>
                                    <div class="col-md-12">
                                        <input type="number" min="0" name="unread-duration" id="unread-duration" value="<?= ($result->unread_duration / 60) ?>" class="form-control form-control-line">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12"><?php echo ($trans["chat_welcome_text"] . ' <small class="text-muted">[' . $trans["anglais"]) ?>]</small></label>
                                  
                                    <div class="col-md-12">
                                        <textarea rows="4" maxlength="255" name="welcome_text" id="welcome_text" class="form-control form-control-line"><?= $result->welcome_text ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12"><?php echo ($trans["chat_welcome_text"] . ' <small class="text-muted">[' . $trans["francais"]) ?>]</small></label>
                                    
                                    <div class="col-md-12">
                                        <textarea rows="4" maxlength="255" name="welcome_text_fr" id="welcome_text_fr" class="form-control form-control-line"><?= $result->content ?></textarea>
                                    </div>
                                </div>

                                <br>
                                <hr>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" name="update-app-settings" class="btn btn-primary"><?php echo ($trans["update"]) ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane <?php if ((isset($_GET['tab']) &&  $_GET['tab'] == 'manager')) {
                                                echo "active";
                                            } ?>" id="manager" role="tabpanel">
                        <div class="card-body">
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
                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["manager_updated"]) . " </div>";
                                    $stmt->execute();
                                    $result = $stmt->fetchObject();
                                } else {
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["manager_failed"]) . " </div>";
                                }
                                unset($_POST);
                            }
                            ?>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="acctInput1"><?php echo ($trans["admin"]["account"]["first_name"]) ?></label>
                                    <input type="text" name="first_name" class="form-control" id="acctInput1" value="<?php echo $result->firstname; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="acctInput2"><?php echo ($trans["admin"]["account"]["last_name"]) ?></label>
                                    <input type="text" name="last_name" class="form-control" id="acctInput2" value="<?php echo $result->lastname; ?>">
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php echo ($trans["admin"]["account"]["gender"]) ?></label>
                                    <div>
                                        <div class="custom-control custom-radio d-inline">
                                            <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input" <?php if ($result->gender == 1) {
                                                echo "checked";
                                            } ?>>
                                            <label class="custom-control-label" for="customRadio1"><?php echo ($trans["admin"]["account"]["male"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio d-inline">
                                            <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input" <?php if ($result->gender == 2) {
                                                echo "checked";
                                            } ?>>
                                            <label class="custom-control-label" for="customRadio2"><?php echo ($trans["admin"]["account"]["female"]) ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phone"><?php echo ($trans["admin"]["account"]["personal_phone"]) ?></label>
                                    <input type="tel" name="phone2" id="phone2" class="form-control phonenumber" value="<?php echo $result->phonep; ?>">
                                    <span id="valid-msg2" data-type="valid-msg" class="hide text-success">✓ Valid</span>
                                    <span id="error-msg2" data-type="error-msg" class="hide text-danger">Invalid number</span>
                                </div>
                                <div class="form-group">
                                    <label for="Email1"><?php echo ($trans["admin"]["account"]["personal_email"]) ?></label>
                                    <input type="email" name="emailc2" class="form-control" id="Email1" value="<?php echo $result->emailp; ?>">
                                </div>
                                <br>
                                <hr>
                                <button type="submit" name="update2" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane <?php if ((isset($_GET['tab']) &&  $_GET['tab'] == 'predefined_messages')) {
                                                echo "active";
                                            } ?>" id="predefined_messages" role="tabpanel">
                        <div class="card-body">
                            <?php
                            if (isset($_POST['update_pred'])) {
                                $welcome_title = (isset($_POST['welcome_title']) && $_POST['welcome_title'] != '') ? htmlspecialchars($_POST['welcome_title']) : NULL;
                                $welcome_description = (isset($_POST['welcome_description']) && $_POST['welcome_description'] != '') ? htmlspecialchars($_POST['welcome_description']) : NULL;
                                $buy_button = (isset($_POST['buy_button']) && $_POST['buy_button'] != '') ? htmlspecialchars($_POST['buy_button']) : NULL;
                                $buy_tooltip = (isset($_POST['buy_tooltip']) && $_POST['buy_tooltip'] != '') ? htmlspecialchars($_POST['buy_tooltip']) : NULL;
                                $credit_box_title = (isset($_POST['credit_box_title']) && $_POST['credit_box_title'] != '') ? htmlspecialchars($_POST['credit_box_title']) : NULL;
                                $credit_box_description = (isset($_POST['credit_box_description']) && $_POST['credit_box_description'] != '') ? htmlspecialchars($_POST['credit_box_description']) : NULL;
                                $alert_payment_success = (isset($_POST['alert_payment_success']) && $_POST['alert_payment_success'] != '') ? htmlspecialchars($_POST['alert_payment_success']) : NULL;
                                $alert_payment_error = (isset($_POST['alert_payment_error']) && $_POST['alert_payment_error'] != '') ? htmlspecialchars($_POST['alert_payment_error']) : NULL;
                                $agent_payment_success = (isset($_POST['agent_payment_success']) && $_POST['agent_payment_success'] != '') ? htmlspecialchars($_POST['agent_payment_success']) : NULL;
                                $agent_payment_error = (isset($_POST['agent_payment_error']) && $_POST['agent_payment_error'] != '') ? htmlspecialchars($_POST['agent_payment_error']) : NULL;
                                $agent_pricing = (isset($_POST['agent_pricing']) && $_POST['agent_pricing'] != '') ? htmlspecialchars($_POST['agent_pricing']) : NULL;
                                $agent_default_message = (isset($_POST['agent_default_message']) && $_POST['agent_default_message'] != '') ? htmlspecialchars($_POST['agent_default_message']) : NULL;

                                $stmt2 = $conn->prepare("UPDATE `accounts_messages` SET `welcome_title`=:wt,`welcome_description`=:wd,`buy_button`=:bb,`buy_tooltip`=:bt,`credit_box_title`=:cbt, `credit_box_description`=:cbd, `alert_payment_success`=:psu, `alert_payment_error`=:per, `agent_payment_success`=:aps, `agent_payment_error`=:ape, `agent_pricing`=:ap, `agent_default_message`=:adm WHERE `id_account`=:ID");
                                $stmt2->bindParam(':wt', $welcome_title, PDO::PARAM_STR);
                                $stmt2->bindParam(':wd', $welcome_description, PDO::PARAM_STR);
                                $stmt2->bindParam(':bb', $buy_button, PDO::PARAM_STR);
                                $stmt2->bindParam(':bt', $buy_tooltip, PDO::PARAM_STR);
                                $stmt2->bindParam(':cbt', $credit_box_title, PDO::PARAM_STR);
                                $stmt2->bindParam(':cbd', $credit_box_description, PDO::PARAM_STR);
                                $stmt2->bindParam(':psu', $alert_payment_success, PDO::PARAM_STR);
                                $stmt2->bindParam(':per', $alert_payment_error, PDO::PARAM_STR);
                                $stmt2->bindParam(':aps', $agent_payment_success, PDO::PARAM_STR);
                                $stmt2->bindParam(':ape', $agent_payment_error, PDO::PARAM_STR);
                                $stmt2->bindParam(':ap', $agent_pricing, PDO::PARAM_STR);
                                $stmt2->bindParam(':adm', $agent_default_message, PDO::PARAM_STR);
                                $stmt2->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                $stmt2->execute();
                                $affected_rows = $stmt2->rowCount();

                                if ($affected_rows != 0) {
                                    echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["predefined_updated"]) . " </div>";
                                    $stmt->execute();
                                    $result = $stmt->fetchObject();
                                } else {
                                    echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " .  ($trans["feedback_msg"]["predefined_failed"]) . " </div>";
                                }
                                unset($_POST);
                            }
                            ?>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="welcome_title"><?php echo ($trans["admin"]["account"]["welcome_title"]) ?></label>
                                    <textarea name="welcome_title" name="welcome_title" class="form-control" id="welcome_title" ><?php echo $result->welcome_title; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="welcome_description"><?php echo ($trans["admin"]["account"]["welcome_description"]) ?></label>
                                    <textarea name="welcome_description" class="form-control" id="welcome_description" ><?php echo $result->welcome_description; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="buy_button"><?php echo ($trans["admin"]["account"]["buy_button"]) ?></label>
                                    <input type="text" name="buy_button" class="form-control" id="buy_button" value="<?php echo $result->buy_button; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="buy_tooltip"><?php echo ($trans["admin"]["account"]["buy_tooltip"]) ?></label>
                                    <input type="text" name="buy_tooltip" class="form-control" id="buy_tooltip" value="<?php echo $result->buy_tooltip; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="credit_box_title"><?php echo ($trans["admin"]["account"]["credit_box_title"]) ?></label>
                                    <input type="text" name="credit_box_title" class="form-control" id="credit_box_title" value="<?php echo $result->credit_box_title; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="credit_box_description"><?php echo ($trans["admin"]["account"]["credit_box_description"]) ?></label>
                                    <textarea name="credit_box_description" class="form-control" id="credit_box_description"><?php echo $result->credit_box_description; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="alert_payment_success"><?php echo ($trans["admin"]["account"]["alert_payment_success"]) ?></label>
                                    <textarea name="alert_payment_success" class="form-control" id="alert_payment_success"><?php echo $result->alert_payment_success; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="alert_payment_error"><?php echo ($trans["admin"]["account"]["alert_payment_error"]) ?></label>
                                    <textarea name="alert_payment_error" class="form-control" id="alert_payment_error"><?php echo $result->alert_payment_error; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="agent_payment_success"><?php echo ($trans["admin"]["account"]["agent_payment_success"]) ?></label>
                                    <input type="text" name="agent_payment_success" class="form-control" id="agent_payment_success" value="<?php echo $result->agent_payment_success; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="agent_payment_error"><?php echo ($trans["admin"]["account"]["agent_payment_error"]) ?></label>
                                    <input type="text" name="agent_payment_error" class="form-control" id="agent_payment_error" value="<?php echo $result->agent_payment_error; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="agent_pricing"><?php echo ($trans["admin"]["account"]["agent_pricing"]) ?></label>
                                    <input type="text" name="agent_pricing" class="form-control" id="agent_pricing" value="<?php echo $result->agent_pricing; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="agent_default_message"><?php echo ($trans["admin"]["account"]["agent_default_message"]) ?></label>
                                    <input type="text" name="agent_default_message" class="form-control" id="agent_default_message" value="<?php echo $result->agent_default_message; ?>">
                                </div>
                                <br>
                                <hr>
                                <button type="submit" name="update_pred" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["update"]) ?></button>
                                <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

    <?php }
    } ?>
</div>
</div>
</div>
<footer class="footer"><?php echo ($trans["footer"]) ?></footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
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
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script type="text/javascript">
    $('.select2').select2();
    var country = "<?php echo $result->country; ?>";
    if (country == null) {
        country = "fr";
    }
    var errorMsg = $("#error-msg"),
        validMsg = $("#valid-msg");
    // here, the index maps to the error code returned from getValidationError - see readme
    var errorMap = ["Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];
    // initialise plugin
    var iti = $("#phone").intlTelInput({
        nationalMode: true,
        autoPlaceholder: "off",
        initialCountry: country,
        utilsScript: "../../assets/int-phone-number/js/utils.js"
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
    // listen to the address dropdown for changes
    var errorMsg2 = $("#error-msg2"),
        validMsg2 = $("#valid-msg2");
    // here, the index maps to the error code returned from getValidationError - see readme
    var errorMap2 = ["Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];
    // initialise plugin
    var iti = $("#phone2").intlTelInput({
        nationalMode: true,
        autoPlaceholder: "off",
        initialCountry: country,
        utilsScript: "../../assets/int-phone-number/js/utils.js"
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
    $('.select2-selection--single').addClass('form-control');
    $('.select2-selection--single').css('border', '1px solid #e9ecef');
    // on keyup / change flag: reset
    $("#phone2").on('change', reset2);
    $("#phone2").on('keyup', reset2);

    $("#country").on('change', function() {
        $('#phone').intlTelInput('setCountry', $(this).val());
        $('#phone2').intlTelInput('setCountry', $(this).val());
    });
</script>
</body>
</html>