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