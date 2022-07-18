<?php
$page_name = "profile";
ob_start();
include('header.php');
?>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<div class="row">
    <?php
    $stmt1 = $conn->prepare("SELECT id_profile FROM users WHERE id_user=:id AND profile=5");
    $stmt1->bindParam(':id', intval($_SESSION['id_user']));
    $stmt1->execute();
    $id_publisher = $stmt1->fetch();
    $id_publisher = intval($id_publisher['id_profile']);
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
    if (isset($_POST['addBank'])) {
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
        $id = intval($_POST['id']);
        $stmt3 = $conn->prepare("UPDATE `publishers_bank` SET `Benefiary`=:a,`Account_currency`=:b, `name`=:c, `country`=:d, `address`=:e, `address2`=:f, `City`=:g, `Province`=:h, `Zip_code`=:i, `IBAN`=:j, `Swift_BIC`=:k, `Routing_Number`=:l WHERE id_bank=:id");
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
        $stmt3->bindParam(':id', $id);
        $stmt3->execute();
        unset($_POST);
        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Bank updated successfully </div></div>";
    }
    if (isset($_POST['save4'])) {
        if (isset($_FILES["document"]["name"]) && $_FILES["document"]["name"] != "") {
            $dirLogo = '../../uploads/documents/';
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
            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Commercial registry document added successfully </div></div>";
        }
        if (isset($_FILES["details"]["name"]) && $_FILES["details"]["name"] != "") {
            $dirLogo = '../../uploads/documents/';
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
            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>bank details added successfully </div></div>";
        }
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
    $stmt = $conn->prepare("SELECT p.*,u.password,u.login,u.password_updated_at FROM publishers p,users u  WHERE id_publisher = u.id_profile AND u.id_user=:ID AND u.profile=5 ");
    $stmt->bindParam(':ID', intval($_SESSION['id_user']), PDO::PARAM_INT);
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
    <div class="col-md-12 col-md-6">
        <div class="card">
            <div class="d-flex flex-row">
                <?php
                if ($publisher->active == 1) {
                    echo '<div class="p-10 bg-success"><h3 class="text-white box m-b-0"><i class="mdi mdi-check"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-success">' . ($trans["approved"]) . '</h3></div>';
                } else echo '<div class="p-10 bg-danger"><h3 class="text-white box m-b-0"><i class="mdi mdi-progress-wrench"></i></h3></div><div class="align-self-center m-l-20"><h3 class="m-b-0 text-danger">' . ($trans["checking"]) . '</h3></div>';

                ?>
            </div>
        </div>
    </div>
    <style>
        .iti {width: 100%;}
        .pull-right {background-color: #ff9d8f;border-color: #ff9d8f;color: white;}
    </style>

    <div class="col-lg-12 col-xlg-9 col-md-12">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" data-toggle="tab" href="#general_informations" role="tab"><?php echo ($trans["general_informations"]) ?></a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Management') {echo "active";} ?>" data-toggle="tab" href="#Management" role="tab">Management</a></li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'bank_details') {echo "active";} ?>" data-toggle="tab" href="#bank_details" role="tab">Bank details</a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Documents ') {echo "active";} ?>" data-toggle="tab" href="#Documents " role="tab">Documents </a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Affiliation') {echo "active";} ?>" data-toggle="tab" href="#Affiliation" role="tab">Affiliation</a> </li>
                <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'authentication_credentials') {echo "active";} ?>" data-toggle="tab" href="#authentication_credentials" role="tab">Authentication credentials</a> </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {echo "active";} ?>" id="general_informations" role="tabpanel">
                    <div class="card-body">
                        <form action="" id="infoForm" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Companyname">Company name : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Companyname" name="Companyname" value="<?= isset($publisher->company_name) ? $publisher->company_name : "" ?>" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Businessname">Business name (optional) : </label>
                                        <input type="text" class="form-control " id="Businessname" name="Businessname" value="<?= isset($publisher->business_name) ? $publisher->business_name : "" ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Registration_number">Registration number : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Registration_number" name="Registration_number" value="<?= isset($publisher->registration_number) ? $publisher->registration_number : "" ?>" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="VAT_number">VAT number (optional) : </label>
                                        <input type="text" class="form-control " id="VAT_number" name="VAT_number" value="<?= isset($publisher->VAT_number) ? $publisher->VAT_number : "" ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Address1">Address <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Address1" name="Address1" required value="<?= isset($publisher->address) ? $publisher->address : "" ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Address2">Address (optional) : </label>
                                        <input type="text" class="form-control " id="Address2" name="Address2" value="<?= isset($publisher->address2) ? $publisher->address2 : "" ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="City">City <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="City" name="City" required value="<?= isset($publisher->city) ? $publisher->city : "" ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Province">Province (optional) : </label>
                                        <input type="text" class="form-control " id="Province" name="Province" value="<?= isset($publisher->province) ? $publisher->province : "" ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Zip_code">Zip code <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Zip_code" name="Zip_code" required value="<?= isset($publisher->Zip_code) ? $publisher->Zip_code : "" ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country"> <?php echo ($trans["country"]) ?> : <span class="danger">*</span> </label>
                                        <select name="country" id="country" class="form-control select-search country form-control-line">
                                            <?php
                                            foreach ($countries as $key => $country) {
                                                if ($key ==  isset($publisher->country) ? $publisher->country : "") {
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
                                        <label for="Contact_email">Contact e-mail <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Contact_email" name="Contact_email" required value="<?= isset($publisher->contact_email) ? $publisher->contact_email : "" ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Billing_email">Billing e-mail : </label>
                                        <input type="text" class="form-control " id="Billing_email" name="Billing_email" required value="<?= isset($publisher->billing_email) ? $publisher->billing_email : "" ?>"> </div>
                                </div>
                            </div>
                            <button type="submit" name="save1" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["save"]) ?></button>
                            <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
                        </form>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Management') {echo "active";} ?>" id="Management" role="tabpanel">
                    <form action="" id="MngForm" method="POST">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname"> <?php echo ($trans["first_name"]) ?> : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="firstname" name="firstname" required value="<?= isset($management->firstname) ? $management->firstname : "" ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname"><?php echo ($trans["last_name"]) ?> : </label>
                                        <input type="text" class="form-control " id="lastname" name="lastname" required value="<?= isset($management->lastname) ? $management->lastname : "" ?>"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">email : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="email" name="email" required value="<?= isset($management->email) ? $management->email : "" ?>"> </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label text-right"><?php echo ($trans["phone"]) ?> :<span class="danger">*</span> </label>
                                    <div class="">
                                        <input name="phone" type="tel" id="phone" class="form-control" style="width:100%" required value="<?= isset($management->phone) ? $management->phone : "" ?>">
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
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'bank_details') {echo "active";} ?>" id="bank_details" role="tabpanel">
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
                                                    <td><a href="#" class="EditBank btn btn-sm waves-effect waves-light btn-info" data-id="<?= $b['id_bank'] ?>">Edit</a><a href="#" class="deleteBank btn btn-sm waves-effect waves-light btn-danger" data-id="<?= $b['id_bank'] ?>">Delete</a></td>
                                                <?php } else echo '<td></td>' ?>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Documents') {echo "active";} ?>" id="Documents" role="tabpanel">
                    <div class="card-body">
                        <div class="table-responsive m-b-40 m-r-0">
                            <table class="display  nowrap table table-hover table-striped" id="documents">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>publisher</th>
                                        <th>type </th>
                                        <th>date add</th>
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
                                                <td><a href="../../uploads/documents/<?= $document['document_name']  ?>" download="Document"><i class="mdi mdi-download px-4" title="download" style="font-size:18px"></i></a><a href="../../uploads/documents/<?= $document['document_name']  ?>"><i class="mdi mdi-eye" title="see" style="font-size:18px"></i></a></td>
                                            </tr>
                                        </tbody>
                                <?php }
                                } ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?php if (isset($_GET['tab']) && $_GET['tab'] == 'Affiliation') {echo "active";} ?>" id="Affiliation" role="tabpanel">
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
                                    if (!password_verify($currentPassword, $publisher->password)) {
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
                                    <input type="text" disabled value="<?php echo $publisher->login ?>" class="form-control form-control-line">
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
    <div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit bank</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="myForm" method="POST">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Benefiary"> Benefiary : <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Benefiary" name="Benefiary" placeholder="Enter Benefiary" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency">Account currency : </label>
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
                                        <label for="Bank_name"> Bank name : <span class="danger">*</span> </label>
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
                                        <label for="Address3">Address <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Address3" name="Address3" placeholder="Enter Address" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Address4">Address (optional) : </label>
                                        <input type="text" class="form-control " id="Address4" name="Address4" placeholder="Enter Address 2" required> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="City2">City <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="City2" name="City2" placeholder="Enter City" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Province2">Province (optional) : </label>
                                        <input type="text" class="form-control " id="Province2" name="Province2" placeholder="Enter Province"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Zip_code2">Zip code <span class="danger">*</span> </label>
                                        <input type="text" class="form-control " id="Zip_code2" name="Zip_code2" placeholder="Enter Zip code" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="IBAN"> Account Number / IBAN : </label>
                                        <input type="text" class="form-control " id="IBAN" name="IBAN" maxlength="34" placeholder="Enter Account Number / IBAN" required> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Swift">Swift / BIC <span class="danger">*</span> </label>
                                        <input type="text" class="form-control required" id="Swift" maxlength="11" minlength="8" name="Swift" placeholder="Enter Swift / BIC" required> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Routing">Routing Number (optional) : </label>
                                        <input type="text" class="form-control " id="Routing" name="Routing" placeholder="Enter Routing Number"> </div>
                                </div>
                                <input type="text" class="form-control id" name="id" hidden>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="save3" class="save btn btn-primary"><?php echo ($trans["save"]) ?></button>
                        <button type="submit" name="addBank" class="addBank btn btn-primary"><?php echo ($trans["add"]) ?></button>
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
                            <label>File</label>
                            <div class="form-group ">
                                <input type="file" class="form-control bank " name="details" id="details">
                                <input type="file" class="form-control document" name="document" id="document">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="save4" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["save"]) ?></button>
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
                        <input type="text" class="form-control " name="id" id="idAff" hidden>
                    </div>
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
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script>
    $('#choice').change(function() {
        $('.document').toggle();
        $('.bank').toggle();
    });
    $(document).ready(function() {
        let active = <?= $publisher->active ?>;
        let login = '<?= $publisher->login ?>';
        let name = '<?= $publisher->company_name ?>';
        let update = '<?= $publisher->password_updated_at ?>';
        if (active == 0 && update == '') {
            Swal.fire({
                title: 'Default Authentification informations',
                html: 'Your login: ' + login + '<br/> your password : ' + name,
                footer: '<a href>Please change your password as soon as possible</a>'
            });
            $('#sidebarnav').on("click", 'a', function(e) {
                e.preventDefault();
            });
        }
    });
    $('#select2').select2();
    var table4 = $('#AffiliationTb').DataTable({
        dom: '<"toolbar d-inline Affiliation">frtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(".Affiliation").html('<button class="btn waves-effect waves-light btn-sm btn-secondary   addAff" data-toggle="modal" data-target="#Aff">Add affiliation</button>');

    $('.bank').toggle();
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        window.history.pushState("", "", "./my_profile.php?tab=" + e.target.hash.substr(1));
    });
    var table3 = $('#banks').DataTable({
        dom: '<"toolbar d-inline bank">frtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(".bank").html('<button class="btn waves-effect waves-light btn-sm btn-secondary   AddBNK" data-toggle="modal" data-target="#form">Add bank</button>');
    var table2 = $('#documents').DataTable({
        dom: '<"toolbar d-inline document">frtip',
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $(".document").html('<button class="btn waves-effect waves-light btn-sm btn-secondary  " data-toggle="modal" data-target="#doc">Add document</button>');
    $('.AddBNK').click(function() {
        $('.save').hide();
        $('.addBank').show();
        $('#myForm input[type=text]').val("");
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
                table3.row($('#bank_' + id)).data(data).draw();
            }
        })
    });
    $('.EditBank').click(function() {
        let id = $(this).data('id');
        $('.save').show();
        $('.id').val(id);
        $('.addBank').hide();
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
                $('#Bank_name').val(data.name);
                $('#country1 option[value=' + data.country + ']').attr('selected', 'selected');
                $('#currency option[value=' + data.Account_currency + ']').attr('selected', 'selected');
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
    var errorMsg = $("#error-msg");
    var validMsg = $("#valid-msg");
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
        $("#error-msg").html("");
        $("#error-msg").addClass("hide");
        $("#valid-msg").addClass("hide");
    };
    // on blur: validate
    $("#phone").on('blur', function() {
        reset();
        if ($("#phone").val().trim()) {
            if ($("#phone").intlTelInput("isValidNumber")) {
                $("#phone").val($("#phone").intlTelInput("getNumber"));
                $("#valid-msg").removeClass("hide");
            } else {
                $("#phone").addClass("error");
                var errorCode = $("#phone").intlTelInput("getValidationError");
                $("#error-msg").html(errorMap[errorCode]);
                $("#error-msg").removeClass("hide");
            }
        }
    });
    // on keyup / change flag: reset
    $("#phone").on('change', reset);
    $("#phone").on('keyup', reset);
</script>