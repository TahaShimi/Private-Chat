<?php
$page_name = "offers";
include('header.php');
$msgRe = (isset($_GET["cde"])) ? $_GET["cde"] : null;
$month = date("n", strtotime("this month"));
$year = date("Y", strtotime("this year"));

$s1 = $conn->prepare("SELECT CASE WHEN tso.content is not null then tso.content ELSE o.title end offer_title, o.status as status, o.id_offer as id_offer, o.discount as discount,o.limit as offer_limit, o.start_date as start_date, o.end_date as end_date,o.created_at as created_at,CASE WHEN ts.content is not null then ts.content ELSE p.title end package_title, pp.price as price, pp.currency as currency, (SELECT count(*) from offers_customers oc where oc.id_offer=o.id_offer and o.discount=100) as attached_private_offers,  (SELECT count(*) from transactionsc tc where tc.id_package=p.id_package) as sails_count FROM `offers` o join `packages` p on o.id_package=p.id_package JOIN `packages_price` pp ON  pp.id_package=p.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang left join translations tso on tso.table='offers' and tso.id_element=o.id_offer and tso.lang=:lang WHERE pp.primary=1 AND o.id_account=:ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
$s1->execute();
$offers = $s1->fetchAll();
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
<?php if ($msgRe == 202 || $msgRe == 203) {
    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["offer_not_found"]) . "</div></div>";
} elseif ($msgRe == 201) {
    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["offer_cant_updated"]) . " </div></div>";
} ?>
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!--<button type="button" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Create New</button>-->
                <div class="table-responsive">
                    <table id="example23" class="display nowrap table table-hover dt-responsive table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["title"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["package"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["price"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["discount"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["discounted_price"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["limit"]) ?> </th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["date_start"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["date_end"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["sales_count"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["created_at"]) ?></th>
                                <th><?php echo ($trans["admin"]["offers"]["offers_table"]["actions"]) ?></th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">
                            <?php foreach ($offers as $offer) { ?>
                                <?php
                                if ($offer["end_date"] != null) {
                                    $parts = explode('-', $offer["end_date"]);
                                    $frontSideEndDate = $parts[2] . '/' . $parts[1] . '/' . $parts[0];
                                }
                                ?>
                                <tr id="row-<?= $offer['id_offer'] ?>">
                                    <td><?= $offer['id_offer'] ?></td>
                                    <td><?= $offer['offer_title'] ?></td>
                                    <td><?= $offer['package_title'] ?></td>
                                    <td><?= $offer['price']  ?> <sup><?= $offer['currency']  ?> </sup></td>
                                    <td><?php echo $offer['discount'] . "%" ?></td>
                                    <td><?php echo (100 - $offer['discount']) * ($offer["price"] / 100) ?><sup><?= $offer['currency']  ?> </sup></td>
                                    <td> <?= $offer['offer_limit'] ?> </td>
                                    <td><?php echo ($offer["start_date"] == null) ?  "--" : $offer["start_date"]; ?></td>
                                    <td><?php echo ($offer["end_date"] == null) ?  "--" : $offer["end_date"]; ?> </td>
                                    <td><?php echo $offer['sails_count'] + $offer['attached_private_offers'] ?></td>
                                    <td> <?= $offer['created_at'] ?></td>
                                    <td class=''>
                                        <?php if (intval($offer['sails_count'] + $offer['attached_private_offers']) == 0) { ?>
                                            <a href="offer_edit.php?id=<?= $offer["id_offer"] ?>" type="button" class="btn btn-sm waves-effect waves-light btn-color"><i class="mdi mdi-pencil m-r-5"></i><?= $trans['edit'] ?></a>
                                            <a href="javascript:void(0)" type='button' class='btn btn-sm btn-danger waves-effect waves-light delete_offer' id="delete-offer-<?= $offer['id_offer'] ?>" data-id="<?= $offer['id_offer'] ?>"><i class="mdi mdi-lock m-r-5"></i><?= $trans['delete'] ?></a>
                                        <?php } ?>

                                        <?php if ($offer['end_date'] == null || $offer['end_date'] > date("Y-m-d")) { ?>
                                            <a href="javascript:void(0)" type='button' class='btn btn-warning btn-sm waves-effect waves-light end_offer' id="ended-offer-<?= $offer['id_offer'] ?>" data-id="<?= $offer['id_offer'] ?>"><i class="mdi mdi-stop m-r-5"></i><?= $trans['stop'] ?></a>
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
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer> <!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
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
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script>
    $('#example23').DataTable({
        dom: 'Bfrtip',
        responsive: false,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
    $('.dt-button').removeClass('dt-button');
    $(document).ready(function() {
        $(".delete_offer").click(function() {
            var offerId = $(this).data('id');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'mr-2 btn btn-danger'
                },
                buttonsStyling: false,
            })

            swalWithBootstrapButtons.fire({
                title: '<?php echo ($trans["admin"]["offers"]["alert"]["title"]) ?>',
                text: '<?php echo ($trans["admin"]["offers"]["alert"]["subtitle"]) ?>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '<?php echo ($trans["admin"]["offers"]["alert"]["confirm"]) ?>',
                cancelButtonText: '<?php echo ($trans["admin"]["offers"]["alert"]["cancel"]) ?>',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "../offerTrait.php",
                        type: "POST",
                        data: {
                            action: "deleteOffer",
                            offerId: offerId,
                        },
                        dataType: "json",
                        success: function(dataResult) {
                            if (dataResult.statusCode == 200) {
                                $('#row-' + offerId).remove();
                                swalWithBootstrapButtons.fire(
                                    "<?php echo ($trans['admin']['offers']['alert']['confirmed']) ?>",
                                    "<?php echo ($trans['admin']['offers']['alert']['confirmed_subtitle']) ?>",
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
                        "<?php echo ($trans['admin']['offers']['alert']['canceled']) ?>",
                        "<?php echo ($trans['admin']['offers']['alert']['canceled_subtitle']) ?>",
                        'error'
                    )
                }
            })
        });
        $(".end_offer").click(function() {
            var offerId = $(this).data('id');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'mr-2 btn btn-danger'
                },
                buttonsStyling: false,
            })
            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You won't end this offer!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, end it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "../offerTrait.php",
                        type: "POST",
                        data: {
                            action: "endOffer",
                            offerId: offerId,
                        },
                        dataType: "json",
                        success: function(dataResult) {
                            if (dataResult.statusCode == 200) {
                                $('#ended-offer-' + offerId).hide();
                                swalWithBootstrapButtons.fire(
                                    'finished!',
                                    'The offer has been deleted.',
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
                        'Cancelled',
                        'Operation canceled',
                        'error'
                    )
                }
            })
        });
    })
</script>
</body>
</html>