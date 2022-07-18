<?php
$page_name = "chat_room";
ini_set('display_errors', 1);

include('header.php');


try {
	$stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM messages where (u.id_user = sender or sender = 0) and status=0 and (receiver=:id or receiver = 0)) as unread_messages_count from customers c join users u on u.id_profile=c.id_customer where c.id_account=:ia and u.profile=4 and u.id_user IN (SELECT Distinct `sender` FROM messages m WHERE m.receiver=:id) and (find_in_set(c.id_website,:idw) or c.id_website is null)");
	$stmt->bindparam(":ia",  $_SESSION['id_company']);
	$stmt->bindparam(":id",  $_SESSION['id_user']);
	$stmt->bindparam(":idw",  $_SESSION['id_websites']);
	$stmt->execute();
	$customers = $stmt->fetchAll(PDO::FETCH_OBJ);
	$stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM messages where (c.id_customer = sender or sender = 0) and status=0 and (receiver=:id or receiver = 0)) as unread_messages_count from customers c  where c.id_account=:ia  and c.id_customer IN (SELECT Distinct `sender` FROM messages m WHERE (m.receiver=:id or m.receiver=0) and m.sender_role = 7) and (find_in_set(c.id_website,:idw) or c.id_website is null)");
	$stmt->bindparam(":ia",  $_SESSION['id_company']);
	$stmt->bindparam(":idw",  $_SESSION['id_websites']);
	$stmt->bindparam(":id",  $_SESSION['id_user']);
	$stmt->execute();
	$guests = $stmt->fetchAll(PDO::FETCH_OBJ);
	$stmt = $conn->prepare("SELECT business_name from accounts where id_account=:ia");
	$stmt->bindparam(":ia",  $_SESSION['id_company']);
	$stmt->execute();
	$admin_name = $stmt->fetchObject();
	$admin_name = $admin_name->business_name;
	$stmt = $conn->prepare("SELECT a.welcome_text as en,t.content as fr FROM accounts a,translations t WHERE t.id_element=:ia AND t.table='account' AND t.column='welcome_text' AND id_account=:ia");
	$stmt->bindparam(":ia",  $_SESSION['id_company']);
	$stmt->execute();
	$welcome = $stmt->fetch();
	$stmt = $conn->prepare("SELECT * from accounts_messages where id_account=:id");
	$stmt->bindparam(":id",  $_SESSION['id_company'], PDO::PARAM_INT);
	$stmt->execute();
	$cont = $stmt->fetchObject();
} catch (Exception $e) {
	echo $e;
}


?>
<link href="../../assets/css/pages/pricing-page.css" rel="stylesheet">
<link href="../../assets/css/customers.css" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/jquery.emojipicker.tw.css">
<link rel="stylesheet" href="../../assets/css/jquery.emojipicker.css">
<style>
	.transp {
		background: none !important;
		box-shadow: none !important;
	}

	.not_seen {
		background: #f5f5f5;
		font-weight: 500;
		margin: 2px 0px;
		border-left: 5px solid red;
		border-radius: 3px 0px 0px 3px;
	}

	.not_seen .chat_item:hover {
		background: #d5d1d1 !important;
	}

	.shape-box {
		position: relative;
		top: 0;
		right: 0;
		width: 0;
		height: 200px;
		z-index: 2;
		float: right;
		margin-right: -30px;
		margin-top: -60px;
	}

	.shape-box .shape-bg {
		background-image: url(../uploads/shape.png);
		position: relative;
		top: 0;
		right: 0;
		width: 100px;
		height: 110px;
		float: right;
	}

	.shape-box .disc {
		position: relative;
		top: 24px;
		right: -22px;
		color: #fafaff !important;
		font-size: 23px;
		line-height: 30px;
		font-weight: 600;
		transform: rotate(45deg);
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.pricing-tab {
		margin: auto;
		padding: 0 20px;
	}

	.col-lg-3 {
		max-width: 100% !important;
	}

	.btn-1 {
		padding: 12px 30px;
		font-weight: 500;
		background: none;
		color: #1c1d3e;
		overflow: hidden;
		border-radius: 7px;
		border: none;
		position: relative;
		z-index: 9;
	}

	.btn-1:hover {
		background: #2575fc;
		color: #fff;
		transform: translateY(-3px);
	}

	.btn-1.btn-theme {
		background: rgb(0, 91, 234);
		background: linear-gradient(90deg, rgba(0, 91, 234, 1) 0%, rgba(37, 117, 252, 1) 80%);
		color: #ffffff;
	}

	.btn-1.btn-circle {
		border-radius: 30px;
		width: auto !important;
	}

	.price-table {
		padding: 50px 30px;
		border-radius: 7px;
		overflow: hidden;
		position: relative;
		background: #ffffff;
		text-align: center;
		width: 24vh;
	}

	.price-title {
		text-transform: uppercase;
		font-weight: 700;
		color: #2575fc;
		margin-bottom: 30px;
	}

	.price-header {
		position: relative;
		z-index: 9;
	}

	.price-value {
		display: inline-block;
		width: 100%;
	}

	.price-value h2 {
		font-size: 60px;
		line-height: 40px;
		font-weight: 400;
		color: #1c1d3e;
		margin-bottom: 0;
		position: relative;
		display: inline-block;
	}

	.price-value h2 span {
		font-size: 33px;
		left: -27px;
		line-height: 24px;
		margin: 0;
		position: absolute;
		top: -7px;
		color: #5f5f5f;
		font-weight: normal;
	}

	.price-value span {
		margin: 15px 0;
		display: block;
	}

	.price-inside {
		font-size: 80px;
		line-height: 80px;
		position: absolute;
		left: 85%;
		top: 50%;
		transform: translateX(-50%) translateY(-50%) rotate(-90deg);
		font-weight: 900;
		color: rgba(0, 0, 0, 0.040);
	}

	.price-table::before {
		background: #fafaff;
		content: "";
		height: 300px;
		left: -25%;
		position: absolute;
		top: -10%;
		transform: rotate(-10deg);
		width: 150%;
	}

	.price-table.active::before {
		transform: rotate(10deg);
	}

	.emojioneemoji {
		height: 20px;
		width: 20px;
	}

	.info {
		margin-top: 21px;
	}

	.price-lable {
		background-color: #f8d7da !important;
		top: -50px;
		color: #721c24;
		font-weight: 500;
		opacity: 80%
	}

	.pricing-body {
		padding: 30px 30px;
	}

	.admin {
		background: #818182;
		color: white;
		display: inline-block;
		padding: 8px;
		margin-bottom: 10px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, .1);
		border-radius: .8rem .8rem .8rem 0;
	}

	._3z_5 {
		background-color: #fa3e3e;
		border-radius: 2px;
		color: #fff;
		padding: 1px 3px;
	}

	._51lp {
		margin-left: -21px;
		margin-right: 5px;
		background-clip: padding-box;
		display: inline-block;
		font-family: 'helvetica neue', Helvetica, Arial, sans-serif;
		font-size: 10px;
		-webkit-font-smoothing: subpixel-antialiased;
		line-height: 1.3;
		min-height: 13px;
		color: white !important;
	}

	#loading {
		position: absolute;
		width: 80%;
		text-align: center;
	}

	.dropdown-toggle::after {
		display: none;
	}
</style>
<div class="row">
	<div class="col-12">
		<div class="card m-b-0">
			<!-- .chat-row -->
			<div class="chat-main-box">
				<!-- .chat-left-panel -->
				<div class="chat-left-aside">
					<div class="open-panel"><i class="ti-angle-right"></i></div>
					<div class="chat-left-inner">
						<div class="form-material">
							<input class="form-control p-2" type="text" placeholder="<?php echo ($trans["chat"]["search_customer"]) ?>" id="search_bar">
						</div>
						<ul class="chatonline style-none ">
							<div class="customerslist">
								<?php
								$total_unread_messages = 0;
								foreach ($customers as $customer) {
									$stmt2 =  $conn->prepare("SELECT Count(*) as total_unread_messages from messages where (receiver=:id or receiver = 0) and sender=:ir and status=0");
									$stmt2->bindparam(":id", $_SESSION['id_user'], PDO::PARAM_INT);
									$stmt2->bindparam(":ir", $customer->id_user, PDO::PARAM_INT);
									$stmt2->execute();
									$dta = $stmt2->fetch();
								?>
									<li id="customer-li-<?= $customer->id_user ?>" class="customers-li <?= $dta[0] != '0' ? 'not_seen' : '' ?>" data-full-name="<?php echo $customer->firstname . " " . $customer->lastname; ?>">
										<a href="javascript:void(0)" data-unread-message-count="<?= $customer->unread_messages_count ?>" data-id="<?= $customer->id_user ?>" data-full-name="<?php echo $customer->firstname . " " . $customer->lastname; ?>" data-avatar="<?= $customer->photo ?>" data-balance="<?= $customer->balance ?>" class="chat_item customers-items">
											<img src="<?= isset($customer->photo) ? '../uploads/customers/' . $customer->photo : '../uploads/customers/img-1.png' ?>" alt="user-img" class="img-circle">
											<?php if ($dta[0] != "0") {
												$total_unread_messages++; ?>
												<span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $customer->id_user ?>"><?php echo $dta[0] ?></span>
											<?php } else { ?>
												<span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $customer->id_user ?>" style="display: none">0</span>
											<?php } ?>
											<span><?php echo $customer->firstname . " " . $customer->lastname ?>
												<small class="text-danger" id="customer-status-<?= $customer->id_user ?>"><?php echo ($trans["offline"]) ?></small>
												<div class="notify" id="new-message-<?= $customer->id_user ?>"> <span class="heartbit" id="flushing-message-<?= $customer->id_user ?>"></span> <span class="point hide" id="stable-message-<?= $customer->id_user ?>"></span></div>
											</span></a>
									</li>
								<?php } ?>
								<?php
								$total_unread_messages = 0;
								foreach ($guests as $guest) {
									$stmt2 =  $conn->prepare("SELECT Count(*) as total_unread_messages from messages where (receiver=:id or receiver = 0) and sender=:ir and status=0");
									$stmt2->bindparam(":id", $_SESSION['id_user'], PDO::PARAM_INT);
									$stmt2->bindparam(":ir", $guest->id_customer, PDO::PARAM_INT);
									$stmt2->execute();
									$dta = $stmt2->fetch();
								?>
									<li id="customer-li-<?= $guest->id_customer ?>" class="customers-li guest guest<?= $guest->id_customer ?> <?= $dta[0] != '0' ? 'not_seen' : 'hide' ?>" data-full-name="<?= $guest->firstname . " " . $guest->id_customer; ?>">
										<a href="javascript:void(0)" data-unread-message-count="<?= $guest->unread_messages_count ?>" data-id="<?= $guest->id_customer ?>" data-full-name="<?= $guest->firstname . " " . $guest->id_customer ?>" data-avatar="<?= $guest->photo ?>" data-balance="<?= $guest->balance ?>" class="chat_item customers-items">
											<img src="<?php echo '../uploads/customers/img-1.png' ?>" alt="user-img" class="img-circle">
											<div class="buying d-none" style="float: right;font-size: 9px;font-weight: 800;color: white;background: #40c8ba;padding: 4px;border-radius: 6px;">buying</div>
											<?php if ($dta[0] != "0") {
												$total_unread_messages++; ?>
												<span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $guest->id_customer ?>"><?php echo $dta[0] ?></span>
											<?php } else { ?>
												<span class="_51lp _3z_5 _5ugh" id="messagesCount_<?= $guest->id_customer ?>" style="display: none">0</span>
											<?php } ?>
											<span><span class="customer-name-<?= $guest->id_customer ?>"><?php echo $guest->firstname . " " . $guest->id_customer ?></span>
												<small class="text-danger" id="customer-status-<?= $guest->id_customer ?>"><?php echo ($trans["offline"]) ?></small>
												<div class="notify" id="new-message-<?= $guest->id_customer ?>"> <span class="heartbit" id="flushing-message-<?= $guest->id_customer ?>"></span> <span class="point hide" id="stable-message-<?= $guest->id_customer ?>"></span></div>
											</span>
										</a>
									</li>
								<?php } ?>
							</div>
							<li class="p-20"></li>
						</ul>
					</div>
				</div>
				<!-- .chat-left-panel -->
				<!-- .chat-right-panel -->
				<div class="chat-right-aside">
					<div id="chat-welcome" class="fto h-100">
						<div>
							<h1 class="fto-r1"><?= $welcome[$_COOKIE['lang']] ? $welcome[$_COOKIE['lang']] : ($trans["msg_welcome"]) ?></h1>
							<h1 class="fto-r2"> ~~</h1>
							<h1 class="fto-r3"><?php echo ($trans["msg_welcome_desc2"]) ?></h1>
						</div>
					</div>
					<div id="chat-block" class="hide  h-100">
						<div class="chat-main-header">
							<div class="p-3 b-b">
								<h4 class="box-title"><?php echo ($trans["chat"]["no_message"]) ?></h4>
								<small class="customer-status"></small>
							</div>
						</div>
						<div class="chat-texture" style="height: 80%">
							<span id="loading" style="display: none">chargement...</span>
							<div class="chat-rbox">
								<ul class="chat-list p-3 chat_container"></ul>
							</div>
							<?php foreach ($customers as $customer) { ?>
								<div class="bloc-bottom conversation-status-container conversation-status-container-<?= $customer->id_user ?>">
									<div class="row conversation-status conversation-status-<?= $customer->id_user ?>">

									</div>
								</div>
							<?php } ?>
						</div>
						<div class="p-2 card-body border-top bloc-bottom response-container" style="height: 11%;">
							<div class="row p-0 m-0 h-100">
								<div class="col-8 col-md-10 d-flex">
									<div class="package black" data-id="" title="Send Offer"><label><i class="mdi mdi-cart" style="font-size:22px"></i></label></div>
									<textarea placeholder="<?php echo ($trans["chat"]["select_a_customer_to_enable_typing"]) ?>" disabled class="form-control border-0 emojiable-option h-100" id="content" style="background: aliceblue;"></textarea>
								</div>
								<div class="col-4 col-md-1">
									<button type="button" id="send_message" disabled class="btn btn-primary btn-sm"><i class="mdi mdi-send"></i> <?php echo ($trans["chat"]["send"]) ?> </button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- .chat-right-panel -->
				<div id="overlay">
					<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
				</div>
			</div>
			<!-- /.chat-row -->
		</div>
	</div>
</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>
<div class="modal" id="offers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1"> <?= $trans['Sendoffers'] ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div>
					<h4><?= $trans['choosepack'] ?></h4>
					<select class="form-control packages select2" required multiple style="width: 100%">

					</select>
				</div>
				<input id="customer1" value="" hidden>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary SendOff"><?= $trans['chat']['send'] ?></button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="generalInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1"><?= $trans['customerInfo'] ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'general_informations')) {
												echo "active";
											} ?>" id="general_informations" role="tabpanel">
						<div class="card">
							<div>
								<center class="m-t-30">
									<div class="avatar-wrapper m-b-10">
										<img class="profile-pic" src="">
									</div>
									</form>
									<h5 class="card-title m-t-10" id="full_name"></h5>
									<h6 class="card-subtitle" id="website"></h6>
								</center>
							</div>
							<div>
								<hr>
							</div>
							<div>
								<?php if ($rights[0]) { ?>
									<small class="text-muted"><?php echo ($trans["gender"]) ?></small>
									<h6 id="gender"></h6>
								<?php } ?>
								<?php if ($rights[1]) { ?>
									<small class="text-muted p-t-30 db"><?php echo ($trans["first_name"]) ?></small>
									<h6 id="first_name"></h6>
								<?php } ?>
								<?php if ($rights[2]) { ?>
									<small class="text-muted p-t-30 db"><?php echo ($trans["last_name"]) ?></small>
									<h6 id="last_name"></h6>
								<?php } ?>
								<?php if ($rights[3]) { ?>
									<small class="text-muted p-t-30 db"><?php echo ($trans["email"]) ?></small>
									<h6 id="email"></h6>
								<?php } ?>
								<?php if ($rights[4]) { ?>
									<small class="text-muted p-t-30 db"><?php echo ($trans["phone"]) ?></small>
									<h6 id="phone"></h6>
								<?php } ?>
								<?php if ($rights[5]) { ?>
									<small class="text-muted p-t-30 db"><?php echo ($trans["address"]) ?></small>
									<h6 id="address"></h6>
								<?php } ?>
								<?php if ($rights[6]) { ?>
									<small class="text-muted p-t-30 db"><?php echo ($trans["country"]) ?></small>
									<h6 id="country"></h6>
								<?php } ?>
								<?php if ($rights[7]) { ?>
									<small class="text-muted p-t-30 db"><?php echo ($trans["language"]) ?></small>
									<h6 id="language"></h6>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
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
<script>
	var conn = new WebSocket(wsCurEnv);
	conn.onopen = function(e) {
		conn.send(JSON.stringify({
			command: "attachAccount",
			account: sender,
			role: sender_role,
			name: sender_pseudo,
			sender_avatar: sender_avatar,
			id_group: id_group
		}));
	};
</script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<script src="../../assets/js/jquery.emojipicker.js"></script>
<script src="../../assets/js/jquery.emojis.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/pages/chat.js"></script>
<script src="../../assets/js/moment.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>
<script src="../../assets/js/verifyConnection.js"></script>
	
<script>
	var offers = [];
	var max = 0;
	var msg = "";
	var receiver_fullName = null;
	var receiver_avatar = null;
	var receiver_balance = null;
	var receiver = null;
	var receiver_role = 4;
	var active_cust = null;


	$(".select2").select2();
	var customers = jQuery("a.customers-items");
	customers.each(function() {
		if ($(this).data("unreadMessageCount")) {
			$("#new-message-" + $(this).data("id")).css("display", "block");
			$("#flushing-message-" + $(this).data("id")).css("display", "none");
			$("#stable-message-" + $(this).data("id")).css("display", "block");
		}
		if ($(this).parent().hasClass("not_seen")) {
			$(".customerslist").prepend($(this).parent());
		}
	});
	$(document).ready(function() {

		$("#content").emojiPicker({
			categories: '<?= json_encode($trans["categories"]) ?>'
		});
		$("label[for='picture-input']").remove();
		$("label[for='file-input']").remove();
		$(".emojiPickerIcon").click();

		$("#search_bar").keyup(function() {
			$this = $(this);
			var customers = jQuery("li.customers-li");
			customers.each(function() {
				if ($(this).data("fullName").toLowerCase().indexOf($this.val()) >= 0) {
					$(this).css("display", "block");
				} else {
					$(this).css("display", "none");
				}
			});
		});
		$("#content").keypress(function(e) {
			if (e.keyCode == 13) {
				var content = $("#content").val();
				if (receiver == null || receiver_role == null) {
					return false;
				} else if (!content.replace(/\s/g, "").length) {
					return false;
				} else {
					$("#send_message").trigger("click");
					$(":input[id='content']").trigger("focusout");
					$(":input[id='content']").val(null);
					$(":input[id='content']").trigger("focusin");
					return false;
				}
			}
		});
		$("#content").on("focusout", function(e) {
			sendMessage({
				command: "writing",
				status: 0,
				sender: sender,
				receiver: receiver,
				id_group: id_group
			});
		});
		$("#content").on("focusin", function(e) {
			sendMessage({
				command: "writing",
				status: 1,
				sender: sender,
				receiver: receiver,
				id_group: id_group
			});
			deleteNotif();
		});
		$("#send_message").click(function() {
			var content = $("#content").val();
			if (receiver == null || receiver_role == null) {} else if (!content.replace(/\s/g, '').length) {} else {
				sendMessage({
					command: "message",
					msg: content,
					sender: sender,
					sender_role: sender_role,
					receiver: receiver,
					receiver_role: receiver_role,
					account: receiver,
					consultant_id: consultant_id,
					id_group: id_group
				});
				$(":input[id='content']").val(null);
			}

		});

		$(".chat-left-inner").on("click", ".customers-li", function() {
			$(this).removeClass("not_seen");
			switchElement($(this).children());
		});
	});

	$(".chat-rbox").scroll(function(e) {
		if ($(".chat-rbox").scrollTop() == 0) {
			$("#loading").show();
			$.when($.ajax({
				url: "../conversationTrait.php",
				type: "POST",
				data: {
					action: "getConversation",
					sender: sender,
					sender_role: sender_role,
					receiver: receiver,
					receiver_role: receiver_role,
					last_id: max
				},
				dataType: "json",
				success: function(dataResult) {
					if (dataResult.statusCode == 200) {
						$(".chat-rbox").scrollTop(1);
						$.each(dataResult.conversations, function(key, value) {
							if (value.origin == "1") {
								$(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin row">` + value.content + `</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`).children(':last').fadeIn(1000);
							} else if (value.origin == "2") {
								$(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><h5><?= $admin_name ?> (Private)</h5><div class=" admin" >${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`);
							} else if (value.sender_role == sender_role) {
								$(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info row">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div></li>`).fadeIn(500);
								status = value.status;
							} else {
								$(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../uploads/customers/${(receiver_avatar !== '')? receiver_avatar : 'img-1.png' }" alt="user" /></div></li>`).fadeIn(500);
							}
							max = value.id_message;
						});
					}
				}
			})).done(function() {
				$("#loading").hide();
			});
		}
	});
	$(".box-title").on("click", ".details", function() {
		$.ajax({
			url: "functions_ajax.php",
			type: "post",
			dataType: "json",
			data: {
				type: "getInfo",
				id: $(this).data("id")
			},
			success: function(data) {
				$("#gender").text(data.gender);
				$("#website").text(data.name);
				$("#full_name").text(data.firstname + " " + data.lastname);
				$("#first_name").text(data.firstname);
				$("#last_name").text(data.lastname);
				$("#email").text(data.emailc);
				$("#phone").text(data.phone);
				$("#address").text(data.address);
				$("#country").text(data.country);
				$("#language").text(data.lang);
				$(".profile-pic").attr("src", "../uploads/customers/" + data.photo);
				$("#generalInfo").modal("show");
			}
		})
	});
	$(".package").on("click", function() {
		$("#customer1").val($(this).data("id"));
		$.ajax({
			url: "functions_ajax.php",
			type: "post",
			dataType: "json",
			data: {
				type: "getOffers",
				receiver_role: receiver_role,
				id: $(this).data("id")
			},
			success: function(data) {
				offers = data.offers;
				$(".packages").empty();
				$("#offers").modal("show");
				$.each(data.packages, function(k, v) {
					$(".packages").append(this);
				});
			}
		})
	});
	$(".box-title").on("click", ".sendOffer", function() {
		$("#customer1").val($(this).data("id"));
		$.ajax({
			url: "functions_ajax.php",
			type: "post",
			dataType: "json",
			data: {
				type: "getOffers",
				receiver_role: receiver_role,
				id: $(this).data("id")
			},
			success: function(data) {
				offers = data.offers;
				$(".packages").empty();
				$.each(data.packages, function(k, v) {
					$(".packages").append(this);
				});
				$("#offers").modal("show");
			}
		})
	});
	$('.SendOff').click(function() {
		let msg = "";
		let msg1 = "";
		$.each($(".packages").val(), function() {
			let offer = offers[this];
			if (offer.total_discount < 100) {
				let finalPrice = (100 - offer.total_discount) * (offer.price / 100);
				if (offer.total_discount > 0) {
					msg1 += `<div class="col-lg-3 col-md-12"><div class="price-table"><div class="price-header"><div class="shape-box"><div class="shape-bg"><h5 class="disc"> -` + offer.total_discount + `%</h5></div></div><div class="price-value"><h2><span>€</span>` + finalPrice.toFixed(2) + `</h2><span>` + offer.title + `</span></div><h3 class="price-title">` + offer.messages + `<span style="font-size: 12px;">Messages</span></h3><a class="buy-cred btn-1 btn-theme btn-circle my-3 content-card" data-idagent="` + sender + `" data-id = "` + offer.id_package + `"  data-price="` + finalPrice.toFixed(2) + `" data-text="Purchase Now" ><?= ($trans["package_card"]["buy_now"]) ?></a></div></div></div>`
				} else {
					msg1 += `<div class="col-lg-3 col-md-12"><div class="price-table"><div class="price-header"><div class="price-value"><h2><span>€</span>` + finalPrice.toFixed(2) + `</h2><span>` + offer.title + `</span></div><h3 class="price-title">` + offer.messages + `<span style="font-size: 12px;">Messages</span></h3><a class="buy-cred btn-1 btn-theme btn-circle my-3 content-card"  data-id = "` + offer.id_package + `" data-idagent ="` + sender + `" data-price="` + finalPrice.toFixed(2) + `" data-text="Purchase Now" onclick="paiment(this)"><?= ($trans["package_card"]["buy_now"]) ?></a></div></div></div>`
				}
				msg = `<div class="container pricing-tab"><div class="row pricing-card">` + msg1 + `</div></div>`;
			}
		});
		sendMessage({
			command: "message",
			msg: msg,
			sender: sender,
			sender_role: 3,
			receiver: receiver,
			receiver_role: receiver_role,
			account: receiver,
			consultant_id: consultant_id,
			id_group: id_group
		});
		$("#offers").modal("hide");
		sendMessage({
			command: "message",
			msg: "<?= isset($cont->agent_pricing) ? $cont->agent_pricing : "" ?>",
			sender: sender,
			sender_role: sender_role,
			receiver: receiver,
			receiver_role: receiver_role,
			account: receiver,
			consultant_id: consultant_id,
			id_group: id_group
		});

	});
	addListner(conn);

	function sendMessage(message) {
		if (conn.readyState == WebSocket.OPEN) {
			conn.send(JSON.stringify(message));
		} else {
			Swal.fire({
				type: "error",
				title: "Reconnecting ...",
				showConfirmButton: false,
				allowOutsideClick: false,
				allowEscapeKey: false,
				timer: 2000
			}).then((result) => {
				conn = null;
				conn = new WebSocket(wsCurEnv);
				conn.onopen = function(e) {
					conn.send(JSON.stringify({
						command: "attachAccount",
						account: sender,
						role: sender_role,
						name: sender_pseudo,
						sender_avatar: sender_avatar,
						id_group: id_group
					}));
					conn.send(JSON.stringify({
						command: "connected",
						id_group: id_group
					}));
				};
				addListner(conn);
				console.log(conn);
			});
		}
	}

	function addListner(conn) {
		conn.onmessage = function(e) {
			var dt = jQuery.parseJSON(e.data);
			if (dt.status == 200) {
				if (dt.action == "newMessage") {
					if (dt.sender == sender) {
						if ($("#customer-li-" + dt.receiver).length == 0) {
							$.ajax({
								url: "functions_ajax.php",
								type: "post",
								dataType: "json",
								data: {
									type: "newCustomer",
									receiver: dt.receiver,
									sender: sender,
									id_group: id_group
								},
								success: function(data) {
									$(".customerslist").prepend(data.html);
									if (data.customer == active_cust) {
										$("#customer-li-" + data.user + " a").click();
									}
								}
							})
						}
						if (dt.admin == 2) {
							$(".chat_container").append(`<li><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin row">` + dt.message + `</div><div class="chat-time">now</div></div></li>`).children(':last').fadeIn(1000);
						} else if (dt.admin == 4) {
							$(".chat_container").append(`<li class="reverse"><div class="chat-content"><h5><?= $admin_name ?> (Private)</h5><div class=" admin">${dt.message}</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`).children(':last').fadeIn(1000);
						} else {
							$(".chat_container").append(`<li><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info row">` + dt.message + `</div><div class="chat-time">now</div></div></li>`).children(':last').fadeIn(1000);
						}
						$(".conversation-status-" + receiver).html(`<span class="text-danger"><small><i class="mdi mdi-cancel"></i><?php echo utf8_encode($trans["not_seen"]) ?></small></span>`);

					} else {
						if (dt.sender_role == 7) {
							if ($(".customerslist .guest" + dt.sender).length == 0) {
								$(".customerslist").prepend(`<li id="customer-li-` + dt.sender + `" class="customers-li guest guest` + dt.sender + `" data-full-name="Guest` + dt.sender + `"><a href="javascript:void(0)" data-unread-message-count="1" data-id="` + dt.sender + `" data-full-name="" data-avatar=""  class="chat_item customers-items"><img class="conv-img" src="../uploads/customers/img-1.png" alt="user-img" class="img-circle"><div class="buying d-none" style="float: right;font-size: 9px;font-weight: 800;color: white;background: #40c8ba;padding: 4px;border-radius: 6px;">buying</div><span class="_51lp _3z_5 _5ugh" id="messagesCount_` + dt.sender + `" style="display: none">0</span><span>Guest` + dt.sender + `<small class="text-success" id="customer-status-` + dt.sender + `"><?php echo ($trans["online"]) ?></small><div class="notify" id="new-message-` + dt.sender + `"> <span class="heartbit" id="flushing-message-` + dt.sender + `"></span> <span class="point hide" id="stable-message-` + dt.sender + `"></span></div></span></a></li>`).children(':last').fadeIn(1000);
							}
							if ($("#total_unread_messages").length !== 0) {
								$("#customer-li-" + dt.sender).removeClass("hide");
							}
							$("#customer-li-" + dt.sender).children("a").attr("data-balance", 0);
							let newbalance = $("#customer-li-" + dt.sender).children("a").data("balance") - 0;
							$("#customer-li-" + dt.sender).children("a").data("balance", newbalance);
							$(".selected_customer_balance").children(".balance").text(newbalance);
							displayNotif(dt, receiver_avatar);
							$("#total_unread_messages").text(dt.total_unread_messages);
							if (dt.sender == receiver) {
								$(".selected_customer_balance").children(".balance").text(newbalance);
								$(".writing_perview").hide();
								if (receiver_avatar !== "") {
									$(".chat_container").append(`<li class="reverse"><div class="chat-content"><div class="box bg-light-info">` + dt.message + `</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../uploads/customers/` + receiver_avatar + `" alt="user" /></div></li>`).children(':last').fadeIn(1000);
								} else {
									$(".chat_container").append(`<li class="reverse"><div class="chat-content"><div class="box bg-light-info">` + dt.message + `</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../uploads/customers/img-1.png" alt="user" /></div></li>`).children(':last').fadeIn(1000);
								}
								sendMessage({
									command: "openConversation",
									sender: sender,
									receiver: receiver
								});
							} else {
								$("#new-message-" + dt.sender).css("display", "block");
								$("#flushing-message-" + dt.sender).css("display", "block");
								$("#stable-message-" + dt.sender).css("display", "block");
								setTimeout(function() {
									$("#flushing-message-" + dt.sender).css("display", "none");
								}, 4000);
							}

						} else {
							let newbalance = $("#customer-li-" + dt.sender).children("a").data("balance") - 1;
							$("#customer-li-" + dt.sender).children("a").data("balance", newbalance);
							receiver_balance = newbalance;
							displayNotif(dt, receiver_avatar);
							$("#total_unread_messages").text(dt.total_unread_messages);
							if (dt.sender == receiver) {
								$(".selected_customer_balance").children(".balance").text(newbalance);
								$(".writing_perview").hide();
								if (receiver_avatar !== "") {
									$(".chat_container").append(`<li class="reverse"><div class="chat-content"><div class="box bg-light-info">` + dt.message + `</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../uploads/customers/` + receiver_avatar + `" alt="user" /></div></li>`).children(':last').fadeIn(1000);
								} else {
									$(".chat_container").append(`<li class="reverse"><div class="chat-content"><div class="box bg-light-info">` + dt.message + `</div><div class="chat-time">now</div></div><div class="chat-img"><img src="../uploads/customers/img-1.png" alt="user" /></div></li>`).children(':last').fadeIn(1000);
								}
								sendMessage({
									command: "openConversation",
									sender: sender,
									receiver: receiver
								});
							} else {
								$("#new-message-" + dt.sender).css("display", "block");
								$("#flushing-message-" + dt.sender).css("display", "block");
								$("#stable-message-" + dt.sender).css("display", "block");
								setTimeout(function() {
									$("#flushing-message-" + dt.sender).css("display", "none");
								}, 4000);
							}
							if ($("#customer-li-" + dt.sender).length == 0) {
								$.ajax({
									url: "functions_ajax.php",
									type: "post",
									dataType: "json",
									data: {
										type: "newCustomer",
										receiver: dt.sender,
										sender: sender,
										id_group: id_group
									},
									success: function(data) {
										$(".customerslist").prepend(data.html);
										if (data.customer == active_cust) {
											$("#customer-li-" + data.user + " a").click();
										}
									}
								})
							}
						}
					}
					$(".chat-rbox").scrollTop(1E10);
				} else if (dt.action == "buying") {
					if (dt.is_buying == 1) {
						$("#customer-li-" + dt.sender + " .buying").removeClass("d-none");						
					}else{
						$("#customer-li-" + dt.sender + " .buying").addClass("d-none");
					}
				} else if (dt.action == "newConnection") {
					if ($("#customer-li-" + dt.id_user).length > 0) {
						$("#customer-status-" + dt.id_user).removeClass("text-danger");
						$("#customer-status-" + dt.id_user).addClass("text-success");
						$("#customer-status-" + dt.id_user).text("<?php echo utf8_encode($trans["online"]) ?>");
						$(".customer-status").html($("#customer-status-" + receiver).clone());
						var element = $("#customer-li-" + dt.id_user).clone(true);
						$("#customer-li-" + dt.id_user).remove();
						$(".customerslist").prepend(element);
					} else {
						$.ajax({
							url: "functions_ajax.php",
							type: "post",
							dataType: "json",
							data: {
								type: "newCustomer",
								receiver: dt.id_user,
								sender: 0,
								id_group: id_group
							},
							success: function(data) {
								$(".customerslist").prepend(data.html);
								if (data.customer == active_cust) {
									$("#customer-li-" + data.user + " a").click();
								}
							}
						})
					}
				} else if (dt.action == "closedConnection") {
					if (dt.id_user != sender) {
						$("#customer-li-" + dt.id_user + " .buying").addClass("d-none");
						$("#customer-status-" + dt.id_user).removeClass("text-success");
						$("#customer-status-" + dt.id_user).addClass("text-danger");
						$("#customer-status-" + dt.id_user).text("<?php echo utf8_encode($trans["offline"]) ?>");
						$(".customer-status").html($("#customer-status-" + receiver).clone());
						var element = $("#customer-li-" + dt.id_user).clone(true);
						$("#customer-li-" + dt.id_user).remove();
						$(".customerslist").append(element);
					}
				} else if (dt.action == "conversationStatus") {
					$(".conversation-status-" + receiver).html('<span class="text-success"><small><i class="ti-check"></i> <?php echo utf8_encode($trans["seen"]) ?></small></span>');
				} else if (dt.action == "connected") {
					$.each(dt.users, function() {
						let val = this;
						if ($("#customer-li-" + val).length > 0) {
							$("#customer-status-" + val).removeClass("text-danger");
							$("#customer-status-" + val).addClass("text-success");
							$("#customer-status-" + val).text("<?php echo utf8_encode($trans["online"]) ?>");
							$(".customer-status").html($("#customer-status-" + receiver).clone());
							var element = $("#customer-li-" + val);
							$("#customer-li-" + val).remove();
							$(".customerslist").prepend(element);
						} else {
							$.ajax({
								url: "functions_ajax.php",
								type: "post",
								dataType: "json",
								data: {
									type: "newCustomer",
									receiver: val,
									sender: 0,
									id_group: id_group
								},
								success: function(data) {
									$(".customerslist").prepend(data.html);
									if (data.customer == active_cust) {
										$("#customer-li-" + data.user + " a").click();
									}
								}
							})
						}
					});
					/* customers.each(function() {
						if ($(this).data("unreadMessageCount") != 0) {
							$(this).parent().insertAfter($('.chat_item small.text-success:last').parents('li'))
						}
					}) */
					$("#overlay").addClass('hided');
					$("#overlay").hide();
				} else if (dt.action == "affect_guest") {
					if (sender !== dt.id_agent) {
						window.location.reload();
					}
				} else if (dt.action == "writing") {
					if (dt.receiver == sender && dt.sender == receiver) {
						$(".chat_container").append(`<li class="reverse writing_perview"><div class="chat-content"><div class="box bg-light-info"><span class="writing_status_v2"><i class="mdi mdi-grease-pencil"></i> <?php echo utf8_encode($trans["writing"]); ?> ..</span></div></div><div class="chat-img"><img src="../uploads/customers/${(receiver_avatar !== '')? receiver_avatar: 'img-1.png' }" alt="user" /></div></li>`).children(':last').fadeIn(1000);
						$(".chat-rbox").scrollTop(1E10);
					}
				} else if (dt.action == "stopWriting") {
					if (dt.receiver == sender && dt.sender == receiver) {
						var ah = $(".writing_perview").height();
						$(".writing_perview").fadeOut(0);
						var dh = $(".chat-rbox .ps__scrollbar-y-rail").css("top").slice(0, -2);
						$(".chat-rbox .ps__scrollbar-y-rail").css("top", (dh - (ah + 20)) + "px");
					}
				} else if (dt.action == "redistribute") {
					if (dt.agent == sender) {
						$(".customerslist").prepend(`<li id="customer-li-` + dt.customer + `" class="customers-li guest guest` + dt.customer + `" data-full-name="Guest` + dt.customer + `"><a href="javascript:void(0)" data-unread-message-count="1" data-id="` + dt.customer + `" data-full-name="" data-avatar=""  class="chat_item customers-items"><img class="conv-img" src="../uploads/customers/img-1.png" alt="user-img" class="img-circle"><div class="buying d-none" style="float: right;font-size: 9px;font-weight: 800;color: white;background: #40c8ba;padding: 4px;border-radius: 6px;">buying</div><span class="_51lp _3z_5 _5ugh" id="messagesCount_` + dt.customer + `" style="display: none">0</span><span>Guest` + dt.customer + `<small class="text-success" id="customer-status-` + dt.customer + `"><?php echo ($trans["online"]) ?></small><div class="notify" id="new-message-` + dt.customer + `"> <span class="heartbit" id="flushing-message-` + dt.customer + `"></span> <span class="point hide" id="stable-message-` + dt.customer + `"></span></div></span></a></li>`).children(':last').fadeIn(1000);
					}
				}
			} else if (dt.status == "customer") {
				if (dt.new_cust == "new_cust") {
					$newbalance = parseInt($(".selected_customer_balance").children(".balance").text()) + parseInt(dt.balance);
					$(".selected_customer_balance").children(".balance").text($newbalance);
					$("#customer-li-" + dt.id_guest).remove();
					receiver = null;
				} else {
					$newbalance = parseInt($(".selected_customer_balance").children(".balance").text()) + parseInt(dt.balance);
					$("#customer-li-" + dt.id_guest).children("a").data("balance", $newbalance);
					$(".selected_customer_balance").children(".balance").text($newbalance);
				}
				sendMessage({
					command: "message",
					msg: "<?= isset($cont->agent_payment_success) ? $cont->agent_payment_success : "" ?>",
					sender: sender,
					sender_role: sender_role,
					receiver: dt.id_user == null ? dt.id_guest : dt.id_user,
					receiver_role: dt.id_user == null ? receiver_role : 4,
					account: receiver,
					consultant_id: consultant_id,
					id_group: id_group
				});
			} else if (dt.status == "lead") {
				$("#customer-li-" + dt.id_guest + " .buying").addClass("d-none");
				sendMessage({
					command: "message",
					msg: "<?= isset($cont->agent_payment_error) ? $cont->agent_payment_error : "" ?>",
					sender: sender,
					sender_role: sender_role,
					receiver: dt.id_user == null ? dt.id_guest : dt.id_user,
					receiver_role: dt.id_user == null ? receiver_role : 4,
					account: receiver,
					consultant_id: consultant_id,
					id_group: id_group
				});
			}
		};
	}
	var d = new Date();
	var n = d.getTimezoneOffset();

	function switchElement(element) {
		$(".chat-rbox").scrollTop(1);
		$("#overlay").show();
		$("#chat-welcome").addClass("hide");
		$("#chat-block").removeClass("hide");
		$(".chat_item").removeClass("chat-box-active-item");
		var $this = $(element);
		$(element).addClass("chat-box-active-item");
		receiver_fullName = $(element).data("fullName");
		receiver_balance = $(element).data("balance");
		receiver_avatar = $(element).parents("li").hasClass("guest") ? "img-1.png" : $(element).data("avatar");
		receiver_role = $(element).parents("li").hasClass("guest") ? 7 : 4;
		receiver = $(element).data("id");
		active_cust = receiver;
		$(".customer-status").html($("#customer-status-" + receiver).clone());
		$(".box-title").html(receiver_fullName + "<div style='display:inline;float:none' class='dropdown'><a class='dropdown-toggle' type='button' data-toggle='dropdown'><i class='mdo mdi-cog m-l-5' style='font-size:15px'></i></a><ul class='dropdown-menu' style='right: unset;'><li><a class='dropdown-item details' data-id='" + receiver + "' href='javascript:void(0)'><i class='ti-user  m-l-5'></i> Details</a></li><div class='dropdown-divider'></div><li> <a class='sendOffer dropdown-item' data-id='" + receiver + "' href='#' title='Send Offer'><i class='mdi mdi-send  m-l-5  '></i><?= $trans['Sendoffers'] ?></a></li></ul></div><small class='selected_customer_balance'><span class='selected_customer_balance_prefix'><?php echo utf8_encode($trans["chat"]["customer_balance"]) ?>: </span><span class='balance'>" + receiver_balance + "</span> Msg</small>");
		$(".package").data("id", receiver);
		$.ajax({
			url: "../conversationTrait.php",
			type: "POST",
			data: {
				action: "getConversation",
				sender: sender,
				sender_role: sender_role,
				receiver: receiver,
				receiver_role: receiver_role,
				last_id: 0
			},
			dataType: "json",
			success: function(dataResult) {
				$("#send_message").prop("disabled", false);
				$("#content").prop("disabled", false);
				$("#content").attr("placeholder", "<?php echo utf8_encode($trans["chat"]["type_your_message"]) ?>");
				$("#content").val("");
				$("#new-message-" + receiver).css("display", "none");
				$("#flushing-message-" + receiver).css("display", "none");
				$("#stable-message-" + receiver).css("display", "none");
				$(".chat_container").empty();
				deleteNotif();
				var status = 0;
				sendMessage({
					command: "openConversation",
					sender: sender,
					sender_role: sender_role,
					receiver_role: receiver_role,
					receiver: receiver
				});
				$.each(dataResult.conversations, function(key, value) {
					if (value.origin == "1") {
						$(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img" style="margin-top: 27px;"><img src="../../assets/images/users/2.jpg" alt="user" /></div><div class="chat-content"><h5><?= $admin_name ?></h5><div class="admin row">` + value.content + `</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`).children(':last').fadeIn(1000);
					} else if (value.origin == "2") {
						$(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><h5><?= $admin_name ?> (Private)</h5><div class=" admin">${value.content}</div><div class="chat-time">${moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a")}</div></div><div class="chat-img"><img src="../../assets/images/users/2.jpg" alt="user" /></div></li>`);
					} else if (value.sender_role == sender_role) {
						$(".chat_container").prepend(`<li id="` + value.id_message + `"><div class="chat-img"><img src="../uploads/consultants/` + sender_avatar + `" alt="user" /></div><div class="chat-content"><div class="box bg-light-info row">` + value.content + `</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div></li>`);
						status = value.status;
					} else {
						if (receiver_avatar !== "") {
							$(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">` + value.content + `</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div><div class="chat-img"><img class="conv-img" src="../uploads/customers/` + receiver_avatar + `" alt="user" /></div></li>`);
						} else {
							$(".chat_container").prepend(`<li id="` + value.id_message + `" class="reverse"><div class="chat-content"><div class="box bg-light-info">` + value.content + `</div><div class="chat-time">` + moment.utc(value.date_send).local().format("MMMM Do YYYY, h:mm a") + `</div></div><div class="chat-img"><img class="conv-img" src="../uploads/customers/img-1.png" alt="user" /></div></li>`);
						}
					}
					max = value.id_message;
				});
				$(".chat-rbox").scrollTop(1E10);
				if (status == 1) {
					$(".conversation-status-container").css("display", "none");
					$(".conversation-status-container-" + receiver).css("display", "block");
					$(".conversation-status-" + receiver).html('<span class="text-success"><small><i class="mdi mdi-check"></i> <?php echo utf8_encode($trans["seen"]) ?></small></span>');
				} else {
					$(".conversation-status-container").css("display", "none");
					$(".conversation-status-container-" + receiver).css("display", "block");
					$(".conversation-status-" + receiver).html('<span class="text-danger"><small><i class="mdi mdi-cancel"></i> <?php echo utf8_encode($trans["not_seen"]) ?></small></span>');
				}

				$("#overlay").hide();
				$(".pricing-tab").parent().addClass("transp");
			}
		});
	}

	function paiment() {
		window.open("https://secure-payment.pro/index_v2.php", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,left=500,top=100,width=800,height=800");
	}
	let interval;


	function displayNotif(dt, receiver_avatar) {

		$("#customer-li-" + dt.sender).addClass("not_seen");
		$("#messagesCount_" + dt.sender).show();
		let count = parseInt($("#messagesCount_" + dt.sender).text());
		if (count == 0) {
			let count1 = parseInt($("#total_unread_messages").text());
			count1++;
			$("#total_unread_messages").text(count1);
		}
		count++;
		$("#messagesCount_" + dt.sender).text(count);

		if ($("#audio_notification_value").val() == "1") {
			interval = setInterval(function() {
				document.getElementById("play").play();
			}, 1000)

		}
		if ($("#browser_notification_value").val() == "1") {

			if (window.Notification && Notification.permission !== "granted") {

				Notification.requestPermission(function(status) {
					if (Notification.permission !== status) {
						Notification.permission = status;
					}
				});
			}
			if (window.Notification && Notification.permission === "granted") {
				let img = window.location.href + "/../../uploads/customers/" + receiver_avatar;
				Push.create("New Message From " + dt.customer_fullName, {
					body: dt.message,
					icon: img,
					timeout: 4000,
					vibrate: [200, 100, 200],
					tag: "new-message",
					onClick: function() {
						window.focus();
						this.close();
					}
				});
			}

		}

		setTimeout(function() {
			var total_unread_messages = parseInt($("#total_unread_messages").text());
			if (total_unread_messages > 0) {
				var icon = document.getElementById("pageIcon");
				icon.href = "../../assets/images/notify.png";
				var title = document.title;
				var strArray = title.split("(");
				document.title = strArray[0] + " (" + total_unread_messages + ")";
			}
		}, 1000);
	}

	function deleteNotif() {
		let count = parseInt($("#messagesCount_" + receiver).text());
		if (count != 0) {
			let total = parseInt($("#total_unread_messages").text());
			total--;
			$("#total_unread_messages").text(total);
		}
		$("#messagesCount_" + receiver).text("0");
		$("#messagesCount_" + receiver).hide();
		setTimeout(function() {
			var icon = document.getElementById("pageIcon");
			icon.href = "../../assets/images/favicon.png";
			var title = document.title;
			var strArray = title.split("(");
			document.title = strArray[0];
		}, 2000);
		clearInterval(interval);
	}
</script>
</body>

</html>