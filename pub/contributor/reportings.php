<?php
$page_name = "reportings";
ob_start();
include('header.php');
?>
<style>
    .form-control {min-height: 30px;}
    .bg-info {background-color: #ff3f5f !important;}
    .text-info {color: #ff3f5f !important;}
    .container-fluid {padding: 0 10px;}
    .radio-toolbar input[type="radio"] {opacity: 0;position: fixed;width: 0;}
    .radio-toolbar label {display: inline-block;background-color: #E6EAEE;margin-bottom: 0px;cursor: pointer;border-radius: 5%;}
    .radio-toolbar label:hover {background-color: #ff6774;}
    .radio-toolbar input[type="radio"]:checked+label {background-color: #ff6774;}
    .radio-toolbar a {color: black;}
    .customtab2 li a.nav-link.active {background: #dddddd;color: black;}
    .card-title {margin-top: auto;margin-bottom: auto;}
    .m-l-10 {width: 80%;}
    .round.round-danger .progress-bar {background-color: #ff6774;}
    .danger {color: #ff6774;}
    table.dataTable {margin: 0 !important}
    .DTFC_ScrollWrapper {height: auto !important}
    .even {background-color: white;}
    thead {background-color: white;}
    .radio-toolbar a .active {background-color: #ff6774;color: white;}
    .bg-secondary {min-height: 140px;padding: 20px;}
    .fa-eye {color: black;}
    .btn-sm {background-color: #ddd;border-color: #ddd;}
    .box {max-width: 100%;}
    .col-10 {background-image: linear-gradient(to right, #ff6472 0%, #ffb199 150%);border-radius: 5%}
</style>
<div class="card">
    <div class="card-body">
        <div style="display: flex">
            <h5 class="card-title"><?= $trans['reportingsPage']['Periode'] ?></h5>
            <div class="text-right ml-auto">
                <div class="radio-toolbar">
                    <a href="#" id="1" class="periode"><label for="Today" class="btn-sm"><?= $trans['reportingsPage']['Today'] ?></label></a>
                    <a href="#" id="2" class="periode"><label for="Previousday" class="btn-sm"><?= $trans['reportingsPage']['Previous_day'] ?></label></a>
                    <a href="#" id="3" class="periode"><label for="Thisweek" class="btn-sm"><?= $trans['reportingsPage']['This_week'] ?></label></a>
                    <a href="#" id="4" class="periode"><label for="Previousweek" class="btn-sm"><?= $trans['reportingsPage']['Previous_week'] ?></label></a>
                    <a href="#" id="5" class="periode"><label for="Thismonth" class="btn-sm"><?= $trans['reportingsPage']['This_month'] ?></label></a>
                    <a href="#" id="6" class="periode"><label for="Previousmonth" class="btn-sm"><?= $trans['reportingsPage']['Previous_month'] ?></label></a>
                    <a href="#" class="custom"><label for="Customperiod" class="btn-sm"><?= $trans['reportingsPage']['Custom_period'] ?></label></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-group">
    <div class="card">
        <div class="card-body " style="min-height: 140px">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-account-group"></i></h3>
                            <p class="text-muted"><?= $trans['reportingsPage']['total_Leads'] ?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter Total text-info"></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">

        <div class="card-body ">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-account"></i></h3>
                            <p class="text-muted"><?= $trans['reportingsPage']['Leads_with_Visit']?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-info totalWith"></h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <span class="text-info totalWithP">0%</span>
                    <div class="progress">
                        <div class="progress-bar bg-primary totalWithprog" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body ">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-account"></i></h3>
                            <p class="text-muted"><?= $trans['reportingsPage']['Leads_without_Visit']?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-info totalWithNot"></h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <span class="text-info totalWithNotP">0%</span>
                    <div class="progress">
                        <div class="progress-bar bg-primary totalWithNotprog" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body ">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-account"></i></h3>
                            <p class="text-muted"><?= $trans['reportingsPage']['Leads_with_sales']?></p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-info totalSales"></h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <span class="text-info totalSalesP">0%</span>
                    <div class="progress">
                        <div class="progress-bar bg-primary totalSalesprog" role="progressbar" style="width: 85%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class=" card-body">
        <div class="table-responsive m-b-40 m-r-0">
            <h3 class="box-title m-b-0"><?= $trans['leads']?></h3>
            <hr>
            <table class="display  nowrap table table-hover table-striped" id="leads" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= $trans['name']?></th>
                        <th><?= $trans['reportingsPage']['program'] ?></th>
                        <th><?= $trans['date_add']?></th>
                        <th><?= $trans['date_end']?></th>
                        <th>Result</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th class="filterhead"><?= $trans['reportingsPage']['program'] ?></th>
                        <th class="filterhead">Date add</th>
                        <th class="filterhead">Date end</th>
                        <th class="filterhead">Result</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="overlay">
        <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
    </div>
</div>
</div>
</div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../../assets/node_modules/bootstrap/bootstrap.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../../assets/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<script>
    $(document).ready(function() {
        $('#overlay').show();
        $('.custom').click(function() {
            $('#myModal').modal('show');
        });
        $('#1').click();
    });
    $('.periode').click(function() {
        $('.radio-toolbar').find('label').removeClass("active");
        $(this).children('label').addClass("active");
        $('#overlay').show();
        let id = $(this).attr('id');
        let text = $(this).text();
        let from = $('.from').val();
        let to = $('.to').val();
        if (id == 7) {
            $('.periode').children('label').removeClass("active");
            $('.custom').children('label').addClass("active");
        }
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            data: {
                id: id,
                action: 'get_leads',
                id_contributor: <?= $_SESSION['id_user'] ?>,
                from: from,
                to: to
            },
            dataType: 'json',
            success: function(data) {
                $("#leads").dataTable().fnDestroy();
                $('#leads').DataTable({
                    dom: 'Bfrtip',
                    responsive: true,
                    orderCellsTop: true,
                    fixedHeader: true,
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    data: data.table,
                    initComplete: function() {
                        var api = this.api();
                        $('.filterhead', api.table().header()).each(function(i) {
                            var column = api.column(i);
                            var select = $('<select class="form-control" style="height: 30px;"><option value="" ></option></select>')
                                .appendTo($(this).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });

                            column.data().unique().sort().each(function(d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>');
                            });
                        });
                    }
                });
                $('.Total').text(data.Total);
                $('.totalWith').text(data.totalWith);
                $('.totalWithP').text(data.totalWithP + "%");
                $('.totalWithprog').width(data.totalWithP + "%");
                $('.totalWithNot').text(data.totalWithNot);
                $('.totalWithNotP').text(data.totalWithNotP + "%");
                $('.totalWithNotprog').width(data.totalWithNotP + "%");
                $('.totalSales').text(data.totalSales);
                $('.totalSalesP').text(data.totalSalesP + "%");
                $('.totalSalesprog').width(data.totalSalesP + "%");
                $('.close').click();
                $('#overlay').hide();
            }
        });
    });
</script>