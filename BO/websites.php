<?php 
$page_name = "Websites";
include('header.php'); ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Websites list</h4>
                                <div class="table-responsive m-t-40">
                                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>URL</th>
                                                <th>Activity</th>
                                                <th>Customer</th>
                                                <th>Status</th>
                                                <th>Used Storage</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="jsgrid-grid-body">
                                            <?php 
                                            $s1 = $conn->prepare("SELECT a.`id_website`, a.`name`, a.`url`, a.`activity`, b.`business_name`,a.storage FROM `websites` a LEFT JOIN `accounts` b ON a.`id_account` = b.`id_account`");
                                            $s1->execute();
                                            $websites = $s1->fetchAll();
                                            foreach ($websites as $web) {
                                                $totalsize = array_sum(array_map('filesize', glob("../uploads/messages/*-".$web['id_website'].".*")));
                                                echo "<tr>
                                                <td class='text-center'>".$web['id_website']."</td>
                                                <td>".$web['name']."</td>
                                                <td>".$web['url']."</td>
                                                <td>".Activity($web['activity'])."</td>
                                                <td>".$web['business_name']."</td>
                                                <td>".Website_status($web['status'])."</td>
                                                <td>" .  number_format($totalsize/1048576,2) . " MB/".$web['storage']." MB</td>
                                                <td class='text-center'>
                                                <a href='website.php?id=".$web['id_website']."' class=''><i class='fa fa-eye'></i></a>
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
    </script>
</body>
</html>