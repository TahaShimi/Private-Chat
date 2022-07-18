<?php
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
    margin: 0;
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
    width: 768px;
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
    width: 50%;
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
    <section id="wrapper" class="login-register" style="background: url(bg_chat.jpg) #fff repeat-x bottom;">
        <div class="login-box">
            <div class="card-body">
                <?php 
                ini_set("display_errors", 1);
                $msg = "";
                    if (isset($_POST['add'])) {
                        include('init.php');
                        $business_name = (isset($_POST['business_name']) && $_POST['business_name'] != '') ? htmlspecialchars($_POST['business_name']) : NULL;
                        $country = (isset($_POST['country']) && $_POST['country'] != '') ? $_POST['country'] : NULL;
                        $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
                        $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                        $datec = date('Y-m-d', strtotime('now'));

                        $stmt1 = $conn->prepare("INSERT INTO `accounts`(`business_name`, `registration`, `taxid`, `address`, `code_postal`, `city`, `country`, `phone`, `emailc`, `website`, `date_add`, `date_end`, `status`) VALUES (:bus,NULL,NULL,NULL,NULL,NULL,:cnt,NULL,:em,:web,:dt,NULL,1)");
                        $stmt1->bindParam(':bus', $business_name, PDO::PARAM_STR);
                        $stmt1->bindParam(':cnt', $country, PDO::PARAM_STR);
                        $stmt1->bindParam(':em', $emailc, PDO::PARAM_STR);
                        $stmt1->bindParam(':web', $website, PDO::PARAM_STR);
                        $stmt1->bindParam(':dt', $datec, PDO::PARAM_STR);
                        $stmt1->execute();
                        $last_id = $conn->lastInsertId();
                        $affected_rows = $stmt1->rowCount();

                        if ($affected_rows != 0) {
                            $stmt2 = $conn->prepare("INSERT INTO `managers`(`gender`, `firstname`, `lastname`, `phonep`, `emailp`, `id_account`) VALUES (NULL,NULL,NULL,NULL,NULL,:ID)");
                            $stmt2->bindParam(':ID', $last_id, PDO::PARAM_INT);
                            $stmt2->execute();

                            $stmt4 = $conn->prepare("INSERT INTO `accounts_settings`(`consultant`, `customer`, `id_account`) VALUES (NULL,NULL,:ID)");
                            $stmt4->bindParam(':ID', $last_id, PDO::PARAM_INT);
                            $stmt4->execute();

                            $login = $emailc;
                            $pwd = strtotime('now')."-".$last_id;
                            $stmt3 = $conn->prepare("INSERT INTO `users`(`username`, `login`, `password`, `profile`, `id_profile`, `date_add`, `picture`, `active`, `last_connect`, `lastday`, `nbr_essai`) VALUES (:us,:lo,:pw,2,:ID,:dt,NULL,1,NULL,NULL,NULL)");
                            $stmt3->bindParam(':us', $business_name, PDO::PARAM_INT);
                            $stmt3->bindParam(':lo', $login, PDO::PARAM_STR);
                            $stmt3->bindParam(':pw', $pwd, PDO::PARAM_STR);
                            $stmt3->bindParam(':ID', $last_id, PDO::PARAM_INT);
                            $stmt3->bindParam(':dt', $datec, PDO::PARAM_STR);
                            $stmt3->execute();

                            $msg = "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The account has been created successfully<br><a href='https://private-chat.pro/accounts/login.php'>Log in to your account</a></div></div>";
                        } else {
                            $msg = "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The account has not been created </div></div>";
                        }
                        unset($_POST);
                    }
                    ?>
                    <img src="assets/images/logo4.png" class="logo-box">
                    <div class="container" id="container">
                        <div class="form-container sign-up-container">
                            <form action="#">
                                <h1>Create Account</h1>
                                <span>use your real email account</span>
                                <input type="text" placeholder="Name" />
                                <input type="email" placeholder="Email" />
                                <input type="password" placeholder="Password" />
                                <button>Sign Up</button>
                            </form>
                        </div>
                        <div class="form-container sign-in-container">
                            <form action="#">
                                <h1>Sign in</h1>
                                <span>use your account access credentials</span>
                                <input type="email" placeholder="Email" />
                                <input type="password" placeholder="Password" />
                                <a href="#">Forgot your password?</a>
                                <button>Sign In</button>
                            </form>
                        </div>
                        <div class="overlay-container">
                            <div class="overlay">
                                <div class="overlay-panel overlay-left">
                                    <h1>Welcome Back!</h1>
                                    <p>To keep connected with us please login with your personal info</p>
                                    <button class="ghost" id="signIn">Sign In</button>
                                </div>
                                <div class="overlay-panel overlay-right">
                                    <h1>Hello, Friend!</h1>
                                    <p>Enter your personal details and start chatting with us</p>
                                    <button class="ghost" id="signUp">Sign Up</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php if ($msg != "") {
                    echo $msg;
                } ?>
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

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });
    </script>
</body>
</html>