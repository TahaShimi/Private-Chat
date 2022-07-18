<?php 
$page_name = "edit_offer";
ob_start();
include('header.php'); ?>
    <link href="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="../../assets/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
        href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css">
    <style>
    .error{font-size: 11px !important;color: #e46a76 !important;}
    #check-spinner{display: none;}
    </style>   
    <?php 
    $s1 = $conn->prepare("SELECT * FROM `websites` WHERE id_account = :ID");
    $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
    $s1->execute();
    $websites = $s1->fetchAll();

    if(isset($_GET["id"])){
        $offerId = $_GET["id"];
    }
    else{
        header('Location: offers.php?cde=202');
        exit();
    }

    $s1 = $conn->prepare("SELECT *,(SELECT count(*) FROM offers o where o.id_package=p.id_package and end_date >= CURDATE()) as offers_count FROM `packages` p WHERE p.id_account = :ID and p.active=1");
    $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
    $s1->execute();
    $packages = $s1->fetchAll();

    $s2 = $conn->prepare("SELECT *,(SELECT count(*) from offers_customers oc where oc.id_offer=o.id_offer and o.discount=100) as attached_private_offers, 
        (SELECT count(*) from transactionsc tc where tc.id_package=o.id_package) as sails_count from offers o left join translations t on t.table='offers' and t.id_element=:IDO where o.id_offer=:IDO");
    $s2->bindParam(":IDO", $offerId, PDO::PARAM_INT);
    $s2->execute();
    $offer= $s2->fetch();    
    if(!$offer){
        header('Location: offers.php?cde=203');
        exit();
    }
    elseif($offer["sails_count"] + $offer["attached_private_offers"] > 0){
        header('Location: offers.php?cde=201');
        exit();
    }
    if($offer["start_date"] != null){
        $parts = explode('-',$offer["start_date"]);
        $frontSideStartDate = $parts[1] . '/' . $parts[2] . '/' . $parts[0];
        $parts = explode('-',$offer["end_date"]);
        $frontSideEndDate = $parts[1] . '/' . $parts[2] . '/' . $parts[0];

        $frontSideDateRange = $frontSideStartDate . ' - ' . $frontSideEndDate;

    }
    else{
        $startDate = null;
        $endDate = null;
    }


    if (isset($_POST['update-offer'])) {
        $title = (isset($_POST['title']) && $_POST['title'] != '') ? htmlspecialchars($_POST['title']) : $offer["title"];
        $discount = (isset($_POST['discount']) && $_POST['discount'] != '') ? htmlspecialchars($_POST['discount']) : $offer["discount"];
        $limit = (isset($_POST['limit']) && $_POST['limit'] != '') ? htmlspecialchars($_POST['limit']) : $offer["limit"];
        $dateType = (isset($_POST['dateType']) && $_POST['dateType'] != '') ? htmlspecialchars($_POST['dateType']) : null;
        $access = (isset($_POST['offerType']) && $_POST['offerType'] != '') ? htmlspecialchars($_POST['offerType']) : $offer["access"];
        if($dateType == 1){
            $dateRange = (isset($_POST['dateRange']) && $_POST['dateRange'] != '') ? htmlspecialchars($_POST['dateRange']) : NULL;
            $dates = explode(" - ", $dateRange);
            $startDate = $dates[0];
            $endDate = $dates[1];
            $parts = explode('/',$startDate);
            $startDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
            $parts = explode('/',$endDate);
            $endDate = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
        }
        else{
            $startDate = null;
            $endDate = null;
        }
        $datec = date('Y-m-d', strtotime('now'));       
        $stmt1 = $conn->prepare("UPDATE `offers` set `title`=:ti, `discount`=:ds, `limit`=:lt, `start_date`=:sd, `end_date`=:ed,
            `updated_at`=NOW(), `access`=:ac where id_offer=:IDO");
        $stmt1->bindParam(':ti', $title, PDO::PARAM_STR);
        $stmt1->bindParam(':ds', $discount, PDO::PARAM_STR);
        $stmt1->bindParam(':lt', $limit, PDO::PARAM_INT);
        $stmt1->bindParam(':sd', $startDate, PDO::PARAM_STR);
        $stmt1->bindParam(':ed', $endDate, PDO::PARAM_STR);
        $stmt1->bindParam(':ac', $access, PDO::PARAM_INT);
        $stmt1->bindParam(':IDO', $offerId, PDO::PARAM_INT);
        if(!$stmt1->execute()){
            echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["offer_updated"]) ." </div></div>";
            unset($_POST);
        }else{
            if(isset($_POST['title_fr'])){
                $titleFr = $_POST['title_fr'];
                if($offer["content"] != null){
                    $stmtl = $conn->prepare("UPDATE translations set content=:ct where `table` like 'offers' and lang like 'fr' and id_element=:IDO");
                    $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
                    $stmtl->bindParam(':IDO', $offerId, PDO::PARAM_INT);
                    $stmtl->execute();
                }else{
                    $table= "offers";
                    $column= "title";
                    $lang= "fr";
                    $stmtl = $conn->prepare("INSERT INTO `translations`(`content`, `table`, `column`, `lang`, `id_element`) VALUES (:ct,:tb,:cl,:lg,:ie)");
                    $stmtl->bindParam(':ct', $titleFr, PDO::PARAM_STR);
                    $stmtl->bindParam(':tb', $table, PDO::PARAM_STR);
                    $stmtl->bindParam(':cl', $column, PDO::PARAM_STR);
                    $stmtl->bindParam(':lg', $lang, PDO::PARAM_STR);
                    $stmtl->bindParam(':ie', $offerId, PDO::PARAM_INT);
                    $stmtl->execute();
                }
            }
            $s2 = $conn->prepare("SELECT *,(SELECT count(*) from offers_customers oc where oc.id_offer=o.id_offer) as attached_private_offers, 
                (SELECT count(*) from transactionsc tc where tc.id_package=o.id_package) as sails_count from offers o left join translations t on t.table='offers' and t.id_element=:IDO  where o.id_offer=:IDO");
            $s2->bindParam(":IDO", $offerId, PDO::PARAM_INT);
            $s2->execute();
            $offer= $s2->fetch();    
            if(!$offer){
                header('Location: offers.php?cde=203');
                exit();
            }
            if($offer["start_date"] != null){
                $parts = explode('-',$offer["start_date"]);
                $frontSideStartDate = $parts[1] . '/' . $parts[2] . '/' . $parts[0];
                $parts = explode('-',$offer["end_date"]);
                $frontSideEndDate = $parts[1] . '/' . $parts[2] . '/' . $parts[0];

                $frontSideDateRange = $frontSideStartDate . ' - ' . $frontSideEndDate;

            }
            else{
                $startDate = null;
                $endDate = null;
            }
            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>  " . ($trans["feedback_msg"]["account_update_failed"]) ." </div></div>";
            unset($_POST);
        }
    }
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-body">
                    <form action="" id="myForm" method="POST" class="col-md-12" novalidate>  
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="title"><?php echo ($trans["admin"]["offers"]["edit_offer"]["title_en"])?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-message-draw"></span>
                                                </span>
                                            </div>
                                            <input type="text"  name="title" class="form-control" value="<?= $offer["title"] ?>" id="title" required placeholder="<?php echo ($trans["admin"]["offers"]["edit_offer"]["title_placeholder_en"])?>" required data-validation-required-message="Offer title is required">
                                            <label id="title-error" class="error col-12" for="title"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="title"><?php echo ($trans["admin"]["offers"]["edit_offer"]["title_fr"])?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-message-draw"></span>
                                                </span>
                                            </div>
                                            <input type="text"  name="title_fr" class="form-control" id="title_fr" value="<?= $offer["content"] ?>" required placeholder="<?php echo ($trans["admin"]["offers"]["edit_offer"]["title_placeholder_fr"])?>" required data-validation-required-message="Offer title is required">
                                            <label id="title-error" class="error col-12" for="title_fr"></label>
                                        </div>
                                    </div>
                                </div>                                   
                            </div>
                            <div class="row">
                                <div class="col-6 ">
                                    <div class="form-group ">
                                        <label for="discount"><?php echo ($trans["admin"]["offers"]["edit_offer"]["discount"])?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3 '>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-percent"></span>
                                                </span>
                                            </div>
                                            <input type="number" min="0" max="100" name="discount" value="<?= $offer["discount"] ?>" class="form-control" id="discount" required placeholder="<?php echo ($trans["admin"]["offers"]["edit_offer"]["discount_placeholder"])?>" required data-validation-required-message="Offer discount is required">
                                            <label id="discount-error" class="error col-12" for="discount"></label>   
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="limit"><?php echo ($trans["admin"]["offers"]["edit_offer"]["limit"])?> <span class="text-danger">*</span></label>
                                        <div class='input-group mb-3'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="mdi mdi-unfold-less"></span>
                                                </span>
                                            </div>
                                            <input type="number" name="limit" min="0" class="form-control" value="<?= $offer["limit"] ?>" id="limit" required placeholder="Offer sell limit"  value="0">
                                            <label id="limit-error" class="error col-12" for="limit"></label>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo ($trans["admin"]["offers"]["edit_offer"]["date"])?></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio1" name="dateType" value="1" class="custom-control-input" <?php if($offer["start_date"] != null){ echo "checked";}?> >
                                    <label class="custom-control-label" for="customRadio1"><?php echo ($trans["admin"]["offers"]["edit_offer"]["periodic"])?></label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio2" name="dateType" value="2" class="custom-control-input" <?php if($offer["start_date"] == null){ echo "checked";}?> >
                                    <label class="custom-control-label" for="customRadio2"><?php echo ($trans["admin"]["offers"]["edit_offer"]["always"])?></label>
                                </div>
                            </div>
                            <div class="row dateRangePickerContainer">
                                <div class="col-12">
                                    <label for="dateRange"><?php echo ($trans["admin"]["offers"]["edit_offer"]["date_range"])?>  <span class="text-danger">*</span></label>
                                    <div class='input-group mb-3'>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="ti-calendar"></span>
                                            </span>
                                        </div>
                                        <input type='text' id="dateRange"  name="dateRange" value="<?= $frontSideDateRange ?>" class="form-control buttonClass" required/>
                                        <label id="dateRange-error" class="error col-12" for="dateRange"></label>  
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo ($trans["admin"]["offers"]["edit_offer"]["access"])?> </label>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="publicOffer" name="offerType" value="1" class="custom-control-input"  <?php if($offer["access"] == 1){ echo "checked";}?> >
                                            <label class="custom-control-label" for="publicOffer"><?php echo ($trans["admin"]["offers"]["edit_offer"]["public"])?></label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="privateOffer" name="offerType" value="2" class="custom-control-input" <?php if($offer["access"] == 2){ echo "checked";}?> >
                                            <label class="custom-control-label" for="privateOffer"><?php echo ($trans["admin"]["offers"]["edit_offer"]["private"])?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="form-actions">
                            <button type="submit" name="update-offer" class="btn btn-primary waves-effect waves-light m-r-10"> <i class="mdi mdi-check"></i> <?php echo ($trans["save"])?></button>
                            <button type="button" class="btn btn-inverse"><?php echo ($trans["cancel"])?></button>
                        </div>
                    </form>
                    </div>
                    </div>
                </div>
                </div>
                </div>
            </div>
        <footer class="footer"> <?php echo  ($trans["footer"]) ?></footer>        
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
    <script src="../../assets/js/custom.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <script src="../../assets/js/pages/jasny-bootstrap.js"></script>
    <script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
    <script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
    <script src="../../assets/js/moment.js"></script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="../../assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="../../assets/node_modules/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="../../assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="../../assets/node_modules/wizard/jquery.validate.min.js"></script>
    <script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
    <script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <script src="../../assets/js/notification.js"></script>
    <script type="text/javascript">
        $("#check-offer-effect").prop("disabled", "true");
        if( $('input[type=radio][name=dateType]').val() == "1"){
            $(".dateRangePickerContainer").css("display", "none");
        }
        $( "#myForm" ).validate({
        rules: {
            messagesCount: {
            required: true,
            digits: true
            },
            'packages_ids[]': {
                required: true,
                maxlength: 1
            }
        },
        messages: {
            'packages_ids[]': {
                required: "You must check at least 1 package",
            }
        }
        });   
        $('.buttonClass').daterangepicker({
            drops: "up",
            buttonClasses: "btn",
            applyClass: "btn-info",
            cancelClass: "btn-danger",
            minDate: new Date(),
        });

        $('#customers-dtable').DataTable({
                responsive: true
            });
        
        $("#select-all-packages").change(function () {
            $("input:checkbox.packages-table").prop('checked', $(this).prop("checked"));
        });

        $("#select-all-customers").change(function () {
            $("input:checkbox.customers-table").prop('checked', $(this).prop("checked"));
        });

        $("#discount").change(function () {
            if($(this).val() > 0  && $(this).val() <= 100){
                $("#check-offer-effect").prop("disabled", false);
            }
        });

        $('input[type=radio][name=dateType]').change(function() {
        if (this.value == '1') {
            $(".dateRangePickerContainer").css("display", "block");
        }
        else if (this.value == '2') {
            $(".dateRangePickerContainer").css("display", "none");
        }
        });

        $("#check-offer-effect").click(function(){
        $(this).prop("disabled","true");
        $(this).text("Checking Offer Effect");
    
        var startDate = null;
        var endDate = null;
        var dateType = $('input[type=radio][name=dateType]').val();
        if(dateType == 1){
            var dateRange = $("#dateRange").val();
            startDate = dateRange.split(' - ')[0];
            endDate = dateRange.split(' - ')[1];
        }

        var discount = $("#discount").val();
        var accountId = <?= $id_account ?> ;
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
				dataType:"json",
				success: function(dataResult){ 
					if(dataResult.statusCode==200){               
                      console.log(dataResult);
                      $.each( dataResult.packages, function( key, value ) { 
                          var extra_discount = ((value.total_discount == null) ? 0 : parseInt(value.total_discount));
                          var discounted_price = (value.price / 100 ) * ( 100 - (parseInt(discount) + extra_discount))
                          console.log(discount + extra_discount);
                        $("#package-"+ value.id_package + "-price-effect").html(discounted_price.toFixed(2) + '<sup>'+ value.currency +'</sup>');
                      });
					}
					else if(dataResult.statusCode==201) {
                      console.log(dataResult);  
					}
				}
			});
    });
    </script>
</body>
</html>