<?php 
include('init.php');
$msgR = "";
$msgRe = (isset($_GET["cde"])) ? $_GET["cde"] : null;
$msg = "";
session_start();
if(!empty($_SESSION['login']) && $_SESSION['login'] == "2") {
  header('Location: accounts/admin/index.php');
  exit();
} elseif(!empty($_SESSION['login']) && $_SESSION['login'] == "3") {
  header('Location: accounts/consultant/index.php');
  exit();
} elseif(!empty($_SESSION['login']) && $_SESSION['login'] == "4") {
  header('Location: accounts/customer/index.php');
  exit();
}
if(isset($_POST['request'])) {
  if (empty($_POST['login'])) {
    $msgR = "Please enter your email or username.";
  } else {
    $login = $_POST["login"];
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `login` = :LOGIN");
    $stmt->bindParam(':LOGIN', $login, PDO::PARAM_STR);
    $stmt->execute();
    $total = $stmt->rowCount();
    $result = $stmt->fetchObject();
    if ($total == 0) {
      $msgR = "This user does not exist !";
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
      $_SESSION['id_account'] = $result->id_profile;
      $stmt3 = $conn->prepare("UPDATE `users` SET `status` = 1 WHERE `id_user` = :ID");
      $stmt3->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
      $stmt3->execute();

      if ($result->profile == 2) {
        $stmt4 = $conn->prepare("SELECT `status`, `id_account` FROM `accounts` WHERE `id_account` = :ID");
        $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
        $stmt4->execute();
        $stat = $stmt4->fetchObject();
        $_SESSION['account_status'] = $stat->status;
        $_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
        $_SESSION['id_company'] = $stat->id_account;
        header('Location: accounts/admin/index.php');

      } elseif ($result->profile == 3) {
        $stmt4 = $conn->prepare("SELECT `status`, `photo`, `firstname`, `lastname`, `id_account` FROM `consultants` WHERE `id_consultant` = :ID");
        $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
        $stmt4->execute();
        $stat = $stmt4->fetchObject();
        $_SESSION['account_status'] = $stat->status;
        $_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
        $_SESSION['avatar'] = $stat->photo;
        $_SESSION['id_company'] = $stat->id_account;
        header('Location: accounts/consultant/index.php');
        
      } elseif ($result->profile == 4) {
        $stmt4 = $conn->prepare("SELECT `status`, `photo`, `firstname`, `lastname`, `balance`, `id_account` FROM `customers` WHERE `id_customer` = :ID");
        $stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
        $stmt4->execute();
        $stat = $stmt4->fetchObject();
        $_SESSION['account_status'] = $stat->status;
        $_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
        $_SESSION['balance'] = $stat->balance;
        $_SESSION['id_company'] = $stat->id_account;
        $_SESSION['avatar'] = $stat->photo;
        header('Location: accounts/customer/index.php');
      }      
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
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Private-chat</title>
    
    <!-- page css -->
    <link rel="stylesheet" href="assets/css/pages/login-register-lock.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.min.css" />

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
    bottom: 50px !important;
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
@media (max-width: 767px){
    .sign-up-container{
        display: contents;
        width : 0%;
    }
    .sign-in-container{
        width : 100%;
    }
    .overlay-container{
        display: none;
    }
    .container{
        margin-top: 30px;
    }
    #signUp2, #signIn2{
        //poition: absolute;
        bottom : 10px;
        background-color: transparent;
        color: #ffcc81;
        display: block;
    }
    .container.right-panel-active .sign-up-container {
        transform: translateX(1000%);
        opacity: 1;
        z-index: 5;
        animation: show 0.6s;
    }
    .sign-up-form, #loginform{
        margin-top: 80px;
    }
    form {
        justify-content: normal;
    }
    

}

@media (max-width: 384px){
    .logo-box{
        width:70%;
    }
}

.form-control {
    margin: 15px;
}
.language-switch{
    position:absolute;
    top:2px;
    right:2px;
    z-index:999;
}

.list-languages{
list-style: none;
}

.language-item{
    padding: 0;
    padding-left:3px;
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
    
      <div class="form-group language-switch">
     				<ul class="list-languages">
     				<?php if(isset($_COOKIE["lang"]) && $_COOKIE["lang"] == "fr"){?>
     				<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class=""><img src="assets/images/fr.png" width="24px" height="24px"> Francais </span></a>
                            <div class="dropdown-menu language-item" aria-labelledby="">
                                <a class="dropdown-item " onclick="update_language('en')" href="#"><span class=""><img src="assets/images/en.png" width="24px" height="24px"> Anglais </span> </a>
                            </div>
                    </li>	
     				<?php } else{ ?>
     				<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class=""><img src="assets/images/en.png" width="24px" height="24px"> English </span></a>
                            <div class="dropdown-menu language-item" aria-labelledby="">
                                <a class="dropdown-item " onclick="update_language('fr')" href="#"><span class=""><img src="assets/images/fr.png" width="24px" height="24px"> French </span> </a>
                            </div>
                    </li>
     				<?php } ?>
                    </ul>
     </div>
     
    <section id="wrapper" class="login-register" style="background: url(assets/images/background/bg_chat.jpg) #fff repeat-x bottom;">
        <div class="login-box">
            <div class="card-body">
                    <img src="assets/images/logo4.png" class="logo-box">
                    <div class="container" id="container">
                        <div class="form-container sign-in-container">
                            <form  id="loginform" action="" method="POST">
                                <?php if($msgRe == 205){?>
                                    <div class="login-info text-danger text-center">The link you used is expired, please request another one<div class="clear"> </div></div>
                                <?php }elseif($msgRe == 204){?>
                                    <div class="login-info text-danger text-center">The link you used is unsupported, please request another one<div class="clear"> </div></div>
                                <?php }elseif($msgRe == 200){?>
                                    <div class="login-info text-success text-center">Your password is updated, you can login now<div class="clear"> </div></div>
                                <?php }?>
                                <h1><?php echo utf8_encode($trans["index"]["forgot_password"])?></h1>
                                <span><?php echo utf8_encode($trans["index"]["forgot_password_subtitle"])?></span>
                                <input  class="form-control" name="login" id="login" type="text" required="" placeholder="Email or username"/>
                                <button type="button" name="valider" id="forgot-password"><?php echo utf8_encode($trans["index"]["send_email"])?></button>
                                <div class="other-link"><a href="index.php"><?php echo utf8_encode($trans["index"]["sign_in_btn"])?></a></div>
                                <div class="login-info text-success text-center"><p id="success-msg"></p><div class="clear"> </div></div>
                                <div class="login-info text-danger text-center"><p id="error-msg"></p><div class="clear"> </div></div>
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
    <script src="assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="assets/node_modules/popper/popper.min.js"></script>
    <script src="assets/node_modules/bootstrap/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });

        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        function update_language(str){
        	document.cookie = "lang="+str;
        	location.reload();
        	
        }

        $(document).ready(function(){
            $("#forgot-password").click(function(){
                var username = $("#login").val();
                var resend = false;
                if(username != ""){
                    $.ajax({
                        url: "accounts/userTrait.php",
                        type: "POST",
                        data: {
                            action: "forgot_password",
                            username: username,
                            resend : resend
                        },
                        dataType:"json",
                        success: function(dataResult){ 
                            if(dataResult.statusCode==200){               
                                $("#success-msg").text(dataResult.response.message);
                                $("#forgot-password").prop("disabled",true);
                            }
                            else if(dataResult.statusCode==201) {
                                if(dataResult.response.code == 202){
                                    $("#error-msg").text(dataResult.response.message + " Click Resend to get new email");
                                    $("#forgot-password").text("Resend Link");
                                    resend = true;
                                }
                                else{
                                    $("#error-msg").text(dataResult.response.message);
                                }
                                
                            }
                        }
                    });
                }
               
            })
        });
    </script>
</body>
</html>