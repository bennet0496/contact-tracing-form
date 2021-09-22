<?php
ob_start();


if (!session_start()) {
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Errror");
    ob_flush();
    die();
}

$strong = false;
$t = openssl_random_pseudo_bytes(16, $strong);
if ($t == false) {
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Errror");
    ob_flush();
    die();
}

define("XSRF_TOKEN", bin2hex($t));
$OLD_TOKEN = $_SESSION['XSRF_TOKEN'] ?? "";
$_SESSION['XSRF_TOKEN'] = XSRF_TOKEN;

require_once __DIR__ . "/include/audit/inc_audit_log_function.php";
if (isset($_SESSION['userdata'])) {
    if (isset($_POST['submit']) && $_POST['submit'] != "back") {
        $in_token = filter_input(INPUT_GET, "xsrf", FILTER_SANITIZE_STRING);
        if ($in_token != $OLD_TOKEN) {
            require_once __DIR__ . "/xsrf_error.php";
            ob_flush();
            exit(0);
        }
        //submit is from search form
        audit($_SESSION['userdata']['id'], "search", json_encode($_POST));
        include_once __DIR__ . "/include/search/inc_search_list.php";
    } else {
        //show search from
        include_once __DIR__ . "/include/search/inc_search_form.php";
    }
} else {
    header("Location: login.php", true, 302);
    ob_flush();
}
