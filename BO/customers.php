<?php
$page_name = "Customers";
include('header.php');

?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Customers list</h4>
                <div class="table-responsive m-t-40">
                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Business name</th>
                                <th>Country</th>
                                <th>Email</th>
                                <th>Websites Nb</th>
                                <th>Manager</th>
                                <th>Start date</th>
                                <th>Status</th>
                                <th>Used Storage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">
                            <?php
                            $s1 = $conn->prepare("SELECT a.*, b.`firstname`, b.`lastname`, (SELECT COUNT(*) FROM `websites` WHERE `id_account` = a.`id_account`) AS nb FROM `accounts` a LEFT JOIN `managers` b ON b.`id_account` = a.`id_account`");
                            $s1->execute();
                            $accounts = $s1->fetchAll();
                            foreach ($accounts as $acc) {
                                $s1 = $conn->prepare("SELECT id_website,storage FROM `websites` WHERE id_account = :ID");
                                $s1->bindParam(':ID',$acc['id_account']);
                                $s1->execute();
                                $websites = $s1->fetchAll();
                                $totalsize = 0;
                                $storage = array_sum(array_column($websites,'storage'));
                                foreach($websites as $website){
                                    $totalsize += array_sum(array_map('filesize', glob("../uploads/messages/*-".$website['id_website'].".*")));
                                }

                                echo "<tr>
                                                <td class='text-center'>" . $acc['id_account'] . "</td>
                                                <td>" . $acc['business_name'] . "</td>
                                                <td>" . Country($acc['country']) . "</td>
                                                <td>" . $acc['emailc'] . "</td>
                                                <td>" . $acc['nb'] . "</td>
                                                <td>" . $acc['firstname'] . " " . $acc['lastname'] . "</td>
                                                <td>" . $acc['date_add'] . "</td>
                                                <td>" . Account_status($acc['status']) . "</td>
                                                <td>" .  number_format($totalsize/1048576,2) . " MB/".$storage." MB</td>
                                                <td class='text-center'>
                                                <a href='customer.php?id=" . $acc['id_account'] . "' class='view-button'><span class='fa fa-eye'></span></a>
                                                <button type='button' class='jsgrid-button jsgrid-delete-button' data-id='" . $acc['id_account'] . "'></button>
                                                </td>
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
<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/node_modules/popper/popper.min.js"></script>
<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../assets/js/custom.min.js"></script>
<!-- This is data table -->
<script src="../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="../assets/node_modules/datatables.net/buttons/dataTables.buttons.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.flash.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/jszip.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/pdfmake.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/vfs_fonts.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.html5.min.js"></script>
<script src="../assets/node_modules/datatables.net/buttons/buttons.print.min.js"></script>
<script>
    $('#example23').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    $(".jsgrid-delete-button").click(function() {
        if (!confirm('Are you sure you want to delete this customer?')) {
            return false;
        }
        var id = $(this).attr('data-id');
        var obj = $(this);
        $.ajax({
            url: 'functions_ajax.php',
            dataType: "json",
            data: {
                type: 'remove_customer',
                id: id
            },
            success: function(code_html, statut) {
                alert(code_html);
            },
            error: function(statut) {
                alert("Unsuccessful request");
            }
        });
    });
</script>
</body>

</html>