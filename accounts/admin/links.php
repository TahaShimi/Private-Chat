<?php
$page_name = "shared_links";
include('header.php');
function encryptIt($q)
{
    $encryptionMethod = "RC4-HMAC-MD5";
    $qEncoded = openssl_encrypt($q, $encryptionMethod, SECRETHASH);
    return $qEncoded;
}
if (isset($_POST['update'])) {
    $id_link = $_POST['id_link'];
    $packages_ids = $_POST['packages'];
    $offers_ids = $_POST['offers_ids'];
    $date_end = $_POST['validity'];
    $s = $conn->prepare("UPDATE links SET date_end=:dt WHERE id_link=:id ");
    $s->bindParam(':dt', $date_end, PDO::PARAM_STR);
    $s->bindParam(':id', $id_link, PDO::PARAM_INT);
    $s->execute();
    foreach ($packages_ids as $idp) {
        $s = $conn->prepare("INSERT INGORE INTO links_packages (id_link,id_package,date_add) values(:idl,:idp,NOW())");
        $s->bindParam(':idl', $linkid, PDO::PARAM_INT);
        $s->bindParam(':idp', $idp, PDO::PARAM_INT);
        $s->execute();
    }
    foreach ($offers_ids as $ido) {
        $s = $conn->prepare("INSERT INGORE INTO links_offers (id_link,id_offer,date_add) values(:idl,:ido,NOW())");
        $s->bindParam(':idl', $linkid, PDO::PARAM_INT);
        $s->bindParam(':ido', $ido, PDO::PARAM_INT);
        $s->execute();
    }
}
if (isset($_POST['add-link'])) {
    $id_website = $_POST['website'];
    $lang = $_POST['lang'];
    $packages_ids = $_POST['packages'];
    $offers_ids = $_POST['offers_ids'];
    $date_end = isset($_POST['validity']) && $_POST['validity'] != "" ? $_POST['validity'] : NULL;
    $err = 0;
    $s = $conn->prepare("INSERT INTO links (id_account,id_website,status,lang,date_create,date_end) values(:ida,:idw,1,:lang,NOW(),:dt)");
    $s->bindParam(':idw', $id_website, PDO::PARAM_INT);
    $s->bindParam(':ida', $id_account, PDO::PARAM_INT);
    $s->bindParam(':dt', $date_end);
    $s->bindParam(':lang', $lang);
    $s->execute();
    $res = $s->rowCount();
    $linkid = $conn->lastInsertId();
    if ($res != 0) {
        foreach ($packages_ids as $idp) {
            $s = $conn->prepare("INSERT INTO links_packages (id_link,id_package,date_add) values(:idl,:idp,NOW())");
            $s->bindParam(':idl', $linkid, PDO::PARAM_INT);
            $s->bindParam(':idp', $idp, PDO::PARAM_INT);
            $s->execute();
        }
        foreach ($offers_ids as $ido) {
            $s = $conn->prepare("INSERT INTO links_offers (id_link,id_offer,date_add) values(:idl,:ido,NOW())");
            $s->bindParam(':idl', $linkid, PDO::PARAM_INT);
            $s->bindParam(':ido', $ido, PDO::PARAM_INT);
            $s->execute();
        }
    }
    if ($res != 0) {
        echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Link added successfully.</div></div>";
    } else if ($res == 0) {
        echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Failed to add link !!</div></div>";
    }
}
$s = $conn->prepare("UPDATE links SET status=0 WHERE date_end<NOW()");
$s->execute();
$s0 = $conn->prepare("SELECT l.*,w.name,(SELECT count(*) FROM customers WHERE id_link=l.id_link) as customers,(SELECT count(*) FROM links_packages WHERE id_link=l.id_link) as packages,(SELECT count(*) FROM links_offers WHERE id_link= l.id_link) as offer  FROM `links` l,websites w WHERE l.id_account = :ID AND w.id_website=l.id_website");
$s0->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s0->execute();
$links = $s0->fetchAll(PDO::FETCH_ASSOC);
$s = $conn->prepare("SELECT `id_website`, `name` FROM `websites` WHERE `id_account` = :ID");
$s->bindParam(':ID', $id_account, PDO::PARAM_INT);
$s->execute();
$websites = $s->fetchAll();
?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive m-b-4 m-r-0">
            <button type="button" class="btn btn-primary float-left ml-2" id="linkadd">Add link</button>
            <table class="table display dt-responsive" id="links_table" style="width:100%">
                <thead>
                    <th>ID</th>
                    <th><?= $trans['Link'] ?></th>
                    <th><?= $trans['website'] ?></th>
                    <th><?= $trans['status'] ?></th>
                    <th><?= $trans['language'] ?></th>
                    <th><?= $trans['packages'] ?></th>
                    <th><?= $trans['offers'] ?></th>
                    <th><?= $trans['customersP'] ?></th>
                    <th><?= $trans['date_end'] ?></th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    <?php foreach ($links as $link) { ?>
                        <tr>
                            <td><?= $link['id_link'] ?></td>
                            <td><?php if ($link['status'] == 1) { ?><button class="btn btn-sm btn-info copy" data-link="<?= BASE_URL . '/register.php?cde=' . urlencode(encryptIt($link['id_link'])) ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= BASE_URL . '/register.php?cde=' . urlencode(encryptIt($link['id_link'])) ?>">Copy Link</button><?php } ?></td>
                            <td><?= $link['name'] ?></td>
                            <td><?= $link['status'] == 1 ? '<span class="badge badge-success badge-pill">Active</span>' : '<span class="badge badge-danger badge-pill">' . $trans['disable'] . '</span>' ?></td>
                            <td><?= $link['lang'] ?></td>
                            <td><?= $link['packages'] ?></td>
                            <td><?= $link['offer'] ?></td>
                            <td><?= $link['customers'] ?></td>
                            <td><?= $link['date_end'] ?></td>
                            <td><?php if ($link['status'] == 1) { ?><button type="button" class="btn btn-sm btn-info mr-1 edit" data-id="<?= $link['id_link'] ?>"><?= $trans['edit'] ?></button><button type="button" class="btn btn-sm btn-danger stop" data-id="<?= $link['id_link'] ?>"><?= $trans['disable'] ?></button><?php } ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <input type="text" id="toCopy" style="display: none;" />
    </div>
</div>
</div>
</div>
</div>
<div class="modal" id="addLink" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Add link</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="myForm" action="" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="websites"><?php echo ($trans["website"]) ?></label>
                        <select id="websites" class="m-b-10 form-control" style="width: 100%" name="website" required>
                            <option value=""></option>
                            <?php
                            foreach ($websites as $web) {
                                echo "<option value='" . $web['id_website'] . "'>" . $web['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="packages"><?php echo ($trans["packages"]) ?></label>
                        <select id="packages" class="select2 m-b-10 select2-multiple" style="width: 100%" multiple="multiple" name="packages[]" data-placeholder="" required>
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="offers"><?php echo ($trans["offers"]) ?></label>
                        <select name="offers_ids[]" id="offers" class="select2 select2-multiple" style="width: 80%" multiple="multiple" data-placeholder="Choose" required>
                            <option></option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="" for="activity"><?php echo ($trans["language"]) ?></label>
                            <div class="">
                                <select name="lang" id="lang" class="form-control select2 select-search">
                                    <option value="en"><?php echo ($trans["english"]) ?></option>
                                    <option value="fr"><?php echo ($trans["french"]) ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="validity"><?= $trans['date_end'] ?> (Optional) :</label>
                        <small><?= $trans['always_valid'] ?>.</small>
                        <input type="date" class="form-control" id="validity" name="validity" />
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-actions">
                        <button type="submit" name="add-link" class="btn btn-primary waves-effect waves-light m-r-10 add"> <i class="mdi mdi-check"></i>Save</button>
                        <button type="submit" name="update" class="btn btn-primary waves-effect waves-light m-r-10 update"> <i class="mdi mdi-check"></i>Update</button>
                        <button type="button" class="btn btn-inverse" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="overlay">
    <h4 style="position: absolute;" id="progress"></h4>
    <div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>
</div>
<footer class="footer">
    <?php echo ($trans["footer"]) ?>
</footer>
</div>
<script src="../../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
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
<script src="../../assets/js/notification.js"></script>
<script src="../../assets/node_modules/datatables.net/jquery.dataTables.min.js"></script>
<script src="../../assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script src="../../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
<script src="../../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
<script src="../../assets/node_modules/sweetalert2/sweetalert2.all.min.js" type="text/javascript"></script>

<script>
    var id = 0;
    var offers = [];
    var packages = [];
    $('#overlay').hide();
    $('#links_table').DataTable({
        responsive: true,
        dom: 'Bfrtip'
    });
    $('.copy').on('click', function() {
        $('#toCopy').val($(this).data('link'));
        copyToClipboard();
    });
    $('.select2').select2();

    function copyToClipboard() {
        var success = true,
            range = document.createRange(),
            selection;

        // For IE.
        if (window.clipboardData) {
            window.clipboardData.setData("Text", $('#toCopy').val());
        } else {
            var tmpElem = $('<div>');
            tmpElem.css({
                position: "absolute",
                left: "-1000px",
                top: "-1000px",
            });
            tmpElem.text($('#toCopy').val());
            $("body").append(tmpElem);
            range.selectNodeContents(tmpElem.get(0));
            selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            try {
                success = document.execCommand("copy", false, null);
            } catch (e) {
                Swal.fire({
                    type: 'error',
                    title: "The text can't be copied !"
                })
            }
            if (success) {
                Swal.fire({
                    type: 'success',
                    title: 'Link copied, try to paste it!'
                })
                tmpElem.remove();
            }
        }
    }

    $('#linkadd').click(function() {
        id = 0;
        $('#offers').empty();
        $('#packages').empty();
        $('.add').show();
        $('.update').hide();
        $('#validity').val('');
        $('#websites').attr('disabled', false);
        $('#exampleModalLabel1').text('<?= $trans['add_link'] ?>');
        $('#addLink').modal('show');
    });
    $('#websites').change(function() {
        $('#overlay').show();
        $.ajax({
            url: "functions_ajax.php",
            type: "POST",
            data: {
                type: 'getpackages',
                id: $(this).val(),
                lang: '<?= $_COOKIE['lang'] ?>',
            },
            dataType: "json",
            success: function(dataResult) {
                $('#packages').empty();
                $.each(dataResult, function() {
                    $('#packages').append("<option value='" + this.id_package + "'>" + this.title + "</option>")
                });
                if (id != 0) {
                    $.each(packages, function() {
                        $('#packages option[value="' + this.id_package + '"]').attr('selected', true);
                    });
                    $('#packages').change();
                }
                $('#overlay').hide();
            }
        });
    });
    $('#packages').change(function() {
        $('#overlay').show();
        $.ajax({
            url: "functions_ajax.php",
            type: "POST",
            data: {
                type: 'getoffers',
                id: $(this).val(),
            },
            dataType: "json",
            success: function(dataResult) {
                $('#offers').empty();
                $.each(dataResult, function() {
                    $('#offers').append("<option value='" + this.id_offer + "'>" + this.offer_title + " ON " + this.title + "</option>")
                });
                if (id != 0) {
                    $.each(offers, function() {
                        $('#offers option[value="' + this.id_offer + '"]').attr('selected', true);
                    });
                }
                $('#overlay').hide();
            }
        });
    });
    $('.stop').click(function() {
        $('#overlay').show();
        let tr = $(this).parents('tr');
        $.ajax({
            url: "functions_ajax.php",
            type: "POST",
            data: {
                type: 'stopLink',
                id: $(this).data('id')
            },
            dataType: "json",
            success: function(dataResult) {
                if (dataResult == 1) {
                    $(tr.children('td')[3]).html('<span class="badge badge-danger badge-pill"><?= $trans['disable'] ?></span>');
                    $(tr.children('td')[1]).html('');
                    $(tr.children('td')[8]).html('');
                    Swal.fire({
                        type: 'success',
                        title: 'Link <?= $trans['disable'] ?> !'
                    })
                } else {
                    Swal.fire({
                        type: 'error',
                        title: "Update failed !"
                    })
                }
                $('#overlay').hide();
            }
        });
    });
    $('.update').click(function() {
        $('#overlay').show();
        $('#myForm').append('<input type="text" value="' + id + '" name="id_link"/>');
    });
    $('.edit').click(function() {
        id = $(this).data('id');
        $('#overlay').show();
        $.ajax({
            url: "functions_ajax.php",
            type: "POST",
            data: {
                type: 'getLink',
                id: $(this).data('id')
            },
            dataType: "json",
            success: function(dataResult) {
                $('#websites option[value="' + dataResult.id_website + '"]').attr('selected', true);
                $('#websites').attr('disabled', true);
                $('#websites').change()
                offers = dataResult.offers
                packages = dataResult.packages
                $('#validity').val(dataResult.date_end);
                $('.add').hide();
                $('.update').show();
                $('#exampleModalLabel1').text('<?= $trans['edit_link'] ?>');
                $('#addLink').modal('show');
                $('#overlay').hide();
            }
        });
    });
</script>