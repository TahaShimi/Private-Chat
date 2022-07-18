<?php
$page_folder = "Websites";
$page_name = "Website";
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
        $stmt = $conn->prepare("SELECT a.*, b.*, c.`business_name`, c.`emailc` FROM `websites` a LEFT JOIN `websites_landing` b ON a.`id_website` = b.`id_website` LEFT JOIN `accounts` c ON a.`id_account` = c.`id_account` WHERE a.`id_website` = :ID");
        $stmt->bindParam(':ID', $id_website, PDO::PARAM_INT);
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
                    <div class="d-flex flex-row">
                        <?php
                        if ($result->status == 0) {
                            echo '<div class="p-10 bg-danger"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-danger">Declined</h3></div>';
                        } elseif ($result->status == 1) {
                            echo '<div class="p-10 bg-success"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-success">New</h3></div><div class="col text-right align-self-center"><button type="button" class="btn btn-outline-info" id="approve"><i class="fa fa-check"></i> Approve</button></div>';
                        } elseif ($result->status == 2) {
                            echo '<div class="p-10 bg-info"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-info">Approved</h3></div>';
                        } elseif ($result->status == 3) {
                            echo '<div class="p-10 bg-warning"><h3 class="text-white box m-b-0"><i class="ti-pin2"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-warning">Checking</h3></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="box-title m-b-30">Wesbite #<?php echo $id_website; ?></h3>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs customtab" role="tablist">
                                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#general" role="tab"><span class="hidden-sm-up"><i class="ti-write"></i></span> <span class="hidden-xs-down">General informations</span></a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#gopaid" role="tab"><span class="hidden-sm-up"><i class="ti-shopping-cart"></i></span> <span class="hidden-xs-down">Payment Gateway (GoPaid)</span></a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#landing" role="tab"><span class="hidden-sm-up"><i class="ti-layout-accordion-list"></i></span> <span class="hidden-xs-down">Landing page</span></a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#pricing" role="tab"><span class="hidden-sm-up"><i class="ti-money"></i></span> <span class="hidden-xs-down">Pricing</span></a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#options" role="tab"><span class="hidden-sm-up"><i class="icon-equalizer"></i></span> <span class="hidden-xs-down">Options</span></a> </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="general" role="tabpanel">
                                    <div class="p-20">
                                        <form action="" method="POST">
                                            <div class="form-group">
                                                <label for="webInput1">Name</label>
                                                <input type="text" name="title" class="form-control" id="webInput1" value="<?php echo $result->name; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="webInput2">Website</label>
                                                <input type="url" name="website" class="form-control" id="webInput2" value="<?php echo $result->url; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="activity">Activity</label>
                                                <select name="activity" id="activity" class="form-control select2 select-search" style="width: 100%;">
                                                    <option></option>
                                                    <option value="ecommerce" <?php if ($result->activity == 'ecommerce') {
                                                                                    echo "selected";
                                                                                } ?>>E-commerce</option>
                                                    <option value="studies_advice" <?php if ($result->activity == 'studies_advice') {
                                                                                        echo "selected";
                                                                                    } ?>>Studies & Advice</option>
                                                    <option value="it_com" <?php if ($result->activity == 'it_com') {
                                                                                echo "selected";
                                                                            } ?>>IT / Telecom</option>
                                                    <option value="business_services" <?php if ($result->activity == 'business_services') {
                                                                                            echo "selected";
                                                                                        } ?>>Business services</option>
                                                    <option value="administration" <?php if ($result->activity == 'administration') {
                                                                                        echo "selected";
                                                                                    } ?>>Administration</option>
                                                    <option value="maintenance_spport" <?php if ($result->activity == 'maintenance_spport') {
                                                                                            echo "selected";
                                                                                        } ?>>Maintenance & Support</option>
                                                    <option value="legal" <?php if ($result->activity == 'legal') {
                                                                                echo "selected";
                                                                            } ?>>Legal Department</option>
                                                    <option value="medical_service" <?php if ($result->activity == 'medical_service') {
                                                                                        echo "selected";
                                                                                    } ?>>Medical service</option>
                                                    <option value="other" <?php if ($result->activity == 'other') {
                                                                                echo "selected";
                                                                            } ?>>Other</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="webInput22">Private-chat.pro folder name</label>
                                                <input type="text" name="url_directory" class="form-control" id="webInput22" value="<?php echo $result->url_directory; ?>">
                                                <small id="webInput22Help" class="form-text text-muted">This name will be used in the url of the landing page (<b>https://private-chat.pro/landing-page/<span><?php echo $result->url_directory; ?></span>/</b>)</small>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row text-center">
                                                        <label class="control-label col-md-12">Login page</label>
                                                        <p class="form-control-static col-md-12"><?php if ($result->url_directory != NULL) {
                                                                                                        echo "<a href='https://private-chat.pro/accounts/login.php'>https://private-chat.pro/accounts/login.php</a>";
                                                                                                    } ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row text-center">
                                                        <label class="control-label col-md-12">Landing page</label>
                                                        <p class="form-control-static col-md-12"><?php if ($result->url_directory != NULL) {
                                                                                                        echo "<a href='https://private-chat.pro/websites/" . $result->url_directory . "/landing-page/'>https://private-chat.pro/landing-page/" . $result->url_directory . "/</a>";
                                                                                                    } ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="webInput3">Return url</label>
                                                <input type="url" name="return_url" class="form-control" id="webInput3" value="<?php echo $result->return_url; ?>">
                                                <small id="webInput3Help" class="form-text text-muted">by default the return url will be your website, here you can put a url of a custom page.</small>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane" id="gopaid" role="tabpanel">
                                    <div class="p-20">
                                        <form action="" method="POST">
                                            <div class="form-group bt-switch">
                                                <label for="acctInput1">Get payment service</label>
                                                <input type="checkbox" name="payment" value="0" data-size="small" data-on-text="Yes" data-off-text="No" data-on-color="success" <?php if ($result->payment == 1) {
                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                } ?> />
                                            </div>
                                            <br>
                                            <div id="payment_bloc0" class="<?php if (intval($result->payment) == 1) {
                                                                                echo "hide";
                                                                            } ?>">
                                                <div class="form-group">
                                                    <label for="webInput3">Own Payment gateway page</label>
                                                    <input type="url" name="payment_url" class="form-control" id="webInput3" value="<?php echo $result->payment_url; ?>">
                                                </div>
                                            </div>
                                            <div id="payment_bloc" class="<?php if (intval($result->payment) != 1) {
                                                                                echo "hide";
                                                                            } ?>">
                                                <div class="col-md-12 row">
                                                    <div class="col-md-6">
                                                        <div class="form-group bt-switch">
                                                            <label for="webInput4">Send receipts to clients : </label>
                                                            <input type="checkbox" name="payment_receipt" id="webInput4" data-size="mini" <?php if ($result->payment_receipt == 1) {
                                                                                                                                                echo "checked";
                                                                                                                                            } ?> />
                                                        </div>
                                                        <div class="form-group bt-switch">
                                                            <label for="webInput5">Send payment notifications : </label>
                                                            <input type="checkbox" name="payment_notification" id="webInput5" data-size="mini" <?php if ($result->payment_notification == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="">Languages for payment page :</label>
                                                            <select id="languages" class="select2 m-b-10 select2-multiple" style="width: 100%" multiple="multiple" name="languages[]" data-placeholder="Choose your languages">
                                                                <option value=""></option>
                                                                <option value="fr" <?php if (in_array('fr', explode(",", $result->languages))) {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>French</option>
                                                                <option value="en" <?php if (in_array('en', explode(",", $result->languages))) {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>English</option>
                                                                <option value="it" <?php if (in_array('it', explode(",", $result->languages))) {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>Italian</option>
                                                                <option value="es" <?php if (in_array('es', explode(",", $result->languages))) {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>Spanish</option>
                                                                <option value="de" <?php if (in_array('de', explode(",", $result->languages))) {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>German</option>
                                                                <option value="pt" <?php if (in_array('pt', explode(",", $result->languages))) {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>Portuguese</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="">Default language for payment page :</label>
                                                            <select id="default_language" class="form-control select2" name="default_language" style="width: 100%" data-placeholder="Choose a default language...">
                                                                <option value=""></option>
                                                                <option value="fr" <?php if ($result->default_language == 'fr') {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>French</option>
                                                                <option value="en" <?php if ($result->default_language == 'en') {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>English</option>
                                                                <option value="it" <?php if ($result->default_language == 'it') {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>Italian</option>
                                                                <option value="es" <?php if ($result->default_language == 'es') {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>Spanish</option>
                                                                <option value="de" <?php if ($result->default_language == 'de') {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>German</option>
                                                                <option value="pt" <?php if ($result->default_language == 'pt') {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>>Portuguese</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 pr-0 pl-0">
                                                        <div class="ribbon-wrapper bg-light col-md-10 float-right">
                                                            <div class="ribbon ribbon-default">GoPaid Account</div>
                                                            <p class="ribbon-content">
                                                                <?php if ($result->id_shop == NULL) {
                                                                    $ch = curl_init();
                                                                    curl_setopt($ch, CURLOPT_URL, 'https://gopaid.pro/API/companies');
                                                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                                        'Authorization: Basic YXBpX2tleTpjYTFmMjk1ZGM2NmE5NDY4MDllYTZhMzZhNzZjOTA1MA==',
                                                                        'access-code: get_all'
                                                                    ));
                                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                                                                    $companies0 = curl_exec($ch);
                                                                    curl_close($ch);
                                                                    $companies = json_decode($companies0, true);
                                                                ?>
                                                                    <div id="add_account_gopaid">
                                                                        <p>Ces informations vont être utiliser pour la création de compte :</p>
                                                                        <table class="table">
                                                                            <tr>
                                                                                <th>Account Name</th>
                                                                                <td class="account_name"><?php echo $result->business_name; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Shop Name</th>
                                                                                <td class="shop_name"><?php echo $result->name; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Email address</th>
                                                                                <td class="email_addr"><?php echo $result->emailc; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" class="text-right">
                                                                                    <p class="existant" data-toggle="modal" data-target="#existant">use existing company or </p>
                                                                                    <button type="button" class="btn waves-effect waves-light btn-success" id="add_company">Create new company</button>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        <div id="existant" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title" id="myLargeModalLabel">Add shop</h4>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <from class="form-horizontal form-material">
                                                                                            <div class="form-group">
                                                                                                <div class="col-md-12 m-b-20">
                                                                                                    <select class="form-control">
                                                                                                        <option>Company</option>
                                                                                                        <?php
                                                                                                        foreach ($companies['data'] as $key => $comp) {
                                                                                                            echo "<option value='" . $comp['id_company'] . "'>" . $comp['name'] . "</option>";
                                                                                                        }
                                                                                                        ?>
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-md-12 m-b-20 text-right"><button type="button" class="btn waves-effect waves-light btn-success" id="add_shop">Add shop</button></div>
                                                                                            </div>
                                                                                        </from>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <div id="account_gopaid">
                                                                        <p>Informations de compte :</p>
                                                                        <table class="table">
                                                                            <tr>
                                                                                <th>Account Name</th>
                                                                                <td><?php echo $result->business_name; ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Shop Name</th>
                                                                                <td><?php echo $result->name; ?> [ID: <?php echo $id_shop; ?>]</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td></td>
                                                                                <td class="text-right">
                                                                                    <button type="button" class="btn waves-effect waves-light btn-danger">Remove</button>
                                                                                </td>
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
                                <div class="tab-pane" id="landing" role="tabpanel">
                                    <div class="p-20">
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
                                                        <label for="webInput16">App Store <i class="fab fa-app-store text-info"></i></label>
                                                        <input type="text" name="appstore" class="form-control" id="webInput16" value="<?php echo $result->appstore; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="webInput17">Google Play <i class="fab fa-google-play text-info"></i></label>
                                                        <input type="text" name="googleplay" class="form-control" id="webInput17" value="<?php echo $result->googleplay; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div>
                                            <h3 class="box-title m-t-40">Displayed Sections</h3>
                                            <hr class="m-t-0 m-b-40">
                                            <div id="accordian-3">
                                                <div class="card">
                                                    <a class="card-header" id="heading8">
                                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                                            <h5 class="mb-0">Sign up form</h5>
                                                        </button>
                                                    </a>
                                                    <div id="collapse8" class="collapse" aria-labelledby="heading8" data-parent="#accordian-3" style="">
                                                        <div class="card-body">
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput21">Display : </label>
                                                                <input type="checkbox" name="section_form" id="webInput21" data-size="mini" <?php if ($result->section_form == 1) {
                                                                                                                                                echo "checked";
                                                                                                                                            } ?> />
                                                            </div>
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput21">Display : </label>
                                                                <input type="checkbox" name="section_features" id="webInput21" data-size="mini" <?php if ($result->section_features == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> />
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
                                                                <label for="webInput18" class="col-md-12 mb-0">block 5</label>
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput21">Display : </label>
                                                                <input type="checkbox" name="section_payments" id="webInput21" data-size="mini" <?php if ($result->section_payments == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> />
                                                            </div>
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput23">Display : </label>
                                                                <input type="checkbox" name="section_qualities" id="webInput23" data-size="mini" <?php if ($result->section_qualities == 1) {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?> />
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput26">Display : </label>
                                                                <input type="checkbox" name="section_video" id="webInput26" data-size="mini" <?php if ($result->section_video == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> />
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput21">Display : </label>
                                                                <input type="checkbox" name="section_process" id="webInput21" data-size="mini" <?php if ($result->section_process == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> />
                                                            </div>
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput244">Statistics</label>
                                                                <input type="checkbox" name="section_statistics" id="webInput244" data-size="mini" <?php if ($result->section_statistics == 1) {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?> />
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput27">Display : </label>
                                                                <input type="checkbox" name="section_pricing" id="webInput27" data-size="mini" <?php if ($result->section_pricing == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> />
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput29">Display : </label>
                                                                <input type="checkbox" name="section_mobileapp" id="webInput29" data-size="mini" <?php if ($result->section_mobileapp == 1) {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?> />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="webInput18">Title</label>
                                                                <input type="text" name="section_mobileapp_title" class="form-control" id="webInput18" value="<?php echo $result->section_mobileapp_title; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="webInput18">Description</label>
                                                                <textarea name="section_mobileapp_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_mobileapp_desc; ?></textarea>
                                                            </div>
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput25">Testimonials</label>
                                                                <input type="checkbox" name="section_testimonials" id="webInput25" data-size="mini" <?php if ($result->section_testimonials == 1) {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?> />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="webInput18">Title</label>
                                                                <input type="text" name="section_testimonials_title" class="form-control" id="webInput18" value="<?php echo $result->section_testimonials_title; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="webInput18">Description</label>
                                                                <textarea name="section_testimonials_desc" class="form-control" id="webInput18" rows="3"><?php echo $result->section_testimonials_desc; ?></textarea>
                                                            </div>
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput21">Display : </label>
                                                                <input type="checkbox" name="section_faqs" id="webInput21" data-size="mini" <?php if ($result->section_faqs == 1) {
                                                                                                                                                echo "checked";
                                                                                                                                            } ?> />
                                                            </div>
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
                                                            <div class="form-group bt-switch">
                                                                <label for="webInput21">Display : </label>
                                                                <input type="checkbox" name="section_support" id="webInput21" data-size="mini" <?php if ($result->section_support == 1) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="webInput18">Email for messages</label>
                                                                <input type="text" name="section_support_email" class="form-control" id="webInput18" value="<?php echo $result->section_support_email; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="">
                                            <h3 class="box-title m-t-40">Testimonials</h3>
                                            <hr class="m-t-0 m-b-40">

                                            <div class="form-group">

                                                <div id="testimonial-detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myLargeModalLabel">View testimonial #<span></span></h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">

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
                                                                            <button class='view-button' type='button' title='View'><i class='fa fa-eye'></i></button>
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
                                <div class="tab-pane" id="pricing" role="tabpanel">
                                    <div class="p-20">
                                        <div class="row m-t-20">
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="row pricing-plan">
                                                    <?php
                                                    if ($pricings_rows > 0) {
                                                        foreach ($pricings as $pri) {
                                                            if ($pri['status'] == 1) {
                                                                echo '<div class="col-md-3 col-xs-12 col-sm-6 no-padding"><div class="pricing-box featured-plan"><div class="pricing-body b-l"><div class="pricing-header"><h4 class="price-lable text-white bg-warning"> Popular</h4><h4 class="text-center">' . $pri["title"] . '</h4><h2 class="text-center"><span class="price-sign">' . Currency($pri["currency"]) . '</span>' . floatval($pri["price"]) . '</h2><p class="uppercase">per messages</p></div><div class="price-table-content"><div class="price-row"><i class="icon-bubble"></i> ' . $pri["messages"] . ' messages</div><div class="price-row"><div id="view-' . $pri["id_pricing"] . '" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog text-left"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">View pricing #' . $pri["id_pricing"] . '</h4><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div><div class="modal-body"><from class="form-horizontal form-material"><div class="form-group"><div class="col-md-12 m-b-20"><input type="text" name="title" class="form-control" value="' . $pri["title"] . '"></div><div class="col-md-12 m-b-20"><input type="text" name="price" class="form-control" value="' . $pri["price"] . '"></div><div class="col-md-12 m-b-20"><select name="currency" class="form-control" placeholder="Currency"><option value="">Currency</option><option value="EUR"';
                                                                if ($pri["currency"] == "EUR") {
                                                                    echo "selected";
                                                                }
                                                                echo '>EUR</option><option value="CHF" ';
                                                                if ($pri["currency"] == "CHF") {
                                                                    echo "selected";
                                                                }
                                                                echo '>CHF</option><option value="GBP" ';
                                                                if ($pri["currency"] == "GBP") {
                                                                    echo "selected";
                                                                }
                                                                echo '>GBP</option><option value="SEK" ';
                                                                if ($pri["currency"] == "SEK") {
                                                                    echo "selected";
                                                                }
                                                                echo '>SEK</option><option value="DKK" ';
                                                                if ($pri["currency"] == "DKK") {
                                                                    echo "selected";
                                                                }
                                                                echo '>DKK</option><option value="CAD" ';
                                                                if ($pri["currency"] == "CAD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>CAD</option><option value="USD" ';
                                                                if ($pri["currency"] == "USD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>USD</option><option value="AUD" ';
                                                                if ($pri["currency"] == "AUD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>AUD</option><option value="NZD" ';
                                                                if ($pri["currency"] == "NZD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>NZD</option></select></div><div class="col-md-12 m-b-20"><input type="number" name="msg_nb" class="form-control" value="' . $pri["messages"] . '" min="0" max="100" step="1"></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="status" value="1" class="custom-control-input" id="customCheck1" ';
                                                                if ($pri["status"] == 1) {
                                                                    echo "checked";
                                                                }
                                                                echo '><label class="custom-control-label" for="customCheck1">Status POPULAR </label></div></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="active" value="1" class="custom-control-input" id="customCheck2" ';
                                                                if ($pri["active"] == 1) {
                                                                    echo "checked";
                                                                }
                                                                echo '><label class="custom-control-label" for="customCheck2">Active </label></div></div></div></from></div></div></div></div><button class="btn btn-success waves-effect waves-light"  data-toggle="modal" data-target="#view-' . $pri["id_pricing"] . '">View</button></div></div></div></div></div>';
                                                            } else {
                                                                echo '<div class="col-md-3 col-xs-12 col-sm-6 no-padding"><div class="pricing-box"><div class="pricing-body b-l"><div class="pricing-header"><h4 class="text-center">' . $pri["title"] . '</h4><h2 class="text-center"><span class="price-sign">' . Currency($pri["currency"]) . '</span>' . floatval($pri["price"]) . '</h2><p class="uppercase">per messages</p></div><div class="price-table-content"><div class="price-row"><i class="icon-bubble"></i> ' . $pri["messages"] . ' messages</div><div class="price-row"><div id="view-' . $pri["id_pricing"] . '" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog text-left"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">View pricing #' . $pri["id_pricing"] . '</h4><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div><div class="modal-body"><from class="form-horizontal form-material"><div class="form-group"><div class="col-md-12 m-b-20"><input type="text" name="title" class="form-control" value="' . $pri["title"] . '"></div><div class="col-md-12 m-b-20"><input type="text" name="price" class="form-control" value="' . $pri["price"] . '"></div><div class="col-md-12 m-b-20"><select name="currency" class="form-control" placeholder="Currency"><option value="">Currency</option><option value="EUR"';
                                                                if ($pri["currency"] == "EUR") {
                                                                    echo "selected";
                                                                }
                                                                echo '>EUR</option><option value="CHF" ';
                                                                if ($pri["currency"] == "CHF") {
                                                                    echo "selected";
                                                                }
                                                                echo '>CHF</option><option value="GBP" ';
                                                                if ($pri["currency"] == "GBP") {
                                                                    echo "selected";
                                                                }
                                                                echo '>GBP</option><option value="SEK" ';
                                                                if ($pri["currency"] == "SEK") {
                                                                    echo "selected";
                                                                }
                                                                echo '>SEK</option><option value="DKK" ';
                                                                if ($pri["currency"] == "DKK") {
                                                                    echo "selected";
                                                                }
                                                                echo '>DKK</option><option value="CAD" ';
                                                                if ($pri["currency"] == "CAD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>CAD</option><option value="USD" ';
                                                                if ($pri["currency"] == "USD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>USD</option><option value="AUD" ';
                                                                if ($pri["currency"] == "AUD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>AUD</option><option value="NZD" ';
                                                                if ($pri["currency"] == "NZD") {
                                                                    echo "selected";
                                                                }
                                                                echo '>NZD</option></select></div><div class="col-md-12 m-b-20"><input type="number" name="msg_nb" class="form-control" value="' . $pri["messages"] . '" min="0" max="100" step="1"></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="status" value="1" class="custom-control-input" id="customCheck1" ';
                                                                if ($pri["status"] == 1) {
                                                                    echo "checked";
                                                                }
                                                                echo '><label class="custom-control-label" for="customCheck1">Status POPULAR </label></div></div><div class="col-md-12 m-b-20"><div class="custom-control custom-checkbox"><input type="checkbox" name="active" value="1" class="custom-control-input" id="customCheck2" ';
                                                                if ($pri["active"] == 1) {
                                                                    echo "checked";
                                                                }
                                                                echo '><label class="custom-control-label" for="customCheck2">Active </label></div></div></div></from></div></div></div></div><button class="btn btn-success waves-effect waves-light"  data-toggle="modal" data-target="#view-' . $pri["id_pricing"] . '">View</button></div></div></div></div></div>';
                                                            }
                                                        }
                                                    } else {
                                                        echo '<div class="col-md-12 text-center m-t-40 m-b-40"><i class="icon-calculator" style="font-size: 120px;color: #dea5aa;margin: 20px 0;display: block;"></i><h3 style="color: #797979;">No pricing yet!</h3></div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane " id="options" role="tabpanel">
                                    <div class="card card-body">
                                        <h3 class="box-title m-b-0">Options</h3>
                                        <p class="text-muted m-b-30 font-13"> Edit the Options</p>
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <?php
                                                if (isset($_POST['update3'])) {
                                                    $rights = array();
                                                    $rights[0] = isset($_POST[0]) ? 1 : 0;
                                                    if ($rights[0] == 0) {
                                                        $stmt2 = $conn->prepare("UPDATE `websites` SET `rigths`=:st WHERE `id_website`=:ID");
                                                        $stmt2->bindParam(':st', json_encode($rights), PDO::PARAM_STR);
                                                        $stmt2->bindParam(':ID', intval($_GET['id']), PDO::PARAM_INT);
                                                    } else {
                                                        $stmt2 = $conn->prepare("SELECT storage FROM `websites` WHERE `id_website`=:ID");
                                                        $stmt2->bindParam(':ID', intval($_GET['id']), PDO::PARAM_INT);
                                                        $stmt2->execute();
                                                        $account = $stmt2->fetchObject();
                                                        $storage = intval($_POST['storage']) + intval($account->storage);
                                                        $stmt2 = $conn->prepare("UPDATE `websites` SET `storage`=:st,`rights`=:rt WHERE `id_website`=:ID");
                                                        $stmt2->bindParam(':rt', json_encode($rights), PDO::PARAM_STR);
                                                        $stmt2->bindParam(':st', $storage);
                                                        $stmt2->bindParam(':ID', intval($_GET['id']), PDO::PARAM_INT);
                                                    }

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
                                                    <div class="table-responsive m-b-40 m-r-0">
                                                        <table class="table display " id="packages-dtable" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <td>Upload files</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="field1" name="0" class="custom-control-input" <?= $readRights[0] == 1 ? 'checked' : '' ?> onclick="show(this)"><label class="custom-control-label" for="field1"></label></div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="form-group storage">
                                                        <label for="storage">ADD Storage(MB)</label>
                                                        <select name="storage" class="form-control" id="storage" required>
                                                            <option value="20">20</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100 </option>
                                                            <option value="200">200</option>
                                                            <option value="400">400</option>
                                                            <option value="500">500</option>
                                                            <option value="1000">1000</option>
                                                            <option value="2000">2000</option>
                                                            <option value="5000">5000</option>
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <hr>
                                                    <button type="submit" name="update3" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
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
    © 2019 Private chat by Diamond services
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
<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/node_modules/popper/popper.min.js"></script>
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<script src="../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!--Custom JavaScript -->
<script src="../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<script src="../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../assets/node_modules/bootstrap-switch/bootstrap-switch.min.js"></script>
<script src="../assets/node_modules/dropify/dropify.min.js"></script>
<script src="../assets/node_modules/icheck/icheck.min.js"></script>
<script src="../assets/node_modules/icheck/icheck.init.js"></script>
<script src="../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup.min.js"></script>
<script src="../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup-init.js"></script>
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

    function show(input) {
        if (input.checked == true) {
            $('.storage').show();

        } else {
            $('.storage').hide();
        }
    }
    $(document).ready(function() {
        $('.storage').hide();
        radioswitch.init();
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

    $("#myTable .view-button").click(function() {
        var id = $(this).parent().attr('data-id');
        $('#testimonial-detail .modal-body').load('getTestimonial.php?id_testimonial=' + id, function() {
            $("#testimonial-detail .modal-title span").text(id);
            $('#testimonial-detail').modal({
                show: true
            });
        });
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

    $("#add_account_gopaid #add_company").click(function() {
        account_name = $("#add_account_gopaid table .account_name").text();
        shop_name = $("#add_account_gopaid table .shop_name").text();
        email_addr = $("#add_account_gopaid table .email_addr").text();
        if (account_name == "") {
            alert("Account name is empty!");
        } else if (shop_name = "") {
            alert("Shop name is empty!");
        } else {
            $.ajax({
                url: 'functions_ajax.php',
                dataType: 'json',
                data: {
                    type: "add_gopaid",
                    account_name: account_name,
                    shop_name: shop_name,
                    email_addr: email_addr,
                    return_url: $("#general input[name=return_url]").val(),
                    email_recu: $("#gopaid input[name=payment_receipt]").val(),
                    email_notif: $("#gopaid input[name=payment_notification]").val(),
                    languages: $("#gopaid select[name=languages]").val(),
                    default_language: $("#gopaid select[name=default_language]").val(),
                    id_website: "<?php echo $id_website; ?>"
                },
                success: function(code_html, statut) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Unsuccessful request");
                }
            });
        }
    });

    $("#add_account_gopaid #add_shop").click(function() {
        account_name = $("#add_account_gopaid table .account_name").text();
        shop_name = $("#add_account_gopaid table .shop_name").text();
        email_addr = $("#add_account_gopaid table .email_addr").text();
        if (account_name == "") {
            alert("Account name is empty!");
        } else if (shop_name = "") {
            alert("Shop name is empty!");
        } else {
            $.ajax({
                url: 'functions_ajax.php',
                dataType: 'json',
                data: {
                    type: "add_gopaid",
                    account_name: account_name,
                    shop_name: shop_name,
                    email_addr: email_addr,
                    return_url: $("#general input[name=return_url]").val(),
                    email_recu: $("#gopaid input[name=payment_receipt]").val(),
                    email_notif: $("#gopaid input[name=payment_notification]").val(),
                    languages: $("#gopaid select[name=languages]").val(),
                    default_language: $("#gopaid select[name=default_language]").val(),
                    id_website: "<?php echo $id_website; ?>"
                },
                success: function(code_html, statut) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Unsuccessful request");
                }
            });
        }
    });

    $("#approve").click(function() {
        $.ajax({
            url: 'functions_ajax.php',
            dataType: 'json',
            data: {
                type: "approve2",
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
</script>
</body>

</html>