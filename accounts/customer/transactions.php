<?php
$page_name = "transactions";
include('header.php');
$s1 = $conn->prepare("SELECT *,CASE WHEN ts.content is not null then ts.content ELSE p.title end title, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount,(SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count  from transactionsc t join packages p on p.id_package=t.id_package  left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang where t.id_customer=:IDC");

$s1->bindParam(':IDC', $id_account, PDO::PARAM_INT);
$s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);

$s1->execute();
$transcations = $s1->fetchAll();
?>
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><?php echo ($trans["transactions_list"]) ?></h4>
                <div class="table-responsive m-t-40">
                    <table id="transactions" class="table table-striped table-hover dt-responsive display nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo ($trans["transactions_table"]["package"]) ?></th>
                                <th><?php echo ($trans["transactions_table"]["messages_count"]) ?></th>
                                <th><?php echo ($trans["transactions_table"]["initial_price"]) ?></th>
                                <th><?php echo ($trans["transactions_table"]["offers_count"]) ?></th>
                                <th><?php echo ($trans["transactions_table"]["total_discount"]) ?></th>
                                <th><?php echo ($trans["transactions_table"]["final_price"]) ?> </th>

                                <th>Date </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">
                            <?php foreach ($transcations as $transcation) { ?>
                                <tr>
                                    <td><?= $transcation["id_transaction"] ?></td>
                                    <td><?= $transcation["title"] ?></td>
                                    <td><?= $transcation["messages"] ?> </td>
                                    <td><?= $transcation["price"] ?><sup> <?= $transcation["currency"] ?></sup></td>
                                    <td><?= $transcation["offers_count"] ?></td>
                                    <td><?php if ($transcation["total_discount"]) {
                                            echo $transcation["total_discount"];
                                        } else {
                                            echo 0;
                                        } ?> %</td>
                                    <td><?= $transcation["final_price"] ?><sup> <?= $transcation["currency"] ?></td>
                                    <td><?= $transcation["date_add"] ?></td>
                                    <td class="text-center">
                                        <a href="invoice.php?id=<?= $transcation["id_transaction"] ?>"><button class="view-button" type="button" title="<?php echo ($trans["transactions_table"]["invoice"]) ?>"><span class="mdi mdi-print"></span></button>
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
</footer>
</div>
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
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="../../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/pages/customers.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/js/pages/update_password.js"></script>
<script>
    var balance = 0;
    $('#transactions').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
    $('.dt-button').removeClass('dt-button');
    if (unlimited == 0) {
        $.ajax({
            url: "../customerTrait.php",
            type: "POST",
            data: {
                action: "getBalance",
                customer_id: customer_id,
            },
            dataType: "json",
            success: function(dataResult) {
                if (dataResult.statusCode == 200) {
                    balance = dataResult.balance;
                    $(".header-balance-text").text(balance);
                    if (balance == 0) {
                        $("#out_of_balace").css("display", "block");
                    }
                }
            }
        });
    }
</script>
</body>

</html>