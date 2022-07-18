<?php 
include('../init.php');
$msgR = "";
$msg = "";
session_start();
if(!empty($_SESSION['login']) && $_SESSION['login'] == "2") {
  header('Location: admin/index.php');
  exit();
} elseif(!empty($_SESSION['login']) && $_SESSION['login'] == "3") {
  header('Location: consultant/index.php');
  exit();
} elseif(!empty($_SESSION['login']) && $_SESSION['login'] == "4") {
  header('Location: customer/index.php');
  exit();
}
if(isset($_GET['token'])) {
    $token = ($_GET['token']);
    $stmt = $conn->prepare("SELECT * from password_resets pr join users u on u.id_user=pr.id_user where pr.token=:tk");
    $stmt->bindparam(":tk", $token);
    $stmt->execute();
    $request = $stmt->fetch();
    if(!$request){
        header('Location: forgot_password.php?err=204');
        exit();
    }
    else{
        $my_date = new DateTime($request["created_at"]);
        if( $my_date->format('Y-m-d') != date('Y-m-d')){
            header('Location: forgot_password.php?cde=205');
            exit();
    
        }
        else{
            if(isset($_POST['valider'])){
                if(!isset($_POST['password']) || !isset($_POST['password_confirmation'])){
                    $msgR = " Please fill in Password and Password confirmation";
                }
                else if(($_POST['password'] != $_POST['password_confirmation'])){
                    $msgR = "Wrong Password Confirmation";
                }
                else{            
                    $newHashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    $stmt2 = $conn->prepare("UPDATE users set password=:HPW,password_updated_at=NOW() where id_user=:ID");
                    $stmt2->bindparam(":ID", $request["id_user"]);
                    $stmt2->bindparam(":HPW", $newHashedPassword);
                    if($stmt2->execute()){
                        $stmt3 = $conn->prepare("DELETE from  password_resets where id_user=:ID");
                        $stmt3->bindparam(":ID", $request["id_user"]);
                        if($stmt3->execute()){
                            header('Location: login.php?cde=200');
                            exit();
                        }
                        else{
                            $msgR = "Error";
                        }
                    }
                }
            }
        }
    }
    
    
}
else{
    header('Location: forgot_password.php?cde=204');
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
    <title>Private-chat</title>
    
    <!-- page css -->
    <link rel="stylesheet" href="../assets/css/pages/login-register-lock.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.min.css" />

    <style type="text/css">

body {
    background: #f6f5f7;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    height: 100vh;
    margin: 0;
}

h1 {
    font-weight: 600;
    margin-bottom: 50px !important;
}

h2 {
    text-align: center;
}

p {
    font-size: 14px;
    font-weight: 100;
    line-height: 20px;
    letter-spacing: 0.5px;
    margin: 20px 0 30px;
}

span {
    font-size: 12px;
}

a {
    color: #333;
    font-size: 14px;
    text-decoration: none;
    margin: 15px 0;
}
.other-link {
    bottom: 40px !important;
    position: absolute;
}
button {
    position: absolute;
    bottom: 70px;
    border-radius: 20px;
    border: 1px solid #ecbc6e;
    background-color: #ffcc81;
    color: #FFFFFF;
    font-size: 12px;
    font-weight: bold;
    padding: 12px 45px;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: transform 80ms ease-in;
}

button:active {
    transform: scale(0.95);
}

button:focus {
    outline: none;
}

button.ghost {
    background-color: transparent;
    border-color: #FFFFFF;
}

form {
    background-color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 40px;
    height: 100%;
    text-align: center;
}

input {
    background-color: #eee;
    border: none;
    padding: 12px 15px;
    margin: 8px 0;
    width: 100%;
}

.container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
            0 10px 10px rgba(0,0,0,0.22);
    position: relative;
    overflow: hidden;
    width: 384px;
    max-width: 100%;
    min-height: 480px;
}

.form-container {
    position: absolute;
    top: 0;
    height: 100%;
    transition: all 0.6s ease-in-out;
}

.sign-in-container {
    left: 0;
    width: 100%;
    z-index: 2;
}

.container.right-panel-active .sign-in-container {
    transform: translateX(100%);
}

.sign-up-container {
    left: 0;
    width: 50%;
    opacity: 0;
    z-index: 1;
}

.container.right-panel-active .sign-up-container {
    transform: translateX(100%);
    opacity: 1;
    z-index: 5;
    animation: show 0.6s;
}

@keyframes show {
    0%, 49.99% {
        opacity: 0;
        z-index: 1;
    }
    
    50%, 100% {
        opacity: 1;
        z-index: 5;
    }
}

.overlay-container {
    position: absolute;
    top: 0;
    left: 50%;
    width: 50%;
    height: 100%;
    overflow: hidden;
    transition: transform 0.6s ease-in-out;
    z-index: 100;
}

.container.right-panel-active .overlay-container{
    transform: translateX(-100%);
}

.overlay {
    background: #FF416C;
    background: -webkit-linear-gradient(to right, #ffe0b0, #dcaf6e);
    background: linear-gradient(to right, #ffe0b0, #dcaf6e);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 0 0;
    color: #FFFFFF;
    position: relative;
    left: -100%;
    height: 100%;
    width: 200%;
    transform: translateX(0);
    transition: transform 0.6s ease-in-out;
}

.container.right-panel-active .overlay {
    transform: translateX(50%);
}

.overlay-panel {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 40px;
    text-align: center;
    top: 0;
    height: 100%;
    width: 50%;
    transform: translateX(0);
    transition: transform 0.6s ease-in-out;
}

.overlay-left {
    transform: translateX(-20%);
}

.container.right-panel-active .overlay-left {
    transform: translateX(0);
}

.overlay-right {
    right: 0;
    transform: translateX(0);
}

.container.right-panel-active .overlay-right {
    transform: translateX(20%);
}

.social-container {
    margin: 20px 0;
}

.social-container a {
    border: 1px solid #DDDDDD;
    border-radius: 50%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    margin: 0 5px;
    height: 40px;
    width: 40px;
}
.login-box {
    width: auto;
    margin: 0 auto;
}
.login-register {
    padding: 3% 0;
}
.logo-box {
    padding-bottom: 3%;
    margin-left: auto;
    margin-right: auto;
    display: block;
}
    </style>
    
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Private-chat</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <section id="wrapper" class="login-register" style="background: url(../assets/images/background/bg_chat.jpg) #fff repeat-x bottom;">
        <div class="login-box">
            <div class="card-body">
                    <img src="../assets/images/logo4.png" class="logo-box">
                    <div class="container" id="container">
                        <div class="form-container sign-in-container">
                            <form  id="loginform" action="" method="POST">
                                <h1>Reset Password</h1>
                                <span></span>
                                <input  class="form-control" name="password" id="password" type="password" required="" placeholder="Password"/>
                                <input  class="form-control" name="password_confirmation" id="password_confirmation" type="password" required="" placeholder="Password Confirmation"/>

                               
                                <button type="submit" name="valider" >Save Password</button>
                                <div class="other-link"><a href="login.php">Sign In</a></div>
                                    <?php if ($msgR != "") {
                                    echo '<div class="login-info text-danger text-center"><p>'.$msgR.'</p><div class="clear"> </div></div>';
                                    } ?>
                            </form>
                  
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
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });

        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

    
    </script>
</body>
</html>