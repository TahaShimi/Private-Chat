<?php 
$page_folder = "Consultants";
$page_name = "Consultant";
include('header.php'); ?>
<style>
.bootstrap-tagsinput {
    width: 100%;
}
</style>
                <div class="row">
                    <?php 
                    $id_consultant = "";
                    if (isset($_GET['id'])) {
                        $id_consultant = intval($_GET['id']);
                    }
                    if ($id_consultant == 0) {
                        echo '<section id="wrapper" class="col-md-12 error-page m-t-40">
                        <div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">Oops! Something went wrong !</h3><p class="text-muted m-t-30 m-b-30">We will work on the resolution right away.</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Return to the dashboard</a></div></div></section>';
                    } else {
                        $stmt = $conn->prepare("SELECT * FROM `consultants` WHERE `id_consultant` = :ID AND `id_account` = :IDA");
                        $stmt->bindParam(':ID', $id_consultant, PDO::PARAM_INT);
                        $stmt->bindParam(':IDA', $id_account, PDO::PARAM_INT);
                        $stmt->execute();
                        $total = $stmt->rowCount();
                        $result = $stmt->fetchObject();
                        if ($total == 0) {
                            echo '<section id="wrapper" class="col-md-12 error-page m-t-40"><div class="error-box"><div class="error-body text-center"><h1 class="text-danger">404</h1><h3 class="text-uppercase">Consultant does not exist !</h3><p class="text-muted m-t-30 m-b-30">We could not find the page you are looking for.</p><a href="index.php" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Return to the dashboard</a> </div></div></section>';
                        } else {
                            $s0 = $conn->prepare("SELECT `id_website`, `name` FROM `websites` WHERE `id_account` = :ID");
                            $s0->bindParam(':ID', $id_account, PDO::PARAM_INT);
                            $s0->execute();
                            $websites_rows = $s0->rowCount();
                            $websites = $s0->fetchAll();
                    ?>
                    <div class="col-md-7">
                        <div class="card card-body">
                            <h3 class="box-title m-b-30">General informations</h3>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
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
                                        $websites = (isset($_POST['websites'])) ? implode(",",$_POST['websites']) : NULL;

                                        if (isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"] != "") {
                                            $dirLogo = '../uploads/consultants/';
                                            $uploadImg= removeAccents($_FILES["photo"]["name"]);
                                            $uploadImgTmp= removeAccents($_FILES["photo"]["tmp_name"]);
                                            $fileData1 = pathinfo(basename($uploadImg));
                                            $Imgnom = basename($uploadImg, ".".$fileData1['extension']);
                                            $photo = substr($Imgnom,0,25)."-".$id_account. '.' . $fileData1['extension'];
                                            $target_path1 = ($dirLogo.$photo);
                                            while(file_exists($target_path1)){
                                                $photo = substr($Imgnom,0,25)."-".$id_account. '.' . $fileData1['extension'];
                                                $target_path1 = ($dirLogo.$photo);
                                            }
                                            move_uploaded_file($uploadImgTmp,$target_path1);
                                        }

                                        $stmt2 = $conn->prepare("UPDATE `consultants` SET `gender`=:gd,`firstname`=:fn,`lastname`=:ln,`pseudo`=:pd, `emailc`=:em, `phone`=:ph0,`photo`=:ph,`profile_url`=:ur,`websites`=:wb WHERE `id_consultant`=:ID");
                                        $stmt2->bindParam(':gd', $gender, PDO::PARAM_INT);
                                        $stmt2->bindParam(':fn', $first_name, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ln', $last_name, PDO::PARAM_STR);
                                        $stmt2->bindParam(':pd', $pseudo, PDO::PARAM_STR);
                                        $stmt2->bindParam(':em', $emailc, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ph0', $phone, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ph', $photo, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ur', $website, PDO::PARAM_STR);
                                        $stmt2->bindParam(':wb', $websites, PDO::PARAM_STR);
                                        $stmt2->bindParam(':ID', $id_consultant, PDO::PARAM_INT);
                                        $stmt2->execute();
                                        $affected_rows = $stmt2->rowCount();

                                        if ($affected_rows != 0) {
                                            echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The consultant informations has been updated successfully </div>";
                                            $stmt->execute();
                                            $result = $stmt->fetchObject();
                                        } else {
                                            echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The consultant informations has not been updated successfully </div>";
                                        }
                                        unset($_POST);
                                    }
                                    ?>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label class="control-label">Gender</label>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input" <?php if ($result->gender == 1) {echo "checked";} ?>>
                                                <label class="custom-control-label" for="customRadio1">Male</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input" <?php if ($result->gender == 2) {echo "checked";} ?>>
                                                <label class="custom-control-label" for="customRadio2">Female</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput1">First name</label>
                                            <input type="text" name="first_name" class="form-control" id="acctInput1" value="<?php echo $result->firstname; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput2">Last name</label>
                                            <input type="text" name="last_name" class="form-control" id="acctInput2" value="<?php echo $result->lastname; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput3">Pseudo</label>
                                            <input type="text" name="pseudo" class="form-control" id="acctInput3" value="<?php echo $result->pseudo; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="example-email" class="col-md-12">Email</label>
                                            <input type="email" value="<?php echo $result->emailc; ?>" class="form-control form-control-line" name="example-email" id="example-email">
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Phone</label>
                                            <input type="text" value="<?php echo $result->phone; ?>" class="form-control form-control-line">
                                        </div>
                                        <div class="form-group">
                                            <label for="input-file">Photo</label>
                                            <input type="file" name="photo" id="input-file" class="dropify" />
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput4">Website profile url</label>
                                            <input type="url" name="website" class="form-control" id="acctInput4" value="<?php echo $result->profile_url; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="websites">Websites / App</label>
                                            <select id="websites" class="select2 m-b-10 select2-multiple" style="width: 100%" multiple="multiple" name="websites[]" data-placeholder="Choose websites">
                                                <option></option>
                                                <?php if ($websites_rows > 0) {
                                                    $arr = explode(",", $result->websites);
                                                    foreach ($websites as $web) {
                                                        if (in_array($web['id_website'], $arr)) {
                                                            echo "<option value='".$web['id_website']."' selected>".$web['name']."</option>";
                                                        } else {
                                                            echo "<option value='".$web['id_website']."'>".$web['name']."</option>";
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                        <br><hr>
                                        <button type="submit" name="update1" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card card-body">
                            <h3 class="box-title m-b-0">Contact section</h3>
                            <p class="text-muted m-b-30 font-13"> Add contact bloc.</p>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <?php 
                                    if (isset($_POST['update2'])) {
                                        $title = (isset($_POST['title']) && $_POST['title'] != '') ? htmlspecialchars($_POST['title']) : NULL;
                                        $description = (isset($_POST['description']) && $_POST['description'] != '') ? htmlspecialchars($_POST['description']) : NULL;
                                        $expertise = (isset($_POST['expertise'])) ? implode(",",$_POST['expertise']) : NULL;
                                        $rating = (isset($_POST['rating']) && $_POST['rating'] != '') ? floatval($_POST['rating']) : NULL;
                                        $real_rating = (isset($_POST['real_rating']) && $_POST['real_rating'] == 'on') ? 1 : 0;
                                        $facebook_url = (isset($_POST['facebook_url']) && $_POST['facebook_url'] != '') ? htmlspecialchars($_POST['facebook_url']) : NULL;
                                        $instagram_url = (isset($_POST['instagram_url']) && $_POST['instagram_url'] != '') ? htmlspecialchars($_POST['instagram_url']) : NULL;

                                        $stmt2 = $conn->prepare("UPDATE `consultants` SET `contact_title`=:tt,`contact_desc`=:ds,`contact_expertise`=:ln,`contact_rating`=:pd,`real_rating`=:rr,`facebook`=:fb,`instagram`=:ins WHERE `id_consultant`=:ID");
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
                                            echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The consultant informations has been updated successfully </div>";
                                            $stmt->execute();
                                            $result = $stmt->fetchObject();
                                        } else {
                                            echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The consultant informations has not been updated successfully </div>";
                                        }
                                        unset($_POST);
                                    }
                                    ?>
                                    <form action="" method="POST">
                                        <div class="form-group">
                                            <label for="contInput1">Title</label>
                                            <input type="text" name="title" class="form-control" id="contInput1" value="<?php echo $result->contact_title; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="contInput2">Description</label>
                                            <textarea name="description" class="form-control" id="contInput2"><?php echo $result->contact_desc; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Expertise</label>
                                            <div class="tags-default">
                                                <input id="tagsinput" type="text" name="expertise" data-role="tagsinput" placeholder="add tags" value="<?php echo $result->contact_expertise; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="contInput3">Rating</label>
                                            <input type="number" name="rating" id="contInput3" class="form-control" min="0" max="5" step="0.5" placeholder="from 0 to 5" value="<?php echo $result->contact_rating; ?>" >
                                        </div>
                                        <div class="form-group">
                                            <label class="custom-control custom-checkbox m-b-0">
                                                <input type="checkbox" class="custom-control-input" name="rating" <?php if ($result->real_rating == 1) {echo "checked";} ?>>
                                                <span class="custom-control-label">Real rating ?</span>
                                            </label>
                                        </div>
                                        <br>
                                        <hr>
                                        <h5 class="text-center">Social media profiles</h5>
                                        <br>
                                        <div class="form-group">
                                            <label for="contInput4">Facebook profile url</label>
                                            <input type="url" name="facebook_url" class="form-control" id="contInput4" value="<?php echo $result->facebook; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="contInput6">Instagram profile url</label>
                                            <input type="url" name="instagram_url" class="form-control" id="contInput6" value="<?php echo $result->instagram; ?>">
                                        </div>
                                        <br><hr>
                                        <button type="submit" name="update2" class="btn btn-primary waves-effect waves-light m-r-10">Update</button>
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>
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
</footer>        <!-- ============================================================== -->
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
    <!--Custom JavaScript -->
    <script src="../../assets/js/custom.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <script src="../../assets/js/pages/jasny-bootstrap.js"></script>
    <script src="../../assets/node_modules/dropify/dropify.min.js"></script>
    <script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
    <script src="../../assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
    <script type="text/javascript">
        photo = "<?php echo $result->photo; ?>";
        if (photo != null && photo != "") {
            $("#input-file").attr("data-default-file", "../uploads/consultants/"+photo);
        }
        $(".select2").select2();
        $('.dropify').dropify();
        $('#tagsinput').tagsinput({
            confirmKeys: [13, 188]
        });
        $('.bootstrap-tagsinput input').on('keypress', function(e){
            if (e.keyCode == 13){
                e.keyCode = 188;
                e.preventDefault();
            };
        });
    </script>
</body>
</html>
