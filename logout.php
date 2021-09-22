<?php
//error_reporting(E_ALL);
//ini_set("display_errors", "on");
ob_start();

session_start();

//require_once __DIR__."/inc_audit_log_function.php";
//audit($_SESSION['userdata']['id'], "logout", "{}");

unset($_SESSION['userdata']);
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(), '', 0, '/');
#session_regenerate_id(true);

header("Location: login.php", true, 302);

ob_flush();
