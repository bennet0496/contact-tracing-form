<?php
ob_start();
const INCLUDED = true;

if(!session_start()){
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Errror");
    ob_flush();
    die();
}

$strong = false;
$t = openssl_random_pseudo_bytes(16,$strong);
if($t == false) {
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Errror");
    ob_flush();
    die();
}

define("XSRF_TOKEN", bin2hex($t));
if(isset($_SESSION['XSRF_TOKEN'])) {
    $OLD_TOKEN = $_SESSION['XSRF_TOKEN'];
} else {
    $OLD_TOKEN = "";
}
$_SESSION['XSRF_TOKEN'] = XSRF_TOKEN;

require_once dirname(__FILE__)."/include/audit/inc_audit_log_function.php";

if(isset($_SESSION['userdata'])) {

    $in_token = filter_input(INPUT_GET, "xsrf", FILTER_SANITIZE_STRING);
    if ($in_token != $OLD_TOKEN) {
        require_once dirname(__FILE__) . "/xsrf_error.php";
        ob_flush();
        exit(0);
    }

    if(isset($_POST['submit']) && $_POST['submit'] == "submit"){
        //submit is from search form
        audit($_SESSION['userdata']['id'], "export", json_encode($_POST));
        include_once dirname(__FILE__) . "/include/export/inc_export_export.php";
    } else {
        //submit is from search form
        include_once dirname(__FILE__) . "/include/export/inc_export_form.php";
    }

} else {
    header("Location: login.php", true, 302);
    ob_flush();
}
