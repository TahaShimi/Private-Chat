<?php
$page_name = "packages";
include('header.php');
$msgRe = (isset($_GET["cde"])) ? $_GET["cde"] : null;
$month = date("n", strtotime("this month"));
$year = date("Y", strtotime("this year"));

$s1 = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title,(CASE WHEN p.id_website IS NOT NUll THEN (SELECT name FROM websites w where p.id_website=w.id_website) WHEN p.id_website IS NUll THEN 'No Website' END) as name,(SELECT COUNT(*) from offers o where p.id_package=o.id_package) as offers_count,(SELECT COUNT(*) from transactionsc tc where tc.id_package=p.id_package) as sails_count FROM packages p JOIN packages_price pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang WHERE pp.primary=1 AND p.id_account=:ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);

$s1->execute();
$packages = $s1->fetchAll();
/*$payments = array();
foreach ($websites as $web) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://gopaid.pro/API/payments//by_month/'.$month.'/'.$year);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Basic YXBpX2tleTpjYTFmMjk1ZGM2NmE5NDY4MDllYTZhMzZhNzZjOTA1MA==',
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $payments0 = curl_exec($ch);
    curl_close($ch);
    array_push($payments, json_decode($payments0, true));
}*/
?>
<style>
    .mdi-check {color: green;font-size: 20px;}
    .mdi-close {color: red;font-size: 20px;}
</style>
<?php if ($msgRe == 202 || $msgRe == 203) {
    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["package_not_found"]) . " </div></div>";
} elseif ($msgRe == 201) {
    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["package_cant_updated"]) . " </div></div>";
} ?>
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!--<button type="button" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Create New</button>-->
                <div class="table-responsive">
                    <table id="example23" class="display nowrap table table-hover dt-responsive table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["title"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["website"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["price"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["messages"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["start_at"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["end_at"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["sells_count"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["offers"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["displayed"]) ?></th>
                                <th><?php echo ($trans["admin"]["packages"]["packages_table"]["actions"]) ?></th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">
                            <?php foreach ($packages as $package) { ?>
                                <tr>
                                    <td><?= $package['id_package'] ?></td>
                                    <td><?= $package['title'] ?></td>
                                    <td><?= $package['name'] ?></td>
                                    <td><?= $package['price'] ?><sup> <?= $package['currency'] ?></sup></td>
                                    <td><?= $package['messages'] ?> </td>
                                    <td><?= $package['start_date'] ?></td>
                                    <td><?= $package['end_date'] ?></td>
                                    <td> <?= $package['sails_count'] ?> </td>
                                    <td> <?= $package['offers_count'] ?> </td>
                                    <td class="text-center"> <?= $package['visible'] == 1 ? '<i class="mdi mdi-check"></i>' : '<i class="mdi mdi-close"></i>' ?> </td>
                                    <td class=''>
                                        <?php if ($package['sails_count'] == 0) { ?>
                                            <a type="button" href="package_edit.php?id=<?= $package["id_package"] ?>" class="btn btn-sm btn-color waves-effect waves-light"><i class="mdi mdi-pencil m-r-5"></i><?= $trans['edit'] ?></a>
                                        <?php } ?>
                                        <?php if ($package['status']) { ?>
                                            <a href='javascript:void(0)' type='button' class='btn btn-sm btn-danger waves-effect waves-light end_package' id="ended-package-<?= $package['id_package'] ?>" data-id="<?= $package['id_package'] ?>"><i class="mdi mdi-lock m-r-5"></i><?= $trans['delete'] ?></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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
    <?php echo ($trans["footer"]) ?>
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
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
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
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>

<script>
    $('#example23').DataTable({
        dom: 'Bfrtip',
        responsive: false,
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
    $('.dt-button').removeClass('dt-button');

    $(document).ready(function() {
        $(".end_package").click(function() {
            var packageId = $(this).data('id');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'mr-2 btn btn-danger'
                },
                buttonsStyling: false,
            })

            swalWithBootstrapButtons.fire({
                title: '<?php echo ($trans["admin"]["packages"]["alert"]["title"]) ?>',
                text: "<?php echo ($trans["admin"]["packages"]["alert"]["subtitle"]) ?>",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '<?php echo ($trans["admin"]["packages"]["alert"]["confirm"]) ?>',
                cancelButtonText: '<?php echo ($trans["admin"]["packages"]["alert"]["cancel"]) ?>',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        url: "../packageTrait.php",
                        type: "POST",
                        data: {
                            action: "endPackage",
                            packageId: packageId,
                        },
                        dataType: "json",
                        success: function(dataResult) {
                            if (dataResult.statusCode == 200) {
                                $("#endeed-package-" + packageId).hide();
                                swalWithBootstrapButtons.fire(
                                    '<?php echo ($trans["admin"]["packages"]["alert"]["confirmed"]) ?>',
                                    '<?php echo ($trans["admin"]["packages"]["alert"]["confirmed_subtitle"]) ?>.',
                                    'success'
                                )
                            } else if (dataResult.statusCode == 201) {

                            }
                        }
                    });

                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        '<?php echo ($trans["admin"]["packages"]["alert"]["canceled"]) ?>',
                        '<?php echo ($trans["admin"]["packages"]["alert"]["canceled_subtitle"]) ?>',
                        'error'
                    )
                }
            })
        });
    })
</script>
</body>
</html>