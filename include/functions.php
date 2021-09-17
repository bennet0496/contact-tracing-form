<?php
if(!defined("INCLUDED"))
    die();

/**
 * @throws Exception
 */
function get_attendee_by_uuid(mysqli $mysqli, string $uuid, bool $throw = false, & $errors = null) {
    $errs = array();

    if(!($stmt = $mysqli->prepare("SELECT * FROM attendees WHERE uuid = ?"))){
        array_push($errs, "Error getting result (attendees) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!$stmt->bind_param("s", $uuid)){
        array_push($errs, "Error getting result (attendees) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!$stmt->execute()){
        array_push($errs, "Error getting result (attendees) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!($result = $stmt->get_result())){
        array_push($errs, "Error getting result (attendees) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }

    if(is_null($errors)) {
        $errors = $errs;
    }

    return $result->fetch_assoc();
}

function get_detail_verification_by_user(mysqli $mysqli, int $uid, bool $throw = false, & $errors = null){
    $errs = array();
    if(!($stmt = $mysqli->prepare("SELECT * FROM detail_verification WHERE user = ?"))){
        array_push($errs, "Error preparing (details) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!$stmt->bind_param("i", $uid)){
        array_push($errs, "Error binding (details) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!$stmt->execute()){
        array_push($errs, "Error executing (details) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!($result = $stmt->get_result())){
        array_push($errs, "Error getting result (details) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }

    if(is_null($errors)) {
        $errors = $errs;
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_verification_data_by_user(mysqli $mysqli, int $uid, bool $throw = false, & $errors = null){
    $errs = array();
    if(!($stmt = $mysqli->prepare("SELECT * FROM verification_data WHERE aid = ?"))){
        array_push($errs, "Error preparing (verification_data) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!$stmt->bind_param("i", $uid)){
        array_push($errs, "Error binding (verification_data) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!$stmt->execute()){
        array_push($errs, "Error executing (verification_data) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }
    if(!($result = $stmt->get_result())){
        array_push($errs, "Error getting result (verification_data) ".$mysqli->errno);
        if(is_null($errors)) {
            $errors = $errs;
        }
        if($throw) {
            throw new \Exception($mysqli->error);
        }
    }

    if(is_null($errors)) {
        $errors = $errs;
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function checkbox2bool(?string $cb) : bool {
    return ($cb == "Yes" || $cb == "yes" ||
        $cb == "On" || $cb == "on" || $cb == "1");
}

function dieOnError($error, $msg = null) {
    if(isset($msg)){
        $ERROR_MSG = $msg;
    }
    if ($error) {
        require_once dirname(__FILE__)."/../error.php";
        die();
    }
}

function saveDie($msg = null) {
    if(isset($msg)){
        $ERROR_MSG = $msg;
    }
    require_once dirname(__FILE__)."/../error.php";
    die();
}

/**
 * @throws Exception
 */
function insert_new_attendee(mysqli $mysqli, ?string $uuid, ?string $surname, ?string $given_name,
                             ?string $email, ?string $phonenumber, ?string $street, ?string $house_nr,
                             ?string $zip_code, ?string $city, ?string $state, ?string $country, ?int $chip,
                             ?bool $privacy_policy){

    if(is_null($uuid)) {
        $uuid = $mysqli->query("SELECT UUID()")->fetch_row()[0];
    }
    if(!($stmt = $mysqli->prepare(
        "INSERT INTO attendees(id, uuid, surname, given_name, email, phonenumber, 
                      street, house_nr, zip_code, city, state, country, chip, privacy_policy) 
                      VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?, ?)"))){
        throw new \Exception("Error saving data (attendees) ".$mysqli->errno);
    }

    if(!$stmt->bind_param("sssssssssssii",
        $uuid, $surname, $given_name, $email, $phonenumber,
        $street, $house_nr, $zip_code, $city, $state, $country, $chip, $privacy_policy)){
        throw new \Exception("Error saving data (attendees) ".$mysqli->errno);
    }

    if(!$stmt->execute()){
        throw new \Exception("Error saving data (attendees) ".$mysqli->errno);
    }

    return $uuid;
}

function verify_core_data(mysqli $mysqli, int $uid){
    if(!($stmt = $mysqli->prepare("INSERT INTO detail_verification(id, user, credential, challenge, challenge_date, verification_date) VALUES (null, ?, 'CORE_DATA', null, NOW(), NOW())"))){
        throw new \Exception( "Error saving to database (detail_verification) ".$mysqli->errno);
    }
    if(!$stmt->bind_param("i", $uid)){
        throw new \Exception( "Error saving to database (detail_verification) ".$mysqli->errno);
    }
    if(!$stmt->execute()){
        throw new \Exception("Error saving to database (detail_verification) ".$mysqli->errno);
    }
    $stmt->close();
}

function verify_person_status(mysqli $mysqli, int $aid, ?bool $vaccination_status, $vaccination_date, ?bool $recovery_status, $recovery_date, ?bool $test_status, $test_datetime, ?string $test_type, ?string $test_agency, ?bool $privacy_policy){
    if (!($stmt = $mysqli->prepare(
        "INSERT INTO verification_data(id, aid, vaccination_status, vaccination_date, recovery_status, recovery_date, test_status, test_datetime, test_type, test_agency, privacy_policy) 
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {
        throw new \Exception("Error saving to database (verification_data) " . $mysqli->errno);
    }

    if (!$stmt->bind_param("iisisisssi",$aid, $vaccination_status, $vaccination_date, $recovery_status, $recovery_date, $test_status, $test_datetime, $test_type, $test_agency, $privacy_policy)) {
        throw new \Exception("Error saving to database (verification_data) " . $mysqli->error);
    }

    if (!$stmt->execute()) {
        throw new \Exception("Error saving to database (verification_data) " . $mysqli->error);
    }

    $stmt->close();
}

function checkin(mysqli $mysqli, int $aid, int $chip = null){
    if(!($stmt = $mysqli->prepare("INSERT INTO check_events(eid, aid, time, event, chip) VALUES (null, ?, NOW(), 'checkin', ?)"))){
        throw new \Exception("Error saving to database (check_events) ".$mysqli->errno);
    }
    if(!$stmt->bind_param("ii", $aid, $chip)){
        throw new \Exception("Error saving to database (check_events) ".$mysqli->errno);
    }
    if(!$stmt->execute()){
        throw new \Exception( "Error saving to database (check_events) ".$mysqli->errno);
    }
    $stmt->close();
}

/**
 * @param array|null $row
 * @return \PHPMailer\PHPMailer\PHPMailer
 * @throws \PHPMailer\PHPMailer\Exception
 */
function setupMail(string $recipent_mail, string $recipent_name): \PHPMailer\PHPMailer\PHPMailer
{
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
    $mail->addAddress($recipent_mail, $recipent_name);
    return $mail;
}

function get_attendee_challenges($mysqli, $aid, $type = "EMAIL"){
    if(!($stmt = $mysqli->prepare("SELECT * FROM detail_verification WHERE user = ?"))){
        throw new \Exception(  "Error getting result (detail_verification) ".$mysqli->errno);
    }
    if(!$stmt->bind_param("i", $aid)){
        throw new \Exception(  "Error getting result (detail_verification) ".$mysqli->errno);
    }
    if(!$stmt->execute()){
        throw new \Exception(  "Error getting result (detail_verification) ".$mysqli->errno);
    }
    if(!($result = $stmt->get_result())){
        throw new \Exception(  "Error getting result (detail_verification) ".$mysqli->errno);
    }

    $data = $result->fetch_all(MYSQLI_ASSOC);

//error_log(print_r($data, true));
//error_log(print_r($errors, true));

    $challenge = [];
    $i = 0;

    foreach ($data as $d) {
        if($d['credential'] == $type) {
            $challenge[$i++] = trim($d['challenge']);
        }
    }

    return $challenge;
}