<?php
$page_name = "transactions";
include('header.php');
/* $s1 = $conn->prepare("SELECT *, CASE WHEN ts.content is not null then ts.content ELSE p.title end title,
                    (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(c.id_customer,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount,
                    (SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(c.id_customer,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count 
                    from transactionsc t join packages p on p.id_package=t.id_package join customers c on t.id_customer=c.id_customer left join translations ts on ts.table='packages' and ts.id_element=p.id_package and ts.lang=:lang where p.id_account=:ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR); */
$s1 = $conn->prepare("SELECT * FROM transactionsc c JOIN customers cu ON c.id_customer=cu.id_customer JOIN packages p ON p.id_package=c.id_package WHERE cu.id_account=:ID");
$s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s1->execute();
$transcations = $s1->fetchAll();
?>
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example23" class="display nowrap table table-hover  dt-responsive table-striped ">
                        <thead>
                            <tr>
                                <th><?= $trans['Transaction_id'] ?></th>
                                <th><?= $trans['Payment_id'] ?></th>
                                <th><?= $trans['Transaction_status'] ?></th>
                                <th><?= $trans['Transaction_Date'] ?></th>
                                <th><?= $trans['Customer_id'] ?></th>
                                <th><?= $trans['Customer_fullname'] ?></th>
                                <th><?= $trans['Customer_country'] ?></th>
                                <th><?= $trans['Amount'] ?></th>
                                <th><?= $trans['package'].' & '.$trans['offer'] ?></th>
                                <th>Action</th>
                                <th><?= $trans['Refund_amount'] ?></th>
                                <th><?= $trans['Refund_currency'] ?></th>
                                <th><?= $trans['Refund_date'] ?></th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">

                            <?php foreach ($transcations as $transcation) { ?>
                                <tr>
                                    <td><?= $transcation["id_transaction"] ?></td>  
                                    <td><?= $transcation["id_provider"] ?></td>
                                    <?php if ($transcation["status"] == 1) { ?>
                                        <td class="text-center"><span class="badge badge-success badge-pill"><?= $trans['success'] ?></span></td>
                                    <?php } else { ?>
                                        <td class="text-center"><span class="badge badge-danger badge-pill"><?= $trans['failed'] ?></span></td>
                                    <?php } ?>
                                    <td><?= $transcation["date_add"] ?></td>
                                    <td><?= $transcation["id_customer"] ?></td>
                                    <td><a href="customer.php?id=<?php echo $transcation['id_customer']  ?>" class='view-button'><?php echo $transcation["firstname"].' '.$transcation["lastname"] ?></a></td>
                                    <td><?= $trans['countries'][$transcation["country"]] ?> </td>
                                    <td><?= $transcation["final_price"] ?><sup><?= $trans['devise'][$transcation["country"]] ?></sup> </td>
                                    <?php 
                                    $s2 = $conn->prepare("SELECT * FROM offers o WHERE o.id_package=:ID");
                                    $s2->bindParam(':ID', $transcation["id_package"], PDO::PARAM_INT);
                                    $s2->execute();
                                    $offre = $s2->fetch();
                                    if ($transcation["date_add"] >= $offre["start_date"] && $transcation["date_add"] <= $offre["end_date"]) {?>
                                     <td> <?=$transcation["title"].' / '.$offre["title"] ; ?></td>
                                    <?php }else{ ?>
                                        <td> <?=$transcation["title"]?></td>
                                    <?php } ?>
                                    <td><button type="button" class="btn btn-sm waves-effect waves-light btn-color"><?php echo $trans['refund'] ?></button></td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
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
<script src="../../assets/js/notification.js"></script>
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
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>

<script>
    $('#example23').DataTable({
        dom: 'Bfrtip',
        responsive: false,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
    });
    $('.dt-button').addClass('btn waves-effect waves-light btn-sm btn-secondary');
        $('.dt-button').removeClass('dt-button');
</script>
</body>
</html>