<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";
require_once HERE."/include/functions.php";

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


try {
    $challenge = get_attendee_challenges($mysqli, $row['id']);
} catch (Exception $e) {
    saveDie();
}


/** @noinspection PhpUndefinedVariableInspection */
if(is_null($challenge)){
    saveDie();
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
<div class="container">
    <div class="py-sm-5 row">
        <div class="col-md-12 col-12 text-center">
            <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH; ?>" alt="" height="72">
            <h2><?php /** @noinspection PhpUndefinedVariableInspection */
                echo EVENT_NAME; ?></h2>
            <p class="lead"><?= LANG("User Details");?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-8 offset-2 offset-md-2">
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
                    <button class="btn btn-primary btn-lg btn-block d-print-none col-12" onclick="window.print()"><?= LANG("Print");?></button>
                </div>
                <div class="col-md-6">
                    <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="uuid" value="<?= $row['uuid'];?>">
                        <input type="hidden" name="code" value="<?= htmlentities($inputs['code']);?>">
                        <button class="btn btn-outline-primary btn-lg btn-block d-print-none col-12" type="submit" name="submit" value="mail"><?= LANG("Send via email");?></button>
                    </form>
                </div>
            </div>
            <div>&nbsp;</div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-secondary btn-lg btn-block d-print-none col-12" onclick="window.location.assign(window.location.href)"><?= LANG("Done");?></button>
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
    <?php require_once HERE."/include/inc_html_head.php"; ?>

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
            <div class="col-md-8 offset-md-2 order-md-1">
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
    <?php require_once HERE."/include/inc_post_content.php"?>
    </body>
    </html>
<?php } ?>
