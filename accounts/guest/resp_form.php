<?php
$id = $_GET["id_guest"];
$first_name = $_GET["first_name"];
$last_name = $_GET["last_name"];
$email = $_GET["email"];
$id_agent = $_GET["id_agent"];
$id_pack = $_GET["id_pack"];
$amount = $_GET["amount"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="../post_pay.php" method="get">
        <select name="response" id="response" required>
            <option value="ok">1</option>
            <option value="ko">2</option>
        </select>
        <input type="hidden" name="id_company" value="48" />
        <input type="hidden" name="id_shop" value="12" />
        <input type="hidden" name="id_guest" value="<?= $id ?>" />
        <input type="hidden" name="id_agent" value="<?= $id_agent ?>">
        <input type="hidden" name="id_pack" value="<?= $id_pack ?>">
        <input type="hidden" name="amount" value="<?= $amount ?>">
        <input type="hidden" name="currency" value="EUR" />
        <label for="name">Select Response: </label>
        <label for="name">Enter your First name: </label>
        <input type="text" name="first_name" id="firstname" value="<?= $first_name ?>" required>
        <label for="name">Enter your Last name: </label>
        <input type="text" name="last_name" id="lastname" value="<?= $last_name ?>" required>
        <label for="name">Enter your Email: </label>
        <input type="email" name="email" id="email" value="<?= $email ?>" required>
        <input type="submit" name="submit" value="Validate-1">
    </form>
</body>

</html>