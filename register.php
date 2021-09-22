<?php  ?>

<?php
if (isset($_POST['submit'])) {
    if ($_POST['submit'] == "verify") {
        include_once __DIR__ . "/include/register/inc_register_form_verify.php";
    } elseif ($_POST['submit'] == "mail") {
        include_once __DIR__ . "/include/register/inc_register_form_mail.php";
    } else {
        include_once __DIR__ . "/include/register/inc_register_form_submit.php";
    }
} else {
    include_once __DIR__ . "/include/register/inc_register_form.php";
}


