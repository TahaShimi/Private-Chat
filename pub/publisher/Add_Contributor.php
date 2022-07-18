<?php
$page_name = 'add_contributor';
include('header.php');
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <h3 class="box-title m-b-0"><?php echo ($trans["add_contributor"]) ?></h3>
            <hr>
            <?php
            if (isset($_POST['add'])) {
                $stmt = $conn->prepare("SELECT * FROM `users` u WHERE u.`login`=:lg AND u.profile=6 ");
                $stmt->bindParam(':lg', $_POST['email']);
                $stmt->execute();
                if ($stmt->fetchObject()) {
                    echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Email already exist</div></div>";
                } else {
                    $Pseudo = $_POST['Pseudo'];
                    $UserName = $_POST['UserName'];
                    $password = $_POST['password'];
                    $email = $_POST['email'];
                    $pwd = password_hash($password, PASSWORD_BCRYPT);
                    $stmt1 = $conn->prepare("INSERT INTO contributors (`pseudo`,`username`,`id_publisher`,`date_add`,`email`) VALUES (:tt,:urd,:id,NOW(),:em)");
                    $stmt1->bindParam(':tt', $Pseudo);
                    $stmt1->bindParam(':urd', $UserName);
                    $stmt1->bindParam(':em', $email);
                    $stmt1->bindParam(':id', intval($_SESSION['id_user']));
                    $stmt1->execute();
                    $last_id = $conn->lastInsertId();

                    $stmt1 = $conn->prepare("INSERT INTO `users`(`login`,`password`, `profile`,`id_profile`,`date_add`,`active`,`status`) VALUES (:tt,:ur,6,:id,NOW(),1,1)");
                    $stmt1->bindParam(':tt', $Pseudo);
                    $stmt1->bindParam(':ur', $pwd);
                    $stmt1->bindParam(':id', intval($last_id));
                    $stmt1->execute();
                    echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Contributor Added successfully </div></div>";

                    unset($_POST);
                }
            }
            ?>
            <form action="" method="POST">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="Pseudo">Pseudo</label>
                        <input type="text" name="Pseudo" class="form-control" id="Pseudo">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="UserName"><?= $trans['publisher']['userName'] ?></label>
                        <input type="text" name="UserName" class="form-control" id="UserName">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="text" name="email" class="form-control" id="email">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password"><?= $trans['publisher']['password'] ?></label>
                        <input type="password" name="password" class="form-control" id="password">
                    </div>
                </div>
                <hr>
                <button type="submit" name="add" class="btn btn-primary waves-effect waves-light m-r-10"><?php echo ($trans["add"]) ?></button>
                <button type="reset" class="btn btn-secondary waves-effect waves-light"><?php echo ($trans["cancel"]) ?></button>
            </form>
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
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
</body>
</html>