<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";

$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
error_log($locale);

(include_once HERE."/locale/".preg_replace("/[\/\\\]/","",$locale).".php") ?: include_once HERE."/locale/default.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);


$inputs = filter_input_array(INPUT_POST, array(
    'given_name' => FILTER_SANITIZE_STRING,
    'surname' => FILTER_SANITIZE_STRING,
    'street' => FILTER_SANITIZE_STRING,
    'house_nr' => FILTER_SANITIZE_STRING,
    'zip_code' => FILTER_SANITIZE_STRING,
    'city' => FILTER_SANITIZE_STRING,
    'state' => FILTER_SANITIZE_STRING,
    'country' => FILTER_SANITIZE_STRING,
    'email' => FILTER_SANITIZE_EMAIL,
    'phonenumber' => FILTER_SANITIZE_STRING,
    'chip' => FILTER_VALIDATE_INT,
    'privacy_policy' => FILTER_SANITIZE_STRING,
    'submit' => FILTER_SANITIZE_STRING,
    'uuid' => FILTER_SANITIZE_STRING
));

$error = false;
$errors = array();

if($inputs['submit'] != "resend") {
    $uuid = $mysqli->query("SELECT UUID()")->fetch_row()[0];
//$id = $mysqli->query("SELECT id from attendees WHERE uuid = \"".$uuid."\"")->fetch_row()[0];
    if (!($stmt = $mysqli->prepare(
        "INSERT INTO attendees(id, uuid, surname, given_name, email, phonenumber, 
                      street, house_nr, zip_code, city, state, country, chip, privacy_policy) 
                      VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?, ?)"))) {
        $error = true;
        array_push($errors, "Error saving data (attendees) " . $mysqli->errno);
    }

    $pp = isset($inputs['privacy_policy']) && ($inputs['privacy_policy'] == "Yes" ||
            $inputs['privacy_policy'] == "yes" ||
            $inputs['privacy_policy'] == "On" ||
            $inputs['privacy_policy'] == "on" ||
            $inputs['privacy_policy'] == "1");

    if (!$stmt->bind_param("sssssssssssii",
        $uuid, $inputs['surname'], $inputs['given_name'],
        $inputs['email'], $inputs['phonenumber'],
        $inputs['street'], $inputs['house_nr'], $inputs['zip_code'], $inputs['city'], $inputs['state'], $inputs['country'],
        $inputs['chip'], $pp)) {
        $error = true;
        array_push($errors, "Error saving data (attendees) " . $mysqli->errno);
    }
    if (!$stmt->execute()) {
        $error = true;
        array_push($errors, "Error saving data (attendees) " . $mysqli->errno);
    }

    if ($error) {
        error_log(print_r($errors, true));
        require_once HERE . "/error.php";
        die();
    }
} else {
    $uuid = $inputs['uuid'];
}

if(!($stmt = $mysqli->prepare("SELECT * FROM attendees WHERE uuid = ?"))){
    $error = true;
    array_push($errors, "Error getting result (attendees) ".$mysqli->errno);
}
if(!$stmt->bind_param("s", $uuid)){
    $error = true;
    array_push($errors, "Error getting result (attendees) ".$mysqli->errno);
}
if(!$stmt->execute()){
    $error = true;
    array_push($errors, "Error getting result (attendees) ".$mysqli->errno);
}
if(!($result = $stmt->get_result())){
    $error = true;
    array_push($errors, "Error getting result (attendees) ".$mysqli->errno);
}

if ($result->num_rows != 1) {
    error_log(print_r($result->fetch_all(), true));
    require_once HERE . "/error.php";
    die();
}

$row = $result->fetch_assoc();

if(isset($row['email']) && $row['email'] != "") {
    try {
        $bytes = random_bytes(5);
        $mail_challenge = bin2hex($bytes);
    } catch (\Exception $e) {
        mt_srand(time());
        $mail_challenge = mt_rand(1000000000, 99999999999);
    }
    if (!($stmt = $mysqli->prepare("INSERT INTO detail_verification(id, user, credential, challenge, challenge_date, verification_date) VALUES (null, ?, 'EMAIL', ?, NOW(), null)"))) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    if (!$stmt->bind_param("is", $row['id'], $mail_challenge)) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    if (!$stmt->execute()) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    $stmt->close();

    if ($error) {
        error_log(print_r($errors, true));
        require_once HERE . "/error.php";
        die();
    }

    require_once HERE."/vendor/autoload.php";

    $mail = new \PHPMailer\PHPMailer\PHPMailer(false);
    //$mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = MAIL_SERVER;
    $mail->SMTPAuth = !empty(MAIL_LOGIN) && !empty(MAIL_PASSWORD);
    $mail->Username = MAIL_LOGIN;
    $mail->Password = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_SSL ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : '';

    $mail->setFrom(MAIL_FROM);
    $mail->addAddress($row['email'], $row['given_name']. " " . $row['surname']);

    $mail->isHTML(false);
    $mail->Subject = LANG("Please Verify your Email");
    /** @noinspection PhpUndefinedVariableInspection */
    $mail->Body = sprintf(LANG("Hi,\r\n\r\nPlease use this code to verify your Email: %s\r\n\r\nBest Regards,\r\n%s\r\n"), $mail_challenge, $ORGANISATION);

    $mail->send();

} else {
    require_once HERE . "/error.php";
    die();
}

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>COVID Contact tracing checkin</title>

    <!-- Bootstrap core CSS -->
    <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicons -->
    <meta name="theme-color" content="#563d7c">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/css/form-validation.css" rel="stylesheet">
    <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH; ?>" alt="" height="72">
        <h2><?= LANG("Verify your Email");?></h2>
        <p class="lead">
            <i class="bi bi-person-check" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        <div class="col-md-12 order-md-1">
            <form class="needs-validation" novalidate="" method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
                <span><?= LANG("Please enter your verification code"); ?></span>
                <div class="mb-3">
                    <label class="visually-hidden" for="code"><?= LANG("Please enter your verification code"); ?></label>
                    <div class="input-group">
                        <div class="input-group-text"><i class="bi bi-mailbox"></i></div>
                        <input type="text" class="form-control" id="code" name="code" placeholder="<?= LANG("Verification Code");?>">
                    </div>
                    <p></p>
                    <span><small><i><?= LANG("Please also check your SPAM-Folder");?></i></small></span>
                </div>
                <hr class="mb-4">
                <input type="hidden" name="uuid" value="<?= $uuid;?>">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="verify"><?= LANG("Submit");?></button>
                <button class="btn btn-secondary btn-lg btn-block" type="submit" name="submit" value="resend"><?= LANG("Resend Code");?></button>
            </form>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/bootstrap.bundle.min.js"></script>

<!--suppress JSUnresolvedVariable -->
<script>
    jQuery(function ($) {
        // get anything with the data-manyselect
        // you don't even have to name your group if only one group
        var $group = $("[data-manyselect]");

        $group.on('input', function () {
            var group = $(this).data('manyselect');
            // set required property of other inputs in group to false
            var allInGroup = $('*[data-manyselect="'+group+'"]');
            // Set the required property of the other input to false if this input is not empty.
            var oneSet = true;
            $(allInGroup).each(function(){
                if ($(this).val() !== "")
                    oneSet = false;
            });
            $(allInGroup).prop('required', oneSet)
        });
    });
</script>
<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/form-validation.js"></script>

</body>
</html>