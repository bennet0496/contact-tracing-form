<?php define("INCLUDED", true)?>

<?php
if (isset($_POST['submit'])) {
    require_once dirname(__FILE__)."/config.php";

    /** @noinspection PhpUndefinedVariableInspection */
    $mysqli = new mysqli($DB_SERVER, $DB_USER, $DB_PASS, $DB_NAME);

    $inputs = filter_input_array(INPUT_POST, array(
        'uuid' => FILTER_SANITIZE_STRING
    ));

    $error = false;
    $errors = array();
    $id = -1;
    $common_name = "NONE";
    $uuid = "000000-0000-0000-0000-0000-00000000";

    if(!($stmt = $mysqli->prepare("SELECT id, concat(given_name, ', ', surname) as common_name, uuid from attendees WHERE uuid = ?"))){
        $error = true;
        array_push($errors, "Error selecting from database (attendee) ".$mysqli->errno);
    }
    if(!$stmt->bind_param("s", $inputs['uuid'])){
        $error = true;
        array_push($errors, "Error selecting from database (attendee) ".$mysqli->errno);
    }
    if(!$stmt->execute()){
        $error = true;
        array_push($errors, "Error selecting from database (attendee) ".$mysqli->errno);
    } else {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        error_log(print_r($row, true));
        $id = $row['id'];
        $common_name = $row['common_name'];
        $uuid = $row['uuid'];
    }

    $stmt->close();

    if(!$error){
        if(!($stmt_time = $mysqli->prepare("INSERT INTO check_events(aid,time,event) VALUES (?, NOW(), 2)"))){
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
            include_once dirname(__FILE__)."/inc_checkout_form.php";
        } else {
            include_once dirname(__FILE__)."/inc_checkout_form.php";
        }
    } else {
        include_once dirname(__FILE__). "/inc_checkout_form.php";
    }
} else {
    include_once dirname(__FILE__). "/inc_checkout_form.php";
}
?>

