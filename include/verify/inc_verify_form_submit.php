<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";

require_once HERE."/include/functions.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

//$uuid = $mysqli->query("SELECT UUID()")->fetch_row()[0];

$inputs = filter_input_array(INPUT_POST, array(
    'uuid' => FILTER_SANITIZE_STRING,
    'data_correct' => FILTER_SANITIZE_STRING,
    'vaccinated' => FILTER_SANITIZE_STRING,
    'vdate' => FILTER_SANITIZE_STRING,
    'recovered' => FILTER_SANITIZE_STRING,
    'rdate' => FILTER_SANITIZE_STRING,
    'tested' => FILTER_SANITIZE_STRING,
    'tdate' => FILTER_SANITIZE_STRING,
    'test_agency' => FILTER_SANITIZE_STRING,
    'test_type' => FILTER_SANITIZE_STRING,
    'chip' => FILTER_VALIDATE_INT,
    'privacy_policy' => FILTER_SANITIZE_STRING,
));

//error_log(print_r($inputs, true));
$error = false;
$errors = array();

try {
    $row_person = get_attendee_by_uuid($mysqli, $inputs['uuid'], false, $errors);
}catch (\Exception $e) {
    require_once dirname(__FILE__)."/../../error.php";
    die();
}

$null = null;

if (!($stmt = $mysqli->prepare(
    "INSERT INTO verification_data(id, aid, vaccination_status, vaccination_date, recovery_status, recovery_date, test_status, test_datetime, test_type, test_agency, privacy_policy) 
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {
    $error = true;
    array_push($errors, "Error saving to database (verification_data) " . $mysqli->errno);
}

$pp = isset($inputs['privacy_policy']) && ($inputs['privacy_policy'] == "Yes" ||
        $inputs['privacy_policy'] == "yes" ||
        $inputs['privacy_policy'] == "On" ||
        $inputs['privacy_policy'] == "on" ||
        $inputs['privacy_policy'] == "1");
$vacced = isset($inputs['vaccinated']) && ($inputs['vaccinated'] == "Yes" ||
        $inputs['vaccinated'] == "yes" ||
        $inputs['vaccinated'] == "On" ||
        $inputs['vaccinated'] == "on" ||
        $inputs['vaccinated'] == "1");

$recct = isset($inputs['recovered']) && ($inputs['recovered'] == "Yes" ||
        $inputs['recovered'] == "yes" ||
        $inputs['recovered'] == "On" ||
        $inputs['recovered'] == "on" ||
        $inputs['recovered'] == "1");

$tested = isset($inputs['tested']) && ($inputs['tested'] == "Yes" ||
    $inputs['tested'] == "yes" ||
    $inputs['tested'] == "On" ||
    $inputs['tested'] == "on" ||
    $inputs['tested'] == "1");

foreach ($inputs as &$i) {
    if(empty($i)){
        $i = null;
    }
}
if (!$stmt->bind_param("iisisisssi",
    $row_person['id'],
    $vacced, $inputs['vdate'] ,
    $recct, $inputs['rdate'],
    $tested, $inputs['tdate'],
    $inputs['test_type'], $inputs['test_agency'], $pp)) {
    $error = true;
    error_log("person ".print_r($row_person, true));
    array_push($errors, "Error saving to database (verification_data) " . $mysqli->error);
}

if (!$stmt->execute()) {
    $error = true;
    error_log("person ".print_r($row_person, true));
    error_log("person ".print_r($inputs, true));
    array_push($errors, "Error saving to database (verification_data) " . $mysqli->error);
}

$stmt->close();

if ($error) {
    error_log(print_r($errors, true));
    require_once HERE."/error.php";
    die();
}

$dc = isset($inputs['data_correct']) && ($inputs['data_correct'] == "Yes" ||
        $inputs['data_correct'] == "yes" ||
        $inputs['data_correct'] == "On" ||
        $inputs['data_correct'] == "on" ||
        $inputs['data_correct'] == "1");

if($dc && $_POST['submit'] != "invalidate") {
    if (!($stmt = $mysqli->prepare("INSERT INTO detail_verification(id, user, credential, challenge, challenge_date, verification_date) VALUES (null, ?, 'CORE_DATA', null, NOW(), NOW())"))) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    if (!$stmt->bind_param("i", $row_person['id'])) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    if (!$stmt->execute()) {
        $error = true;
        array_push($errors, "Error saving to database (detail_verification) " . $mysqli->errno);
    }
    $stmt->close();
}
if ($error) {
    require_once HERE."/error.php";
    die();
}

if(!($stmt = $mysqli->prepare("INSERT INTO check_events(eid, aid, time, event, chip) VALUES (null, ?, NOW(), 'checkin', ?)"))){
    $error = true;
    array_push($errors, "Error saving to database (check_events) ".$mysqli->errno);
}
if(!$stmt->bind_param("ii", $row_person['id'], $inputs['chip'])){
    $error = true;
    array_push($errors, "Error saving to database (check_events) ".$mysqli->errno);
}
if(!$stmt->execute()){
    $error = true;
    array_push($errors, "Error saving to database (check_events) ".$mysqli->errno);
}
$stmt->close();

if ($error) {
    require_once HERE."/error.php";
    die();
}

require_once dirname(__FILE__)."/inc_verify_done.php";