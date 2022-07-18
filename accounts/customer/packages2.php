<?php
$page_name = "packages";
ob_start();
include('header.php');

if (isset($_GET["response"])) {
    $response = $_GET["response"];
    if ($response == "ok") {
        if (!isset($_GET["package_id"]) || !isset($_GET["amount"])  || !isset($_GET["currency"])) {
            echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>" . ($trans["feedback_msg"]["missing_payment_data"]) . "</div></div>";
        } else {
            $package_id = (isset($_GET["package_id"])) ? $_GET["package_id"] : null;
            $amount = (isset($_GET["amount"])) ? $_GET["amount"] : null;
            $currency = (isset($_GET["currency"])) ? $_GET["currency"] : null;
            $provider_id = (isset($_GET["provider_id"])) ? intval($_GET["provider_id"]) : null;
            $st = $conn->prepare("SELECT * from packages  where  id_package=:IDP");
            $st->bindParam(':IDP', $package_id, PDO::PARAM_INT);
            $st->execute();
            $pckg = $st->fetch();
            if ($pckg) {
                $trans = $conn->prepare("INSERT into transactionsc(`id_customer`, `id_package`, `status`, `date_add`, `final_price`, `id_provider`) 
                                                 VALUES(:IDC,:IDP,1,NOW(),:FPR,:IDP)");
                $trans->bindParam(':IDC', $id_account, PDO::PARAM_INT);
                $trans->bindParam(':IDP', $package_id, PDO::PARAM_INT);
                $trans->bindParam(':FPR', $amount, PDO::PARAM_INT);
                $trans->bindParam(':IDP', $provider_id, PDO::PARAM_INT);
                if ($trans->execute()) {
                    $updateBalance = $conn->prepare("UPDATE customers set balance=balance+:BAL where id_customer=:IDC");
                    $updateBalance->bindParam(':IDC', $id_account, PDO::PARAM_INT);
                    $updateBalance->bindParam(':BAL', $pckg["messages"], PDO::PARAM_INT);
                    $updateBalance->execute();
                    $updateLead = $conn->prepare("UPDATE leads set status=2,update_date=NOW() where id_customer=:IDC");
                    $updateLead->bindParam(':IDC', $id_account, PDO::PARAM_INT);
                    $updateLead->execute();
                    $description = "buy new package";
                    $stmt2 = $conn->prepare("INSERT INTO logs(id_user,description,meta,log_type,date) 
                                                    VALUES(:iu,:ds,:mt,2,NOW())");
                    $stmt2->bindParam(':iu', $id_user, PDO::PARAM_INT);
                    $stmt2->bindParam(':ds', $description, PDO::PARAM_STR);
                    $stmt2->bindParam(':mt', $package_id, PDO::PARAM_INT);
                    $stmt2->execute();
                    header("Location: index.php");
                    ob_end_flush();
                }
            }
        }
    } else {
        $reason = (isset($_GET["reason"])) ? $_GET["reason"] : null;
        echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> " . ($trans["feedback_msg"]["payment_failed"])  . " : " . $reason . " </div></div>";
    }
}

$stmt = $conn->prepare("SELECT a.*, b.*, c.`name` AS website_name FROM `users` a LEFT JOIN `customers` b ON a.`id_profile` = b.`id_customer` LEFT JOIN `websites` c ON b.`id_website` = c.`id_website` WHERE a.`id_user` = :ID");
$stmt->bindParam(':ID', $id_user, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchObject();

$id_website = intval($result->id_website);

$packages = $conn->prepare("SELECT p.*,pp.price,pp.currency,CASE WHEN t.content is not null then t.content ELSE p.title end title, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount, (SELECT count(*) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select GROUP_CONCAT(oc.id_customer) from offers_customers oc where o.id_offer=oc.id_offer)))) as offers_count from packages p  JOIN packages_price pp ON  pp.id_package=p.id_package left join translations t on t.table='packages' and t.id_element=p.id_package and t.lang=:lang where pp.primary=1 AND p.status=1 and (p.id_website is null or p.id_website=:IDW) ");
$packages->bindParam(':IDW', $id_website, PDO::PARAM_INT);
$packages->bindParam(':IDC', $id_account, PDO::PARAM_INT);
$packages->bindParam(':lang', $_COOKIE["lang"], PDO::PARAM_STR);
$packages->execute();
$pricings_rows = $packages->rowCount();
$pricings = $packages->fetchAll();
?>
<style>
    #pForm {display: none;}
    body {-webkit-font-smoothing: antialiased;}
    section {background: #647df9;color: #7a90ff;padding: 2em 0 8em;min-height: 100vh;position: relative;-webkit-font-smoothing: antialiased;}
    .pricing {display: -webkit-flex;-webkit-flex-wrap: wrap;flex-wrap: wrap;width: 100%;margin: 0 auto 3em;}
    .pricing-item {position: relative;display: -webkit-flex;display: flex;-webkit-flex-direction: column;flex-direction: column;-webkit-align-items: stretch;align-items: stretch;text-align: center;-webkit-flex: 0 1 330px;flex: 0 1 244px;}
    .pricing-action {color: inherit;border: none;background: none;}
    .pricing-action:focus {outline: none;}
    .pricing-feature-list {text-align: left;}
    .pricing-palden .pricing-item {font-family: 'Open Sans', sans-serif;cursor: default;color: #84697c;background: #fff;box-shadow: 0 0 10px rgba(46, 59, 125, 0.23);border-radius: 20px 20px 10px 10px;margin: 1em;}
    @media screen and (min-width: 66.25em) {.pricing-palden .pricing-item {margin: 1em 1em;}.pricing-palden .pricing__item--featured {margin: 0;z-index: 10;  box-shadow: 0 0 20px rgba(46, 59, 125, 0.23);}}
    .pricing-palden .pricing-deco {border-radius: 10px 10px 0 0;background: rgb(255 120 124);padding: 3em 0 5em;position: relative;}
    .pricing-palden .pricing-deco-img {position: absolute;bottom: 0;left: 0;width: 100%;height: 70px;}
    .remise {margin: 0px 50px 0px 50px !important;border-top: 3px solid #ff787c;padding: 5px;color: black !important;background: white;font-size: 12px !important;border-radius: 10px;}
    .pricing-palden .pricing-title {font-size: 0.75em;margin: 0;text-transform: uppercase;letter-spacing: 5px;color: #fff;}
    .pricing-palden .deco-layer {-webkit-transition: -webkit-transform 0.5s;transition: transform 0.5s;}
    .pricing-palden .pricing-item:hover .deco-layer--1 {-webkit-transform: translate3d(15px, 0, 0);transform: translate3d(15px, 0, 0);}
    .pricing-palden .pricing-item:hover .deco-layer--2 {-webkit-transform: translate3d(-15px, 0, 0);transform: translate3d(-15px, 0, 0);}
    .pricing-palden .icon {font-size: 2.5em;}
    .pricing-palden .pricing-price {font-size: 5em;font-weight: bold;padding: 0;color: #fff;margin: 0 0 0.25em 0;line-height: 0.75;}
    .pricing-palden .pricing-currency {font-size: 0.15em;vertical-align: top;}
    .pricing-palden .pricing-period {font-size: 0.8em;padding: 0 0.5em 0 0;font-style: italic;vertical-align: super;}
    .pricing-palden .pricing__sentence {font-weight: bold;margin: 0 0 1em 0;padding: 0 0 0.5em;}
    .pricing-palden .pricing-feature-list {margin: 0;padding: 0.25em 0 2.5em;list-style: none;text-align: center;}
    .pricing-palden .pricing-feature {padding: 0;}
    .pricing-palden .pricing-action {font-weight: bold;margin: auto 3em 2em 3em;padding: 1em 2em;color: #fff;border-radius: 30px;background: #ff7179;-webkit-transition: background-color 0.3s;transition: background-color 0.3s;}
    .pricing-palden .pricing-action:hover,.pricing-palden .pricing-action:focus {background-color: #d05a5d;}
    .pricing-palden .pricing-item--featured .pricing-deco {padding: 5em 0 8.885em 0;}
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Packages</h4>
                <div class='pricing pricing-palden'>
                    <?php if ($pricings_rows > 0) {
                        foreach ($pricings as $pri) {
                            if ($pri['total_discount'] == 100) {
                                $stmt = $conn->prepare("SELECT *, (SELECT count(*) from offers_customers oc where oc.id_offer=o.id_offer and oc.id_customer=:idc) as pick_count from offers o where o.id_package=:idp and  o.discount=100");
                                $stmt->bindParam(':idp', $pri["id_package"], PDO::PARAM_INT);
                                $stmt->bindParam(':idc', $id_account, PDO::PARAM_INT);
                                $stmt->execute();
                                $rs = $stmt->fetchObject();
                                if ($rs->limit > $rs->pick_count) {
                                    echo "<div class='pricing-item'>
                                    <div class='pricing-deco'>
                                    <div style='position: absolute;top: -20px;min-height: 15px;'><h3 class='pricing-title remise'>" . ($trans["package_card"]["discount"]) . ' -' . $pri["total_discount"] . "%</h3></div>
                                        <svg class='pricing-deco-img' enable-background='new 0 0 300 100' height='100px' id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100' width='300px' x='0px' xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' y='0px'>
                                            <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729&#x000A; c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z' fill='#FFFFFF' opacity='0.6'></path>
                                            <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729&#x000A;  c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z' fill='#FFFFFF' opacity='0.6'></path>
                                            <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716&#x000A;  H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                                            <path class='deco-layer deco-layer--4' d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428&#x000A; c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                                        </svg>
                                        <div class='pricing-price'>".$pri['messages']."<span class='pricing-currency'>Msg</span></div>
                                        <h3 class='pricing-title'>".$pri['title']."</h3>
                                    </div>
                                    <ul class='pricing-feature-list'>
                                        <li class='pricing-feature'><span class='pricing-period'><del class='uppercase'>" . $pri["price"] . "</del></span>".round(((100 - $pri['total_discount']) * ($pri['price'] / 100)), 2)." ".$pri['currency']."</li>
                                    </ul>
                                    <button class='pricing-action'><a href='javascript:void(0)' data-id='" . $pri["id_package"] . "' class='buy-offer' style='all: unset;'>" . ($trans["package_card"]["get_for_free"]) . "</a></button>
                                </div>";
                                }
                            }
                            if ($pri['total_discount'] < 100) {
                                if ($pri['total_discount'] > 0) {
                                    echo "<div class='pricing-item'>
                                    <div class='pricing-deco'>
                                    <div style='position: absolute;top: -20px;min-height: 15px;'><h3 class='pricing-title remise'>" . ($trans["package_card"]["discount"]) . ' -' . $pri["total_discount"] . "%</h3></div>
                                        <svg class='pricing-deco-img' enable-background='new 0 0 300 100' height='100px' id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100' width='300px' x='0px' xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' y='0px'>
                                            <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729&#x000A; c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z' fill='#FFFFFF' opacity='0.6'></path>
                                            <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729&#x000A;  c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z' fill='#FFFFFF' opacity='0.6'></path>
                                            <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716&#x000A;  H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                                            <path class='deco-layer deco-layer--4' d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428&#x000A; c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                                        </svg>
                                        <div class='pricing-price'>".$pri['messages']."<span class='pricing-currency'>Msg</span></div>
                                        <h3 class='pricing-title'>" . $pri["title"] . "</h3>
                                    </div>
                                    <ul class='pricing-feature-list'>
                                        <li class='pricing-feature'><span class='pricing-period'><del class='uppercase'>".$pri['price']."</del></span>".round(((100 - $pri['total_discount']) * ($pri['price'] / 100)), 2)." ".$pri['currency']."</li>
                                    </ul>
                                    <button class='pricing-action'><a href='javascript:void(0)' data-id='" . $pri["id_package"] . "' class='buy-offer' style='all: unset;'>" . ($trans["package_card"]["buy_now"]) . "</a></button>
                                </div>";
                                } else {
                                    echo "<div class='pricing-item'>
                                <div class='pricing-deco'>
                                <div style='position: relative;top: -45px;min-height: 15px;'></div>
                                    <svg class='pricing-deco-img' enable-background='new 0 0 300 100' height='100px' id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100' width='300px' x='0px' xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' y='0px'>
                                        <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729&#x000A; c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z' fill='#FFFFFF' opacity='0.6'></path>
                                        <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729&#x000A;  c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z' fill='#FFFFFF' opacity='0.6'></path>
                                        <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716&#x000A;  H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                                        <path class='deco-layer deco-layer--4' d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428&#x000A; c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                                    </svg>
                                    <div class='pricing-price'>".$pri['messages']."<span class='pricing-currency'>Msg</span></div>
                                    <h3 class='pricing-title'>" . $pri["title"] . "</h3>
                                </div>
                                <ul class='pricing-feature-list'>
                                    <li class='pricing-feature'>".round(((100 - $pri['total_discount']) * ($pri['price'] / 100)), 2)." ".$pri['currency']."</li>
                                </ul>
                                <button class='pricing-action'><a href='javascript:void(0)' data-id='" . $pri["id_package"] . "' class='buy-offer' style='all: unset;'>" . ($trans["package_card"]["buy_now"]) . "</a></button>
                            </div>";
                                }
                            }
                        }
                    } else {
                        echo '<div class="text-center"><h3>' . $trans["package_card"]["not_available"] . '</h3></div>';
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<form method="post" id="pForm" action="https://secure-payment.pro/index_v2.php">
    <input type="hidden" name="id_company" value="" />
    <input type="hidden" name="id_shop" value="" />
    <input type="hidden" name="amount" value="">
    <input type="hidden" name="currency" value="" />
    <input type="hidden" name="country" value="">
    <input type="hidden" name="last_name" value="">
    <input type="hidden" name="first_name" value="">
    <input type="hidden" name="email" value="">
    <input type="hidden" name="package_id" value="">
    <input type="submit" value="Validate">
</form>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>

</footer> <!-- ============================================================== -->
</div>
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
<!--Menu sidebar -->
<script src="../../assets/js/sidebarmenu.js"></script>
<!--stickey kit -->
<script src="../../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
<script src="../../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
<!--Custom JavaScript -->
<script src="../../assets/js/custom.min.js"></script>
<!-- This is data table -->

<script src="../../assets/node_modules/push.js/push.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js"></script>

<script src="../../assets/js/pages/customers.js"></script>
<script src="../../assets/js/pages/update_password.js"></script>

<script>
    var balance = 0;
    $(document).ready(function() {
        $(window).on('unload', function() {
            logout();
        });
    });

    function logout() {
        $.post("functionsAjax.php", {
            action: "logout",
            sender: sender,
        });
    }

    $(".get-offer").click(function() {
        var packageId = $(this).data("id");
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'mr-2 btn btn-danger'
            },
            buttonsStyling: false,
        });
        swalWithBootstrapButtons.fire({
            title: "<?php echo ($trans["package_alert"]["title"]) ?>",
            text: "<?php echo ($trans["package_alert"]["subtitle"]) ?>",
            type: 'success',
            showCancelButton: true,
            confirmButtonText: "<?php echo ($trans["package_alert"]["confirm"]) ?>",
            cancelButtonText: "<?php echo ($trans["package_alert"]["cancel"]) ?>",
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "../packageTrait.php",
                    type: "POST",
                    data: {
                        action: "get_package",
                        packageId: packageId,
                        accountId: <?= $id_account ?>,
                        websiteId: <?= $id_website ?>,
                    },
                    dataType: "json",
                    success: function(dataResult) {
                        if (dataResult.statusCode == 200) {
                            window.location.href = 'index.php';
                        } else if (dataResult.statusCode == 201) {
                            swalWithBootstrapButtons.fire(
                                'Cancelled',
                                "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                                'error'
                            )
                        }
                    }
                });
            } else if (
                // Read more about handling dismissals
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                    'error'
                )
            }
        })
    })
    $(".buy-offer").click(function() {
        var packageId = $(this).data("id");
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'mr-2 btn btn-danger'
            },
            buttonsStyling: false,
        });
        swalWithBootstrapButtons.fire({
            title: "<?php echo ($trans["package_alert"]["title"]) ?>",
            text: "<?php echo ($trans["package_alert"]["subtitle"]) ?>",
            type: 'success',
            showCancelButton: true,
            confirmButtonText: "<?php echo ($trans["package_alert"]["confirm"]) ?>",
            cancelButtonText: "<?php echo ($trans["package_alert"]["cancel"]) ?>",
            reverseButtons: true
        }).then((result) => {
            if (result.value) {

                $.ajax({
                    url: "../packageTrait.php",
                    type: "POST",
                    data: {
                        action: "buy_package",
                        packageId: packageId,
                        accountId: <?= $id_account ?>,
                        websiteId: <?= $id_website ?>,
                        currency: 'EUR'
                    },
                    dataType: "json",
                    success: function(dataResult) {
                        if (dataResult.statusCode == 200) {
                            $("input[name='id_company']").val(dataResult.response.payment_requirement.id_company);
                            $("input[name='id_shop']").val(dataResult.response.payment_requirement.id_shop);
                            $("input[name='amount']").val(dataResult.response.payment_requirement.amount);
                            $("input[name='currency']").val(dataResult.response.payment_requirement.currency);
                            $("input[name='country']").val(dataResult.response.payment_requirement.country);
                            $("input[name='first_name']").val(dataResult.response.payment_requirement.first_name);
                            $("input[name='last_name']").val(dataResult.response.payment_requirement.last_name);
                            $("input[name='email']").val(dataResult.response.payment_requirement.email);
                            $("input[name='package_id']").val(packageId);
                            $("#pForm").submit();
                        } else if (dataResult.statusCode == 201) {
                            swalWithBootstrapButtons.fire(
                                'Cancelled',
                                "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                                'error'
                            )
                        }
                    }
                });

            } else if (
                // Read more about handling dismissals
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    "<?php echo ($trans["package_alert"]["canceled"]) ?>",
                    'error'
                )
            }
        })

    })
</script>
</body>
</html>