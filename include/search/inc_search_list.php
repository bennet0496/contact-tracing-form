<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";
require_once HERE."/include/functions.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$input = filter_input(INPUT_POST, "search", FILTER_SANITIZE_STRING);

$words = explode(" ", strtolower($input));

$prep = implode(", ", array_map(function(){ return '?';}, $words));
$bind = implode("", array_map(function(){ return 's';}, $words));

$num_key = array_search(true, array_map(function ($e){ return preg_match("/^[0-9]*$/", $e) === 1;}, $words));

//error_log(print_r($words, true));
//error_log(print_r(array_map(function ($e){ return preg_match("/^[0-9]*$/", $e) === 1;}, $words), true));
if($num_key !== false) {
    //chip number found
    $chip = intval($words[$num_key]);
    if(count($words) == 1) {
        $stmt = $mysqli->prepare("SELECT * FROM attendees WHERE chip = ?");
        $stmt->bind_param("i", $chip);
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM attendees WHERE (LOWER(surname) IN (" . $prep . ") OR LOWER(given_name) IN (" . $prep . ")) AND chip = ?");
        $b = $bind . $bind . "i";
        call_user_func_array([$stmt, "bind_param"], array_merge([$b], $words, $words, [$chip]));
    }

} else {
    $stmt = $mysqli->prepare("SELECT * FROM attendees WHERE LOWER(surname) IN (" . $prep . ") OR LOWER(given_name) IN (" . $prep . ")");
    $b = $bind.$bind;
    call_user_func_array([$stmt, "bind_param"], array_merge([$b], $words, $words));
}

$stmt->execute();

$result = $stmt->get_result();


?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>COVID Contact tracing checkin</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicons -->
    <meta name="theme-color" content="#563d7c">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/css/form-validation.css" rel="stylesheet">
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH ?>" alt="" height="72">
        <h2>Verification form</h2>
        <p class="lead">
            <i class="bi bi-person-check" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        <?php require_once HERE."/include/inc_sidebar.php"?>
        <div class="col-md-8 order-md-1">
            <form class="needs-validation" novalidate="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?xsrf=<?php echo XSRF_TOKEN;?>">
                <div class="mb-3">
                    <label class="visually-hidden" for="search">Search Term</label>
                    <div class="input-group">
                        <div class="input-group-text"><i class="bi bi-search"></i></div>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Enter Name or Chipnumber" value="<?= htmlentities($input); ?>">
                    </div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="submit">Search</button>
            </form>
            <hr class="mb-4">
            <div class="list-group mb-3">
                <form class="" novalidate="" method="POST" action="verify.php?xsrf=<?php echo XSRF_TOKEN;?>">
                    <input type="hidden" name="submit" value="submit" />
            <?php while (($row = $result->fetch_assoc())){
                $row = array_map(function($e){ return htmlentities($e); }, $row);?>
                <button class="list-group-item d-flex justify-content-between lh-condensed" name="search" value="<?php echo $row['uuid']?>" style="width: 100%; text-align: left">
                    <span>
                        <span class="h6 my-0">
                            <?php
                            $LOCKED = false;
                            $verified = false;
                            try {
                                $vd = get_verification_data_by_user($mysqli, $row['id'], true, $errors);
                                $LOCKED = count($vd) > 0;
                                $details = get_detail_verification_by_user($mysqli, $row['id'], true, $errors);
                                $verified = array_search(true, array_map(function($e){
                                    return $e['credential'] == "CORE_DATA";
                                }, $details)) !== false;
                            } catch (\Exception $e) {}

                            if($LOCKED) {
                                echo '<span class="text-muted">';
                            }
                            printf("%s, %s", $row['surname'], $row['given_name']);
                            if(isset($row['chip'])){
                                printf(" (%s)", $row['uuid']);
                            }
                            if($LOCKED) {
                                echo '</span>';
                            }

                            if($LOCKED) {
                                echo '&nbsp;<i class="bi bi-lock"></i>';
                                if($verified){
                                    echo '&nbsp;<i class="bi bi-patch-check-fill" style="color: #0d6efd" title="core data is verified"></i>';
                                } else {
                                    echo '&nbsp;<i class="bi bi-patch-exclamation" style="color: #bd2130" title="core data is invalid"></i>';
                                }
                            }
                            ?>
                        </span>
                        <br/>
                        <small class="text-muted">
                            <?php printf("%s %s, %s %s, %s", $row['street'], $row['house_nr'], $row['zip_code'], $row['city'], $row['country']);?> <br />
                            <?php printf("%s, %s", $row['email'], $row['phonenumber']);?>
                        </small>
                    </span>
                </button>
            <?php } ?>
                    </form>
            </div>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/bootstrap.bundle.min.js"></script>

<!--suppress JSUnresolvedVariable -->
<script>
    jQuery(function ($) {
        // get anything with the data-manyselect
        // you don't even have to name your group if only one group
        var $group = $("[data-manyselect]");

        $group.on('input', function () {
            var group = $(this).data('manyselect');
            // set required property of other inputs in group to false
            var allInGroup = $('*[data-manyselect="'+group+'"]');
            // Set the required property of the other input to false if this input is not empty.
            var oneSet = true;
            $(allInGroup).each(function(){
                if ($(this).val() !== "")
                    oneSet = false;
            });
            $(allInGroup).prop('required', oneSet)
        });
    });
</script>
<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/form-validation.js"></script>

</body>
</html>

