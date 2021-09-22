<?php
ob_start();


if (!session_start()) {
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Error");
    ob_flush();
    die();
}

$strong = false;
$t = openssl_random_pseudo_bytes(16, $strong);
if ($t == false) {
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Error");
    ob_flush();
    die();
}

define("XSRF_TOKEN", bin2hex($t));
$OLD_TOKEN = $_SESSION['XSRF_TOKEN'] ?? "";
$_SESSION['XSRF_TOKEN'] = XSRF_TOKEN;

if (isset($_SESSION['userdata'])) {
    $in_token = filter_input(INPUT_GET, "xsrf", FILTER_SANITIZE_STRING);
    if ($in_token != $OLD_TOKEN) {
        require_once __DIR__ . "/xsrf_error.php";
        ob_flush();
        exit(0);
    }
    //submit is from search form
    include_once __DIR__ . "/include/audit/inc_audit_list.php";
} else {
    header("Location: login.php", true, 302);
    ob_flush();
}
