<?php 
$msgR = "";
include('../init.php');
session_start();
if(!empty($_SESSION['login']) && intval($_SESSION['login']) == 1) {
  header('Location: index.php');
  exit();
}
if(isset($_POST['valider'])) {
  if (empty($_POST['login']) || empty($_POST['pwd'])) {
    $msgR = "Please fill in the 2 fields.";
  } else {
    $login = $_POST["login"];
    $pwd = $_POST["pwd"];
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `login` = :LOGIN");
    $stmt->bindParam(':LOGIN', $login, PDO::PARAM_STR);
    $stmt->execute();
    $total = $stmt->rowCount();
    $result = $stmt->fetchObject();
    if ($total == 0) {
      $msgR = "This username does not exist !";
    } elseif (!password_verify( $pwd, $result->password)) {
      $lastday = $result->lastday;
      $nbr_essai = (int)$result->nbr_essai;
      if ($lastday == date("Y-m-d")) {
        $msgR = "You have reached the attempt quota, try tomorrow !";
      } else {
        $msgR = "The password entered is incorrect !";
        $nbr_essai++;
        if ($nbr_essai == 3) {
          $stmt2 = $conn->prepare("UPDATE `users` SET `nbr_essai` = 0, `lastday` = CURDATE() WHERE `id_user` = :ID");
          $stmt2->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
          $stmt2->execute();
        } else {
          $stmt2 = $conn->prepare("UPDATE `users` SET `nbr_essai` = :nbr WHERE `id_user` = :ID");
          $stmt2->bindParam(':nbr', $nbr_essai, PDO::PARAM_INT);
          $stmt2->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
          $stmt2->execute();
        }
      }
    } elseif ($login == $result->login && (password_verify( $pwd, $result->password))) {
      session_start();
      $_SESSION['login'] = $result->profile;
      $_SESSION['user'] = $result->username;
      $_SESSION['id_user'] = $result->id_user;

      $stmt2 = $conn->prepare("UPDATE `users` SET `status` = 1 WHERE `id_user` = :ID");
      $stmt2->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
      $stmt2->execute();

      header('Location: index.php');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Private chat</title>
    
    <!-- page css -->
    <link href="../assets/css/pages/login-register-lock.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.min.css" rel="stylesheet">
    
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="skin-default card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Private chat</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <section id="wrapper">
        <div class="login-register" style="background-image:url(../assets/images/background/bg-login.jpg);">
            <div class="login-box card">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="loginform" action="" method="POST">
                        <h3 class="box-title m-b-20 text-center">Sign In</h3>
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input class="form-control" name="login" type="text" required="" placeholder="Username"> </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" name="pwd" type="password" required="" placeholder="Password"> </div>
                        </div>
                        <div class="form-group text-center">
                            <div class="col-xs-12">
                                <button class="btn btn-block btn-lg btn-primary btn-rounded" type="submit" name="valider">Log In</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                                <div class="social">
                                    <a href="javascript:void(0)" class="btn  btn-facebook" data-toggle="tooltip" title="Login with Facebook"> <i aria-hidden="true" class="fab fa-facebook"></i> </a>
                                    <a href="javascript:void(0)" class="btn btn-googleplus" data-toggle="tooltip" title="Login with Google"> <i aria-hidden="true" class="fab fa-google-plus"></i> </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="form-group">
                      <div class="col-md-12">
                        <?php if ($msgR != "") {
                          echo '<div class="login-info text-danger text-center"><p>'.$msgR.'</p><div class="clear"> </div></div>';
                        } ?>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
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
    <!--Custom JavaScript -->
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        // ============================================================== 
        // Login and Recover Password 
        // ============================================================== 
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
    </script>
    
</body>
</html>