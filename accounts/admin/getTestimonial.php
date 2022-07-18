<?php 
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR.utf8','fra');

if ((isset($_GET['id_testimonial']) && intval($_GET['id_testimonial']) > 0)) {
    $id_testimonial = intval($_GET['id_testimonial']);
    try {
        $conn = new PDO('mysql:host=localhost;dbname=privatechat;charset=utf8', 'privatechat', 'privatechat@2019');
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    $s0 = $conn->prepare("SELECT * FROM `testimonials` WHERE `id_testimonial` = :ID");
    $s0->bindParam(':ID', $id_testimonial, PDO::PARAM_INT);
    $s0->execute();
    $result = $s0->fetchObject();
    ?>
    <section>
        <form class="rating-wrapper" action="" method="">
            <div class="form-group">
                <div class="col-md-12 m-b-20">
                    <label>First name</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $result->username; ?>">
                </div>
                <div class="col-md-12 m-b-20">
                    <label>Rating</label>
                    <input type="number" name="rating" class="form-control" value="<?php echo $result->rating; ?>" min="0" max="5" step="1">
                </div>
                <div class="col-md-12 m-b-20">
                    <label>Title</label>
                    <input type="text" name="title0" class="form-control" value="<?php echo $result->title; ?>">
                </div>
                <div class="col-md-12 m-b-20">
                    <label>Text</label>
                    <textarea name="content" class="form-control" rows="3"><?php echo $result->content; ?></textarea>
                </div>
            </div>
        </form>
    </section>
<?php } ?>