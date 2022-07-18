<?php
$page_name = "dashboard";
ob_start();
include('header.php');
$st = $conn->prepare("SELECT (SELECT count(*) from contributors_programs cp,users u where cp.id_contributor=u.id_profile AND u.profile=6 AND u.id_user=:IDA AND cp.status=1) as programs, (SELECT count(*) from leads l where l.id_contributor=:IDA ) as leads, (SELECT count(*) from leads l where l.id_contributor=:IDA  AND l.update_date IS NOT NULL) as leadsConverted");
$st->bindparam(":IDA", $_SESSION['id_user']);
$st->execute();
$stat = $st->fetchObject();

$st = $conn->prepare("SELECT COUNT(*) as total ,update_date from leads l WHERE l.id_contributor=:IDA AND update_date IS NOT NULL AND YEAR(update_date)=YEAR(CURRENT_TIMESTAMP) GROUP BY EXTRACT(YEAR_MONTH FROM update_date)");
$st->bindparam(":IDA", $_SESSION['id_user']);
$st->execute();
$leads = $st->fetchAll();
$chart = ['01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0];
foreach ($leads as $lead) {
    $m = date("m", strtotime($lead['update_date']));
    $chart[$m] = intval($lead['total']);
}
?>
<div class="card-group">
    <!-- Column -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-earth"></i></h3>
                            <p class="text-muted"><?= $trans['programs'] ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-cyan"><?= $stat->programs ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-account-group"></i></h3>
                            <p class="text-muted"><?= $trans['leads'] ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-purple"><?= $stat->leads ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi account"></i></h3>
                            <p class="text-muted"><?= $trans['converted_leads'] ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-success"><?= $stat->leadsConverted ?></h2>
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
</div>
<footer class="footer">
    © 2019 Private chat by Diamond services
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/custom.min.js"></script>
<script src="../../assets/js/highcharts.js"></script>
<script>
    Highcharts.chart('container', {
        chart: {
            type: 'line'
        },
        title: {
            text: '<?= $trans['Monthly_Converted_Leads'] ?>'
        },
        xAxis: {
            categories: <?= json_encode($trans["months"]) ?>
        },
        yAxis: {
            title: {
                text: '<?= $trans['Leads_number'] ?>'
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
            name: '<?= $trans['leads'] ?>',
            data: <?= json_encode(array_values($chart)) ?>
        }]
    });
</script>