<?php
ini_set("display_errors", 1);
$page_name = "publisher";
ob_start();
include('header.php');
?>
<link href="../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<div class="row">
    <?php
    $id_publisher = intval($_GET['id']);

    if (isset($_POST['save1'])) {
        $Companyname = $_POST['Companyname'];
        $Businessname = $_POST['Businessname'];
        $Registration_number = $_POST['Registration_number'];

        $VAT_number = $_POST['VAT_number'];
        $Address1 = $_POST['Address1'];
        $Address2 = $_POST['Address2'];

        $City = $_POST['City'];
        $Province = $_POST['Province'];
        $Zip_code = $_POST['Zip_code'];

        $country = $_POST['country'];
        $Contact_email = $_POST['Contact_email'];
        $Billing_email = $_POST['Billing_email'];

        $stmt1 = $conn->prepare("UPDATE `publishers` SET `country`=:a, `date_add`=NOW(), `company_name`=:b, `business_name`=:c, `registration_number`=:d, `VAT_number`=:e, `address`=:f, `address2`=:g, `city`=:h, `province`=:i, `Zip_code`=:j, `contact_email`=:k, `billing_email`=:l");
        $stmt1->bindParam(':a', $country);
        $stmt1->bindParam(':b', $Companyname);
        $stmt1->bindParam(':c', $Businessname);
        $stmt1->bindParam(':d', intval($Registration_number));
        $stmt1->bindParam(':e', intval($VAT_number));
        $stmt1->bindParam(':f', $Address1);

        $stmt1->bindParam(':g', $Address2);
        $stmt1->bindParam(':h', $City);
        $stmt1->bindParam(':i', $Province);

        $stmt1->bindParam(':j', intval($Zip_code));
        $stmt1->bindParam(':k', $Contact_email);
        $stmt1->bindParam(':l', $Billing_email);
        $stmt1->execute();
        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Informations updated successfully </div></div>";
    }
    if (isset($_POST['save2'])) {

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $stmt2 = $conn->prepare("UPDATE `publishers_management` SET `firstname`=:fn ,`lastname`=:ln, `email`=:em, `phone`=:ph, `id_publisher`=:id");
        $stmt2->bindParam(':fn', $firstname);
        $stmt2->bindParam(':ln', $lastname);
        $stmt2->bindParam(':em', $email);
        $stmt2->bindParam(':ph', $phone);
        $stmt2->bindParam(':id', intval($id_publisher));
        $stmt2->execute();
        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Management updated successfully </div></div>";
    }
    if (isset($_POST['saveBank'])) {

        $Benefiary = $_POST['Benefiary'];
        $currency = $_POST['currency'];
        $Bank_name = $_POST['Bank_name'];
        $country1 = $_POST['country1'];
        $Address3 = $_POST['Address3'];
        $Address4 = $_POST['Address4'];
        $City2 = $_POST['City2'];
        $Zip_code2 = $_POST['Zip_code2'];
        $Province = $_POST['Province2'];
        $IBAN = $_POST['IBAN'];
        $Swift = $_POST['Swift'];
        $Routing = $_POST['Routing'];
        $stmt3 = $conn->prepare("UPDATE `publishers_bank` SET `Benefiary`=:a,`Account_currency`=:b, `name`=:c, `country`=:d, `address`=:e, `address2`=:f, `City`=:g, `Province`=:h, `Zip_code`=:i, `IBAN`=:j, `Swift_BIC`=:k, `Routing_Number`=:l");
        $stmt3->bindParam(':a', $Benefiary);
        $stmt3->bindParam(':b', $currency);
        $stmt3->bindParam(':c', $Bank_name);
        $stmt3->bindParam(':d', $country1);
        $stmt3->bindParam(':e', $Address3);
        $stmt3->bindParam(':f', $Address4);
        $stmt3->bindParam(':g', $City2);
        $stmt3->bindParam(':h', $Province);
        $stmt3->bindParam(':i', $Zip_code2);
        $stmt3->bindParam(':j', $IBAN);
        $stmt3->bindParam(':k', $Swift);
        $stmt3->bindParam(':l', $Routing);
        $stmt3->execute();
        unset($_POST);
        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Bank updated successfully </div></div>";
    }
    if (isset($_POST['addAffiliation'])) {

        $affiliation = $_POST['affiliation'];
        $Service = $_POST['Service'];

        $stmt2 = $conn->prepare("INSERT INTO `publisher_Affiliation`(id_publisher,country,service,date_add) values(:id,:co,:se,NOW())");

        foreach ($affiliation as $country) {
            $stmt2->bindParam(':co', $country);
            $stmt2->bindParam(':se', $Service);
            $stmt2->bindParam(':id', intval($id_publisher));
            $stmt2->execute();
        }
    }
    if (isset($_POST['saveAffiliation'])) {

        $affiliation = $_POST['affiliation'];
        $Service = $_POST['Service'];
        $id = $_POST['id'];

        $stmt2 = $conn->prepare("UPDATE `publisher_Affiliation` SET country=:co,service=:se WHERE id_Affiliation=:id");
        $stmt2->bindParam(':co', $affiliation[0]);
        $stmt2->bindParam(':se', $Service);
        $stmt2->bindParam(':id', intval($id));
        $stmt2->execute();
    }
    if (isset($_POST['add'])) {

        $Benefiary = $_POST['Benefiary'];
        $currency = $_POST['currency'];
        $Bank_name = $_POST['Bank_name'];
        $country1 = $_POST['country1'];
        $Address3 = $_POST['Address3'];
        $Address4 = $_POST['Address4'];
        $City2 = $_POST['City2'];
        $Zip_code2 = $_POST['Zip_code2'];
        $Province = $_POST['Province2'];
        $IBAN = $_POST['IBAN'];
        $Swift = $_POST['Swift'];
        $Routing = $_POST['Routing'];
        $stmt3 = $conn->prepare("INSERT INTO `publishers_bank`(`Benefiary`,`Account_currency`, `name`, `country`, `address`, `address2`, `City`, `Province`, `Zip_code`, `IBAN`, `Swift_BIC`, `Routing_Number`,id_publisher) VALUES (:a,:b,:c,:d,:e,:f,:g,:h,:i,:j,:k,:l,:id)");
        $stmt3->bindParam(':a', $Benefiary);
        $stmt3->bindParam(':b', $currency);
        $stmt3->bindParam(':c', $Bank_name);
        $stmt3->bindParam(':d', $country1);
        $stmt3->bindParam(':e', $Address3);
        $stmt3->bindParam(':f', $Address4);
        $stmt3->bindParam(':g', $City2);
        $stmt3->bindParam(':h', $Province);
        $stmt3->bindParam(':i', $Zip_code2);
        $stmt3->bindParam(':j', $IBAN);
        $stmt3->bindParam(':k', $Swift);
        $stmt3->bindParam(':l', $Routing);
        $stmt3->bindParam(':id', $id_publisher);
        $stmt3->execute();
        unset($_POST);
        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>bank added successfully </div></div>";
    }
    if (isset($_POST['save3'])) {
        if (isset($_FILES["document"]["name"]) && $_FILES["document"]["name"] != "") {
            $dirLogo = '../uploads/documents/';
            $uploadFile = removeAccents($_FILES["document"]["name"]);
            $uploadFileTmp = removeAccents($_FILES["document"]["tmp_name"]);
            $fileData1 = pathinfo(basename($uploadFile));
            $Filenom = basename($uploadFile, "." . $fileData1['extension']);
            $photo = uniqid() . "-" . $id_publisher . '.' . $fileData1['extension'];
            $target_path1 = ($dirLogo . $photo);
            while (file_exists($target_path1)) {
                $photo = uniqid() . "-" . $id_publisher . '.' . $fileData1['extension'];
                $target_path1 = ($dirLogo . $photo);
            }
            move_uploaded_file($uploadFileTmp, $target_path1);
            $stmt2 = $conn->prepare("INSERT INTO `publisher_documents`(id_publisher,document_name,type,date_add) values (:id,:ph,1,NOW())");
            $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
            $stmt2->bindParam(':id', $id_publisher, PDO::PARAM_INT);
            $stmt2->execute();
            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Document added successfully </div></div>";
        }
        if (isset($_FILES["details"]["name"]) && $_FILES["details"]["name"] != "") {
            $dirLogo = '../uploads/documents/';
            $uploadFile = removeAccents($_FILES["details"]["name"]);
            $uploadFileTmp = removeAccents($_FILES["details"]["tmp_name"]);
            $fileData1 = pathinfo(basename($uploadFile));
            $Filenom = basename($uploadFile, "." . $fileData1['extension']);
            $photo = uniqid() . "-" . $id_publisher . '.' . $fileData1['extension'];
            $target_path1 = ($dirLogo . $photo);
            while (file_exists($target_path1)) {
                $photo = uniqid() . "-" . $id_publisher . '.' . $fileData1['extension'];
                $target_path1 = ($dirLogo . $photo);
            }
            move_uploaded_file($uploadFileTmp, $target_path1);
            $stmt2 = $conn->prepare("INSERT INTO `publisher_documents`(id_publisher,document_name,type,date_add) values (:id,:ph,2,NOW())");
            $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
            $stmt2->bindParam(':id', $id_publisher, PDO::PARAM_INT);
            $stmt2->execute();
            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Document added successfully </div></div>";
        }
    }
    $stmt = $conn->prepare("SELECT * FROM publishers  WHERE id_publisher = :ID");
    $stmt->bindParam(':ID', $id_publisher, PDO::PARAM_INT);
    $stmt->execute();
    $publisher = $stmt->fetchObject();
    $stmt1 = $conn->prepare("SELECT * FROM publishers_management  WHERE id_publisher = :ID");
    $stmt1->bindParam(':ID', $id_publisher, PDO::PARAM_INT);
    $stmt1->execute();
    $management = $stmt1->fetchObject();
    $stmt2 = $conn->prepare("SELECT * FROM publishers_bank  WHERE id_publisher = :ID");
    $stmt2->bindParam(':ID', $id_publisher, PDO::PARAM_INT);
    $stmt2->execute();
    $bank = $stmt2->fetchAll();
    $stmt2 = $conn->prepare("SELECT pd.*,p.company_name FROM publisher_documents pd,users u,publishers p WHERE pd.id_publisher = :ID AND p.id_publisher=u.id_profile AND u.id_user=:ID");
    $stmt2->bindParam(':ID', $id_publisher, PDO::PARAM_INT);
    $stmt2->execute();
    $documents = $stmt2->fetchAll();
    $stmt2 = $conn->prepare("SELECT pd.*,p.company_name FROM publisher_Affiliation pd,users u,publishers p WHERE pd.id_publisher = :ID AND p.id_publisher=u.id_profile AND u.id_user=:ID");
    $stmt2->bindParam(':ID', $id_publisher, PDO::PARAM_INT);
    $stmt2->execute();
    $Affiliations = $stmt2->fetchAll();

    ?>
    <style>
        .pull-right {
            background-color: #ff9d8f;
            border-color: #ff9d8f;
            color: white;
        }
    </style>
    <div class="col-lg-12 col-xlg-9 col-md-12">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#general_informations" role="tab"><?php echo ($trans["general_informations"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Affiliation') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#Affiliation" role="tab"><?= $trans['publisher']['Affiliation'] ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Management') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#Management" role="tab"><?= $trans['publisher']['Management'] ?></a></li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'bank_details') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#bank_details" role="tab"><?= $trans['publisher']['Bank_details'] ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Documents ') {
                                                                echo "active";
                                                            } ?>" data-toggle="tab" href="#Documents " role="tab"><?= $trans['publisher']['Documents'] ?> </a> </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {
                                            echo "active";
                                        } ?>" id="general_informations" role="tabpanel">
                    <div class="card-body">
                        <form action="" id="companyForm" method="POST">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Companyname"><?= $trans['publisher']['Company_name'] ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Companyname" name="Companyname" value="<?= $publisher->company_name ?>" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Businessname"><?= $trans['publisher']['Business_name'] ?> (optional) : </label>
                                        <input type="text" class="form-control " id="Businessname" name="Businessname" value="<?= $publisher->business_name ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Registration_number"><?= $trans['publisher']['Registration_number'] ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Registration_number" name="Registration_number" value="<?= $publisher->registration_number ?>" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="VAT_number"><?= $trans['publisher']['VAT_number'] ?> (optional) : </label>
                                        <input type="text" class="form-control " id="VAT_number" name="VAT_number" value="<?= $publisher->VAT_number ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Address1"><?= $trans['publisher']['Address'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Address1" name="Address1" required value="<?= $publisher->address ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Address2"><?= $trans['publisher']['Address'] ?> (optional) : </label>
                                        <input type="text" class="form-control " id="Address2" name="Address2" value="<?= $publisher->address2 ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="City"><?= $trans['publisher']['City'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="City" name="City" required value="<?= $publisher->city ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Province"><?= $trans['publisher']['Province'] ?>(optional) : </label>
                                        <input type="text" class="form-control " id="Province" name="Province" value="<?= $publisher->province ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Zip_code"><?= $trans['publisher']['Zip_code'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Zip_code" name="Zip_code" required value="<?= $publisher->Zip_code ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country"> <?php echo ($trans["country"]) ?> : <span class="danger">*</span> </label>
                                        <select name="country" id="country" class="form-control select-search country form-control-line">
                                            <?php
                                            foreach ($countries as $key => $country) {
                                                if ($key ==  $publisher->country) {
                                                    echo '<option value="' . $key . '" selected>' . $country . '</option>';
                                                } else {
                                                    echo '<option value="' . $key . '">' . $country . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Contact_email"><?= $trans['publisher']['Contact_email'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Contact_email" name="Contact_email" required value="<?= $publisher->contact_email ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Billing_email"><?= $trans['publisher']['Billing_email'] ?>: </label>
                                        <input type="text" class="form-control " id="Billing_email" name="Billing_email" required value="<?= $publisher->billing_email ?>"> </div>
                                </div>
                            </div>
                            <button type="submit" name="save1" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["save"]) ?></button>
                            <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Management') {
                                            echo "active";
                                        } ?>" id="Management" role="tabpanel">
                    <form action="" id="managementForm" method="POST">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname"> <?php echo ($trans["first_name"]) ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="firstname" name="firstname" required value="<?= $management->firstname ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname"><?php echo ($trans["last_name"]) ?> : </label>
                                        <input type="text" class="form-control " id="lastname" name="lastname" required value="<?= $management->lastname ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">email : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="email" name="email" required value="<?= $management->email ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label text-right"><?php echo ($trans["phone"]) ?> :<span class="danger">*</span> </label>
                                    <div class="">
                                        <input name="phone" type="tel" id="phone" class="form-control" style="width:100%" required value="<?= $management->phone ?>">
                                        <span id="valid-msg" class="hide text-success">� Valid</span>
                                        <span id="error-msg" class="hide text-danger">✗ Invalid number</span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="save2" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["save"]) ?></button>
                            <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>

                        </div>
                    </form>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'bank_details') {
                                            echo "active";
                                        } ?>" id="bank_details" role="tabpanel">
                    <div class="card-body">
                        <div class="table-responsive m-b-40 m-r-0">
                            <table class="display  nowrap table table-hover table-striped" id="banks">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?= $trans['publisher']['Benefiary'] ?></th>
                                        <th><?= $trans['publisher']['Account_currency'] ?> </th>
                                        <th><?= $trans['publisher']['name'] ?></th>
                                        <th><?= $trans['country'] ?></th>
                                        <th><?= $trans['publisher']['Address'] ?></th>
                                        <th>IBAN</th>
                                        <th><?= $trans['publisher']['End_date'] ?></th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($bank) {
                                        foreach ($bank as $b) {
                                    ?>
                                            <tr id="bank_<?= $b['id_bank'] ?>">
                                                <td><?= $b['id_bank'] ?></td>
                                                <td><?= $b['Benefiary'] ?></td>
                                                <td><?= $b['Account_currency'] ?></td>
                                                <td><?= $b['name']  ?></td>
                                                <td><?= $trans['countries'][$b['country']] ?></td>
                                                <td><?= $b['address'] ?></td>
                                                <td><?= $b['IBAN'] ?></td>
                                                <td><?= $b['date_end'] ?></td>
                                                <?php if (!$b['date_end']) { ?>
                                                    <td><a href="#" class="EditBank badge badge-pill badge-info" data-id="<?= $b['id_bank'] ?>">Edit</a><a href="#" class="deleteBank badge badge-pill badge-danger" data-id="<?= $b['id_bank'] ?>">Delete</a></td>
                                                <?php } else echo '<td></td>' ?>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Documents') {
                                            echo "active";
                                        } ?>" id="Documents" role="tabpanel">
                    <div class="card-body">
                        <div class="table-responsive m-b-40 m-r-0">
                            <table class="display  nowrap table table-hover table-striped" id="documents">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?= $trans['publisher']['publisher'] ?></th>
                                        <th><?= $trans['publisher']['type'] ?> </th>
                                        <th><?= $trans['publisher']['Added_date'] ?></th>
                                        <th>actions</th>
                                    </tr>
                                </thead>
                                <?php if ($documents) {
                                    foreach ($documents as $document) {
                                ?>
                                        <tbody>
                                            <tr>
                                                <td><?= $document['id_document'] ?></td>
                                                <td><?= $document['company_name'] ?></td>
                                                <td><?= $document['type'] == 1 ? 'Commercial registry document' : 'Bank details' ?></td>
                                                <td><?= $document['date_add']  ?></td>
                                                <td><a href="../uploads/documents/<?= $document['document_name']  ?>" download="Document"><i class="fa fa-download px-4" title="download" style="font-size:18px"></i></a><a href="../uploads/documents/<?= $document['document_name']  ?>"><i class="fa fa-eye" title="see" style="font-size:18px"></i></a></td>
                                            </tr>
                                        </tbody>
                                <?php }
                                } ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Affiliation') {
                                            echo "active";
                                        } ?>" id="Affiliation" role="tabpanel">
                    <div class="card-body">
                        <div class="table-responsive m-b-40 m-r-0">
                            <table class="display  nowrap table table-hover table-striped" id="AffiliationTb">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?= $trans['country'] ?></th>
                                        <th><?= $trans['publisher']['Service'] ?></th>
                                        <th><?= $trans['publisher']['Added_date'] ?></th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <?php if ($Affiliations) {
                                    foreach ($Affiliations as $Affiliation) {
                                ?>
                                        <tbody>
                                            <tr id="<?= $Affiliation['id_Affiliation'] ?>">
                                                <td><?= $Affiliation['id_Affiliation'] ?></td>
                                                <td><?= $trans['countries'][$Affiliation['country']] ?></td>
                                                <td><?= $Affiliation['service'] ?></td>
                                                <td><?= $Affiliation['date_add']  ?></td>
                                                <td><a href="#" class="badge badge-pill badge-danger Stop" data-id="<?= $Affiliation['id_Affiliation'] ?>">Stop</a><a href="#" class="badge badge-pill badge-info Edit" data-id="<?= $Affiliation['id_Affiliation'] ?>">Edit</a></td>
                                            </tr>
                                        </tbody>
                                <?php }
                                } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?= $trans['publisher']['Add_bank'] ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="bankForm" method="POST">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Benefiary"><?= $trans['publisher']['Benefiary'] ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Benefiary" name="Benefiary" placeholder="Enter Benefiary" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency"><?= $trans['publisher']['Account_currency'] ?> : </label>
                                        <select class="form-control " id="currency" name="currency" required>
                                            <option></option>
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
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Bank_name"><?= $trans['publisher']['Bank_name'] ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Bank_name" name="Bank_name" placeholder="Enter Bank name" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country1"> <?php echo ($trans["country"]) ?> : <span class="danger">*</span> </label>
                                        <select name="country1" id="country1" class="form-control select-search country form-control-line">
                                            <?php
                                            foreach ($countries as $key => $country) {
                                                if ($key == "TN") {
                                                    echo '<option value="' . $key . '" selected>' . $country . '</option>';
                                                } else {
                                                    echo '<option value="' . $key . '">' . $country . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Address3"><?= $trans['publisher']['Address'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Address3" name="Address3" placeholder="Enter Address" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Address4"><?= $trans['publisher']['Address'] ?> (optional) : </label>
                                        <input type="text" class="form-control " id="Address4" name="Address4" placeholder="Enter Address 2" required> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="City2"><?= $trans['publisher']['City'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="City2" name="City2" placeholder="Enter City" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Province2"><?= $trans['publisher']['Province'] ?> (optional) : </label>
                                        <input type="text" class="form-control " id="Province2" name="Province2" placeholder="Enter Province"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Zip_code2"><?= $trans['publisher']['Zip_code'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Zip_code2" name="Zip_code2" placeholder="Enter Zip code" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="IBAN"> <?= $trans['publisher']['Account_Number_IBAN'] ?> : </label>
                                        <input type="text" class="form-control " id="IBAN" name="IBAN" maxlength="34" placeholder="Enter Account Number / IBAN" required> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Swift"><?= $trans['publisher']['Swift_BIC'] ?> <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Swift" maxlength="11" minlength="8" name="Swift" placeholder="Enter Swift / BIC" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Routing"><?= $trans['publisher']['Routing_Number'] ?> (optional) : </label>
                                        <input type="text" class="form-control " id="Routing" name="Routing" placeholder="Enter Routing Number"> </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="saveBank" class="saveBank btn btn-primary"><?php echo ($trans["save"]) ?></button>
                        <button type="submit" name="add" class="add btn btn-primary"><?php echo ($trans["add"]) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="doc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?= $trans['publisher']['Add_Documents'] ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body">

                            <div>
                                <label for="option">document type</label>
                                <select class="form-group form-control" name="option" id="choice">
                                    <option value="document"><?= $trans['publisher']['Commercial_document'] ?></option>
                                    <option value="details"><?= $trans['publisher']['Bank_details'] ?></option>
                                </select>
                            </div>
                            <label for="option">File</label>
                            <div class="form-group ">
                                <input type="file" class="form-control bank " name="details" id="details">
                                <input type="file" class="form-control document" name="document" id="document">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="save3" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["save"]) ?></button>
                        <button type="reset" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="Aff" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?= $trans['publisher']['Affiliation'] ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="affiliation"><?= $trans['publisher']['Countries'] ?> :</label>
                                <select name="affiliation[]" id="select2" style="width:100%" multiple>
                                    <?php foreach ($trans['countries'] as $key => $country) { ?>
                                        <option value="<?= $key ?>"><?= $country ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="Service"><?= $trans['publisher']['Service'] ?> :</label>
                                <input type="text" class="form-control " name="Service" id="Service">
                            </div>
                        </div>
                    </div>
                    <input type="text" class="form-control " name="id" id="idAff" hidden>

                    <div class="modal-footer">
                        <button type="submit" name="addAffiliation" class="addAffiliation btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["add"]) ?></button>
                        <button type="submit" name="saveAffiliation" class="saveAffiliation  btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["save"]) ?></button>
                        <button type="reset" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<script src="../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>


<script>
    $('.bank').toggle();

    $('#select2').select2();

    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        window.history.pushState("", "", "./publisher.php?id=<?= $id_publisher ?>&tab=" + e.target.hash.substr(1));
    });
    var table2 = $('#banks').DataTable({
        dom: '<"toolbar d-inline bank">frtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(".bank").html('<button class="btn waves-effect waves-light btn-sm btn-secondary btn addBank" data-toggle="modal" data-target="#form">Add bank</button>');
    var table3 = $('#documents').DataTable({
        dom: '<"toolbar d-inline document">frtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(".document").html('<button class="btn waves-effect waves-light btn-sm btn-secondary btn " data-toggle="modal" data-target="#doc">Add document</button>');
    var table4 = $('#AffiliationTb').DataTable({
        dom: '<"toolbar d-inline Affiliation">frtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(".Affiliation").html('<button class="btn waves-effect waves-light btn-sm btn-secondary  addAff" data-toggle="modal" data-target="#Aff">Add affiliation</button>');
    $(document).ready(function() {

        $('#choice').change(function() {
            $('.document').toggle();
            $('.bank').toggle();
        });
        $('.addBank').click(function() {
            $('.add').show();
            $('.saveBank').hide();
        });
        $('.EditBank').click(function() {
            let id = $(this).data('id');
            $('.add').hide();
            $('.saveBank').show();
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'get_bank',
                    id: id
                },
                success: function(data) {
                    $('#Benefiary').val(data.Benefiary);
                    $('#currency').val(data.Account_currency);
                    $('#Bank_name').val(data.name);
                    $('#country1 option[value=' + data.country + ']').attr('selected', 'selected');
                    $('#Address3').val(data.address);
                    $('#Address4').val(data.address2);
                    $('#City2').val(data.City);
                    $('#Province2').val(data.Province);
                    $('#Zip_code2').val(data.Zip_code);
                    $('#IBAN').val(data.IBAN);
                    $('#Swift').val(data.Swift_BIC);
                    $('#Routing').val(data.Routing_Number);
                    $('#form').modal('show');
                }
            })
        });
        $('.deleteBank').click(function() {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'del_bank',
                    id: id
                },
                success: function(data) {
                    table2.row($('#bank_' + id)).data(data).draw();
                }
            })
        });
        $('.delete').click(function() {
            let id = $(this).data('id');
            $.ajax({
                url: 'functions_ajax.php',
                dataType: 'JSON',
                type: 'post',
                data: {
                    id: id,
                    'action': 'delete_aff'
                },
                success: function(data) {
                    if (data == 1) {
                        table4.rows($('#' + id)).remove().draw();
                    }
                }
            })
        });

        $('.addAff').click(function() {
            $('.addAffiliation').show();
            $('.saveAffiliation').hide();
        });
        $('.Edit').click(function() {
            let id = $(this).data('id');
            $('#idAff').val(id);
            $('.addAffiliation').hide();
            $('.saveAffiliation').show();
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'get_affiliation',
                    id: id
                },
                success: function(data) {
                    $('#select2 option[value=' + data.country + ']').attr('selected', 'selected');
                    $('#select2').change();
                    $('#Service').val(data.service);
                    $('#Aff').modal('show');
                }
            })
        });
    })
</script>