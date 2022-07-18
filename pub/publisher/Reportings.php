<?php
$page_name = "reportings";
ob_start();
include('header.php');
?>
<link href="../../assets/node_modules/switchery/switchery.min.css" rel="stylesheet" />
<style>
    .form-control {min-height: 30px;}
    .imp {border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-right: 1px solid #dddddd;background-color: #f8f9fa}
    .imp:first-child {border-left: 1px solid #dddddd;}
    .pair {border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-right: 1px solid #dddddd;}
    .pair:first-child {border-left: 1px solid #dddddd;}
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
        <div class="card-body ">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h3><i class="mdi mdi-account-group"></i></h3>
                            <p class="text-muted">total advertisers</p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-info TotalAdvetiser"></h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <span class="text-info "><a href="#" class="btn waves-effect waves-light btn-xs btn-secondary advertisers">details</a></span>
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
                            <h3><i class="mdi mdi-account-group"></i></h3>
                            <p class="text-muted">Total contributors</p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-info TotalContributor"></h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <span class="text-info "><a href="#" class="btn waves-effect waves-light btn-xs btn-secondary contributors">details</a></span>
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
                            <p class="text-muted">Total leads</p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-info Total"></h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <span class="text-info "><a href="#" class="btn waves-effect waves-light btn-xs btn-secondary totalDetails">details</a></span>
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
                            <p class="text-muted">Total sales</p>
                        </div>
                        <div class="ml-auto">
                            <h2 class="counter text-info Sales">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <span class="text-info "><a href="#" class="btn waves-effect waves-light btn-xs btn-secondary salesDetails">details</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs profile-tab" role="tablist">
            <li class="nav-item ">
                <h4 style="padding: 15px 20px;display:block">View by</h4>
            </li>
            <li class="nav-item "> <a class="nav-link <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'byContributor')) {echo "active";} ?>" data-toggle="tab" href="#Contributors" role="tab">Contributors</a> </li>
            <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'byAdvertisers') {echo "active";} ?>" data-toggle="tab" href="#Advertisers" role="tab">Advertisers</a></li>
            <li class="nav-item "> <a class="nav-link <?php if (isset($_GET['tab']) && $_GET['tab'] == 'byPrograms') {echo "active";} ?>" data-toggle="tab" href="#programs" role="tab">Programs</a> </li>
        </ul>
        <div class="tab-content" id="content">
            <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'byContributor')) {echo "active";} ?>" id="Contributors" role="tabpanel">
                <table class="display  nowrap table table-hover table-striped" id="VBcontributors" style="width:100%">
                    <thead>
                        <tr>
                            <th class="imp text-center" rowspan="2" class="text-center"><?= $trans['reportingsPage']['Agent'] ?></th>
                            <th class="pair text-center" colspan="2" class="text-center"><?= $trans['reportingsPage']['Leads'] ?></th>
                            <th class="imp text-center" colspan="2" class="text-center"><?= $trans['reportingsPage']['Leads_Sales'] ?></th>
                            <th class="pair text-center" colspan="2" class="text-center">Leads with visits</th>
                            <th class="imp text-center" colspan="2" class="text-center"><?= $trans['reportingsPage']['Unconverted_Leads'] ?></th>
                            <th class="pair text-center" rowspan="2" class="text-center">Actions</th>
                        </tr>
                        <tr>
                            <th class="pair"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="pair"><?= $trans['reportingsPage']['Percentage'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                            <th class="pair"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="pair"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                            <th class="imp"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'byAdvertisers')) {echo "active";} ?>" id="Advertisers" role="tabpanel">
                <table class="display  nowrap table table-hover table-striped" id="VBAdvertisers" style="width:100%">
                    <thead>
                        <tr>
                            <th class="imp text-center" rowspan="2">Advertiser name</th>
                            <th class="pair text-center" colspan="2"><?= $trans['reportingsPage']['Leads'] ?></th>
                            <th class="imp text-center" colspan="2"><?= $trans['reportingsPage']['Leads_Sales'] ?></th>
                            <th class="pair text-center" colspan="2">Leads with visits</th>
                            <th class="imp text-center" colspan="2"><?= $trans['reportingsPage']['Unconverted_Leads'] ?></th>
                            <th class="pair text-center" rowspan="2">Actions</th>
                        </tr>
                        <tr>
                            <th class="pair"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="pair"><?= $trans['reportingsPage']['Percentage'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                            <th class="pair"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="pair"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                            <th class="imp"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="tab-pane <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'byPrograms')) {echo "active";} ?>" id="programs" role="tabpanel">
                <table class="display  nowrap table table-hover table-striped" id="VBPrograms" style="width:100%">
                    <thead>
                        <tr>
                            <th class="imp text-center" rowspan="2">Program name</th>
                            <th class="pair text-center" colspan="2"><?= $trans['reportingsPage']['Leads'] ?></th>
                            <th class="imp text-center" colspan="2"><?= $trans['reportingsPage']['Leads_Sales'] ?></th>
                            <th class="pair text-center" colspan="2">Leads with visits</th>
                            <th class="imp text-center" colspan="2"><?= $trans['reportingsPage']['Unconverted_Leads'] ?></th>
                            <th class="pair text-center" rowspan="2">Actions</th>
                        </tr>
                        <tr>
                            <th class="pair"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="pair"><?= $trans['reportingsPage']['Percentage'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                            <th class="pair"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="pair"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                            <th class="imp"><?= $trans['reportingsPage']['Number'] ?></th>
                            <th class="imp"><?= $trans['reportingsPage']['Percentage'] ?> </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal" id="leads" tabindex="-1" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel2">Total leads</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="leadsProg" style="width:100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Lead name</th>
                                <th>Date add</th>
                                <th>Date update</th>
                                <th>Date Result</th>
                                <th><?= $trans['reportingsPage']['contributor'] ?></th>
                                <th><?= $trans['reportingsPage']['program'] ?></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead">Date add</th>
                                <th class="filterhead">Date update</th>
                                <th class="filterhead">Date Result</th>
                                <th class="filterhead"><?= $trans['reportingsPage']['contributor'] ?></th>
                                <th class="filterhead"><?= $trans['reportingsPage']['program'] ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="sales" tabindex="-1" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel2">Sales</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="salesProg" style="width:100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Lead name</th>
                                <th><?= $trans['reportingsPage']['contributor'] ?></th>
                                <th><?= $trans['reportingsPage']['program'] ?></th>
                                <th>Date add</th>
                                <th>Date end</th>
                                <th>Result</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead"><?= $trans['reportingsPage']['contributor'] ?></th>
                                <th class="filterhead"><?= $trans['reportingsPage']['program'] ?></th>
                                <th class="filterhead">Date add</th>
                                <th class="filterhead">Date end</th>
                                <th class="filterhead">Result</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="contributors" tabindex="-1" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel2">Contributors</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="contriProg" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pseudo </th>
                                <th>Email </th>
                                <th><?= $trans['publisher']['Advertiser_program'] ?></th>
                                <th><?= $trans['publisher']['Creation_date'] ?></th>
                                <th><?= $trans['publisher']['End_date'] ?></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="filterhead"><?= $trans['publisher']['Advertiser_program'] ?></th>
                                <th class="filterhead"><?= $trans['publisher']['Creation_date'] ?></th>
                                <th class="filterhead"><?= $trans['publisher']['End_date'] ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="AdvertisersPop" tabindex="-1" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel2">Advertisers</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="AdvertProg" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Advertiser name</th>
                                <th>Date start</th>
                                <th>Date end</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th class="filterhead">Advertiser name</th>
                                <th class="filterhead">Date start</th>
                                <th class="filterhead">Date end</th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0"><?= $trans['reportingsPage']['select_periode'] ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" id="myForm" method="POST" class="col-md-12 m-t-20" novalidate>
                    <div class="row">
                        <div class="col-6">
                            <label for="from"><?= $trans['from'] ?></label>
                            <input type="date" name="from" class="form-control from" required>
                        </div>
                        <div class="col-6">
                            <label for="to"><?= $trans['to'] ?></label>
                            <input type="date" name="to" class="form-control to" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="" class="btn btn-default" data-dismiss="modal"><?php echo ($trans["cancel"]) ?></button>
                        <button type="button" id="7" name="submit" class="periode btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal" id="VBCpopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0">Contributor leads</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="VBCdetails" style="width:100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Lead name</th>
                                <th>Date add</th>
                                <th>Date end</th>
                                <th>Result</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead">Date add</th>
                                <th class="filterhead">Date end</th>
                                <th class="filterhead">Result</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal" id="VBPpopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0">Program leads</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="VBPdetails" style="width:100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Lead name</th>
                                <th>Date add</th>
                                <th>Date end</th>
                                <th>Result</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead">Date add</th>
                                <th class="filterhead">Date end</th>
                                <th class="filterhead">Result</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="VBApopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel0">Advertiser Leads</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow:scroll">
                <div class="table-responsive m-b-40 m-r-0">
                    <table class="display  nowrap table table-hover table-striped" id="VBAdetails" style="width:100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Lead name</th>
                                <th>Date add</th>
                                <th>Date end</th>
                                <th>Result</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="filterhead">Date add</th>
                                <th class="filterhead">Date end</th>
                                <th class="filterhead">Result</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="overlay">
    <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
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
        $('a[data-toggle="tab"]').on("shown.bs.tab", function(e) {
            $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
        });
    });
    var table4 = $('#VBcontributors').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table5 = $('#VBAdvertisers').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    var table6 = $('#VBPrograms').DataTable({
        orderCellsTop: true,
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('.totalDetails').click(function() {
        $("#leadsProg").dataTable().fnDestroy();
        $('#leadsProg').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            orderCellsTop: true,
            fixedHeader: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            initComplete: function() {
                var api = this.api();
                $('.filterhead', api.table().header()).each(function(i) {
                    var column = api.column(i + 2);
                    var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
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
        $('#leads').modal('show');
    });
    $('.salesDetails').click(function() {
        $("#salesProg").dataTable().fnDestroy();
        $('#salesProg').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            orderCellsTop: true,
            fixedHeader: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            initComplete: function() {
                var api = this.api();
                $('.filterhead', api.table().header()).each(function(i) {
                    var column = api.column(i + 2);
                    var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
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
        $('#sales').modal('show');
    });
    $('.contributors').click(function() {
        $("#contriProg").dataTable().fnDestroy();
        $('#contriProg').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            orderCellsTop: true,
            fixedHeader: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            initComplete: function() {
                var api = this.api();
                $('.filterhead', api.table().header()).each(function(i) {
                    var column = api.column(i + 3);
                    var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
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
        $('#contributors').modal('show');
    });
    $('.advertisers').click(function() {
        $("#AdvertProg").dataTable().fnDestroy();
        $('#AdvertProg').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            orderCellsTop: true,
            fixedHeader: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            initComplete: function() {
                var api = this.api();
                $('.filterhead', api.table().header()).each(function(i) {
                    var column = api.column(i + 1);
                    var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
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
        $('#AdvertisersPop').modal('show');
    });
    $('#VBcontributors').on('click', '.VBCdetails', function() {
        $('#overlay').show();
        let id = $('.active').parent('a').attr('id');
        let text = $(this).text();
        let from = $('.from').val();
        let to = $('.to').val();
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            data: {
                id: id,
                action: 'getCL',
                contributor: $(this).data('id'),
                from: from,
                to: to
            },
            dataType: 'json',
            success: function(data) {
                $("#VBCdetails").dataTable().fnDestroy();
                $('#VBCdetails').DataTable({
                    orderCellsTop: true,
                    dom: 'Bfrtip',
                    scrollX: true,
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    data: data,
                    initComplete: function() {
                        var api = this.api();
                        $('.filterhead', api.table().header()).each(function(i) {
                            var column = api.column(i + 2);
                            var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
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
                $('#overlay').hide();
            }
        });
        $('#VBCpopup').modal('show');
    });
    $('#VBAdvertisers').on('click', '.VBAdetails', function() {
        $('#overlay').show();
        let id = $('.active').parent('a').attr('id');
        let text = $(this).text();
        let from = $('.from').val();
        let to = $('.to').val();
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            data: {
                id: id,
                action: 'getAL',
                advertiser: $(this).data('id'),
                publisher: <?= $_SESSION['id_user'] ?>,
                from: from,
                to: to
            },
            dataType: 'json',
            success: function(data) {
                $("#VBAdetails").dataTable().fnDestroy();
                $('#VBAdetails').DataTable({
                    orderCellsTop: true,
                    dom: 'Bfrtip',
                    scrollX: true,
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    data: data,
                    initComplete: function() {
                        var api = this.api();
                        $('.filterhead', api.table().header()).each(function(i) {
                            var column = api.column(i + 2);
                            var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
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
                $('#overlay').hide();
            }
        });
        $('#VBApopup').modal('show');
    });
    $('#VBPrograms').on('click', '.VBPdetails', function() {
        $('#overlay').show();
        let id = $('.active').parent('a').attr('id');
        let text = $(this).text();
        let from = $('.from').val();
        let to = $('.to').val();
        $.ajax({
            url: 'functions_ajax.php',
            type: 'post',
            data: {
                id: id,
                action: 'getPL',
                program: $(this).data('id'),
                publisher: <?= $_SESSION['id_user'] ?>,
                from: from,
                to: to
            },
            dataType: 'json',
            success: function(data) {
                $("#VBPdetails").dataTable().fnDestroy();
                $('#VBPdetails').DataTable({
                    orderCellsTop: true,
                    dom: 'Bfrtip',
                    scrollX: true,
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    data: data,
                    initComplete: function() {
                        var api = this.api();
                        $('.filterhead', api.table().header()).each(function(i) {
                            var column = api.column(i + 2);
                            var select = $('<select class="form-control" style="height:30px"><option value=""></option></select>')
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
                $('#overlay').hide();
            }
        });
        $('#VBPpopup').modal('show');
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
                action: 'getStat',
                id_publisher: <?= $_SESSION['id_user'] ?>,
                from: from,
                to: to
            },
            dataType: 'json',
            success: function(data) {
                $('#leadsProg').DataTable().clear();
                $('#leadsProg').DataTable().rows.add(data.table).draw();

                $('#salesProg').DataTable().clear();
                $('#salesProg').DataTable().rows.add(data.table1).draw();

                $('#contriProg').DataTable().clear();
                $('#contriProg').DataTable().rows.add(data.table2).draw();

                $('#AdvertProg').DataTable().clear();
                $('#AdvertProg').DataTable().rows.add(data.table3).draw();

                table4.clear();
                table4.rows.add(data.table4).draw();

                table5.clear();
                table5.rows.add(data.table5).draw();

                table6.clear();
                table6.rows.add(data.table6).draw();

                $('.Total').text(data.total);
                $('.Sales').text(data.sales);
                $('.TotalAdvetiser').text(data.advertisers);
                $('.TotalContributor').text(data.Contributors);
                $('.close').click();
                $('#overlay').hide();
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            }
        });
    });
</script>