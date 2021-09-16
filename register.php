<?php define("INCLUDED", true)?>

<?php
if (isset($_POST['submit'])) {
    if($_POST['submit'] == "verify"){
        include_once dirname(__FILE__) . "/include/register/inc_register_form_verify.php";
    } else if ($_POST['submit'] == "mail") {
        include_once dirname(__FILE__) . "/include/register/inc_register_form_mail.php";
    }
    else {
        include_once dirname(__FILE__) . "/include/register/inc_register_form_submit.php";
    }
} else {
    include_once dirname(__FILE__) . "/include/register/inc_register_form.php";
}
?>

