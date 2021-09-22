<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');



require_once __DIR__."/../../config.php";

require_once HERE."/include/functions.php";

$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
error_log($locale);

(include_once HERE."/locale/".preg_replace("/[\/\\\]/", "", $locale).".php") ?: include_once HERE."/locale/default.php";

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

if ($inputs['submit'] != "resend") {
    $pp = checkbox2bool($inputs['privacy_policy']);

    if (is_null($inputs['surname']) || is_null($inputs['given_name']) || is_null($inputs['email']) ||
        is_null($inputs['phonenumber']) || is_null($inputs['street']) || is_null($inputs['house_nr']) ||
        is_null($inputs['zip_code']) || is_null($inputs['city']) || !$pp) {
        define("ERROR", true);
        require_once __DIR__."/inc_register_form.php";
        exit();
    }

    try {
        /** @noinspection PhpExpressionAlwaysConstantInspection */
        $uuid = insert_new_attendee(
            $mysqli,
            null,
            $inputs['surname'],
            $inputs['given_name'],
            $inputs['email'],
            $inputs['phonenumber'],
            $inputs['street'],
            $inputs['house_nr'],
            $inputs['zip_code'],
            $inputs['city'],
            $inputs['state'],
            $inputs['country'],
            $inputs['chip'],
            $pp
        );
    } catch (Exception $e) {
        saveDie();
    }
} else {
    $uuid = $inputs['uuid'];
}

try {
    /** @noinspection PhpUndefinedVariableInspection */
    $row = get_attendee_by_uuid($mysqli, $uuid);
} catch (Exception $e) {
    saveDie();
}

if (isset($row['email']) && $row['email'] != "") {
    try {
        $bytes = random_bytes(5);
        $mail_challenge = bin2hex($bytes);
    } catch (Exception $e) {
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

    try {
        $mail = setupMail($row['email'], $row['given_name'] . " " . $row['surname']);
        $mail->isHTML(false);
        $mail->Subject = LANG("Please Verify your Email");
        /** @noinspection PhpUndefinedVariableInspection */
        $mail->Body = sprintf(LANG("Hi,\r\n\r\nPlease use this code to verify your Email: %s\r\n\r\nBest Regards,\r\n%s\r\n"), $mail_challenge, $ORGANISATION);

        $mail->send();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        saveDie();
    }
} else {
    saveDie();
}

?>

<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"?>

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
        <div class="col-md-8 order-md-1 offset-md-2">
            <form class="needs-validation" novalidate="" method="POST" action="<?= filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL); ?>">
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
<?php require_once HERE."/include/inc_post_content.php"?>
</body>
</html>