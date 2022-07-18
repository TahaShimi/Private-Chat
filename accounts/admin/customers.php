<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$page_name = "customersP";
ob_start();
include('header.php');
$stmt = $conn->prepare("SELECT u.id_user ,p.company_name FROM publisher_advertiser pa,users u,publishers p WHERE pa.id_advertiser=:id AND  p.id_publisher=u.id_profile AND u.profile = 5 AND pa.date_end IS NULL");
$stmt->bindParam(':id', intval($_SESSION['id_company']));
$stmt->execute();
$publishers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$s1 = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title,(SELECT count(*) FROM offers o where o.id_package=p.id_package and end_date >= CURDATE()) as offers_count FROM `packages` p JOIN packages_price pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang WHERE p.id_account = :ID  and  p.status=1");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
$s1->execute();
$packages = $s1->fetchAll(PDO::FETCH_ASSOC);
?>
<link href="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="../../assets/css/pages/conversations-app-page.css" rel="stylesheet">
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">
<style>
    #searchForm {
        margin: 20px;
    }

    .iti {
        width: 100% !important;
    }

    .col2 {
        padding-right: 20px;
    }

    #formHeader {
        font-size: 1.7em;
        padding: 3% 0;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .first {
        border-right: 1px solid #c7c5c5
    }

    .custom-radio {
        display: inline-block;
    }

    label {
        margin-top: 5px;
    }

    .select2 {
        width: 100% !important;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" id="headingOne">
                <button class="btn btn-outline-primary" data-toggle="collapse" data-target="#searchForm" aria-expanded="true" aria-controls="searchForm" id="showForm">
                    <i class="" id="icontoggle"></i>
                    <span id="FormText"><?php echo ($trans["hide_form"]) ?></span>
                </button>
            </div>
            <div class="card-body p-0">
                <form action="" method="post" id="searchForm" class="collapse">
                    <div id="accordian-3">
                        <div class="row">
                            <div class="col-6 first">
                                <h5 class="card-title"><?php echo ($trans["admin"]["General_info"]) ?></h5>
                                <hr>
                                <div class="card-body p-0">
                                    <div class="form-group">
                                        <div>
                                            <label class="control-label"><?php echo ($trans["gender"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="customRadio11" name="gender" value="1" data-text="Male" class="custom-control-input">
                                            <label class="custom-control-label" for="customRadio11"><?php echo ($trans["male"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="customRadio22" name="gender" value="2" data-text="Female" class="custom-control-input">
                                            <label class="custom-control-label" for="customRadio22"><?php echo ($trans["female"]) ?></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="firstName"><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></label>
                                        <input class="form-control" type="text" placeholder="<?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?> " name="firstName" />
                                    </div>
                                    <div class="form-group">
                                        <label for="lastName"><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></label>
                                        <input class="form-control" type="text" placeholder="<?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?>" name="lastName" />
                                    </div>

                                    <div class="form-group">
                                        <label for="email"><?php echo ($trans["admin"]["customers"]["customers_table"]["email"]) ?></label>
                                        <input class="form-control" type="email" placeholder="<?php echo ($trans["admin"]["customers"]["customers_table"]["email"]) ?>" name="email" />
                                    </div>
                                    <div class="form-group">
                                        <label for="phone" class="control-label text-right"><?php echo ($trans["phone"]) ?></label>
                                        <div>
                                            <input name="phone" type="tel" id="phone" class="form-control" style="width:100%" placeholder="Enter Phone Number">
                                            <span id="valid-msg1" class="hide text-success">� Valid</span>
                                            <span id="error-msg1" class="hide text-danger">✗ Invalid number</span>
                                        </div>
                                    </div>

                                    <div class="row ">
                                        <div class="col-sm-12 form-group">
                                            <label for="SelectBox"><?php echo ($trans["admin"]["customers"]["customers_table"]["country"]) ?></label>
                                            <select class="SelectBox" style="width: 100%" multiple="multiple" name="SelectBox[]">
                                                <?php
                                                $st = $conn->prepare("SELECT DISTINCT country FROM `customers` ");
                                                $st->execute();
                                                $countries = $st->fetchAll();
                                                foreach ($countries as $key => $country) {
                                                    echo "<option  value='" . $country['country'] . "'>" . $trans['countries'][$country['country']] . "</option>";
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 form-group m-b-0">
                                            <label class="control-label"><?php echo ($trans["admin"]["customers"]["customers_table"]["created_at"]) ?></label>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="from"><?php echo ($trans["from"]) ?></label>
                                            <input class="form-control" type="date" name="from" />
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="to"><?php echo ($trans["to"]) ?></label>
                                            <input class="form-control" type="date" name="to" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <label class="control-label"><?php echo ($trans["add_way"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio m-r-10">
                                            <input type="radio" id="addWayRadio1" name="addWay" value="Unit" class="way custom-control-input">
                                            <label class="custom-control-label" for="addWayRadio1"><?php echo ($trans["Add_unit"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio m-r-10">
                                            <input type="radio" id="addWayRadio2" name="addWay" value="Imported" class="way custom-control-input">
                                            <label class="custom-control-label" for="addWayRadio2"><?php echo ($trans["Import"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio m-r-10">
                                            <input type="radio" id="addWayRadio3" name="addWay" value="affiliates" class="way custom-control-input">
                                            <label class="custom-control-label" for="addWayRadio3"><?php echo ($trans["Affiliates"]) ?></label>
                                        </div>
                                        <div class="custom-control custom-radio m-r-10">
                                            <input type="radio" id="addWayRadio4" name="addWay" value="link" class="way custom-control-input">
                                            <label class="custom-control-label" for="addWayRadio4"><?php echo ($trans["shared_links"]) ?></label>
                                        </div>
                                    </div>
                                    <div class="form-group" id="publishers">
                                        <label for="Publisher"><?php echo ($trans["publisher"]["publisher"]) ?></label>
                                        <select name="Publisher" class="form-control">
                                            <option></option>
                                            <?php foreach ($publishers as $publisher) {
                                                echo '<option value="' . $publisher['id_user'] . '">' . $publisher['company_name'] . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="importation">
                                        <label for="Publisher">Importation ID</label>
                                        <input type="text" name="importation" id="importation" class="form-control" placeholder="Importation ID" />
                                    </div>
                                    <div class="form-group" id="sharedLink">
                                        <label for="link">Link ID</label>
                                        <input type="text" name="link" id="link" class="form-control" placeholder="Link ID" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5 class="card-title"><?php echo ($trans["admin"]["Sales_info"]) ?></h5>
                                <hr>
                                <div class="card-body p-0">
                                    <div class="row form-group">
                                        <div class="col-sm-12">
                                            <label><?php echo ($trans["packages"]) ?></label>
                                            <select class="SelectPackages" multiple="multiple" name="SelectPackages[]">
                                                <?php
                                                $stmnt = $conn->prepare("SELECT DISTINCT pack.id_package,pack.title FROM `packages` as pack ,`transactionsc` as tr where pack.id_package=tr.id_package");
                                                $stmnt->execute();
                                                $packages1 = $stmnt->fetchAll();
                                                foreach ($packages1 as $key => $package) {
                                                    echo "<option  value='" . $package['id_package'] . "'>" . $package['title'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-12">
                                            <label>Offres</label>
                                            <select class="SelectOffre" multiple="multiple" name="SelectOffre[]">
                                                <?php

                                                $stmnt = $conn->prepare("SELECT DISTINCT of.id_offer,of.title FROM `offers` as of,`offers_customers` as oc where of.id_offer=oc.id_offer");
                                                $stmnt->execute();
                                                $offers1 = $stmnt->fetchAll();
                                                foreach ($offers1 as $key => $offer1) {
                                                    echo "<option  value='" . $offer1['id_offer'] . "'>" . $offer1['title'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="buyCount"><?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?></label>
                                        <div class="row">
                                            <div class="col-4">
                                                <select name="operator" class="form-control buysOp">
                                                    <option value=">"><?php echo ($trans["higher"]) ?> (>)</option>
                                                    <option value=">="><?php echo ($trans["higher_equal"]) ?> (>=)</option>
                                                    <option value="<"><?php echo ($trans["lower"]) ?> (<)</option> <option value="<="><?php echo ($trans["lower_equal"]) ?> (<=)</option> <option value="="><?php echo ($trans["equal"]) ?> (=)</option>
                                                    <option value="<>"><?php echo ($trans["different"]) ?> (!=)</option>
                                                    <option value="Between"><?php echo ($trans["between"]) ?></option>
                                                </select>
                                            </div>
                                            <div class="col-8 oth">
                                                <input class="form-control" type="number" placeholder="<?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?>" name="buyCount" />
                                            </div>
                                            <div class="col-4 btw" style="display: none">
                                                <input class="form-control" type="number" placeholder="<?php echo ($trans["from"]) ?>" name="minbuyCount" />
                                            </div>
                                            <div class="col-4 btw" style="display: none">
                                                <input class="form-control" type="number" placeholder="<?php echo ($trans["to"]) ?>" name="maxbuyCount" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 form-group m-b-0">
                                            <label><?php echo ($trans["transactions"]) ?></label>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="After"><?php echo ($trans["from"]) ?></label>
                                            <input class="form-control" type="date" name="After" />
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="Before"><?php echo ($trans["to"]) ?></label>
                                            <input class="form-control" type="date" name="Before" />
                                        </div>
                                    </div>
                                    <label for="buyCount"><?php echo ($trans["admin"]["customers"]["customers_table"]["balance"]) ?></label>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <select name="balanceOp" class="form-control balance">
                                                <option value=">"><?php echo ($trans["higher"]) ?> (>)</option>
                                                <option value=">="><?php echo ($trans["higher_equal"]) ?> (>=)</option>
                                                <option value="<"><?php echo ($trans["lower"]) ?> (<)</option> <option value="<="><?php echo ($trans["lower_equal"]) ?> (<=)</option> <option value="="><?php echo ($trans["equal"]) ?> (=)</option>
                                                <option value="<>"><?php echo ($trans["different"]) ?> (!=)</option>
                                                <option value="Between"><?php echo ($trans["between"]) ?></option>
                                            </select>
                                        </div>
                                        <div class="col-8 oth1">
                                            <input class="form-control" type="number" placeholder="<?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?>" name="balance" />
                                        </div>
                                        <div class="col-4 btw1" style="display: none">
                                            <input class="form-control" type="number" placeholder="<?php echo ($trans["from"]) ?>" name="minbalance" />
                                        </div>
                                        <div class="col-4 btw1" style="display: none">
                                            <input class="form-control" type="number" placeholder="<?php echo ($trans["to"]) ?>" name="maxbalance" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-5">
                    <button type="button" name="submit" class="btn  btn-primary submit"><i class="fa fa-search"></i> search </button>
                </form>
            </div>
        </div>
        <div class="card " id="table">
            <div class="card-body">
                <h4 class="card-title"><?php echo ($trans["admin"]["customers"]["subtitle"]) ?></h4>
                <div class="m-b-30" style="position: absolute; top: 10px; right: 10px;">
                    <button type="button" class="btn btn-info addPackages disabled" id="addPackages" data-toggle="modal" data-target="#packagesModal" data-whatever="@fat">Add Packages</button>
                    <button type="button" class="btn btn-info addOffers disabled" id="addOffers" data-toggle="modal" data-target="#offersModal" data-whatever="@fat"><?php echo ($trans["admin"]["customers"]["add_offers"]) ?></button>
                </div>
                <?php
                $s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.* FROM `offers` o join `packages` p on p.id_package=o.id_package WHERE o.id_account = :ID");
                $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
                $s1->execute();
                $offers = $s1->fetchAll();
                if (isset($_POST['add-offers'])) {
                    if (isset($_POST["offers_ids"]) && isset($_POST["customers_ids"])) {
                        $offers_ids = $_POST["offers_ids"];
                        $customers_ids = $_POST["customers_ids"];
                        foreach ($offers_ids as $key_of => $value_of) {
                            foreach ($customers_ids as $key_cu => $value_cu) {
                                $stmt1 = $conn->prepare("INSERT INTO `offers_customers`(`id_customer`, `id_offer`, `created_at`) VALUES (:ic,:iof,NOW())");
                                $stmt1->bindParam(':ic', $value_cu, PDO::PARAM_STR);
                                $stmt1->bindParam(':iof', $value_of, PDO::PARAM_STR);
                                if ($stmt1->execute()) {
                                    foreach ($offers as $offer) {
                                        if ($offer["id_offer"] == $value_of) {
                                            if ($offer["discount"] == 100) {
                                                $stmt1 = $conn->prepare("UPDATE customers set balance=balance+:ba where id_customer=:ic ");
                                                $stmt1->bindParam(':ic', $value_cu, PDO::PARAM_INT);
                                                $stmt1->bindParam(':ba', $offer["messages"], PDO::PARAM_INT);
                                                $stmt1->execute();
                                                $description = "Free offer attached to customer";
                                            }
                                            $stmt = $conn->prepare("SELECT id_user from users where id_profile=:ip and profile=4");
                                            $stmt->bindParam(':ip', $value_cu, PDO::PARAM_INT);
                                            $stmt->execute();
                                            $customer = $stmt->fetchObject();
                                            $id_user = intval($customer->id_user);
                                            $description = "New offer attached to customer";
                                            $stmt2 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) 
                                                                                         VALUES(:iu,:ds,:mt,3,NOW())");
                                            $stmt2->bindParam(':iu', $id_user, PDO::PARAM_INT);
                                            $stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
                                            $stmt2->bindParam(':mt', $offer["id_offer"], PDO::PARAM_INT);
                                            $stmt2->execute();
                                        }
                                    }
                                } else {
                                    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" .  ($trans["feedback_msg"]["offer_not_addedd"]) . " </div></div>";
                                    unset($_POST);
                                }
                            }
                        }
                        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" .  ($trans["feedback_msg"]["offer_addedd"]) . " </div></div>";
                    } else echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" .  ($trans["feedback_msg"]["offer_not_addedd"]) . " </div></div>";
                }
                if (isset($_POST['add-packages'])) {
                    if (isset($_POST["packages_ids"]) && isset($_POST["customers_ids"])) {
                        $packages_ids = $_POST["packages_ids"];
                        $customers_ids = $_POST["customers_ids"];
                        foreach ($packages_ids as $key_of => $value_of) {
                            foreach ($customers_ids as $key_cu => $value_cu) {
                                $stmt1 = $conn->prepare("INSERT INTO `customers_packages`(`id_customer`, `id_package`, `date_add`) VALUES (:ic,:iof,NOW())");
                                $stmt1->bindParam(':ic', $value_cu, PDO::PARAM_STR);
                                $stmt1->bindParam(':iof', $value_of, PDO::PARAM_STR);
                                $stmt1->execute();
                                if($stmt1->rowCount()==0){
                                    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Package not added</div></div>";
                                    unset($_POST);
                                }
                            }
                        }
                        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Package Added successfully </div></div>";
                    } else echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Packages not added</div></div>";
                }
                ?>
                <div>
                    <div class="table-responsive m-b-40 m-r-0">
                        <table class="display  nowrap table table-hover table-striped dt-responsive" id="customers-dtable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox checkbox-info form-check">
                                            <input class="custom-control-input" name="select_all" value="" id="select-all-customers" type="checkbox" />
                                            <label class="custom-control-label" for="select-all-customers"></label>
                                        </div>
                                    </th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["email"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["country"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["phone"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["created_at"]) ?></th>
                                    <th><?php echo ($trans["admin"]["customers"]["customers_table"]["actions"]) ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="modal" id="offersModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1"><?php echo ($trans["admin"]["customers"]["add_offers_modal"]["title"]) ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" id="myForm" method="POST" class="col-md-12 m-t-20" novalidate>
                    <p class="text-muted font-13"><?php echo ($trans["admin"]["offers"]["subtitle"]) ?></p>
                    <div class="row">
                        <div class="col-12 m-b-30">
                            <div class="form-group ">
                                <h5 class=""><?php echo ($trans["admin"]["customers"]["add_offers_modal"]["choose_offers"]) ?> :</h5>
                                <div class='input-group mb-3'>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="mdi mdi-package-variant-closed"></span>
                                        </span>
                                    </div>
                                    <select name="offers_ids[]" id="offers" class="select2 select2-multiple" style="width: 80%" multiple="multiple" data-placeholder="Choose">
                                        <?php
                                        foreach ($offers as $offer) {
                                            echo '<option value="' . $offer["id_offer"] . '">' . $offer["offer_title"] . ' on ' . $offer["title"] . ' ( ' . $offer["messages"] . ' messages at ' . (((100 - $offer["discount"]) * ($offer["price"] / 100))) . ' ' . $offer["currency"] . ' )' . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="submit" name="add-offers" class="btn btn-primary"><?php echo ($trans["admin"]["offers"]["add_offer"]["p_title"]) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="MessageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1"><?= $trans['sendMSG'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div>
                    <h4><?= $trans['Choose_conversation'] ?></h4>
                    <select id="conversation" class="form-control" required>

                    </select>
                </div>
                <input id="user" value="" hidden>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-4" style="border-right: 1px solid #ddd">
                        <div>
                            <label class="control-label"><?= $trans['Sendas'] ?> :</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="custom1" value="2" name="role" class="custom-control-input" checked>
                            <label class="custom-control-label" for="custom1">Admin</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="custom2" value="3" name="role" class="custom-control-input">
                            <label class="custom-control-label" for="custom2"><?= $trans['consultant'] ?></label>
                        </div>
                    </div>
                    <div class="col-6">
                        <textarea placeholder="<?php echo ($trans["chat"]["type_your_message"]) ?>" class="form-control border-0" id="content"></textarea>
                    </div>
                    <div class="col-2 text-right">
                        <button type="button" id="send_message" class="btn btn-primary  btn-sm m-t-10"><i class="fas fa-paper-plane"></i> <?php echo ($trans["chat"]["send"]) ?> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="addOff" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#send_offers" role="tab"><span class="hidden-xs-down"><?= $trans['Sendoffers'] ?></span></a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#create_offer" role="tab"><span class="hidden-xs-down"><?= $trans['Create_dedicated_offer'] ?></span></a> </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane p-20 active" id="send_offers" role="tabpanel">
                    <div class="modal-body">
                        <div class="form-group">
                            <h4><?= $trans['ChooseExpert'] ?></h4>
                            <select class="form-control experts1" required>

                            </select>
                        </div>
                        <div class="form-group">
                            <h4><?= $trans['Choosepackage'] ?></h4>
                            <select class="form-control packages select2" required multiple style="width: 100%">

                            </select>
                        </div>
                        <input id="customer1" value="" hidden>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="button" class="btn btn-primary SendOff"><?php echo ($trans["chat"]["send"]) ?></button>
                    </div>
                </div>
                <div class="tab-pane p-20 " id="create_offer" role="tabpanel">
                    <form action="" id="Form" method="POST" class="col-md-12" novalidate>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="title"><?php echo ($trans["admin"]["offers"]["add_offer"]["title_en"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-message-draw"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="title" class="form-control" id="title" required placeholder="<?php echo ($trans["admin"]["offers"]["add_offer"]["title_placeholder_en"]) ?>" required data-validation-required-message="Offer title is required">
                                            <label id="title-error" class="error col-12" for="title"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="title"><?php echo ($trans["admin"]["offers"]["add_offer"]["title_fr"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-message-draw"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="title_fr" class="form-control" id="title_fr" required placeholder="<?php echo ($trans["admin"]["offers"]["add_offer"]["title_placeholder_fr"]) ?>" required data-validation-required-message="Offer title is required">
                                            <label id="title-error" class="error col-12" for="title_fr"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 ">
                                    <div class="form-group ">
                                        <label for="discount"><?php echo ($trans["admin"]["offers"]["add_offer"]["discount"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3 '>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-percent"></span>
                                                </span>
                                            </div>
                                            <input type="number" min="0" max="100" name="discount" class="form-control" id="discount" required placeholder="<?php echo ($trans["admin"]["offers"]["add_offer"]["discount_placeholder"]) ?>" required data-validation-required-message="Offer discount is required">
                                            <label id="discount-error" class="error col-12" for="discount"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="limit"><?php echo ($trans["admin"]["offers"]["add_offer"]["limit"]) ?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-unfold-less"></span>
                                                </span>
                                            </div>
                                            <input type="number" name="limit" min="0" class="form-control" id="limit" required value="1" disabled>
                                            <label id="limit-error" class="error col-12" for="limit"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo ($trans["admin"]["offers"]["add_offer"]["date"]) ?></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio1" name="dateType" value="1" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="customRadio1"><?php echo ($trans["admin"]["offers"]["add_offer"]["periodic"]) ?></label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio2" name="dateType" value="2" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadio2"><?php echo ($trans["admin"]["offers"]["add_offer"]["always"]) ?></label>
                                </div>
                            </div>
                            <div class="row dateRangePickerContainer">
                                <div class="col-8">
                                    <label for="dateRange"><?php echo ($trans["admin"]["offers"]["add_offer"]["check_offer"]) ?> <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="ti-calendar"></span>
                                            </span>
                                        </div>
                                        <input type='text' id="dateRange" name="dateRange" class="form-control buttonClass" required />
                                        <label id="dateRange-error" class="error col-12" for="dateRange"></label>
                                    </div>
                                </div>
                                <div class="col-4 p-t-30">
                                    <button class="btn btn-secondary waves-effect waves-light" id="check-offer-effect" type="button">
                                        <span class="btn-label" id="check-span"><i class="fas fa-check"></i></span>
                                        <?php echo ($trans["admin"]["offers"]["add_offer"]["check_offer"]) ?></button>
                                </div>
                            </div>
                            <div class="row packagesTableContainer">
                                <div class="col-12">
                                    <div class="table-responsive m-b-40 m-r-0">
                                        <table class="table display dt-responsive" id="packages-dtable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="custom-control custom-checkbox checkbox-info form-check">
                                                            <input class="custom-control-input" name="select_all" value="" id="select-all-packages" type="checkbox" />
                                                            <label class="custom-control-label" for="select-all-packages"></label>
                                                        </div>
                                                    </th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["title"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["messages"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["initial_price"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["offers"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["final_price"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["start_at"]) ?></th>
                                                    <th><?php echo ($trans["admin"]["offers"]["add_offer"]["packages_table"]["end_at"]) ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($packages as $package) { ?>
                                                    <tr>
                                                        <td>
                                                            <div class="custom-control custom-checkbox checkbox-info form-check">
                                                                <input class="custom-control-input check-package packages-table" id="pkg-<?= $package['id_package'] ?>" type="checkbox" name="packages_ids[]" value="<?= $package['id_package'] ?>">
                                                                <label class="custom-control-label" for="pkg-<?= $package['id_package'] ?>"><?= $package['id_package'] ?></label>
                                                            </div>
                                                        </td>
                                                        <td><?= $package['title'] ?></td>
                                                        <td><?= $package['messages'] ?> <sup> Messages</sup></td>
                                                        <td><?= $package['price'] ?><sup><?= $package['currency'] ?></sup></td>
                                                        <td><?php echo $package['offers_count'] ?></td>
                                                        <td id="package-<?= $package['id_package'] ?>-price-effect">--</sup></td>
                                                        <td><?= $package['start_date'] ?></td>
                                                        <td><?= $package['end_date'] ?></td>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table><label for="packages_ids[]" d="packages_ids[]-error" class="error"></label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="button" name="add-package" class="btn btn-primary addOffer"> <?php echo ($trans["save"]) ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="packagesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Add Packages</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" id="PKGForm" method="POST" class="col-md-12 m-t-20" novalidate>
                    <div class="row">
                        <div class="col-12 m-b-30">
                            <div class="form-group ">
                                <h5 class=""><?php echo ($trans["packages"]) ?> :</h5>
                                <div class='input-group mb-3'>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="mdi mdi-package-variant-closed"></span>
                                        </span>
                                    </div>
                                    <select name="packages_ids[]" id="packages" class="select2 select2-multiple" style="width: 80%" multiple="multiple" data-placeholder="Choose">
                                        <?php
                                        $s1 = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title,(SELECT count(*) FROM offers o where o.id_package=p.id_package and end_date >= CURDATE()) as offers_count FROM `packages` p JOIN packages_price pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang WHERE pp.primary=1 AND p.id_account = :ID  and  p.public=0");
                                        $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                        $s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
                                        $s1->execute();
                                        $packages1 = $s1->fetchAll();
                                        foreach ($packages1 as $package) {
                                            echo '<option value="' . $package["id_package"] . '">' . $package["title"] . ' ( ' . $package["messages"] . ' messages at ' . ($package["price"]) . ' ' . $package["currency"] . ' )' . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="submit" name="add-packages" class="btn btn-primary">Add Packages</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/js/env.js"></script>
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
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<!-- This is data table -->
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="../../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script type="text/javascript" src="../../assets/node_modules/multiselect/js/jquery.multi-select.js"></script>
<script src="../../assets/node_modules/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../../assets/js/moment.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
    var sender = 0;
    var receiver = 0;
    var role = 0;
    var offers = [];
    var content = '';

    $('.select2').select2();
    addListner(conn);
    $("#select-all-packages").change(function() {
        $("input:checkbox.packages-table").prop('checked', $(this).prop("checked"));
    });
    $('.SendOff').click(function() {
        $.each($('.packages').val(), function() {
            let offer = offers[this];
            sender = $('.experts1 option:selected').val();
            receiver = $('#customer1').val();
            if (offer.total_discount < 100) {
                let finalPrice = (100 - offer.total_discount) * (offer.price / 100);
                if (offer.total_discount > 0) {
                    content = '<div class="row"><div class="col-4 text-center info"><h4 class = "text-center">' + offer.title + '</h4></div><div class="col-4 text-center info"><h6 class = "price-lable"><?= ($trans["package_card"]["discount"]) ?> -' + offer.total_discount + '%</h6><h4 class = "text-center"><sup>' + offer.currency + '</sup>' + finalPrice.toFixed(2) + '<del class = "uppercase" style="font-size: 12px;">' + offer.price + '</del></h4></div><div class="col-4 text-center info">' + offer.messages + 'Messages</div><div class="col-md-12 text-center"><a href = "javascript:void(0)" data-id = "' + offer.id_package + '" class = "btn btn-sm text-white waves-effect waves-light buy-offer" ><?= ($trans["package_card"]["buy_now"]) ?> </a></div></div>'
                } else {
                    content = '<div class="row"><div class="col-4 text-center info"><h4 class = "text-center">' + offer.title + '</h4></div><div class="col-4 text-center info"><h6 class = "price-lable">Package</h6><h4 class = "text-center"><sup>' + offer.currency + '</sup>' + finalPrice.toFixed(2) + '</h4></div><div class="col-4 text-center info">' + offer.messages + 'Messages</div><div class="col-md-12 text-center"><a href = "javascript:void(0)" data-id = "' + offer.id_package + '" class = "btn btn-sm text-white waves-effect waves-light buy-offer" ><?= ($trans["package_card"]["buy_now"]) ?> </a></div></div>'
                }
            }
            sendMessage({
                command: "message",
                msg: content,
                sender: sender,
                sender_role: 3,
                receiver: receiver,
                receiver_role: 4,
                account: <?= $_SESSION['id_account'] ?>,
                id_group: <?= $_SESSION['id_account'] ?>,
                admin: 2
            });
        });
        $('#addOff').modal('hide');
    });
    $('#customers-dtable').on('click', '.sendOffer', function() {
        $('#customer1').val($(this).data('id'));
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                type: 'getPublisher',
                id: $(this).data('id')
            },
            success: function(data) {
                $.each(data.consultants, function() {
                    $('.experts1').append('<option value="' + this.id_user + '">' + this.pseudo + '</option>');
                })
                offers = data.offers;
                $('.packages').empty();
                $.each(data.packages, function(k, v) {
                    $('.packages').append(this);
                });
            }
        })
        $('#addOff').modal('show');
    });
    $('.addOffer').click(function() {
        $('#overlay').show();
        let form = $('#Form').serializeArray();
        let id = $('#customer1').val();
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            dataType: 'json',
            data: {
                type: 'SetOffer',
                form: form,
                id_account: <?= $id_account ?>,
                user_id: id
            },
            success: function(data) {
                if (data == 1) {
                    Swal.fire({
                        type: 'success',
                        title: 'Offer added successfully !'
                    })
                } else if (data == 0) {
                    Swal.fire({
                        type: 'error',
                        title: 'Offer has not added!'
                    })
                }
                $('#overlay').hide();
                $('#addOff').modal('hide');
            }
        })
    });
    $('.buttonClass').daterangepicker({
        drops: "up",
        buttonClasses: "btn",
        applyClass: "btn-info",
        cancelClass: "btn-danger",
        minDate: new Date(),
    });
    $('input[type=radio][name=dateType]').change(function() {
        if (this.value == '1') {
            $(".dateRangePickerContainer").css("display", "block");
        } else if (this.value == '2') {
            $(".dateRangePickerContainer").css("display", "none");
        }
    });
    $("#discount").change(function() {
        if ($(this).val() > 0 && $(this).val() <= 100) {
            $("#check-offer-effect").prop("disabled", false);
        }
    });
    $("#check-offer-effect").click(function() {
        $(this).prop("disabled", "true");
        $(this).text("Checking Offer Effect");
        var startDate = null;
        var endDate = null;
        var dateType = $('input[type=radio][name=dateType]').val();
        if (dateType == 1) {
            var dateRange = $("#dateRange").val();
            startDate = dateRange.split(' - ')[0];
            endDate = dateRange.split(' - ')[1];
        }
        var discount = $("#discount").val();
        var accountId = <?= $id_account ?>;
        $.ajax({
            url: "investigateOffer.php",
            type: "POST",
            data: {
                dateType: dateType,
                startDate: startDate,
                endDate: endDate,
                discount: discount,
                accountId: accountId
            },
            dataType: "json",
            success: function(dataResult) {
                if (dataResult.statusCode == 200) {
                    console.log(dataResult);
                    $.each(dataResult.packages, function(key, value) {
                        var extra_discount = ((value.total_discount == null) ? 0 : parseInt(value.total_discount));
                        var discounted_price = (value.price / 100) * (100 - (parseInt(discount) + extra_discount));
                        console.log(discount + extra_discount);
                        $("#package-" + value.id_package + "-price-effect").html(discounted_price.toFixed(2) + '<sup>' + value.currency + '</sup>');
                    });
                } else if (dataResult.statusCode == 201) {
                    console.log(dataResult);
                }
            }
        });
    });
    $(document).ready(function() {
        $('#publishers').hide();
        $('#importation').hide();
        $('#sharedLink').hide();
        $('.way').click(function() {
            $('#publishers').hide();
            $('#importation').hide();
            $('#sharedLink').hide();
            if ($('#addWayRadio3').is(':checked')) {
                $('#publishers').show();
            } else if ($('#addWayRadio2').is(':checked')) {
                $('#importation').show();
            } else if ($('#addWayRadio4').is(':checked')) {
                $('#sharedLink').show();
            }
        });
        $(".SelectBox").select2({
            placeholder: 'Select an option'
        });
        $(".SelectOffre").select2({
            placeholder: 'Select an option'
        });
        $(".SelectPackages").select2({
            placeholder: 'Select an option'
        });
        $("#offers").select2({
            placeholder: 'Select offers'
        });
        $("#customers-dtable").on('click', '.sendMSG', function() {
            $('#overlay').show();
            let id = $(this).data('id');
            $('#user').val(id);
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                data: {
                    type: 'getConv',
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    $('#conversation').empty();
                    $.each(data, function() {
                        $('#conversation').append('<option value="' + this.receiver + '" data-id="' + this.id_consultant + '">' + this.pseudo + '</option>')
                    });
                }
            });
            $('#overlay').hide();
            $('#MessageModal').modal('show');
        });
        $("#send_message").click(function() {
            $('#overlay').show();
            content = $("#content").val();
            role = $("input[name='role']:checked").val();
            sender = $('#conversation').children("option:selected").val();
            var consultant_id = $('#conversation').children("option:selected").data('id');
            receiver = $('#user').val();
            if (receiver == null || content == "" || sender == null) {} else if (!content.replace(/\s/g, '').length) {} else {
                sendMessage({
                    command: "message",
                    msg: content,
                    sender: sender,
                    sender_role: 3,
                    receiver: receiver,
                    receiver_role: 4,
                    account: <?= intval($_SESSION['id_account']) ?>,
                    consultant_id: consultant_id,
                    id_group: <?= intval($_SESSION['id_account']) ?>,
                    admin: role
                });
                $(':input[id="content"]').val(null);
            }
            $('#overlay').hide();
        });
        var table = $('#customers-dtable').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            orderCellsTop: true,
            fixedHeader: true,
            columnDefs: [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
            }],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        $('.buysOp').change(function() {
            if ($(this).val() == 'Between') {
                $('.btw').show();
                $('.oth').hide();
            } else {
                $('.btw').hide();
                $('.oth').show();
            }
        });
        $('.balance').change(function() {
            if ($(this).val() == 'Between') {
                $('.btw1').show();
                $('.oth1').hide();
            } else {
                $('.btw1').hide();
                $('.oth1').show();
            }
        });
        $('#customers-dtable thead tr').clone(true).appendTo('#customers-dtable thead');
        $('#customers-dtable thead tr:eq(1) th').each(function(i) {
            if (i < 8) {
                var title = $(this).text();
                switch (i) {
                    case 0:
                        title = "ID";
                        break;
                    case 1:
                        title = "<?php echo ($trans["admin"]["customers"]["customers_table"]["firstname"]) ?>";
                        break;
                    case 2:
                        title = "<?php echo ($trans["admin"]["customers"]["customers_table"]["lastname"]) ?>";
                        break;
                    case 3:
                        title = "<?php echo ($trans["admin"]["customers"]["customers_table"]["email"]) ?>";
                        break;
                    case 4:
                        title = "<?php echo ($trans["admin"]["customers"]["customers_table"]["country"]) ?>";
                        break;
                    case 5:
                        title = "<?php echo ($trans["admin"]["customers"]["customers_table"]["phone"]) ?>";
                        break;
                    case 6:
                        title = "<?php echo ($trans["admin"]["customers"]["customers_table"]["buys_count"]) ?>";
                        break;
                    case 7:
                        title = "<?php echo ($trans["admin"]["customers"]["customers_table"]["created_at"]) ?>";
                        break;
                }
                $(this).html('<input type="text" placeholder="' + title + '" style="width:inherit;" />');
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            } else {
                $(this).html('');
            }
        });
        $(".submit").click(function() {
            $('.preloader').css('display', 'block');
            let form = new FormData(document.getElementById('searchForm'));
            form.append('type','getCustomers');
            $.when($.ajax({
                url: 'functions_ajax.php',
                processData: false,
                contentType: false,
                type: 'POST',
                data: form,
                dataType: "json",
                success: function(data) {
                    table.clear();
                    $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
                    $('.dt-button').removeClass('dt-button');
                    if (data.length > 500) {
                        table.rows.add(data).draw();
                        $('.buttons-excel').click();
                        table.clear();
                    } else {
                        table.rows.add(data).draw();
                        $('#table').show();
                        $('#showForm').click();
                    }
                }
            })).done(function() {
                $('.preloader').css('display', 'none');
            });
        });
        $('#searchForm').on('shown.bs.collapse', function() {
            $("#icontoggle").removeClass();
            $("#icontoggle").toggleClass('fa fa-angle-double-up');
            $("#FormText").text("<?php echo ($trans["hide_form"]) ?>");

        });
        $('#searchForm').on('hidden.bs.collapse', function() {
            $("#icontoggle").removeClass();
            $("#icontoggle").toggleClass('fa fa-angle-double-down');
            $("#FormText").text("<?php echo ($trans["show_form"]) ?>");
        });
        $('#myForm').submit(function(eventObj) {
            $(this).append($('input[name="customers_ids[]"]'));
            return true;
        });
        $('#PKGForm').submit(function(eventObj) {
            $(this).append($('input[name="customers_ids[]"]'));
            return true;
        });
        $("#customers-dtable").on('click', '.delete-button', function() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'mr-2 btn btn-danger'
                },
                buttonsStyling: false,
            })
            swalWithBootstrapButtons.fire({
                title: '<?php echo ($trans["admin"]["customers"]["delete_customer"]) ?>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '<?php echo ($trans["admin"]["websites_list"]["alert"]["confirm"]) ?>',
                cancelButtonText: '<?php echo ($trans["admin"]["websites_list"]["alert"]["cancel"]) ?>',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    var id = $(this).attr('data-id');
                    var obj = $(this);
                    $.ajax({
                        url: 'functions_ajax.php',
                        dataType: "json",
                        data: {
                            type: 'remove_customer',
                            id: id
                        },
                        success: function(code_html, statut) {
                            alert(code_html);
                        },
                        error: function(statut) {
                            alert("<?php echo ($trans["admin"]["customers"]["delete_customer_failed"]) ?>");
                        }
                    });
                }
            });
        });
        $('#select-all-customers').on('click', function() {
            var rows = table.rows({
                'search': 'applied'
            }).nodes();
            $('input[type=checkbox]', rows).prop('checked', this.checked);
        });
        $('#customers-dtable tbody').on('change', 'input[type="checkbox"]', function() {
            // If checkbox is not checked
            if (!this.checked) {
                var el = $('#select-all-customers').get(0);
                // If "Select all" control is checked and has 'indeterminate' property
                if (el && el.checked && ('indeterminate' in el)) {
                    // Set visual state of "Select all" control
                    // as 'indeterminate'
                    el.indeterminate = true;
                }
            }
            if ($('#customers-dtable .custom-checkbox input:checked').length == 0) {
                $('#addOffers').addClass("disabled");
                $('#addOffers').attr("disabled", true);
                $('#addPackages').addClass("disabled");
                $('#addPackages').attr("disabled", true);
            } else {
                $('#addOffers').removeClass("disabled");
                $('#addOffers').attr("disabled", false);
                $('#addPackages').removeClass("disabled");
                $('#addPackages').attr("disabled", false);
            }
        });
        $('#customers-dtable .custom-checkbox input').change(function() {
            if ($('#customers-dtable .custom-checkbox input:checked').length == 0) {
                $('#addOffers').addClass("disabled");
                $('#addOffers').prop("disabled", true);
                $('#addPackages').addClass("disabled");
                $('#addPackages').attr("disabled", true);
            } else {
                $('#addOffers').removeClass("disabled");
                $('#addOffers').prop("disabled", false);
                $('#addPackages').removeClass("disabled");
                $('#addPackages').attr("disabled", false);
            }
        });
        var errorMsg = $("#error-msg"),
            validMsg = $("#valid-msg");
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

        if ($(".alert-success").length || $(".alert-danger").length) {
            $("#table").show();
            $('#searchForm').trigger('hidden.bs.collapse');
        } else {
            $("#table").hide();
            $('#showForm').click();
        }
    });

    function sendMessage(message) {
        if (conn.readyState == WebSocket.OPEN) {
            conn.send(JSON.stringify(message));
        } else {
            Swal.fire({
                type: 'error',
                title: 'Reconnecting ...',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            conn = null;
            conn = new WebSocket(wsCurEnv);
            conn.onopen = function(e) {
                conn.send(JSON.stringify({
                    command: "attachAccount",
                    account: id_user,
                    id_group: id_company,
                    role: 2
                }));
                conn.send(JSON.stringify({
                    command: "connected",
                    id_group: id_company
                }));
            };
            addListner(conn);
        }
    }

    function addListner(conn) {
        conn.onmessage = function(e) {
            var dt = jQuery.parseJSON(e.data);
            if (dt.status == 200) {
                if (dt.action == "newMessage") {
                    if (dt.sender == sender && dt.receiver == receiver && dt.message == content) {
                        $('#MessageModal').modal('hide');
                        Swal.fire({
                            type: 'success',
                            title: 'Message sended successfully'
                        });
                    }
                }
            }
        }
    }
</script>
</body>

</html>>