<?php
$page_name = 'add_advertiser';
include('header.php');
$stmt = $conn->prepare("SELECT * FROM accounts WHERE country IN (SELECT country FROM publisher_Affiliation pa,users u WHERE pa.id_publisher=u.id_profile AND u.id_user=:id )");
$stmt->bindParam(':id', intval($_SESSION['id_user']));
$stmt->execute();
$accounts = $stmt->fetchAll();
?>
<link src="../../assets/node_modules/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <h3 class="box-title m-b-0"><?php echo ($trans["add_advertiser"]) ?></h3>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="home2" role="tabpanel">
                            <div class="p-20">
                                <?php
                                if (isset($_POST['add'])) {
                                    $tab = explode('-', $_POST['Advertiser']);
                                    $Advertiser = $tab[1];
                                    $websites = $_POST['websites'];
                                    $stmt1 = $conn->prepare("SELECT * FROM publisher_advertiser WHERE id_advertiser=:ad AND id_publisher=:id");
                                    $stmt1->bindParam(':ad', intval($Advertiser));
                                    $stmt1->bindParam(':id', intval($_SESSION['id_user']));
                                    $stmt1->execute();
                                    $user = $stmt1->fetchObject();
                                    if (!$user) {
                                        $stmt1 = $conn->prepare("INSERT INTO `publisher_advertiser`(`id_advertiser`,`id_publisher`, `date_add`) VALUES (:ad,:id,NOW())");
                                        $stmt1->bindParam(':ad', intval($Advertiser));
                                        $stmt1->bindParam(':id', intval($_SESSION['id_user']));
                                        $stmt1->execute();
                                        $stmt = $conn->prepare("INSERT INTO `publishers_programs`(`id_program`,`id_publisher`, `date_start`,status) VALUES (:pr,:id,NOW(),0)");
                                        foreach ($websites as $website) {
                                            $stmt->bindParam(':id', intval($_SESSION['id_user']));
                                            $stmt->bindParam(':pr', intval($website));
                                            $stmt->execute();
                                        }
                                        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Advertiser Added successfully </div></div>";
                                    } else {
                                        $programsAffected = 0;
                                        $stmt = $conn->prepare("SELECT * FROM `publishers_programs` WHERE id_publisher=:id AND id_program=:pr AND status=0");
                                        foreach ($websites as $website) {
                                            $stmt->bindParam(':id', intval($_SESSION['id_user']));
                                            $stmt->bindParam(':pr', intval($website));
                                            $stmt->execute();
                                            $prog = $stmt->fetch();
                                            if (!$prog) {
                                                $stmt1 = $conn->prepare("INSERT INTO `publishers_programs`(`id_program`,`id_publisher`, `date_start`,status) VALUES (:pr,:id,NOW(),0)");
                                                $stmt1->bindParam(':id', intval($_SESSION['id_user']));
                                                $stmt1->bindParam(':pr', intval($website));
                                                $stmt1->execute();
                                                $programsAffected++;
                                            }
                                        }
                                        if ($programsAffected > 0) {
                                            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Advertiser already exist " . $programsAffected . " Programs Added successfully </div></div>";
                                        }else{
                                            echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Advertiser and Programs already exist</div></div>";
                                        }
                                    }
                                    unset($_POST);
                                }
                                ?>
                                <form action="" method="POST">
                                    <div class="row">
                                        <div class="form-group col-md-6  ">
                                            <label for="Advertiser"><?= $trans['publisher']['Advertiser'] ?> Code</label>
                                            <div class="input-group">
                                                <input name="Advertiser" id="Advertiser" class="form-control" aria-describedby="inputGroup-sizing-sm" />
                                                <div class="input-group-append">
                                                    <button class="input-group-text advert" id="inputGroup-sizing-sm"><?= $trans['verify'] ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="websites"><?= $trans['publisher']['Advertiser_program'] ?></label>
                                            <select name="websites[]" id="websites" class="form-control select2 " multiple>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" name="add" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["add"]) ?></button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
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
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<script type="text/javascript">
    $(".select2").select2();
    $(".advert").click(function(e) {
        e.preventDefault();
        let val = $("#Advertiser").val();
        if (val != "") {
            $.ajax({
                url: 'functions_ajax.php',
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'getWebsites',
                    val: val
                },
                success: function(data) {
                    $('#autoComplete').empty();
                    if (data.existe) {
                        $.each(data.websites, function() {
                            $('#websites').append('<option value="' + this.id + '">' + this.name + '</option>');
                        });
                        Swal.fire({
                            type: 'success',
                            title: 'Code Correct',
                            footer: '<a href>You can choose your programs</a>'
                        });
                    } else {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: 'your code is wrong!',
                            footer: '<a href>Please contact the administrator</a>'
                        })
                    }
                }
            })
        }
    });
</script>
</body>
</html>