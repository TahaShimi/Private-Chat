<?php
$page_name = "Payments";
include('header.php');

$month = date("n", strtotime("this month"));
$year = date("Y", strtotime("this year"));

$s1 = $conn->prepare("SELECT `id_website`, `name`, `id_shop` FROM `websites` WHERE `id_account` = :ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->execute();
$websites = $s1->fetchAll();
$payments = array();
foreach ($websites as $web) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://gopaid.pro/API/payments//by_month/' . $month . '/' . $year);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Basic YXBpX2tleTpjYTFmMjk1ZGM2NmE5NDY4MDllYTZhMzZhNzZjOTA1MA==',
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $payments0 = curl_exec($ch);
    curl_close($ch);
    array_push($payments, json_decode($payments0, true));
}
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Payments list</h4>
                <div class="table-responsive m-t-40">
                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Wesbite</th>
                                <th>Customer</th>
                                <th>Paid amount</th>
                                <th>Currency</th>
                                <th>Provider fee</th>
                                <th>Retention amount</th>
                                <th>Refund amount</th>
                                <th>Receivable amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">
                            <?php
                            foreach ($payments['payments'] as $pay) {
                                $cons_prct = floatval($pay['cons_prct']);
                                $ret = (floatval($pay['montant']) - floatval($pay['refund_mnt'])) * $cons_prct / 100;
                                $fee = floatval($pay['fixed_fee']) + (floatval($pay['montant']) * (floatval($pay['percent_fee']) / 100));
                                $net = ((floatval($pay['montant']) - $ret) - floatval($pay['refund_mnt'])) - $fee;
                                if (floatval($pay['refund_mnt']) == floatval($pay['montant'])) {
                                    $net = (floatval($pay['montant']) - floatval($pay['refund_mnt'])) - $fee;
                                }

                                echo "<tr>
                                                <td>" . $pay['id_payment'] . "</td>
                                                <td>" . $pay['id_shop'] . "</td>
                                                <td>" . $pay['prenom'] . " " . $pay['prenom'] . "</td>
                                                <td>" . $pay['montant'] . "</td>
                                                <td>" . $pay['devise'] . "</td>
                                                <td>" . $fee . "</td>
                                                <td>" . $ret . "</td>
                                                <td>" . $pay['refund_mnt'] . "</td>
                                                <td>" . $net . "</td>
                                                <td>" . $pay['date_pay'] . "</td>
                                                </tr>";
                            }
                            ?>
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
    © 2019 Private chat by Diamond services
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
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/notification.js"></script>
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
<script>
    $('#example23').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
</script>
</body>

</html>