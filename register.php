<?php
include('init.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$exist = 0;
$msg = '';
if (isset($_GET['cde']) && $_GET['cde'] != "") {
	$s = $conn->prepare("UPDATE links SET status=0 WHERE date_end<NOW()");
	$s->execute();
	$params = decryptIt(htmlentities($_GET['cde']));
	$stmt = $conn->prepare("SELECT * FROM links WHERE id_link=:lk AND status=1");
	$stmt->bindParam(':lk', $params);
	$stmt->execute();
	$link = $stmt->fetchObject();
	if ($link) {
		$exist = 1;
	}
}
if (isset($_POST['add'])) {
	$stmt = $conn->prepare("SELECT c.*,u.*,(CASE WHEN phone=:ph THEN 1 ELSE 0 END) as exist FROM customers c,users u WHERE u.profile=4 AND u.id_profile=c.id_customer AND u.login=:lg");
	$stmt->bindparam(":lg", $_POST['email'], PDO::PARAM_STR);
	$stmt->bindparam(":ph", $_POST['phone'], PDO::PARAM_STR);
	$stmt->execute();
	$user = $stmt->fetchObject();
	if ($user) {
		if ($user->exist == "1") {
			session_start();
			$_SESSION['login'] = $user->profile;
			$_SESSION['id_user'] = $user->id_user;
			$_SESSION['id_account'] = $user->id_profile;
			$_SESSION['lang'] = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : $user->lang;
			setcookie("lang", $_SESSION['lang'], time() + 2 * 24 * 60 * 60);
			$stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm ,`browser` =:br ,`last_connect` =NOW()  WHERE `id_user` = :ID");
			$stmt3->bindParam(':ID', $user->id_user, PDO::PARAM_INT);
			$stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$stmt3->bindParam(':br', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
			$stmt3->execute();
			$stmt3 = $conn->prepare("UPDATE leads SET status=1,update_date=NOW()  WHERE `id_customer` = :ID AND status=0");
			$stmt3->bindParam(':ID', $user->id_customer, PDO::PARAM_INT);
			$stmt3->execute();
			$stmt4 = $conn->prepare("SELECT  * FROM packages p,transactionsc t WHERE t.id_customer =:ID AND t.id_package=p.id_package AND p.messages IS NULL AND date(CURRENT_TIMESTAMP) BETWEEN t.date_add AND t.date_add + interval p.period day");
			$stmt4->bindParam(':ID', $user->id_profile, PDO::PARAM_INT);
			$stmt4->execute();
			$obj = $stmt4->fetchObject();
			$_SESSION['unlimited'] = $obj ? 1 : 0;
			$_SESSION['account_status'] = $user->status;
			$_SESSION['full_name'] = $user->firstname . " " . $user->lastname;
			$_SESSION['balance'] = $user->balance;
			$_SESSION['id_website'] = $user->id_website;
			$_SESSION['id_company'] = $user->id_account;
			$_SESSION['avatar'] = $user->photo;
			$_SESSION['country'] = $user->country;
			$_SESSION['id_link'] = $user->id_link;
			$_SESSION['currency'] = strlen($user->currency) == 3 ? $user->currency : $currencies[$user->currency];
			$stmt4 = $conn->prepare("INSERT INTO connecting_log(id_customer,date) VALUES (:ID,NOW())");
			$stmt4->bindParam(':ID', $user->id_user, PDO::PARAM_INT);
			$stmt4->execute();
			header('Location: accounts/customer/index.php');
		} else if ($user->exist == "0") {
			header('Location: index.php?exist=1');
		}
	} else {
		$firstName = isset($_POST['firstname']) ? htmlentities($_POST['firstname']) : NULL;
		$lastName = isset($_POST['lastname']) ? htmlentities($_POST['lastname']) : NULL;
		$gender = isset($_POST['gender']) ? $_POST['gender'] : NULL;
		$emailAddress = isset($_POST['email']) ? htmlentities($_POST['email']) : NULL;
		$phone = isset($_POST['phone']) ? $_POST['phone'] : NULL;
		$lang = isset($_POST['lang']) ? $_POST['lang'] : NULL;
		$country = isset($_POST['country']) ? $_POST['country'] : NULL;
		$address = isset($_POST['address']) ? htmlentities($_POST['address']) : NULL;
		if ($link) {
			$datec = date('Y-m-d', strtotime('now'));
			$stmt = $conn->prepare("INSERT into customers (firstname, lastname, emailc, phone, country, address, date_start, status, id_account, gender, id_website,id_link) values (:fn, :ln, :em, :ph, :ct, :ad, now(), 1, :ia, :ge, :idw , :idl)");
			$stmt->bindparam(":fn", $firstName, PDO::PARAM_STR);
			$stmt->bindparam(":ln", $lastName, PDO::PARAM_STR);
			$stmt->bindparam(":em", $emailAddress, PDO::PARAM_STR);
			$stmt->bindparam(":ph", $phone, PDO::PARAM_STR);
			$stmt->bindparam(":ct", $country, PDO::PARAM_STR);
			$stmt->bindparam(":ad", $address, PDO::PARAM_STR);
			$stmt->bindparam(":ia", $link->id_account, PDO::PARAM_INT);
			$stmt->bindparam(":idw", $link->id_website, PDO::PARAM_INT);
			$stmt->bindparam(":idl", $link->id_link, PDO::PARAM_INT);
			$stmt->bindparam(":ge", $gender, PDO::PARAM_INT);
			$stmt->execute();
			$last_id = $conn->lastInsertId();
			$affected_rows = $stmt->rowCount();
			if ($affected_rows != 0) {
				$lang = isset($_POST['lang']) && $_POST['lang'] != "" ? $_POST['lang'] : "en";
				$profile = 4;
				$profileId = $last_id;
				$password = password_hash($emailAddress, PASSWORD_BCRYPT);
				$stmt1 = $conn->prepare("INSERT INTO `users`(`login`, `password`, `profile`, `id_profile`, `date_add`, `active`, `status`,lang) VALUES (:lg,:pwd,:pr,:ip,:da,1,1,:lang)");
				$stmt1->bindParam(':lg', $emailAddress, PDO::PARAM_STR);
				$stmt1->bindParam(':pwd', $password, PDO::PARAM_STR);
				$stmt1->bindParam(':pr', $profile, PDO::PARAM_INT);
				$stmt1->bindParam(':ip', $profileId, PDO::PARAM_INT);
				$stmt1->bindParam(':da', $datec, PDO::PARAM_STR);
				$stmt1->bindparam(":lang", $lang, PDO::PARAM_STR);
				$stmt1->execute();
				$affected_rows = $stmt1->rowCount();
				$id_user = $conn->lastInsertId();
				$balance = 0;
				if ($affected_rows != 0) {
					$s1 = $conn->prepare("SELECT id_offer FROM `links_offers` WHERE id_link = :id");
					$s1->bindParam(':id', $link->id_link, PDO::PARAM_INT);
					$s1->execute();
					$offers_ids = array_column($s1->fetchAll(PDO::FETCH_ASSOC), 'id_offer');
					if ($offers_ids != null) {
						foreach ($offers_ids as $key => $value) {
							$stmt1 = $conn->prepare("INSERT INTO `offers_customers`(`id_customer`, `id_offer`, `created_at`) VALUES (:ic,:iof,NOW())");
							$stmt1->bindParam(':ic', $last_id, PDO::PARAM_INT);
							$stmt1->bindParam(':iof', $value, PDO::PARAM_INT);
							if ($stmt1->execute()) {
								$s1 = $conn->prepare("SELECT o.id_offer,o.title as offer_title ,o.discount, p.* FROM `offers` o join `packages` p on p.id_package=o.id_package WHERE o.id_offer = :ID ");
								$s1->bindParam(':ID', $value, PDO::PARAM_INT);
								$s1->execute();
								$offer = $s1->fetchObject();
								if ($offer->id_offer == $value) {
									if ($offer->discount == 100) {
										$balance += intval($offer->messages);
										$stmt1 = $conn->prepare("UPDATE customers set balance=balance+:ba where id_customer=:id");
										$stmt1->bindParam(':id', $last_id, PDO::PARAM_INT);
										$stmt1->bindParam(':ba', $offer->messages, PDO::PARAM_INT);
										$stmt1->execute();
										$stmt = $conn->prepare("SELECT id_user from users where id_profile=:ip and profile=4");
										$stmt->bindParam(':ip', $last_id, PDO::PARAM_INT);
										$stmt->execute();
										$id_user = intval($stmt->fetchObject()->id_user);
										$description = "Free offer attached to customer";
										$stmt2 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) VALUES(:iu,:ds,:mt,3,NOW())");
										$stmt2->bindParam(':iu', $id_user, PDO::PARAM_INT);
										$stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
										$stmt2->bindParam(':mt', $offer->id_offer, PDO::PARAM_INT);
										$stmt2->execute();
									} else {
										$stmt = $conn->prepare("SELECT id_user from users where id_profile=:ip and profile=4");
										$stmt->bindParam(':ip', $last_id, PDO::PARAM_INT);
										$stmt->execute();
										$id_user = intval($stmt->fetchObject()->id_user);
										$description = "Free offer attached to customer";
										$stmt2 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) VALUES(:iu,:ds,:mt,3,NOW())");
										$stmt2->bindParam(':iu', $id_user, PDO::PARAM_INT);
										$stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
										$stmt2->bindParam(':mt', $offer->id_offer, PDO::PARAM_INT);
										$stmt2->execute();
									}
								}
							}
						}
					}
				}
				session_start();
				$_SESSION['login'] = $profile;
				$_SESSION['id_user'] = $id_user;
				$_SESSION['id_account'] = $link->id_account;
				$_SESSION['lang'] = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : $lang;
				setcookie("lang", $_SESSION['lang'], time() + 2 * 24 * 60 * 60);
				$stmt3 = $conn->prepare("UPDATE `users` SET `remote_address` =:rm ,`browser` =:br ,`last_connect` =NOW()  WHERE `id_user` = :ID");
				$stmt3->bindParam(':ID', $id_user, PDO::PARAM_INT);
				$stmt3->bindParam(':rm', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
				$stmt3->bindParam(':br', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
				$stmt3->execute();
				$stmt4 = $conn->prepare("SELECT  * FROM packages p,transactionsc t WHERE t.id_customer =:ID AND t.id_package=p.id_package AND p.messages IS NULL AND date(CURRENT_TIMESTAMP) BETWEEN t.date_add AND t.date_add + interval p.period day");
				$stmt4->bindParam(':ID', $result->id_profile, PDO::PARAM_INT);
				$stmt4->execute();
				$obj = $stmt4->fetchObject();
				$stmt4 = $conn->prepare("SELECT  currency FROM accounts WHERE id_account=:id");
				$stmt4->bindParam(':id', $link->id_account, PDO::PARAM_INT);
				$stmt4->execute();
				$currency = $stmt4->fetchObject();
				$_SESSION['unlimited'] = $obj ? 1 : 0;
				$_SESSION['account_status'] = 1;
				$_SESSION['full_name'] = $firstName . " " . $lastName;
				$_SESSION['balance'] = $balance;
				$_SESSION['id_website'] = $link->id_website;
				$_SESSION['id_company'] = $link->id_account;
				$_SESSION['avatar'] = null;
				$_SESSION['id_link'] = $link->id_link;
				$_SESSION['country'] = $country;
				$_SESSION['currency'] = $country == NULL ? $currency->currency : $currencies[$country];
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
	<link rel="stylesheet" href="assets/css/pages/login-register-lock.css">
	<link rel="stylesheet" type="text/css" href="assets/int-phone-number/css/intlTelInput.css">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<style>
	.iti.iti--allow-dropdown {
		width: 100%;
	}
</style>

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

	<section id="register" class="login-register" style="background: url(assets/images/background/bg_chat2.jpg) #fff repeat-x bottom;">
		<div class="login-box">
			<div class="card-body">
				<img src="assets/images/logo4.png" class="logo-box">
				<div class="container p-1" id="container" style="<?= $exist == 0 ? 'width:700px' : '' ?>">
					<?php if ($exist == 1) { ?>
						<div class="form-container sign-in-container">
							<form action="#" class="sign-up-form p-4" method="POST">
								<div class="form-group">
									<label class="control-label"><?php echo ($trans["gender"]) ?></label>
									<div>
										<div class="custom-control custom-radio d-inline">
											<input type="radio" id="customRadio1" name="gender" value="1" data-text="Male" class="custom-control-input" checked>
											<label class="custom-control-label" for="customRadio1"><?php echo ($trans["male"]) ?></label>
										</div>
										<div class="custom-control custom-radio d-inline ">
											<input type="radio" id="customRadio2" name="gender" value="2" data-text="Female" class="custom-control-input">
											<label class="custom-control-label" for="customRadio2"><?php echo ($trans["female"]) ?></label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="firstname"> <?php echo ($trans["first_name"]) ?> : <span class="danger">*</span> </label>
											<input type="text" class="form-control required" id="firstname" name="firstname" placeholder="Enter First Name">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="lastname"><?php echo ($trans["last_name"]) ?> : <span class="danger">*</span> </label>
											<input type="text" class="form-control required" id="lastname" name="lastname" placeholder="Enter Last Name">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="country"> <?php echo ($trans["country"]) ?> : </label>
											<select name="country" id="country" class="form-control select-search country form-control-line">
												<option></option>
												<?php
												foreach ($countries as $key => $country) {
													echo '<option value="' . $key . '">' . $country . '</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<label for="activity"><?php echo ($trans["language"]) ?></label>
										<div>
											<select name="lang" id="lang" class="form-control select2 select-search">
												<option></option>
												<option value="en"><?php echo ($trans["english"]) ?></option>
												<option value="fr"><?php echo ($trans["french"]) ?></option>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="email"> <?php echo ($trans["email"]) ?> : <span class="danger">*</span></label>
											<input type="email" class="form-control required" id="email" name="email" placeholder="Enter Email Address">
										</div>
									</div>
									<div class="form-group col-6">
										<label class="control-label text-right"><?php echo ($trans["phone"]) ?></label>
										<div class="">
											<input name="phone" type="tel" id="phone" class="form-control" style="width:100%" placeholder="Enter Phone Number">
											<span id="valid-msg" class="hide text-success">� Valid</span>
											<span id="error-msg" class="hide text-danger">✗ Invalid number</span>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="form-group col-md-12">
										<label for="address"> <?php echo ($trans["address"]) ?> : </label>
										<input type="text" class="form-control" id="address" name="address" placeholder="Enter Address">
									</div>
								</div>
								<?php if ($msg != "") {
									echo '<div class="login-info text-danger text-center"><p>' . $msg . '</p><div class="clear"> </div></div>';
								} ?>
								<div class="form-actions text-center">
									<button type="submit" name="add" class="btn ghost mb-3"><?php echo utf8_encode($trans["index"]["sign_up_btn"]) ?></button>
								</div>
							</form>
						</div>
						<div class="overlay-container">
							<div class="overlay">
								<div class="overlay-panel overlay-right">
									<h1><?php echo ($trans["index"]["sign_up"]) ?></h1>
									<p><?php echo ($trans["index"]["sign_up_aside_subtitle"]) ?></p>
								</div>
							</div>
						</div>
					<?php } else { ?>
						<div class="overlay-container" style="left: 0;width:100%">
							<div class="overlay">
								<div class="overlay-panel overlay-right">
									<div class="error-box">
										<div class="error-body text-center">
											<h1>Something wrong !!</h1>
											<h3 class="text-uppercase">Wrong or expired Link</h3>
											<p class="m-t-30 m-b-30">Please contact the administration for more information.</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
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
	<script src="assets/int-phone-number/js/intlTelInput-jquery.js"></script>
	<script src="assets/node_modules/popper/popper.min.js"></script>
	<script src="assets/node_modules/bootstrap/bootstrap.min.js"></script>
	<script src="assets/node_modules/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
	<script type="text/javascript">
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
		var errorMsg = $("#error-msg"),
			validMsg = $("#valid-msg");
		// here, the index maps to the error code returned from getValidationError - see readme
		var errorMap = ["Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];
		// initialise plugin
		var iti = $("#phone").intlTelInput({
			nationalMode: true,
			autoPlaceholder: "off",
			initialCountry: "fr",
			utilsScript: "assets/int-phone-number/js/utils.js"
		});
		var reset = function() {
			$("#phone").removeClass("error");
			errorMsg.html("");
			errorMsg.addClass("hide");
			validMsg.addClass("hide");
		};
		// on blur: validate
		$("#phone").on('blur', function() {
			reset();
			if ($("#phone").val().trim()) {
				if ($("#phone").intlTelInput("isValidNumber")) {
					$("#phone").val($("#phone").intlTelInput("getNumber"));
					validMsg.removeClass("hide");
				} else {
					$("#phone").addClass("error");
					var errorCode = $("#phone").intlTelInput("getValidationError");
					errorMsg.html(errorMap[errorCode]);
					errorMsg.removeClass("hide");
				}
			}
		});
		// on keyup / change flag: reset
		$("#phone").on('change', reset);
		$("#phone").on('keyup', reset);
		$('#country').change(function() {
			$("#phone").intlTelInput("setCountry", $(this).val());
		});
	</script>
</body>

</html>