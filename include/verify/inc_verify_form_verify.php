<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";

require_once HERE."/include/functions.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$inputs = filter_input_array(INPUT_POST, array(
    'search' => FILTER_SANITIZE_STRING
));

$uuid = $inputs['search'];

$error = false;
$errors = array();

$row = array();

try {
    $row = get_attendee_by_uuid($mysqli, $uuid, true, $errors);
    $details = get_detail_verification_by_user($mysqli, $row['id'], true, $errors);
    $vd = get_verification_data_by_user($mysqli, $row['id'], true, $errors);
}catch (\Exception $e) {
    require_once dirname(__FILE__)."/../../error.php";
    die();
}

if(count($vd) > 0){
    $LOCKED = true;
}else {
    $LOCKED = false;
}

if ($error) {
    require_once HERE."/error.php";
    die();
}
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
            <?php if ($LOCKED){ ?>
                <div class="alert alert-warning" role="alert">
                    Case is closed. You may not make any changes!
                </div>
            <?php } ?>
            <?php
            $coredata_verified = in_array(true, array_map(function($el){ return $el['credential'] == "CORE_DATA" && isset($el['verification_date']); }, $details));
            ?>
            <h4 class="mb-3">Core data
                <?php if($coredata_verified) {?>
                    <i class="bi bi-patch-check-fill" style="color: #0d6efd" title="core data is verified"></i>
                <?php } else {
                    if($LOCKED){?>
                    <i class="bi bi-patch-exclamation" style="color: #bd2130" title="core data is invalid"></i>
                <?php }} ?>
            </h4>
            <form class="needs-validation" novalidate="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?xsrf=<?php echo XSRF_TOKEN;?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">First name</label>
                        <input type="text" class="form-control" id="firstName" name="given_name" value="<?php echo htmlentities($row['given_name'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Last name</label>
                        <input type="text" class="form-control" id="lastName" name="surname" value="<?php echo htmlentities($row['surname'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="street">Street</label>
                        <input type="text" class="form-control" id="street" name="street" value="<?php echo htmlentities($row['street'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid street is required.
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="house_nr">House number</label>
                        <input type="text" class="form-control" id="houseNr" name="house_nr" value="<?php echo htmlentities($row['house_nr'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid House number is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="zip_code">ZIP code</label>
                        <input type="text" class="form-control" id="zipCode" name="zip_code" value="<?php echo htmlentities($row['zip_code'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid ZIP Code is required.
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlentities($row['city'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid City is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state">State/Province</label>
                        <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlentities($row['state'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid State is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country">Country</label>
                        <select class="form-control" id="country" name="country" disabled>
                        <?php
                        require_once HERE."/config.php";
                        /** @noinspection PhpUndefinedVariableInspection */
                        $f = fopen(ISO_CODES, 'r');
                        while(($csv = fgetcsv($f, 0, ";"))){
                            ?>
                            <option value="<?php echo $csv[0]; ?>" <?php if($row['country'] == $csv[0]) echo "selected"?>><?php echo $csv[1]; ?></option>
                            <?php
                        }
                        ?>
                        </select>
                        <div class="invalid-feedback">
                            Valid Country is required.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <?php
                        $email_verified = in_array(true, array_map(function($el){ return $el['credential'] == "EMAIL" && isset($el['verification_date']); }, $details));
                    ?>
                    <label for="email">Email
                        <?php if($email_verified) {?>
                            <i class="bi bi-patch-check-fill" style="color: #0d6efd" title="email is verified"></i>
                        <?php } else { ?>
                            <i class="bi bi-patch-exclamation" style="color: #bd2130" title="email was not verified yet!"></i>
                        <?php } ?>
                    </label>
                    <input type="email" class="form-control" id="email" name="email" data-manyselect="contact" value="<?php echo htmlentities($row['email'])?>" disabled>
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>

                <div class="mb-3">
                    <?php
                    $phone_verified = in_array(true, array_map(function($el){ return $el['credential'] == "PHONE_NUMBER" && isset($el['verification_date']); }, $details));
                    ?>
                    <label for="phonenumber">Phone number
                        <?php if($phone_verified) {?>
                            <i class="bi bi-patch-check-fill" style="color: #0d6efd" title="phone number is verified"></i>
                        <?php } else { ?>
                            <i class="bi bi-patch-exclamation" style="color: #bd2130" title="phone number was not verified yet!"></i>
                        <?php } ?>
                    </label>
                    <input type="text" class="form-control" id="phonenumber" name="phonenumber" data-manyselect="contact" value="<?php echo htmlentities($row['phonenumber'])?>" disabled>
                    <div class="invalid-feedback">
                        Please enter a valid phone number.
                    </div>
                </div>
                <hr class="mb-4">
                <h3>Person Status</h3>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="dataCorrect" name="data_correct" <?php echo $LOCKED ? "disabled" : ""; ?> <?php echo $coredata_verified ? "checked" : ""; ?>>
                        <label class="form-check-label" for="dataCorrect">
                            Data is correct (checked with government ID)
                        </label>
                    </div>
                </div>
                <?php if(REQUIRE_VACCINATION_STATUS){ ?>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="vaccinated" name="vaccinated" <?php echo $LOCKED ? "disabled" : ""; ?> <?php echo !is_null($vd) && $vd['vaccination_status'] ? "checked" : ""; ?>>
                        <label class="form-check-label" for="vaccinated" >
                            Valid (Full) Vaccination Certification shown<br/>
                            <small><i>1/1 for Janssen; 2/2 for Comirnaty (BioNTech), Spikevax (Moderna) and Vaxzevria (AstraZeneca)</i><br/>
                                No other vaccine is considered Valid by <a href="https://www.pei.de/DE/arzneimittel/impfstoffe/covid-19/covid-19-node.html" target="_blank">STIKO and PEI</a></small>
                        </label>
                    </div>
                </div>
                <?php } ?>
                <?php if(REQUIRE_VACCINATION_DATE){ ?>
                <div class="mb-3">
                    <label for="vdate">Date of Full Vaccination</label>
                    <input type="date" class="form-control" id="vdate" name="vdate" <?php echo $LOCKED ? "disabled" : ""; ?> <?php echo !is_null($vd) && !is_null($vd['vaccination_date']) ? "value='".$vd['vaccination_date']."'" : ""; ?>>
                    <div class="invalid-feedback">
                        Please enter a valid date.
                    </div>
                </div>
                <?php } ?>
                <?php if(REQUIRE_RECOVERY_STATUS){ ?>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="recovered" name="recovered" <?php echo $LOCKED ? "disabled" : ""; ?> <?php echo !is_null($vd) && $vd['recovery_status'] ? "checked" : ""; ?>>
                        <label class="form-check-label" for="recovered">
                            Valid Recovery Certification shown
                        </label>
                    </div>
                </div>
                <?php } ?>
                <?php if(REQUIRE_RECOVERY_DATE){ ?>
                <div class="mb-3">
                    <label for="rdate">Recovery Date</label>
                    <input type="date" class="form-control" id="rdate" name="rdate" <?php echo $LOCKED ? "disabled" : ""; ?> <?php echo !is_null($vd) && !is_null($vd['recovery_date']) ? "value='".$vd['recovery_date']."'" : ""; ?>>
                    <div class="invalid-feedback">
                        Please enter a valid date.
                    </div>
                </div>
                <?php } ?>
                <?php if(REQUIRE_TEST_STATUS){ ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tested" name="tested" <?php echo $LOCKED ? "disabled" : ""; ?> <?php echo !is_null($vd) && $vd['test_status'] ? "checked" : ""; ?>>
                            <label class="form-check-label" for="tested">
                                Valid negative test shown
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <?php if(REQUIRE_TEST_DATE){ ?>
                    <div class="mb-3">
                        <label for="tdate">Test Date and Time</label>
                        <input type="datetime-local" class="form-control" id="tdate" name="tdate" <?php echo $LOCKED ? "disabled" : ""; ?> <?php echo !is_null($vd) && !is_null($vd['test_datetime']) ? "value='".(new DateTime($vd['test_datetime']))->format("Y-m-d\TH:i:s")."'" : ""; ?>>
                        <div class="invalid-feedback">
                            Please enter a valid date.
                        </div>
                    </div>
                <?php } ?>
                <?php if(!empty(REQUIRE_TEST_AGENCY_OF)){ ?>
                    <div class="mb-3">
                        <p>Test Agency</p>
                        <?php foreach (REQUIRE_TEST_AGENCY_OF as $key => $agency) { ?>
                            <input type="radio" class="form-check-input"
                                   id="testAgency<?= $key; ?>" name="test_agency" value="<?= $agency; ?>"
                                   <?php if(!is_null($vd) && $vd['test_agency'] == $agency) echo "checked "; ?>
                                <?php echo $LOCKED ? "disabled" : ""; ?>>
                            <label class="form-check-label" for="testAgency<?= $key; ?>"><?= $agency; ?></label>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if(!empty(REQUIRE_TEST_TYPE_OF)){ ?>
                    <div class="mb-3">
                        <p>Test Type</p>
                        <?php foreach (REQUIRE_TEST_TYPE_OF as $key => $tt) { ?>
                            <input type="radio" class="form-check-input"
                                   id="testType<?= $key; ?>" name="test_type" value="<?= $tt; ?>"
                                <?php if(!is_null($vd) && $vd['test_type'] == $tt) echo "checked "; ?>
                                <?php echo $LOCKED ? "disabled" : ""; ?>>
                            <label class="form-check-label" for="testType<?= $key; ?>"><?= $tt; ?></label>
                        <?php } ?>
                    </div>
                <?php } ?>
                <!--<hr class="mb-4">
                <h3>Chip</h3>
                <div class="mb-3">
                    <label for="chip">Chip number</label>
                    <input type="text" class="form-control" id="chip" name="chip" value="<?php echo htmlentities($row['chip']);?>" <?php echo $LOCKED ? "disabled" : ""; ?>>
                </div>-->
                <hr class="mb-4">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="privacy_policy" name="privacy_policy" <?php echo $row['privacy_policy'] || (!is_null($vd) && $vd['privacy_policy']) ? "checked" : "";?> <?php echo $LOCKED ? "disabled" : ""; ?>>
                        <label class="form-check-label" for="privacy_policy">
                            Agreed to Privacy Policy
                        </label>
                    </div>
                </div>
                <hr class="mb-4">
                <div class="mb-3">
                    <label for="uuid">UUID</label>
                    <input type="text" class="form-control" id="uuid" name="uuid" value="<?php echo $row['uuid'];?>" disabled>
                    <input type="hidden" class="form-control" id="uuid" name="uuid" value="<?php echo $row['uuid'];?>">
                </div>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-md-9">
                        <button class="btn btn-outline-secondary btn-lg btn-block" type="submit" name="submit" value="back">Back</button>
                        <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="verify" <?php echo $LOCKED ? "disabled" : ""; ?>>Verify</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-danger btn-lg btn-block" style="float: right;" type="submit" name="submit" value="invalidate" <?php echo $LOCKED ? "disabled" : ""; ?>>Invalidate</button>
                    </div>
                </div>
            </form>
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

