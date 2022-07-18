<?php
$page_name = "dashboard";
include('header.php');

$st = $conn->prepare("SELECT (SELECT count(*) from messages m where m.sender=:IDU) as sent_messages,
                       (SELECT count(*) from messages m where m.receiver=:IDU) as received_messages,
                       (SELECT count(*) from(SELECT (SELECT receiver FROM messages msg, users u  WHERE u.id_profile=t.id_customer AND u.profile=4 AND msg.sender=u.id_user AND msg.date_send<t.date_add ORDER BY date_send DESC LIMIT 1) as receiver FROM transactionsc t,customers c
                       WHERE t.id_customer=c.id_customer AND c.id_account=:IDA ) as c WHERE c.receiver=:IDU ) as transactions,
                       (SELECT AVG(TIME_TO_SEC(TIMEDIFF(CASE when (SELECT ms.date_send from messages ms where ms.sender=:IDU and ms.receiver=m.sender and ms.date_send>m.date_send order by ms.id_message LIMIT 1) is not null then (SELECT ms.date_send from messages ms where ms.sender=:IDU and ms.receiver=m.sender and ms.date_send>m.date_send order by ms.id_message  LIMIT 1)else now() end,m.date_send )))
                        from messages m where m.receiver=:IDU order by m.id_message) as latency,(SELECT AVG(TIME_TO_SEC(TIMEDIFF(
            CASE when (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender=firstMsg.receiver order by date_send LIMIT 1) is not null then (SELECT msg.date_send from messages msg where msg.receiver=firstMsg.sender and msg.date_send>firstMsg.date_send AND msg.sender_role=3 and msg.sender = firstMsg.receiver order by date_send LIMIT 1)else now() end,firstMsg.date_send )))  from (SELECT sender,receiver ,date_send from messages where  receiver=:IDU AND sender_role=4 GROUP BY sender) as firstMsg) as pick");
$st->bindparam(":IDU", $_SESSION['id_user']);
$st->bindparam(":IDA", $account->id_account);
$st->execute();
$stat = $st->fetchObject();
?>
<style>
    .card {
        min-height: 85%;
        max-height: 85%;
    }

    .ml-auto {
        margin-top: 15px;
    }
</style>
<link href="../../assets/node_modules/morrisjs/morris.css" rel="stylesheet">
<link href="../../assets/css/pages/dashboard1.css" rel="stylesheet">
<div class="card-group">
    <div class="card ">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block">
                        <div>
                            <h3><i class="mdi mdi-send"></i></h3>
                            <p class="text-muted"><?php echo ($trans["consultant_account"]["dashboard"]["sent_messages"]) ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-primary"><?= $stat->sent_messages ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="card ">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block">
                        <div>
                            <h3><i class="mdi mdi-call-received"></i></h3>
                            <p class="text-muted"><?php echo ($trans["consultant_account"]["dashboard"]["received_messages"]) ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-cyan"><?= $stat->received_messages ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="card ">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block">
                        <div>
                            <h3><i class="mdi mdi-shopping"></i></h3>
                            <p class="text-muted">transactions</p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-purple"><?= $stat->transactions   ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="card ">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block">
                        <div>
                            <h3><i class="mdi mdi-timer"></i></h3>
                            <p class="text-muted"><?php echo ($trans["consultant_account"]["dashboard"]["average_response_time"]) ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-success"><?php echo gmdate("H:i:s", $stat->latency); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="card ">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block">
                        <div>
                            <h3><i class="mdi mdi-phone"></i></h3>
                            <p class="text-muted"><?= $trans['reportingsPage']['Pick_up_time'] ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-success"><?php echo gmdate("H:i:s", $stat->pick); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- End Info box -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Over Visitor, Our income , slaes different and  sales prediction -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
<footer class="footer">
    © 2019 Private chat by Diamond services
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
<!-- Bootstrap popper Core JavaScript -->
<script src="../../assets/node_modules/popper/popper.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/js/pages/consultants.js"></script>
</body>

</html>