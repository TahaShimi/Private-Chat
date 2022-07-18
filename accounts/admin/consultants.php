<?php
$page_name = "consultants";
ini_set("display_errors", 1);
include('header.php'); ?>
<div class="row el-element-overlay consultants">
    <?php
    $s1 = $conn->prepare("SELECT `id_consultant`, `gender`, `firstname`, `lastname`, `pseudo`, `photo` FROM `consultants` WHERE `id_account` = :ID");
    $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
    $s1->execute();
    $total = $s1->rowCount();
    $consultants = $s1->fetchAll();
    if ($total == 0) {
        echo '<div class="empty-box"><div class="empty-body text-center">
        <h1>204</h1>
        <h3 class="text-uppercase">Empty consultants list !</h3>
        <p class="text-muted m-t-30 m-b-30">YOU SEEM TO BE TRYING TO ADD CONSULTANTS</p>
        <a href="consultant_add.php" class="btn btn-primary btn-rounded waves-effect waves-light m-b-40">' . ($trans["add_consultant"]) . '</a>
        </div></div>';
    } else {
        foreach ($consultants as $cons) {
            $image = (isset($cons["photo"]) && $cons["photo"] != NULL) ? "../uploads/consultants/" . $cons["photo"] : ((intval($cons["gender"]) == 2) ? "../../assets/images/consultant_women.jpg" : "../../assets/images/consultant_men.jpg");
            echo '<div class="col-lg-2 col-md-6" id="' . $cons["id_consultant"] . '"><div class="card"><div class="el-card-item">
            <div class="el-card-avatar el-overlay-1"><img src="' . $image . '" alt="user" style="height: 18rem"/><div class="el-overlay"><ul class="el-info">
            <li><a class="btn default btn-outline" href="consultant.php?id=' . $cons["id_consultant"] . '"><i class="mdi mdi-pencil"></i></a></li>
            <li><a class="btn default btn-outline delete" href="javascript:void(0);" data-id="' . $cons["id_consultant"] . '"><i class="mdi mdi-delete"></i></a></li>
            </ul></div></div><div class="el-card-content"><h4 class="box-title">' . $cons["firstname"] . ' ' . $cons["lastname"] . '</h4><small>' . $cons["pseudo"] . '</small><br/></div></div></div></div>';
        }
    }
    ?>
</div>
</div>
</div>
<footer class="footer"><?php echo ($trans["footer"]) ?></footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/js/notification.js"></script>
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
<!-- Magnific popup JavaScript -->
<script src="../../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup.min.js"></script>
<script src="../../assets/node_modules/Magnific-Popup-master/jquery.magnific-popup-init.js"></script>
<script type="text/javascript">
    $(".delete").click(function() {
        if (!confirm('Are you sure you want to delete this consultant?')) {
            return false;
        }
        var id = $(this).attr('data-id');
        $.ajax({
            url: 'functions_ajax.php',
            dataType: "json",
            data: {
                type: 'remove_consultant',
                id: id
            },
            success: function(code_html, statut) {
                $('.consultants').prepend(code_html);
                $('#' + id).remove();
            },
            error: function(statut) {
                alert("Unsuccessful request");
            }
        });
    });
</script>
</body>
</html>