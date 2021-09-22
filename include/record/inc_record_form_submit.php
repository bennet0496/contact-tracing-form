<?php


require_once __DIR__."/../../config.php";

require_once HERE."/include/functions.php";


$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

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

$error = false;
$errors = array();

$pp = checkbox2bool($inputs['privacy_policy']);

try {
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
    $row_person = get_attendee_by_uuid($mysqli, $uuid);

    $vacced = checkbox2bool($inputs['vaccinated']);
    $recct = checkbox2bool($inputs['recovered']);
    $tested = checkbox2bool($inputs['tested']);
    foreach ($inputs as &$i) {
        if (empty($i)) {
            $i = null;
        }
    }
    verify_person_status(
        $mysqli,
        $row_person['id'],
        $vacced,
        $inputs['vdate'],
        $recct,
        $inputs['rdate'],
        $tested,
        $inputs['tdate'],
        $inputs['test_type'],
        $inputs['test_agency'],
        $pp
    );

    verify_core_data($mysqli, $row_person['id']);
    checkin($mysqli, $row_person['id'], $inputs['chip']);
} catch (Exception $e) {
    saveDie();
}

require_once __DIR__."/inc_record_done.php";
