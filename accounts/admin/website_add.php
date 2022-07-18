<?php 
$page_name = 'add_website';
include('header.php'); ?>
<div class="row">
    <?php if ($account_status != 2) {
        echo '<div class="col-md-12 msg_bloc"><h2>your account is not approved yet</h2></div>';
    } ?>
    <div class="col-md-12">
        <div class="card card-body p-0">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#home2" role="tab"><span class="hidden-sm-up"><i class="ti-write"></i></span> <span class="hidden-xs-down">General informations</span></a> </li>
                        <li class="nav-item desactive"> <a class="nav-link" data-toggle="tab" href="#profile2" role="tab"><span class="hidden-sm-up"><i class="ti-shopping-cart"></i></span> <span class="hidden-xs-down">GoPaid</span></a> </li>
                        <li class="nav-item desactive"> <a class="nav-link" data-toggle="tab" href="#messages2" role="tab"><span class="hidden-sm-up"><i class="ti-layout-accordion-list"></i></span> <span class="hidden-xs-down">Landing page</span></a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="home2" role="tabpanel">
                            <div class="p-20">
                                <?php 
                                if (isset($_POST['add'])) {
                                    $title = (isset($_POST['title']) && $_POST['title'] != '') ? htmlspecialchars($_POST['title']) : NULL;
                                    $url_directory = (isset($_POST['url_directory']) && $_POST['url_directory'] != '') ? htmlspecialchars($_POST['url_directory']) : NULL;
                                    $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                                    $activity = (isset($_POST['activity']) && $_POST['activity'] != '') ? $_POST['activity'] : NULL;
                                    $return_url = (isset($_POST['return_url']) && $_POST['return_url'] != '') ? htmlspecialchars($_POST['return_url']) : NULL;
                                    $datec = date('Y-m-d', strtotime('now'));

                                    $stmt1 = $conn->prepare("INSERT INTO `websites`(`name`,`url_directory`, `url`, `activity`, `return_url`, `payment`, `payment_receipt`, `payment_notification`, `languages`, `default_language`, `date_add`, `status`, `id_account`,`storage`,`rights`,`max_size`,`max_time`) VALUES (:tt,:urd,:ur,:ac,:ret,0,0,0,NULL,NULL,:dt,1,:ID,0,'[0]',0,1)");
                                    $stmt1->bindParam(':tt', $title, PDO::PARAM_STR);
                                    $stmt1->bindParam(':urd', $url_directory, PDO::PARAM_STR);
                                    $stmt1->bindParam(':ur', $website, PDO::PARAM_STR);
                                    $stmt1->bindParam(':ac', $activity, PDO::PARAM_STR);
                                    $stmt1->bindParam(':ret', $return_url, PDO::PARAM_STR);
                                    $stmt1->bindParam(':dt', $datec, PDO::PARAM_STR);
                                    $stmt1->bindParam(':ID', $id_account, PDO::PARAM_INT);
                                    $stmt1->execute();
                                    $last_id = $conn->lastInsertId();
                                    $affected_rows = $stmt1->rowCount();

                                    if ($affected_rows > 0) {
                                        /* $stmt2 = $conn->prepare("INSERT INTO `websites_landing`(`id_website`) VALUES (:ID)");
                                        $stmt2->bindParam(':ID', $last_id, PDO::PARAM_INT);
                                        $stmt2->execute();

                                        if (!file_exists('../../landing-page/'.$url_directory)) {
                                            mkdir('../../landing-page/'.$url_directory, 0755, true);
                                            copy('/home/privatec/public_html/landing-page/assets/html/index.html', '/home/privatec/public_html/landing-page/'.$url_directory.'/index.html');
                                        } */
                                        echo "<div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The website has been added successfully <br><a href='wesbite.php?id=".$last_id."' class='text-muted'>Click to view this website details</a></div>";
                                    } else {
                                        echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> Failed to add this website ! </div>";
                                    }
                                    unset($_POST);
                                }
                                ?>
                                <form action="" method="POST">
                                    <div class="form-group">
                                        <label for="webInput1"><?php echo  ($trans["admin"]["add_website"]["name"]) ?></label>
                                        <input type="text" name="title" class="form-control" id="webInput1">
                                    </div>
                                    <div class="form-group">
                                        <label for="webInput2"><?php echo  ($trans["admin"]["add_website"]["website"]) ?></label>
                                        <input type="url" name="website" class="form-control" id="webInput2">
                                    </div>
                                    <div class="form-group">
                                        <label for="activity"><?php echo  ($trans["admin"]["add_website"]["activity"]) ?></label>
                                        <select name="activity" id="activity" class="form-control  select-search">
                                            <option></option>
                                            <option value="ecommerce"><?php echo  ($trans["admin"]["activities"]["ecommerce"]) ?></option>
                                            <option value="studies"><?php echo  ($trans["admin"]["activities"]["studies"]) ?></option>
                                            <option value="advice"><?php echo  ($trans["admin"]["activities"]["advice"]) ?></option>
                                            <option value="it_com"><?php echo  ($trans["admin"]["activities"]["it_com"]) ?></option>
                                            <option value="business_services"><?php echo  ($trans["admin"]["activities"]["business_services"]) ?></option>
                                            <option value="administration"><?php echo  ($trans["admin"]["activities"]["administration"]) ?></option>
                                            <option value="maintenance"><?php echo  ($trans["admin"]["activities"]["maintenance"]) ?></option>
                                            <option value="support"><?php echo  ($trans["admin"]["activities"]["support"]) ?></option>
                                            <option value="legal"><?php echo  ($trans["admin"]["legal"]) ?></option>
                                            <option value="medical_service"><?php echo  ($trans["admin"]["activities"]["medical_service"]) ?></option>
                                            <option value="other"><?php echo  ($trans["admin"]["activities"]["other"]) ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="webInput22"><?php echo  ($trans["admin"]["add_website"]["pc_folder_name"]) ?></label>
                                        <input type="text" name="url_directory" class="form-control" id="webInput22" value="">
                                        <small id="webInput22Help" class="form-text text-muted"><?php echo  ($trans["admin"]["add_website"]["pc_folder_name_note"]) ?></small>
                                    </div>
                                    <div class="form-group">
                                        <label for="webInput3"><?php echo  ($trans["admin"]["add_website"]["return_url"]) ?></label>
                                        <input type="url" name="return_url" class="form-control" id="webInput3">
                                        <small id="webInput3Help" class="form-text text-muted"><?php echo  ($trans["admin"]["add_website"]["return_url_note"]) ?></small>
                                    </div>
                                    <br>
                                    <hr>
                                    <button type="submit" name="add" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo  ($trans["add"]) ?></button>
                                    <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo  ($trans["cancel"]) ?></button>
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
   <?php echo  ($trans["footer"]) ?>
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
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<script src="../../assets/js/pages/jasny-bootstrap.js"></script>
<script src="../../assets/node_modules/bootstrap-switch/bootstrap-switch.min.js"></script>
<script src="../../assets/node_modules/dropify/dropify.min.js"></script>
<script type="text/javascript">
    $('.dropify').dropify();
    $(".select2").select2();
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
    $('input[name=payment_service]').on('switchChange.bootstrapSwitch', function (event, state) {
        if (state) {
            $('input[name=payment_service]').val(1);
            $('#payment_bloc').removeClass('hide');
        } else {
            $('input[name=payment_service]').val(0);
            $('#payment_bloc').addClass('hide');
        }
    });
    $('#webInput1').change(function() {
        String.prototype.sansAccent = function(){
            var accent = [
        /[\300-\306]/g, /[\340-\346]/g, // A, a
        /[\310-\313]/g, /[\350-\353]/g, // E, e
        /[\314-\317]/g, /[\354-\357]/g, // I, i
        /[\322-\330]/g, /[\362-\370]/g, // O, o
        /[\331-\334]/g, /[\371-\374]/g, // U, u
        /[\321]/g, /[\361]/g, // N, n
        /[\307]/g, /[\347]/g, // C, c
        ];
        var noaccent = ['A','a','E','e','I','i','O','o','U','u','N','n','C','c'];
        
        var str = this;
        for(var i = 0; i < accent.length; i++){
            str = str.replace(accent[i], noaccent[i]);
        }
        
        return str;
    }
    var str = $(this).val();
    var str2 = str.toLowerCase();
    var str3 = str2.replace(" ", "-");
    var str4 = str3.sansAccent();
    $('#webInput22').val(str4);
    $('#webInput22Help span').text(str4);
});
    $('#webInput22').change(function() {
        $('#webInput22Help span').text($(this).val());
    });

</script>
</body>
</html>