<?php
$page_name = "websites";
include('header.php');
?>
<link href="../../assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css" rel="stylesheet">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="myTable" class="table table-striped dt-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo ($trans["admin"]["websites_list"]["websitestable"]["name"]) ?></th>
                                <th><?php echo ($trans["admin"]["websites_list"]["websitestable"]["url"]) ?></th>
                                <th><?php echo ($trans["admin"]["websites_list"]["websitestable"]["activity"]) ?></th>
                                <th><?php echo ($trans["admin"]["websites_list"]["websitestable"]["status"]) ?></th>
                                <th>Used Storage</th>
                                <th><?php echo ($trans["admin"]["websites_list"]["websitestable"]["actions"]) ?></th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">
                            <?php
                            $s1 = $conn->prepare("SELECT `id_website`, `name`, `url`, `activity`, `status`,storage FROM `websites` WHERE `id_account` = :ID");
                            $s1->bindParam(':ID', $id_account, PDO::PARAM_INT);
                            $s1->execute();
                            $websites = $s1->fetchAll();
                            foreach ($websites as $web) {
                                $totalsize += array_sum(array_map('filesize', glob("../../uploads/messages/pictures/*-" . $web['id_website'] . ".*")));
                                $totalsize += array_sum(array_map('filesize', glob("../../uploads/messages/files/*-" . $web['id_website'] . ".*")));
                                echo "<tr>
                                <td>" . $web['id_website'] . "</td>
                                <td>" . $web['name'] . "</td>
                                <td>" . $web['url'] . "</td>
                                <td>" . Activity($web['activity']) . "</td>
                                <td>" . Website_status($web['status']) . "</td>
                                <td>" .  number_format($totalsize / 1048576, 2) . " MB/" . $web['storage'] . " MB</td>
                                <td>
                                <a type='button' href='website.php?id=" . $web['id_website'] . "' class='btn btn-sm btn-color waves-effect waves-light'><i class='mdi mdi-pencil m-r-5'></i>" . $trans['edit'] . "</a>
                                <a type='button' href='javascript:void(0)' class='btn btn-sm btn-danger waves-effect waves-light delete-button' data-id='" . $web['id_website'] . "'><i class='mdi mdi-delete m-r-5'></i>" . $trans['delete'] . "</a>
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
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/notification.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<!-- This is data table -->
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>

<script>
    $(function() {
        $('#myTable').DataTable({
            dom: 'Bfrtip',
            responsive: true
        });
    });
    $(".delete-button").click(function() {
        const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'mr-2 btn btn-danger'
                },
                buttonsStyling: false,
            })
        swalWithBootstrapButtons.fire({
            title: '<?php echo ($trans["admin"]["websites_list"]["alert"]["title"]) ?>',
            text: "<?php echo ($trans["admin"]["websites_list"]["alert"]["subtitle"]) ?>",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '<?php echo ($trans["admin"]["websites_list"]["alert"]["confirm"]) ?>',
            cancelButtonText: '<?php echo ($trans["admin"]["websites_list"]["alert"]["cancel"]) ?>',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                var id = $(this).attr('data-id');
                var obj = $(this);
                $.ajax({
                    url: 'functions_ajax.php',
                    dataType: "json",
                    data: {
                        type: 'remove_website',
                        id: id
                    },
                    success: function(code_html, statut) {
                        alert(code_html);
                    },
                    error: function(statut) {
                        alert("Unsuccessful request");
                    }
                });
            }
        });
    });
</script>
</body>
</html>