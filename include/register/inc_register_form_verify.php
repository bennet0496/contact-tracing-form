<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";

require_once dirname(__FILE__)."/../../config.php";

$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
error_log($locale);

(include_once HERE."/locale/".preg_replace("/[\/\\\]/","",$locale).".php") ?: include_once HERE."/locale/default.php";


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

    if (!($stmt = $mysqli->prepare("UPDATE detail_verification SET verification_date = NOW() WHERE user = ? AND credential = 'EMAIL'"))) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    if (!$stmt->bind_param("i", $row['id'])) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    if (!$stmt->execute()) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    $stmt->close();

    if ($error) {
        require_once HERE . "/error.php";
        die();
    }

    require_once HERE."/vendor/autoload.php";
    ?>
<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"?>

<body class="bg-light">
<div class="container-sm">
    <div class="py-sm-5 row">
        <div class="col-md-12 col-6 text-center">
            <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH; ?>" alt="" height="72">
            <h2><?php /** @noinspection PhpUndefinedVariableInspection */
                echo $EVENT_NAME; ?></h2>
            <p class="lead"><?= LANG("User Details");?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-6">
            <div class="row">
                <div class="col mb-3 text-center">
                    <img src="<?php /** @noinspection PhpUndefinedVariableInspection */
                    echo (new \chillerlan\QRCode\QRCode())->render($row['uuid']) ?>" />
                    <p class="text-muted"><?= $row['uuid'];?></p>
                </div>
            </div>

            <div class="row d-print-none">
                <div class="alert alert-success" role="alert">
                    <?= LANG("Thank you for registering. You now may print this document to get access to the Location. If you don't have a printer you can also send this to your Email or use Browsers integrated PDF printer to save this as a PDF or take screenshot of the browser window, and show this document digitally on entrance.");?>
                </div>
            </div>

            <div class="row">
                <div class="col-5">
                    <p>
                        <b><?= LANG("Name");?></b><br />
                        <?php /** @noinspection PhpUndefinedVariableInspection */
                        echo htmlentities($row["given_name"])." ".htmlentities($row["surname"]); ?>
                    </p>
                    <p>
                        <b><?= LANG("Email");?></b><br />
                        <?php echo $row['email'] != "" ? htmlentities($row['email']) : "N/A" ;?>
                    </p>
                    <p>
                        <b><?= LANG("Phone number");?></b><br />
                        <?php echo $row['phonenumber'] != "" ? htmlentities($row['phonenumber']) : "N/A" ;?>
                    </p>
                    <p>
                        <b><?= LANG("Address");?></b><br />
                        <?php
                        echo htmlentities(<<<EOT
{$row['street']} {$row['house_nr']}, 
{$row['zip_code']} {$row['city']}, {$row['state']}, 
{$row['country']}
EOT );
                        ?>
                    </p>
                </div>
                <div class="col-7">
                    <p><?= LANG("If you get test COVID-19 positive within 14-days of the admission time, please tell us and the health authorities, that you attended this event.");?></p>
                    <p><small><?= RESPONSIBLE; ?></small></p>
                </div>
            </div>
            <hr class="mb-4 d-print-none">
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-primary btn-lg btn-block d-print-none" onclick="window.print()"><?= LANG("Print");?></button>
                </div>
                <div class="col-md-6">
                    <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="uuid" value="<?= $row['uuid'];?>">
                        <input type="hidden" name="code" value="<?= htmlentities($inputs['code']);?>">
                        <button class="btn btn-outline-primary btn-lg btn-block d-print-none" type="submit" name="submit" value="mail"><?= LANG("Send via email");?></button>
                    </form>
                </div>
            </div>
            <div>&nbsp;</div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-secondary btn-lg btn-block d-print-none" onclick="window.location.assign(window.location.href)"><?= LANG("Done");?></button>
                </div>
            </div>
            <div>&nbsp;</div>
        </div>
    </div>
</div>
<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php } else { ?>
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
                <div class="alert alert-danger" role="alert">
                    <?= LANG("The code you entered was invalid");?>
                </div>
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
                    <input type="hidden" name="uuid" value="<?= htmlentities($inputs['uuid']);?>">
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
<?php } ?>
