<?php
include('../init.php');
$msgR = "";
$msgRe = (isset($_GET["cde"])) ? $_GET["cde"] : null;
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
if (isset($_POST['valider'])) {
	if (empty($_POST['login']) || empty($_POST['pwd'])) {
		$msgR = utf8_encode($trans["index"]["fill_all"]);
	} else {
		$login = $_POST["login"];
		$pwd = $_POST["pwd"];
		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `login` = :LOGIN");
		$stmt->bindParam(':LOGIN', $login, PDO::PARAM_STR);
		$stmt->execute();
		$total = $stmt->rowCount();
		$result = $stmt->fetchObject();
		if ($total == 0) {
			$msgR = utf8_encode($trans["index"]["username_not_exist"]);
		} elseif (!password_verify($pwd, $result->password)) {
			$msgR = utf8_encode($trans["index"]["password_incorrect"]);
		} elseif ($login == $result->login && (password_verify($pwd, $result->password))) {
			$stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm   WHERE `id_user` = :ID");
			$stmt3->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
			$stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$stmt3->execute();
			if ($result->profile == 5) {
				$stmt = $conn->prepare("SELECT * FROM `publishers` WHERE `id_publisher` = :id");
				$stmt->bindParam(':id', intval($result->id_profile), PDO::PARAM_STR);
				$stmt->execute();
				$user = $stmt->fetchObject();
			} elseif ($result->profile == 6) {
				$stmt = $conn->prepare("SELECT * FROM `contributors` WHERE `id_contributor` = :id");
				$stmt->bindParam(':id', intval($result->id_profile), PDO::PARAM_STR);
				$stmt->execute();
				$user = $stmt->fetchObject();
				$_SESSION['pseudo'] = $user->pseudo;
			}
			if ($user->date_end) {
				$msgR = 'your account is blocked .please contact the administrator';
			} else {
				$_SESSION['login'] = $result->profile;
				$_SESSION['id_user'] = $result->id_user;
				$_SESSION['id_account'] = $result->id_profile;
				$_SESSION['lang'] = $result->lang;
				setcookie("lang", $result->lang, time() + 2 * 24 * 60 * 60);
				if (isset($_POST['remember'])) {
					setcookie("login", $_POST['login'], time() + 60 * 60 * 24 * 100, "/");
					setcookie("pass", $_POST['pwd'], time() + 60 * 60 * 24 * 100, "/");
				} else {
					setcookie("login", "", NULL, "/");
					setcookie("pass", "", NULL, "/");
				}
				if ($result->profile == 5) {
					header('Location: publisher/index.php');
				} elseif ($result->profile == 6) {
					header('Location: contributor/index.php');
				}
			}
		}
	}
}
if (isset($_POST['add'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$registration = $_POST['registration'];
	$stmt = $conn->prepare("SELECT * FROM `users` WHERE login=:tt");
	$stmt->bindParam(':tt', $email);
	$stmt->execute();
	$user = $stmt->fetchObject();
	if ($user) {
		$msgR="User already exist !";
	} else {
		if (is_numeric($registration)) {
			$stmt = $conn->prepare("INSERT INTO  `publishers` (company_name,contact_email,registration_number) values (:na,:em,:num)");
			$stmt->bindParam(':na', $name, PDO::PARAM_STR);
			$stmt->bindParam(':em', $email, PDO::PARAM_STR);
			$stmt->bindParam(':num', $registration, PDO::PARAM_STR);
			$stmt->execute();
			$publisher_id = $conn->lastInsertId();

			$pwd = password_hash($name, PASSWORD_BCRYPT);
			if ($publisher_id != 0) {
				$stmt = $conn->prepare("INSERT INTO `users`(`login`,`password`, `profile`,`id_profile`,`date_add`,`active`,`status`) VALUES (:tt,:ur,5,:id,NOW(),1,1)");
				$stmt->bindParam(':tt', $email);
				$stmt->bindParam(':ur', $pwd);
				$stmt->bindParam(':id', intval($publisher_id));
				$stmt->execute();
				$user_id = $conn->lastInsertId();

				$stmt = $conn->prepare("SELECT * FROM `users` WHERE `id_user` = :id_user");
				$stmt->bindParam(':id_user', $user_id, PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchObject();

				$_SESSION['login'] = $result->profile;
				$_SESSION['id_user'] = $result->id_user;
				$_SESSION['id_account'] = $result->id_profile;
				$_SESSION['lang'] = $result->lang;
				setcookie("lang", $result->lang, time() + 2 * 24 * 60 * 60);
				header('Location: publisher/my_profile.php');
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
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
	<link href="../assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		body {background: #f6f5f7;display: flex;justify-content: center;align-items: center;flex-direction: column;height: 100vh;margin: 0;}
		h1 {font-weight: 600;margin: 0;}
		h2 {text-align: center;}
		p {font-size: 14px;font-weight: 100;line-height: 20px;letter-spacing: 0.5px;margin: 20px 0 30px;}
		span {font-size: 12px;}
		a {color: #333;font-size: 14px;text-decoration: none;margin: 15px 0;}
		button {position: absolute;bottom: 70px;border-radius: 20px;border: 1px solid #ff9189;background-image: linear-gradient(#ff9189 0%, #ffb199 100%);color: #FFFFFF;font-size: 12px;font-weight: bold;padding: 12px 45px;letter-spacing: 1px;text-transform: uppercase;transition: transform 80ms ease-in;}
		button:active {transform: scale(0.95);}
		button:focus {outline: none;}
		button.ghost {background-color: transparent;border-color: #FFFFFF;}
		form {background-color: #FFFFFF;display: flex;align-items: center;justify-content: center;flex-direction: column;padding: 0 40px;height: 100%;text-align: center;}
		.container {background-color: #fff;border-radius: 10px;box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25),0 10px 10px rgba(0, 0, 0, 0.22);position: relative;overflow: hidden;width: 768px;max-width: 100%;min-height: 560px;}
		.form-container {position: absolute;top: 0;height: 100%;transition: all 0.6s ease-in-out;}
		.sign-in-container {left: 0;width: 50%;z-index: 2;}
		.container.right-panel-active .sign-in-container {transform: translateX(100%);}
		.sign-up-container {left: 0;width: 50%;opacity: 0;z-index: 1;}
		.container.right-panel-active .sign-up-container {transform: translateX(100%);opacity: 1;z-index: 5;animation: show 0.6s;}
		@keyframes show {
			0%,49.99% {opacity: 0;z-index: 1;}
			50%,100% {opacity: 1;z-index: 5;}
		}
		.overlay-container {position: absolute;top: 0;left: 50%;width: 50%;height: 100%;overflow: hidden;transition: transform 0.6s ease-in-out;z-index: 100;}
		.container.right-panel-active .overlay-container {transform: translateX(-100%);}
		.overlay {background: #FF416C;background-image: linear-gradient(#ff0844 0%, #ffb199 100%);background-repeat: no-repeat;background-size: cover;background-position: 0 0;color: #FFFFFF;position: relative;left: -100%;height: 100%;width: 200%;transform: translateX(0);transition: transform 0.6s ease-in-out;}
		.container.right-panel-active .overlay {transform: translateX(50%);}
		.overlay-panel {position: absolute;display: flex;align-items: center;justify-content: center;flex-direction: column;padding: 0 40px;text-align: center;top: 0;height: 100%;width: 50%;transform: translateX(0);transition: transform 0.6s ease-in-out;}
		.overlay-left {transform: translateX(-20%);}
		.container.right-panel-active .overlay-left {transform: translateX(0);}
		.overlay-right {right: 0;transform: translateX(0);}
		.container.right-panel-active .overlay-right {transform: translateX(20%);}
		.social-container {margin: 20px 0;}
		.social-container a {border: 1px solid #DDDDDD;border-radius: 50%;display: inline-flex;justify-content: center;align-items: center;margin: 0 5px;height: 40px;width: 40px;}
		.login-box {width: auto;margin: 0 auto;}
		.login-register {padding: 3% 0;}
		.logo-box {padding-bottom: 3%;margin-left: auto;margin-right: auto;display: block;}
		#signUp2,#signIn2 {display: none;}
		@media (max-width: 767px) {
			.sign-up-container {display: contents;width: 0%;}
			.sign-in-container {width: 100%;}
			.overlay-container {display: none;}
			.container {margin-top: 30px;}
			#signUp2,#signIn2 {bottom: 10px;background-color: transparent;color: #ffcc81;display: block;}
			.container.right-panel-active .sign-up-container {transform: translateX(1000%);opacity: 1;z-index: 5;animation: show 0.6s;}
			.sign-up-form,#loginform {margin-top: 80px;}
			form {justify-content: normal;}
		}
		@media (max-width: 384px) {.logo-box {width: 70%;}}
		.form-control {margin: 15px;}
		.language-switch {position: absolute;top: 2px;right: 2px;z-index: 999;}
		.list-languages {list-style: none;}
		.language-item {padding: 0;padding-left: 3px;}
	</style>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
	<div class="preloader">
		<div class="loader">
			<div class="loader__figure"></div>
			<p class="loader__label">Private-chat</p>
		</div>
	</div>
	<div class="form-group language-switch">
		<ul class="list-languages">
			<?php if (isset($_COOKIE["lang"]) && $_COOKIE["lang"] == "fr") { ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class=""><img src="../assets/images/fr.png" width="24px" height="24px"> Francais </span></a>
					<div class="dropdown-menu language-item" aria-labelledby="">
						<a class="dropdown-item " href="#" onclick="update_language('en')"><span class=""><img src="../assets/images/en.png" width="24px" height="24px"> Anglais </span> </a>
					</div>
				</li>
			<?php } else { ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class=""><img src="../assets/images/en.png" width="24px" height="24px"> English </span></a>
					<div class="dropdown-menu language-item" aria-labelledby="">
						<a class="dropdown-item " onclick="update_language('fr')" href="#"><span class=""><img src="../assets/images/fr.png" width="24px" height="24px"> French </span> </a>
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
	<section id="wrapper" class="login-register" style="background: url(../assets/images/background/bg_chat2.png) #fff repeat-x bottom;">
		<div class="login-box">
			<div class="card-body">
				<img src="../assets/images/logo4.png" class="logo-box">
				<div class="container" id="container">
					<div class="form-container sign-up-container">
						<form action="#" class="sign-up-form" method="POST">
							<h2><?php echo utf8_encode($trans["index"]["sign_up"]) ?></h2>
							<span><?php echo utf8_encode($trans["index"]["sign_up_subtitle"]) ?></span>
							<input class="form-control" type="text" name="name" placeholder="<?php echo utf8_encode($trans["index"]["companyName"]) ?>" required />
							<input class="form-control" type="email" name="email" placeholder="<?php echo utf8_encode($trans["index"]["email"]) ?>" required />
							<input class="form-control" type="text" name="registration" placeholder="<?php echo utf8_encode($trans["index"]["Registration"]) ?>" required />
							<?php if ($msgR != "") {
								echo '<div class="login-info text-danger text-center"><p>' . $msgR . '</p><div class="clear"> </div></div>';
							} ?>
							<button type="submit" name="add"><?php echo utf8_encode($trans["index"]["sign_up_btn"]) ?></button>
							<button type="button" class="ghost" id="signIn2"><?php echo utf8_encode($trans["index"]["sign_in_btn"]) ?></button>
						</form>
					</div>
					<div class="form-container sign-in-container">
						<form id="loginform" action="" method="POST">
							<h2><?php echo utf8_encode($trans["index"]["sign_in"]) ?></h2>
							<span><?php echo utf8_encode($trans["index"]["sign_in_subtitle"]) ?></span>
							<input class="form-control" name="login" type="text" required="" placeholder="<?php echo utf8_encode($trans["index"]["username"]) ?>" value="<?php if (isset($_COOKIE['login'])) {echo $_COOKIE['login'];} ?>" />
							<input class="form-control" name="pwd" type="password" required="" placeholder="<?php echo utf8_encode($trans["index"]["password"]) ?>" value="<?php if (isset($_COOKIE['pass'])) {echo $_COOKIE['pass'];} ?>" />
							<div>
								<input type="checkbox" name="remember" <?php if (isset($_COOKIE['login'])) {echo "checked";} ?> />
								<label for="remember">Remember me</label>
							</div>
							<a href="forgot_password.php"><?php echo utf8_encode($trans["index"]["forgot_password"]) ?></a>
							<button type="submit" name="valider"><?php echo utf8_encode($trans["index"]["sign_in"]) ?></button>
							<button type="button" class="ghost" id="signUp2"><?php echo utf8_encode($trans["index"]["sign_in_btn"]) ?></button>
							<?php if ($msgRe == 200) { ?>
								<div class="login-info text-success text-center"><?php echo utf8_encode($trans["index"]["password_updated"]) ?><div class="clear"> </div>
								</div>
							<?php } ?>
							<?php if ($msgR != "") {
								echo '<div class="login-info text-danger text-center"><p>' . $msgR . '</p><div class="clear"> </div></div>';
							} ?>
						</form>
						<div class="form-group">
							<div class="col-md-12">
							</div>
						</div>
					</div>
					<div class="overlay-container">
						<div class="overlay">
							<div class="overlay-panel overlay-left">
								<h1><?php echo utf8_encode($trans["index"]["sign_in_aside_title"]) ?></h1>
								<p><?php echo utf8_encode($trans["index"]["sign_in_aside_subtitle"]) ?></p>
								<button class="ghost" id="signIn"><?php echo utf8_encode($trans["index"]["sign_in_btn"]) ?></button>
							</div>
							<div class="overlay-panel overlay-right">
								<h1><?php echo utf8_encode($trans["index"]["sign_up_aside_title"]) ?></h1>
								<p><?php echo utf8_encode($trans["index"]["sign_up_aside_subtitle"]) ?></p>
								<button class="ghost" id="signUp"><?php echo utf8_encode($trans["index"]["sign_up_btn"]) ?></button>
							</div>
						</div>
					</div>
				</div>
				<?php if ($msgR != "") {
					echo $msgR;
				} ?>
			</div>
		</div>
	</section>
	<script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
	<!-- Bootstrap tether Core JavaScript -->
	<script src="../assets/node_modules/popper/popper.min.js"></script>
	<script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(function() {
			$(".preloader").fadeOut();
		});
		function update_language(str) {
			document.cookie = "lang=" + str;
			location.reload();
		}
		const signUpButton = document.getElementById('signUp');
		const signUp2Button = document.getElementById('signUp2');
		const signInButton = document.getElementById('signIn');
		const signIn2Button = document.getElementById('signIn2');
		const container = document.getElementById('container');
		signUpButton.addEventListener('click', () => {
			container.classList.add("right-panel-active");
		});
		signUp2Button.addEventListener('click', () => {
			container.classList.add("right-panel-active");
		});
		signInButton.addEventListener('click', () => {
			container.classList.remove("right-panel-active");
		});
		signIn2Button.addEventListener('click', () => {
			container.classList.remove("right-panel-active");
		});
	</script>
</body>
</html>