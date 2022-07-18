<?php
include('init.php');
ini_set("display_errors", 1);

$msgR = "";
$msgRe = (isset($_GET["cde"])) ? $_GET["cde"] : null;

session_start();
if (!empty($_SESSION['login']) && $_SESSION['login'] == "2") {
	header('Location: accounts/admin/index.php');
	exit();
} elseif (!empty($_SESSION['login']) && $_SESSION['login'] == "3") {
	header('Location: accounts/consultant/index.php');
	exit();
} elseif (!empty($_SESSION['login']) && $_SESSION['login'] == "4") {
	header('Location: accounts/customer/index.php');
	exit();
}
if (isset($_GET['cod']) && isset($_GET['id'])) {
	if ($_GET['cod'] == 200) {
		$stmt = $conn->prepare("SELECT * FROM `accounts` WHERE `shared_key` = :key");
		$stmt->bindParam(':key', $_GET['id'], PDO::PARAM_STR);
		$stmt->execute();
		$ac = $stmt->fetchObject();
	}
}

if (isset($_GET['customer'])) {
	if (isset($_SESSION['login'])) {
		$msgR = utf8_encode($trans["index"]["user_logged"]);
	} else {
		$login = htmlentities($_GET["customer"]);
		$pwd = htmlentities($_GET["customer"]);

		$stmt = $conn->prepare("SELECT * FROM `users` WHERE `login` = :LOGIN");
		$stmt->bindParam(':LOGIN', $login, PDO::PARAM_STR);
		$stmt->execute();
		$total = $stmt->rowCount();
		$result = $stmt->fetchObject();
		if ($total == 0) {
			$msgR = utf8_encode($trans["index"]["username_not_exist"]);
		} elseif (!password_verify($pwd, $result->password)) {
			$lastday = $result->lastday;
			$nbr_essai = (int) $result->nbr_essai;
			if ($lastday == date("Y-m-d")) {
				$msgR = utf8_encode($trans["index"]["reach_limit"]);
			} else {
				$msgR = utf8_encode($trans["index"]["password_incorrect"]);
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
		} elseif ($login == $result->login && (password_verify($pwd, $result->password))) {
			session_start();
			$_SESSION['login'] = $result->profile;
			$_SESSION['id_user'] = $result->id_user;
			$_SESSION['id_account'] = $result->id_profile;
			$_SESSION['lang'] = $result->lang;
			setcookie("lang", $result->lang, time() + 2 * 24 * 60 * 60);
			$stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm,`browser` =:br,`last_connect` =NOW() WHERE `id_user` = :ID");
			$stmt3->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
			$stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$stmt3->bindParam(':br', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
			$stmt3->execute();

			if ($result->profile == 2) {
				$stmt4 = $conn->prepare("SELECT  `id_account` FROM `accounts` WHERE `id_account` = :ID");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$stat = $stmt4->fetchObject();
				$_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
				$_SESSION['id_company'] = $stat->id_account;
				header('Location: accounts/admin/index.php');
			} elseif ($result->profile == 3) {
				$stmt4 = $conn->prepare("SELECT  `photo`, `firstname`, `lastname`, `id_account`, `pseudo` FROM `consultants` WHERE `id_consultant` = :ID");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$stat = $stmt4->fetchObject();
				$_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
				$_SESSION['pseudo'] = $stat->pseudo;
				$_SESSION['avatar'] = $stat->photo;
				$_SESSION['id_company'] = $stat->id_account;
				header('Location: accounts/consultant/index.php');
			} elseif ($result->profile == 4) {
				$stmt4 = $conn->prepare("SELECT  `photo`, `firstname`, `lastname`, `balance`, `id_account`,`id_website` ,(CASE c.country WHEN NULL THEN (SELECT currency FROM accounts WHERE id_account=c.id_account) ELSE c.country END) currency FROM `customers` c WHERE c.id_customer = :ID");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$stat = $stmt4->fetchObject();
				$stmt4 = $conn->prepare("SELECT  * FROM packages p,transactionsc t WHERE t.id_customer = :ID AND t.id_package=p.id_package AND p.messages IS NULL AND date(CURRENT_TIMESTAMP) BETWEEN p.start_date AND p.end_date");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$obj = $stmt4->fetchObject();
				$_SESSION['unlimited'] = $obj ? 1 : 0;
				$_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
				$_SESSION['balance'] = $stat->balance;
				$_SESSION['id_website'] = $stat->id_website;
				$_SESSION['id_company'] = $stat->id_account;
				$_SESSION['avatar'] = $stat->photo;
				$_SESSION['currency'] = strlen($stat->currency) == 3 ? $stat->currency : $currencies[$stat->currency];
				header('Location: accounts/customer/index.php');
			}
		}
	}
}
if (isset($_POST['valider'])) {
	if (isset($_POST['leadCode']) && $_POST['leadCode'] != "") {
		$code = $_POST['leadCode'];
		$lead = explode('-', $code);
		$stmt = $conn->prepare("SELECT cu.*,u.* FROM `users` u,`customers` cu,`leads` l,`contributors` c WHERE u.id_user =:id AND u.id_profile=cu.id_customer AND cu.id_customer=l.id_customer AND (SELECT c.pseudo FROM contributors c1 ,users u1 WHERE u1.id_user=l.id_contributor AND c.id_contributor=u1.id_profile) LIKE '" . $lead[0] . "%'");
		$stmt->bindParam(':id', $lead[1], PDO::PARAM_INT);
		$stmt->execute();
		$stat = $stmt->fetchObject();
		if ($stat) {
			session_start();
			$_SESSION['login'] = $stat->profile;
			$_SESSION['id_user'] = $stat->id_user;
			$_SESSION['id_account'] = $stat->id_profile;
			$_SESSION['lang'] = $stat->lang;
			setcookie("lang", $stat->lang, time() + 2 * 24 * 60 * 60);
			$stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm,`browser` =:br ,`last_connect` =NOW()  WHERE `id_user` = :ID");
			$stmt3->bindParam(':ID', $stat->id_user, PDO::PARAM_INT);
			$stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$stmt3->bindParam(':br', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
			$stmt3->execute();
			$stmt3 = $conn->prepare("UPDATE leads SET status=1,update_date=NOW()  WHERE `id_customer` = :ID AND status=0");
			$stmt3->bindParam(':ID', $stat->id_customer, PDO::PARAM_INT);
			$stmt3->execute();
			$_SESSION['account_status'] = $stat->status;
			$_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
			$_SESSION['balance'] = $stat->balance;
			$_SESSION['id_website'] = $stat->id_website;
			$_SESSION['id_company'] = $stat->id_account;
			$_SESSION['avatar'] = $stat->photo;
			$_SESSION['country'] = $stat->country;
			$stmt4 = $conn->prepare("INSERT INTO connecting_log(id_customer,date) VALUES (:ID,NOW())");
			$stmt4->bindParam(':ID', $stat->id_user, PDO::PARAM_INT);
			$stmt4->execute();
			header('Location: accounts/customer/index.php');
		} else $msgR = "Invalide Code!";
	} else if (empty($_POST['login']) || empty($_POST['pwd'])) {
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
			$lastday = $result->lastday;
			$nbr_essai = (int) $result->nbr_essai;
			if ($lastday == date("Y-m-d")) {
				$msgR = utf8_encode($trans["index"]["reach_limit"]);
			} else {
				$msgR = utf8_encode($trans["index"]["password_incorrect"]);
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
		} elseif ($login == $result->login && (password_verify($pwd, $result->password))) {
			session_start();
			$_SESSION['login'] = $result->profile;
			$_SESSION['id_user'] = $result->id_user;
			$_SESSION['id_account'] = $result->id_profile;
			$_SESSION['lang'] = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : $result->lang;
			setcookie("lang", $_SESSION['lang'], time() + 2 * 24 * 60 * 60);
			$stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm ,`browser` =:br ,`last_connect` =NOW()  WHERE `id_user` = :ID");
			$stmt3->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
			$stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$stmt3->bindParam(':br', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
			$stmt3->execute();

			if ($result->profile == 2) {
				$stmt4 = $conn->prepare("SELECT  * FROM `accounts` WHERE `id_account` = :ID");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$stat = $stmt4->fetchObject();
				$_SESSION['account_status'] = $stat->status;
				$_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
				$_SESSION['id_company'] = $stat->id_account;
				$_SESSION['id_account'] = $stat->id_account;
				$_SESSION['business_name'] = $stat->business_name;
				$_SESSION['currency'] = $stat->currency;
				header('Location: accounts/admin/index.php');
			} elseif ($result->profile == 3) {
				$stmt4 = $conn->prepare("SELECT  `photo`, `firstname`, `lastname`, `id_account`,`status`, `pseudo`, `websites` FROM `consultants` WHERE `id_consultant` = :ID");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$stat = $stmt4->fetchObject();
				$_SESSION['account_status'] = $stat->status;
				$_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
				$_SESSION['pseudo'] = $stat->pseudo;
				$_SESSION['avatar'] = $stat->photo;
				$_SESSION['id_company'] = $stat->id_account;
				$_SESSION['id_websites'] = $stat->websites;
				header('Location: accounts/consultant/index.php');
			} elseif ($result->profile == 4) {
				$stmt4 = $conn->prepare("SELECT  id_customer,`photo`, `firstname`, `lastname`, `balance`, `id_account`,`status`,`id_website`,`country`,`id_link`  ,(CASE c.country WHEN NULL THEN (SELECT currency FROM accounts WHERE id_account=c.id_account) ELSE c.country END) currency FROM `customers` c WHERE c.id_customer = :ID");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$stat = $stmt4->fetchObject();
				$stmt3 = $conn->prepare("UPDATE leads SET status=1,update_date=NOW()  WHERE `id_customer` = :ID AND status=0");
				$stmt3->bindParam(':ID', $stat->id_customer, PDO::PARAM_INT);
				$stmt3->execute();
				$stmt4 = $conn->prepare("SELECT  * FROM packages p,transactionsc t WHERE t.id_customer =:ID AND t.id_package=p.id_package AND p.messages IS NULL AND date(CURRENT_TIMESTAMP) BETWEEN t.date_add AND t.date_add + interval p.period day");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$obj = $stmt4->fetchObject();
				$_SESSION['unlimited'] = $obj ? 1 : 0;
				$_SESSION['account_status'] = $stat->status;
				$_SESSION['full_name'] = $stat->firstname . " " . $stat->lastname;
				$_SESSION['balance'] = $stat->balance;
				$_SESSION['id_website'] = $stat->id_website;
				$_SESSION['id_company'] = $stat->id_account;
				$_SESSION['avatar'] = $stat->photo;
				$_SESSION['country'] = $stat->country;
				$_SESSION['id_link'] = $stat->id_link;
				$_SESSION['currency'] = strlen($stat->currency) == 3 ? $stat->currency : $currencies[$stat->currency];

				$stmt4 = $conn->prepare("INSERT INTO connecting_log(id_customer,date) VALUES (:ID,NOW())");
				$stmt4->bindParam(':ID', $result->id_user, PDO::PARAM_INT);
				$stmt4->execute();
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
	<!-- Custom CSS -->
	<link rel="stylesheet" href="assets/css/style.min.css" />
	<link href="assets/node_modules/select2/select2.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="assets/css/pages/login-register-lock.css">
	<link rel="stylesheet" href="assets/node_modules/dropify/dropify.min.css">
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
			<?php if (isset($_COOKIE["lang"]) && $_COOKIE["lang"] == "fr") { ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class=""><img src="assets/images/fr.svg" width="24px" height="24px"> Francais </span></a>
					<div class="dropdown-menu language-item" aria-labelledby="">
						<a class="dropdown-item " href="#" onclick="update_language('en')"><span class=""><img src="assets/images/en.svg" width="24px" height="24px"> Anglais </span> </a>
					</div>
				</li>
			<?php } else { ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class=""><img src="assets/images/en.svg" width="24px" height="24px"> English </span></a>
					<div class="dropdown-menu language-item" aria-labelledby="">
						<a class="dropdown-item " onclick="update_language('fr')" href="#"><span class=""><img src="assets/images/fr.svg" width="24px" height="24px"> French </span> </a>
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
	<section id="wrapper" class="login-register" style="background: url(assets/images/background/bg_chat2.jpg) #fff repeat-x bottom;">
		<div class="login-box">
			<div class="card-body">
				<?php
				ini_set("display_errors", 1);
				$msg = "";
				$email = "";
				if (isset($_POST['email'])) {
					$email = $_GET["email"];
				}
				if (isset($_POST['add'])) {
					include('init.php');
					$business_name = (isset($_POST['business_name']) && $_POST['business_name'] != '') ? htmlspecialchars($_POST['business_name']) : NULL;
					$country = (isset($_POST['country']) && $_POST['country'] != '') ? $_POST['country'] : NULL;
					$emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
					$website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
					$datec = date('Y-m-d', strtotime('now'));

					$stmt1 = $conn->prepare("INSERT INTO `accounts`(`business_name`, `registration`, `taxid`, `address`, `code_postal`, `city`, `country`, `phone`, `emailc`, `website`, `date_add`, `date_end`,`currency`) VALUES (:bus,NULL,NULL,NULL,NULL,NULL,:cnt,NULL,:em,:web,:dt,NULL,:cu)");
					$stmt1->bindParam(':bus', $business_name, PDO::PARAM_STR);
					$stmt1->bindParam(':cnt', $country, PDO::PARAM_STR);
					$stmt1->bindParam(':em', $emailc, PDO::PARAM_STR);
					$stmt1->bindParam(':web', $website, PDO::PARAM_STR);
					$stmt1->bindParam(':dt', $datec, PDO::PARAM_STR);
					$stmt1->bindParam(':cu', $trans["devise"][$country], PDO::PARAM_STR);
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
						$pwd = strtotime('now') . "-" . $last_id;
						$stmt3 = $conn->prepare("INSERT INTO `users`( `login`, `password`, `profile`, `id_profile`, `date_add`, `picture`, `active`, `last_connect`, `lastday`, `nbr_essai`) VALUES (:lo,:pw,2,:ID,:dt,NULL,1,NULL,NULL,NULL)");
						$stmt3->bindParam(':lo', $login, PDO::PARAM_STR);
						$stmt3->bindParam(':pw', $pwd, PDO::PARAM_STR);
						$stmt3->bindParam(':ID', $last_id, PDO::PARAM_INT);
						$stmt3->bindParam(':dt', $datec, PDO::PARAM_STR);
						$stmt3->execute();
						$msg = "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . utf8_encode($trans["index"]["account_created_successfully"]) .  " <br><a href='https://private-chat.pro/accounts/login.php'>" . utf8_encode($trans["index"]["log_in"]) .  "</a></div></div>";
					} else {
						$msg = "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . utf8_encode($trans["index"]["account_creation_failed"]) .  " </div></div>";
					}
					unset($_POST);
				}
				?>
				<img src="assets/images/logo_private-chat.png" class="logo-box">
				<div class="container" id="container">
					<div class="form-container sign-up-container">
						<form action="#" class="sign-up-form">
							<h2><?php echo utf8_encode($trans["index"]["sign_up"]) ?></h2>
							<span><?php echo utf8_encode($trans["index"]["sign_up_subtitle"]) ?></span>
							<input class="form-control" type="text" placeholder="<?php echo utf8_encode($trans["index"]["name"]) ?>" />
							<input class="form-control" type="email" placeholder="<?php echo utf8_encode($trans["index"]["email"]) ?>" />
							<input class="form-control" type="password" placeholder="<?php echo utf8_encode($trans["index"]["password"]) ?>" />
							<button><?php echo utf8_encode($trans["index"]["sign_up_btn"]) ?></button>
							<button type="button" class="ghost" id="signIn2"><?php echo utf8_encode($trans["index"]["sign_in_btn"]) ?></button>
						</form>
					</div>
					<div class="form-container sign-in-container">
						<form id="loginform" action="" method="POST">
							<h2><?php echo utf8_encode($trans["index"]["sign_in"]) ?></h2>
							<span><?php echo utf8_encode($trans["index"]["sign_in_subtitle"]) ?></span>
							<input class="form-control" name="login" type="text" placeholder="<?php echo utf8_encode($trans["index"]["username"]) ?>" value="<?= $email ?>" />
							<input class="form-control" name="pwd" type="password" placeholder="<?php echo utf8_encode($trans["index"]["password"]) ?>" />
							<a href="forgot_password.php"><?php echo utf8_encode($trans["index"]["forgot_password"]) ?></a>
							<button type="submit" name="valider"><?php echo utf8_encode($trans["index"]["sign_in"]) ?></button>
							<button type="button" class="ghost" id="signUp2"><?php echo utf8_encode($trans["index"]["sign_up_btn"]) ?></button>
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
								<?php if (isset($_REQUEST['exist']) && $_REQUEST['exist'] == "1") { ?>
									<h1><?php echo utf8_encode($trans["index"]["exist_title"]) ?></h1>
									<p><?php echo utf8_encode($trans["index"]["exist_subtitle"]) ?></p>
								<?php } else { ?>
									<h1><?php echo utf8_encode($trans["index"]["sign_up_aside_title"]) ?></h1>
									<p><?php echo utf8_encode($trans["index"]["sign_up_aside_subtitle"]) ?></p>
									<button class="ghost" id="signUp"><?php echo utf8_encode($trans["index"]["sign_up_btn"]) ?></button>
								<?php } ?>
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
	<script src="assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
	<script src="assets/node_modules/dropify/dropify.min.js"></script>
	<script src="assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
	<script type="text/javascript">
		$(".select2").select2();
		$('.dropify').dropify();
		$('#tagsinput').tagsinput({
			confirmKeys: [13, 188]
		});
		$('.bootstrap-tagsinput input').on('keypress', function(e) {
			if (e.keyCode == 13) {
				e.keyCode = 188;
				e.preventDefault();
			};
		});
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