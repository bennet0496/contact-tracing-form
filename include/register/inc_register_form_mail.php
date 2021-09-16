<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$inputs = filter_input_array(INPUT_POST, array(
    'code' => FILTER_SANITIZE_STRING,
    'uuid' => FILTER_SANITIZE_STRING,
));

//error_log(print_r($inputs, true));

$error = false;
$errors = array();

//$id = $mysqli->query("SELECT id from attendees WHERE uuid = \"".$uuid."\"")->fetch_row()[0];
if(!($stmt = $mysqli->prepare("SELECT * FROM attendees WHERE uuid = ?"))){
    $error = true;
    array_push($errors, "Error getting result (attendees) ".$mysqli->errno);
}
if(!$stmt->bind_param("s", $inputs['uuid'])){
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

if ($error) {
    error_log(print_r($errors, true));
    require_once HERE."/error.php";
    die();
}

if(!($stmt = $mysqli->prepare("SELECT * FROM detail_verification WHERE user = ?"))){
    $error = true;
    array_push($errors, "Error getting result (detail_verification) ".$mysqli->errno);
}
if(!$stmt->bind_param("i", $row['id'])){
    $error = true;
    array_push($errors, "Error getting result (detail_verification) ".$mysqli->errno);
}
if(!$stmt->execute()){
    $error = true;
    array_push($errors, "Error getting result (detail_verification) ".$mysqli->errno);
}
if(!($result = $stmt->get_result())){
    $error = true;
    array_push($errors, "Error getting result (detail_verification) ".$mysqli->errno);
}

$data = $result->fetch_all(MYSQLI_ASSOC);

//error_log(print_r($data, true));
//error_log(print_r($errors, true));

$challenge = [];
$i = 0;

foreach ($data as $d) {
    if($d['credential'] == "EMAIL") {
        $challenge[$i] = trim($d['challenge']);
        $i++;
    }
}

if(is_null($challenge)){
    require_once HERE . "/error.php";
    die();
}

if(isset($inputs['code']) && in_array(trim($inputs['code']),$challenge)) {

    require_once HERE."/vendor/autoload.php";

    $qr = tempnam("/tmp", "qr");
    (new \chillerlan\QRCode\QRCode())->render($row['uuid'], $qr);

    $row = array_map(function ($e){ return htmlentities($e);}, $row);
    $row["email"] = $row['email'] != "" ? htmlentities($row['email']) : "N/A";
    $row["phonenumber"] = $row['phonenumber'] != "" ? htmlentities($row['phonenumber']) : "N/A";

    $css = file_get_contents(HERE."/css/bootstrap.min.css");
    /**
     * @noinspection PhpUndefinedVariableInspection
     */
    $html =<<<EOH
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <!-- Bootstrap core CSS -->
    <style>
        {$css}
    </style>

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
        @page {
            size: 105mm 148mm;
        }
    </style>
</head>

<body class="bg-light">
<div class="container-sm">
    <div class="py-sm-5 row">
        <div class="col-md-12 col-6 text-center">
            <img class="d-block mx-auto mb-4" src="cid:logo.png" alt="" height="72">
            <h2>{$EVENT_NAME}</h2>
            <p class="lead">User Details</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-6">
            <div class="row">
                <div class="col mb-3 text-center">
                    <img src="cid:qrcode.png" />
                    <p class="text-muted">{$row['uuid']}</p>
                </div>
            </div>

            <div class="row d-print-none">
                <div class="alert alert-success" role="alert">
                    Thank you for registering. You now may print this document to get access to the Location.
                    If you don't have a printer you can also use Browsers integrated PDF printer to save this as a PDF or
                    take screenshot of the browser window, and show this document digitally on entrance
                </div>
            </div>

            <div class="row">
                <div class="col-5">
                    <p>
                        <b>Name</b><br />
                        {$row["given_name"]} {$row["surname"]}
                    </p>
                    <p>
                        <b>Email</b><br />
                        {$row['email']}
                    </p>
                    <p>
                        <b>Phone number</b><br />
                        {$row['phonenumber']}
                    </p>
                    <p>
                        <b>Address</b><br />
                        {$row['street']} {$row['house_nr']}, 
                        {$row['zip_code']} {$row['city']}, {$row['state']}, 
                        {$row['country']}
                    </p>
                </div>
                <div class="col-7">
                    <p>If you get test COVID-19 positive within 14-days of the admission time, please tell us and the
                        health authorities, that you attended this event.</p>
                    <p><small>{$RESPONSIBLE}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
EOH;
    $mail = new \PHPMailer\PHPMailer\PHPMailer(false);
    //$mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = MAIL_SERVER;
    $mail->SMTPAuth = !empty(MAIL_LOGIN) && !empty(MAIL_PASSWORD);
    $mail->Username = MAIL_LOGIN;
    $mail->Password = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_SSL ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : '';

    /** @noinspection PhpUndefinedVariableInspection */
    $mail->setFrom(MAIL_FROM);
    $mail->addAddress($row['email'], $row['given_name']. " " . $row['surname']);
    $mail->addEmbeddedImage(LOGO_FS_PATH, "logo.png", "logo.png");
    $mail->addEmbeddedImage($qr, "qrcode.png", "qrcode.png");

    $mail->isHTML(true);
    $mail->Subject = ORGANISATION." Registration Data";
    $mail->Body = $html;
    $mail->AltBody = "Please enable HTML to view this message.".
        " Or download and show the qrcode.png attachment while verification";

    $mail->send();

    unlink($qr);
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
            <h2>Registration form</h2>
            <p class="lead">
                <i class="bi bi-person-plus" style="font-size: 48px"></i>
            </p>
        </div>

        <div class="row">
            <div class="col-md-12 order-md-1">
                <div class="alert alert-success" role="alert">
                    Successfully sent email. Refreshing in <span id="timer">5</span>...
                </div>
            </div>
        </div>

        <script>
            window.counter = 5;
            function countd() {
                if(counter === 0) {
                    window.location.assign(window.location.href)
                } else {
                    window.counter--;
                    document.getElementById("timer").innerText = window.counter;
                    setTimeout(countd, 1000);
                }
            }
            setTimeout(countd, 1000);
        </script>
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

    <?php
 } else {
    require_once HERE."/error.php";
    die();
} ?>
