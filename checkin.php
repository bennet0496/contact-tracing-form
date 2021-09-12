<?php define("INCLUDED", true)?>

<?php
if (isset($_POST['submit'])) {
    require_once dirname(__FILE__)."/config.php";

    /** @noinspection PhpUndefinedVariableInspection */
    $mysqli = new mysqli($DB_SERVER, $DB_USER, $DB_PASS, $DB_NAME);

    $uuid = $mysqli->query("SELECT UUID()")->fetch_row()[0];

    $inputs = filter_input_array(INPUT_POST, array(
        'given_name' => FILTER_SANITIZE_STRING,
        'surname' => FILTER_SANITIZE_STRING,
        'email' => FILTER_VALIDATE_EMAIL,
        'phonenumber' => FILTER_SANITIZE_STRING
    ));

    $error = false;
    $errors = array();

    if(!($stmt = $mysqli->prepare("INSERT INTO attendees(id, uuid, surname, given_name, email, phonenumber) VALUES (NULL, ?, ?, ?, ?, ?)"))){
        $error = true;
        array_push($errors, "Error saving to database (attendee) ".$mysqli->errno);
    }
    if(!$stmt->bind_param("sssss", $uuid, $inputs['surname'], $inputs['given_name'], $inputs['email'], $inputs['email'])){
        $error = true;
        array_push($errors, "Error saving to database (attendee) ".$mysqli->errno);
    }
    if(!$stmt->execute()){
        $error = true;
        array_push($errors, "Error saving to database (attendee) ".$mysqli->errno);
    }

    $stmt->close();

    if(!$error){
        $id = $mysqli->query("SELECT id from attendees WHERE uuid = \"".$uuid."\"")->fetch_row()[0];
        if(!($stmt_time = $mysqli->prepare("INSERT INTO check_events(aid,time,event) VALUES (?, NOW(), 1)"))){
            $error = true;
            array_push($errors, "Error saving to database (event) ".$mysqli->errno);
        }
        if(!$stmt_time->bind_param("i", $id)){
            $error = true;
            array_push($errors, "Error saving to database (event) ".$mysqli->errno);
        }
        if(!$stmt_time->execute()){
            $error = true;
            array_push($errors, "Error saving to database (event) ".$mysqli->errno);
        }
        $stmt_time->close();
        if($error){
            include_once dirname(__FILE__)."/inc_checkin_form.php";
        } else {
            include_once dirname(__FILE__)."/inc_checkin_print.php";
        }
    } else {
        include_once dirname(__FILE__). "/inc_checkin_form.php";
    }
} else {
    include_once dirname(__FILE__). "/inc_checkin_form.php";
}
?>

