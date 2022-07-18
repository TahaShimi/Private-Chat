<?php
$page_name = "dashboard";
ini_set("display_errors", 1);
include('header.php');
$st = $conn->prepare("SELECT unread_duration FROM `accounts` WHERE id_account=:IDA");
$st->bindparam(":IDA", $_SESSION['id_account']);
$st->execute();
$duration = $st->fetchObject();
$unread_duration = date('Y-m-d H:i:s', strtotime("- " . $duration->unread_duration . " seconds" ));
$st = $conn->prepare("SELECT * FROM customers c left join users u on u.id_profile=c.id_customer join messages m ON m.id_message = ( SELECT id_message FROM messages mi WHERE ((mi.sender = c.id_customer or mi.sender = 0 or mi.sender = u.id_user) or mi.receiver = c.id_customer) ORDER BY mi.sender DESC, mi.id_message DESC LIMIT 1 ) where m.status =0 and seen_at is NULL and m.date_send < :x_temps and c.id_account=:IDA and (c.id_customer IN (SELECT DISTINCT sender from messages where sender_role = 7) or u.id_user IN (SELECT DISTINCT sender from messages where sender_role = 4));");
$st->bindparam(":x_temps", $unread_duration);
$st->bindparam(":IDA", $_SESSION['id_account']);
$st->execute();
$mssgs = $st->fetchAll(PDO::FETCH_OBJ);
$nbrmssgs = $st->rowCount();
$st = $conn->prepare("SELECT (SELECT count(*) from websites w where w.id_account=:IDA) as websites, (SELECT count(*) from customers c where c.id_account=:IDA) as customers, (SELECT count(*) from transactionsc tc join customers c on tc.id_customer=c.id_customer where c.id_account=:IDA) as transactions, (SELECT currency from accounts where id_account=:IDA) as currency");
$st->bindparam(":IDA", $_SESSION['id_account']);
$st->execute();
$stat = $st->fetchObject();
$st = $conn->prepare("SELECT sum(tc.final_price) as income,c.country from transactionsc tc join customers c on tc.id_customer=c.id_customer where c.id_account=:IDA GROUP BY c.country");
$st->bindparam(":IDA", $_SESSION['id_account']);
$st->execute();
$incomes = $st->fetchAll();
foreach ($incomes as  $key => $income) {
    $income['currency'] = $currencies[$income['country']];
    $incomes[$key] = $income;
}
$st = $conn->prepare("SELECT COUNT(*) as total ,t.date_add FROM transactionsc t,customers c WHERE t.id_customer=c.id_customer AND c.id_account=:AC  AND YEAR(t.date_add)=YEAR(CURRENT_TIMESTAMP) GROUP BY EXTRACT(YEAR_MONTH FROM t.date_add)");
$st->bindparam(":AC", $_SESSION['id_account']);
$st->execute();
$current = $st->fetchAll();
$chart = ['01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0];
foreach ($current as $sale) {
    $m = date("m", strtotime($sale['date_add']));
    $chart[$m] = intval($sale['total']);
}
$st = $conn->prepare("SELECT COUNT(*) as total ,t.date_add FROM transactionsc t,customers c WHERE t.id_customer=c.id_customer AND c.id_account=:AC  AND YEAR(t.date_add)='" . date("Y", strtotime("-1 year")) . "' GROUP BY EXTRACT(YEAR_MONTH FROM t.date_add)");
$st->bindparam(":AC", $_SESSION['id_account']);
$st->execute();
$last = $st->fetchAll();
$chart1 = ['01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0];
foreach ($last as $sale) {
    $m = date("m", strtotime($sale['date_add']));
    $chart1[$m] = intval($sale['total']);
}
$s1 = $conn->prepare("SELECT id_website,storage FROM `websites` WHERE id_account = :ID");
$s1->bindParam(':ID', $_SESSION['id_account']);
$s1->execute();
$websites = $s1->fetchAll();
$totalsize = 0;
$storage = array_sum(array_column($websites, 'storage'));
foreach ($websites as $website) {
    $totalsize += array_sum(array_map('filesize', glob("../../uploads/messages/pictures/*-" . $website['id_website'] . ".*")));
    $totalsize += array_sum(array_map('filesize', glob("../../uploads/messages/files/*-" . $website['id_website'] . ".*")));
}
function dividing($val, $on)
{
    return $on != 0 ? $val / $on : 0;
}
?>
<style>
    .text-red{
        color: red;
    }
    .col-lg-3 {
        padding-right: 0;
        padding-left: 0;
    }
</style>
<link href="../../assets/css/pages/progressbar-page.css" rel="stylesheet">
<div class="card-group">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-web"></i></h3>
                            <p class="text-muted"><?= $trans['websites'] ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-primary"><?= $stat->websites ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-account-multiple-outline"></i></h3>
                            <p class="text-muted"><?= $trans['customersP'] ?></p>
                        </div>
                        <div class="ml-auto">
                            <h3 class="counter text-cyan"><?= $stat->customers ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-shopping"></i></h3>
                            <p class="text-muted">transactions</p>
                        </div>
                        <div class="ml-auto">
                            <h3 class="counter text-purple"><?= $stat->transactions  ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div>
                        <div class="d-flex no-block align-items-center">
                            <div>
                                <h3><i class="mdi mdi-comment-multiple-outline"></i></h3>
                                <p class="text-muted"><?= $trans['reportingsPage']['Unreaded_messages'] ?></p>
                            </div>
                            <div class="ml-auto">
                                <h3 class="counter text-red"><?= $nbrmssgs ?></h2>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <a href="unread_messages.php"> <button type="button" class="btn btn-sm btn-secondary"><?= $trans['details']  ?></button> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-cash-multiple"></i></h3>
                            <p class="text-muted"><?= $trans['Totalincomes']  ?></p>
                        </div>
                        <div class="ml-auto text-right">
                            <h3 class="counter text-success income"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#myModal"><?= $trans['details']  ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div>
                        <div class="d-flex no-block align-items-center">
                            <div>
                                <h3><i class="mdi mdi-database"></i></h3>
                                <p class="text-muted"><?= $trans['TotalStorage'] ?></p>
                            </div>
                            <div class="ml-auto">
                                <h3 class="counter text-success"><?= $storage  ?> MB</h2>
                            </div>
                        </div>
                        <span class="text-muted"><?= number_format(dividing(dividing($totalsize, 1048576), $storage), 2) . "% " . $trans['Used'] ?> </span>
                        <div class="progress" style="width:100%">
                            <div class="progress-bar <?php if (number_format(dividing(dividing($totalsize, 1048576), $storage), 2) > 80) {
                                                            echo "bg-danger";
                                                        } else {
                                                            echo "bg-success";
                                                        } ?> wow animated progress-animated" style=<?= "width:" . number_format(dividing(dividing($totalsize, 1048576), $storage), 2) . "%;height:6px;" ?> role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div id="container"></div>
    </div>
</div>
</div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['Totalincomes'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="display  nowrap table table-hover table-striped">
                    <thead>
                        <th>Income</th>
                        <th>Currency</th>
                    </thead>
                    <tbody>
                        <?php foreach ($incomes as $income) { ?>
                            <tr>
                                <td><?= $income['income'] ?></td>
                                <td><?= $currencies[$income['country']] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<!-- Bootstrap popper Core JavaScript -->
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<!-- ============================================================== -->
<!-- This page plugins -->
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<!-- ============================================================== -->
<!-- Chart JS -->
<script src="../../assets/js/dashboard1.js"></script>
<script src="../../assets/js/highcharts.js"></script>
<script src="../../assets/js/notification.js"></script>

<script>
    var incomes = <?= json_encode($incomes) ?>;
    var total = 0;
    var change;
    Highcharts.chart('container', {
        chart: {
            type: 'line'
        },
        title: {
            text: '<?= $trans["Monthly_Sales"] ?>'
        },
        xAxis: {
            categories: <?= json_encode($trans["months"]) ?>
        },
        yAxis: {
            title: {
                text: '<?= $trans["Sales_number"] ?>'
            }
        },
        credits: false,
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        yAxis: {
            tickInterval: 1
        },
        series: [{
            name: "<?= $trans["Current_year"] ?>",
            data: <?= json_encode(array_values($chart)) ?>
        }, {
            name: "<?= $trans["Last_year"] ?>",
            data: <?= json_encode(array_values($chart1)) ?>
        }],
        exporting: {
            sourceWidth: 400,
            sourceHeight: 400,
        }
    });
    $.when($.ajax({
        url: "https://api.exchangeratesapi.io/latest?base=<?= $stat->currency ?>",
        success: function(data) {
            change = data.rates;
        }
    })).done(function() {
        if (incomes.length > 0) {
            $.each(incomes, function() {
                if (this.currency == '<?= $stat->currency ?>') {
                    total += parseFloat(this.income);
                } else total += parseFloat(this.income) * parseFloat(change[this.currency]);
            });
        }
        $('.income').html(total.toFixed(3) + '<sup><?= $stat->currency ?></sup>');
    })
</script>
</body>

</html>