<?php
$page_name = "invoice";
include('header.php');
$s1 = $conn->prepare("SELECT *, CASE WHEN ts.content is not null then ts.content ELSE p.title end title, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(t.id_customer,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount, (SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(t.id_customer,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count from transactionsc t join packages p on p.id_package=t.id_package left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang where t.id_transaction=:IDT");
$s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
$s1->bindParam(':IDT', $_GET["id"], PDO::PARAM_INT);
$s1->execute();
$transcation = $s1->fetch();

$s2 = $conn->prepare("SELECT * from accounts a where id_account=:IDA");
$s2->bindParam(':IDA', $account->id_account, PDO::PARAM_INT);
$s2->execute();
$company = $s2->fetch();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body printableArea">
            <h3><b><?php echo ($trans["invoice"]) ?></b> <span class="pull-right">#<?= $transcation["id_transaction"] ?></span></h3>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-left">
                        <address>
                            <h3> &nbsp;<b class="text-danger"><?= $company["business_name"] ?></b></h3>
                            <p class="text-muted m-l-5"><?= $company["address"] ?>,
                                <br /> <?= $company["city"] ?>,
                                <br /> <?= $countries[$company["country"]] ?></p>
                        </address>
                    </div>
                    <div class="pull-right text-right">
                        <address>
                            <h3><?php echo ($trans["to"]) ?>,</h3>
                            <h4 class="font-bold"><?= $account->firstname . ' ' . $account->lastname ?>,</h4>
                            <p class="text-muted m-l-30"><?= $account->address ?>,
                                <br /> <?= $countries[$account->country] ?></p>
                            <p class="m-t-30"><b><?php echo ($trans["invoice_date"]) ?> :</b> <i class="mdi mdi-calendar"></i> <?= $transcation["date_add"] ?></p>
                        </address>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive m-t-40" style="clear: both;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th><?php echo ($trans["invoice_table"]["item"]) ?></th>
                                    <th class="text-right"><?php echo ($trans["invoice_table"]["messages_count"]) ?></th>
                                    <th class="text-right"><?php echo ($trans["invoice_table"]["total_discount"]) ?></th>
                                    <th class="text-right"><?php echo ($trans["invoice_table"]["total_price"]) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><?= $transcation["id_transaction"] ?></td>
                                    <td><?= $transcation["title"] ?></td>
                                    <td class="text-right"><?= $transcation["messages"] ?> </td>
                                    <td class="text-right"> <?= $transcation["total_discount"] ?> %</td>
                                    <td class="text-right"> <?= $transcation["final_price"] ?> <sup><?= $transcation["currency"] ?></sup></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="pull-right m-t-30 text-right">
                        <!-- <p>Sub - Total amount: $13,848</p>
                                        <p>vat (10%) : $138 </p>-->
                        <hr>
                        <h3><b><?php echo ($trans["invoice_table"]["total"]) ?> :</b> <?= $transcation["final_price"] ?> <sup><?= $transcation["currency"] ?></h3>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="text-right">
                        <button id="print" class="btn btn-default btn-outline" type="button"> <span><i class="mdi mdi-print"></i> <?php echo ($trans["print"]) ?></span> </button>
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
<script>
    var conn = new WebSocket(wsCurEnv);
    conn.onopen = function(e) {
        conn.send(JSON.stringify({
            command: "attachAccount",
            account: sender,
            role: sender_role,
            name: sender_pseudo,
            sender_avatar: sender_avatar,
            id_group: id_group
        }));
    };
</script>
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
<!-- This is data table -->
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/pages/customers.js"></script>
<script src="../../assets/js/pages/update_password.js"></script>
<script>
    var balance = 0;
</script>
</body>
</html>