<?php
$page_name = "Notifications";
include('header.php');
$contact = $conn->prepare("SELECT n.*,(SELECT c.pseudo FROM consultants c,users u WHERE n.id_consultant=u.id_user AND c.id_consultant=u.id_profile) as expert,(SELECT CONCAT(c.firstname,' ',c.lastname) FROM customers c,users u WHERE n.id_customer=u.id_user AND c.id_customer=u.id_profile) as customer FROM notifications n WHERE  n.id_account=:id ORDER BY n.date DESC");
$contact->bindParam(':id',  $id_account, PDO::PARAM_INT);
$contact->execute();
$notifications = $contact->fetchAll();
$stmt2 = $conn->prepare("UPDATE notifications SET seen=1 WHERE id_account = :ID");
$stmt2->bindParam(':ID', $id, PDO::PARAM_INT);
$stmt2->execute();
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Notifications</h4>
                <div class="table-responsive m-t-40">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= $trans['title']?></th>
                                <th><?= $trans['customer']?></th>
                                <th><?= $trans['consultant']?></th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="jsgrid-grid-body">
                            <?php foreach ($notifications as $notification) { ?>
                                <tr>
                                    <td><?= $notification['id_notif'] ?></td>
                                    <td><?= $notification['action'] == 1 ? $trans['complaint'] : $trans['late']  ?></td>
                                    <td><?= $notification['customer'] ?></td>
                                    <td><?= $notification['expert'] ?></td>
                                    <td><?= $notification['date'] ?></td>
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
<footer class="footer">© 2019 Private chat by Diamond services</footer>
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
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/notification.js"></script>
<!-- This is data table -->
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script>
    $(function() {
        $('#myTable').DataTable({
            "order": [
                [4, "desc"]
            ]
        });
    });
</script>
</body>
</html>