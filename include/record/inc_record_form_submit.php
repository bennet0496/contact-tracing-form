<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/config.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli($DB_SERVER, $DB_USER, $DB_PASS, $DB_NAME);

$uuid = $mysqli->query("SELECT UUID()")->fetch_row()[0];

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
    'vaccinated' => FILTER_SANITIZE_STRING,
    'vdate' => FILTER_SANITIZE_STRING,
    'recovered' => FILTER_SANITIZE_STRING,
    'rdate' => FILTER_SANITIZE_STRING,
    'chip' => FILTER_VALIDATE_INT,
    'privacy_policy' => FILTER_SANITIZE_STRING,
));

$error = false;
$errors = array();

if(!($stmt = $mysqli->prepare(
    "INSERT INTO attendees(id, uuid, surname, given_name, email, phonenumber, 
                      street, house_nr, zip_code, city, state, country, chip, privacy_policy) 
                      VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?, ?)"))){
    $error = true;
    array_push($errors, "Error saving data (attendees) ".$mysqli->errno);
}

$pp = isset($inputs['privacy_policy']) && ($inputs['privacy_policy'] == "Yes" ||
        $inputs['privacy_policy'] == "yes" ||
        $inputs['privacy_policy'] == "On" ||
        $inputs['privacy_policy'] == "on" ||
        $inputs['privacy_policy'] == "1");

if(!$stmt->bind_param("sssssssssssii",
    $uuid, $inputs['surname'], $inputs['given_name'],
    $inputs['email'], $inputs['phonenumber'],
    $inputs['street'], $inputs['house_nr'], $inputs['zip_code'], $inputs['city'], $inputs['state'], $inputs['country'],
    $inputs['chip'], $pp)){
    $error = true;
    array_push($errors, "Error saving data (attendees) ".$mysqli->error);
}
if(!$stmt->execute()){
    $error = true;
    array_push($errors, "Error saving data (attendees) ".$mysqli->error);
}

if ($error) {
    error_log(print_r($errors, true));
    require_once dirname(__FILE__)."/error.php";
    die();
}

//$id = $mysqli->query("SELECT id from attendees WHERE uuid = \"".$uuid."\"")->fetch_row()[0];
if(!($stmt = $mysqli->prepare("SELECT * FROM attendees WHERE uuid LIKE ?"))){
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
        require_once dirname(__FILE__) . "/error.php";
        die();
    }

$row_person = $result->fetch_assoc();



$null = null;

if ($error) {
    require_once dirname(__FILE__)."/error.php";
    die();
}

if (!($stmt = $mysqli->prepare("INSERT INTO verification_data(id, aid, vaccination_date, recovery_date, privacy_policy) VALUES (NULL, ?, ?, ?, ?)"))) {
    $error = true;
    array_push($errors, "Error saving to database (verification_data) " . $mysqli->errno);
}

$v = isset($inputs['vaccinated']) && ($inputs['vaccinated'] == "Yes" ||
        $inputs['vaccinated'] == "yes" ||
        $inputs['vaccinated'] == "On" ||
        $inputs['vaccinated'] == "on" ||
        $inputs['vaccinated'] == "1") ? $inputs['vdate'] : $null;
$r = isset($inputs['recovered']) && ($inputs['recovered'] == "Yes" ||
        $inputs['recovered'] == "yes" ||
        $inputs['recovered'] == "On" ||
        $inputs['recovered'] == "on" ||
        $inputs['recovered'] == "1") ? $inputs['rdate'] : $null;

if (!$stmt->bind_param("issi", $row_person['id'], $v , $r , $pp)) {
    $error = true;
    error_log("person ".print_r($row_person, true));
    array_push($errors, "Error saving to database (verification_data) " . $mysqli->error);
}
if (!$stmt->execute()) {
    $error = true;
    error_log("person ".print_r($row_person, true));
    array_push($errors, "Error saving to database (verification_data) " . $mysqli->error);
}

$stmt->close();

if ($error) {
    error_log(print_r($errors, true));
    require_once dirname(__FILE__)."/error.php";
    die();
}

if(!($stmt = $mysqli->prepare("INSERT INTO detail_verification(id, user, credential, challenge, challenge_date, verification_date) VALUES (null, ?, 'CORE_DATA', null, NOW(), NOW())"))){
    $error = true;
    array_push($errors, "Error saving to database (detail_verification) ".$mysqli->errno);
}
if(!$stmt->bind_param("i", $row_person['id'])){
    $error = true;
    array_push($errors, "Error saving to database (detail_verification) ".$mysqli->errno);
}
if(!$stmt->execute()){
    $error = true;
    array_push($errors, "Error saving to database (detail_verification) ".$mysqli->errno);
}
$stmt->close();

if ($error) {
    require_once dirname(__FILE__)."/error.php";
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
    require_once dirname(__FILE__)."/error.php";
    die();
}

require_once dirname(__FILE__)."/inc_record_done.php";

?>
