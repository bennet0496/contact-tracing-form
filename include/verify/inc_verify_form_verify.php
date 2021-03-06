<?php


require_once __DIR__."/../../config.php";

require_once HERE."/include/functions.php";


$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$inputs = filter_input_array(INPUT_POST, array(
    'search' => FILTER_SANITIZE_STRING
));

$uuid = $inputs['search'];

$error = false;
$errors = array();

$row = array();

try {
    $row = get_attendee_by_uuid($mysqli, $uuid);
    if (is_null($row)) {
        define("ERROR", true);
        require_once __DIR__."/inc_verify_form.php";
        exit();
    }
    $details = get_detail_verification_by_user($mysqli, $row['id']);
    $vd = get_verification_data_by_user($mysqli, $row['id']);
} catch (Exception $e) {
    require_once __DIR__."/../../error.php";
    die();
}

if (count($vd) > 0) {
    $LOCKED = true;
} else {
    $LOCKED = false;
}

if (count($vd) > 1) {
    require_once HERE."/error.php";
    die();
}

$vd = $vd[0];

//error_log(print_r($vd, true));
?>
<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"; ?>
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
            <?php if ($LOCKED) { ?>
                <div class="alert alert-warning" role="alert">
                    Case is closed. You may not make any changes!
                </div>
            <?php } ?>
            <?php
            $coredata_verified = in_array(true, array_map(function ($el) {
                return $el['credential'] == "CORE_DATA" && isset($el['verification_date']);
            }, $details));
            ?>
            <h4 class="mb-3">Core data
                <?php if ($coredata_verified) {?>
                    <i class="bi bi-patch-check-fill" style="color: #0d6efd" title="core data is verified"></i>
                <?php } else {
                    if ($LOCKED) {?>
                    <i class="bi bi-patch-exclamation" style="color: #bd2130" title="core data is invalid"></i>
                    <?php }
                } ?>
            </h4>
            <form class="needs-validation" novalidate="" method="POST"
                  action="<?= filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL); ?>?xsrf=<?= XSRF_TOKEN;?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">First name</label>
                        <input type="text" class="form-control" id="firstName" name="given_name"
                               value="<?= htmlentities($row['given_name'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Last name</label>
                        <input type="text" class="form-control" id="lastName" name="surname"
                               value="<?= htmlentities($row['surname'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="street">Street</label>
                        <input type="text" class="form-control" id="street" name="street"
                               value="<?= htmlentities($row['street'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid street is required.
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="houseNr">House number</label>
                        <input type="text" class="form-control" id="houseNr" name="house_nr"
                               value="<?= htmlentities($row['house_nr'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid House number is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="zipCode">ZIP code</label>
                        <input type="text" class="form-control" id="zipCode" name="zip_code"
                               value="<?= htmlentities($row['zip_code'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid ZIP Code is required.
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city"
                               value="<?= htmlentities($row['city'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid City is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state">State/Province</label>
                        <input type="text" class="form-control" id="state" name="state"
                               value="<?= htmlentities($row['state'])?>" disabled>
                        <div class="invalid-feedback">
                            Valid State is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country">Country</label>
                        <select class="form-control" id="country" name="country" disabled>
                        <?php
                        require_once __DIR__."/../../config.php";
                        $f = fopen(ISO_CODES, 'r');
                        while (($csv = fgetcsv($f, 0, ";"))) {
                            ?>
                            <option value="<?= $csv[0]; ?>"
                                <?php if ($row['country'] == $csv[0]) {
                                    echo "selected";
                                }?>>
                                <?= $csv[1]; ?>
                            </option>
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
                        $email_verified = in_array(
                            true,
                            array_map(function ($el) {
                                    return $el['credential'] == "EMAIL" && isset($el['verification_date']);
                            },
                            $details)
                        );
                        ?>
                    <label for="email">Email
                        <?php if ($email_verified) {?>
                            <i class="bi bi-patch-check-fill" style="color: #0d6efd" title="email is verified"></i>
                        <?php } else { ?>
                            <i class="bi bi-patch-exclamation" style="color: #bd2130" title="email was not verified yet!"></i>
                        <?php } ?>
                    </label>
                    <input type="email" class="form-control" id="email" name="email" data-manyselect="contact"
                           value="<?= htmlentities($row['email'])?>" disabled>
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>

                <div class="mb-3">
                    <?php
                    $phone_verified = in_array(
                        true,
                        array_map(function ($el) {
                                return $el['credential'] == "PHONE_NUMBER" && isset($el['verification_date']);
                        },
                        $details)
                    );
                    ?>
                    <label for="phonenumber">Phone number
                        <?php if ($phone_verified) {?>
                            <i class="bi bi-patch-check-fill" style="color: #0d6efd" title="phone number is verified"></i>
                        <?php } else { ?>
                            <i class="bi bi-patch-exclamation" style="color: #bd2130" title="phone number was not verified yet!"></i>
                        <?php } ?>
                    </label>
                    <input type="text" class="form-control" id="phonenumber" name="phonenumber" data-manyselect="contact"
                           value="<?= htmlentities($row['phonenumber'])?>" disabled>
                    <div class="invalid-feedback">
                        Please enter a valid phone number.
                    </div>
                </div>
                <hr class="mb-4">
                <h3>Person Status</h3>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="dataCorrect" name="data_correct"
                            <?= $LOCKED ? "disabled" : ""; ?>
                            <?= $coredata_verified ? "checked" : ""; ?>>
                        <label class="form-check-label" for="dataCorrect">
                            Data is correct (checked with government ID)
                        </label>
                    </div>
                </div>
                <?php if (REQUIRE_VACCINATION_STATUS) { ?>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="vaccinated" name="vaccinated"
                            <?= $LOCKED ? "disabled" : ""; ?>
                            <?= !is_null($vd) && $vd['vaccination_status'] ? "checked" : ""; ?>>
                        <label class="form-check-label" for="vaccinated" >
                            Valid (Full) Vaccination Certification shown<br/>
                            <small><i>1/1 for Janssen; 2/2 for Comirnaty (BioNTech), Spikevax (Moderna) and Vaxzevria
                                    (AstraZeneca)</i><br/>
                                No other vaccine is considered Valid by
                                <a href="https://www.pei.de/DE/arzneimittel/impfstoffe/covid-19/covid-19-node.html"
                                   target="_blank">STIKO and PEI</a>
                            </small>
                        </label>
                    </div>
                </div>
                <?php } ?>
                <?php if (REQUIRE_VACCINATION_DATE) { ?>
                <div class="mb-3">
                    <label for="vdate">Date of Full Vaccination</label>
                    <input type="date" class="form-control" id="vdate" name="vdate"
                        <?= $LOCKED ? "disabled" : ""; ?>
                        <?= !is_null($vd) && !is_null($vd['vaccination_date']) ?
                            "value='".$vd['vaccination_date']."'" : ""; ?>>
                    <div class="invalid-feedback">
                        Please enter a valid date.
                    </div>
                </div>
                <?php } ?>
                <?php if (REQUIRE_RECOVERY_STATUS) { ?>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="recovered" name="recovered"
                            <?= $LOCKED ? "disabled" : ""; ?>
                            <?= !is_null($vd) && $vd['recovery_status'] ? "checked" : ""; ?>>
                        <label class="form-check-label" for="recovered">
                            Valid Recovery Certification shown
                        </label>
                    </div>
                </div>
                <?php } ?>
                <?php if (REQUIRE_RECOVERY_DATE) { ?>
                <div class="mb-3">
                    <label for="rdate">Recovery Date</label>
                    <input type="date" class="form-control" id="rdate" name="rdate"
                        <?= $LOCKED ? "disabled" : ""; ?>
                        <?= !is_null($vd) && !is_null($vd['recovery_date']) ?
                            "value='".$vd['recovery_date']."'" : ""; ?>>
                    <div class="invalid-feedback">
                        Please enter a valid date.
                    </div>
                </div>
                <?php } ?>
                <?php if (REQUIRE_TEST_STATUS) { ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tested" name="tested"
                                <?= $LOCKED ? "disabled" : ""; ?>
                                <?= !is_null($vd) && $vd['test_status'] ? "checked" : ""; ?>>
                            <label class="form-check-label" for="tested">
                                Valid negative test shown
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <?php if (REQUIRE_TEST_DATE) { ?>
                    <div class="mb-3">
                        <label for="tdate">Test Date and Time</label>
                        <input type="datetime-local" class="form-control" id="tdate" name="tdate"
                            <?= $LOCKED ? "disabled" : ""; ?>
                            <?php try {
                                echo !is_null($vd) && !is_null($vd['test_datetime']) ?
                                "value='" . (new DateTime($vd['test_datetime']))->format("Y-m-d\TH:i:s") . "'" : "";
                            } catch (Exception $e) {
                            } ?>>
                        <div class="invalid-feedback">
                            Please enter a valid date.
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty(REQUIRE_TEST_AGENCY_OF)) { ?>
                    <div class="mb-3">
                        <p>Test Agency</p>
                        <?php foreach (REQUIRE_TEST_AGENCY_OF as $key => $agency) { ?>
                            <input type="radio" class="form-check-input"
                                   id="testAgency<?= $key; ?>" name="test_agency" value="<?= $agency; ?>"
                                   <?php if (!is_null($vd) && $vd['test_agency'] == $agency) {
                                        echo "checked ";
                                   } ?>
                                <?= $LOCKED ? "disabled" : ""; ?>>
                            <label class="form-check-label" for="testAgency<?= $key; ?>"><?= $agency; ?></label>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty(REQUIRE_TEST_TYPE_OF)) { ?>
                    <div class="mb-3">
                        <p>Test Type</p>
                        <?php foreach (REQUIRE_TEST_TYPE_OF as $key => $tt) { ?>
                            <input type="radio" class="form-check-input"
                                   id="testType<?= $key; ?>" name="test_type" value="<?= $tt; ?>"
                                <?php if (!is_null($vd) && $vd['test_type'] == $tt) {
                                    echo "checked ";
                                } ?>
                                <?= $LOCKED ? "disabled" : ""; ?>>
                            <label class="form-check-label" for="testType<?= $key; ?>"><?= $tt; ?></label>
                        <?php } ?>
                    </div>
                <?php } ?>
                <!--<hr class="mb-4">
                <h3>Chip</h3>
                <div class="mb-3">
                    <label for="chip">Chip number</label>
                    <input type="text" class="form-control" id="chip" name="chip"
                    value="<?= htmlentities($row['chip']);?>" <?= $LOCKED ? "disabled" : ""; ?>>
                </div>-->
                <hr class="mb-4">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="privacy_policy" name="privacy_policy"
                            <?= $row['privacy_policy'] || (!is_null($vd) && $vd['privacy_policy']) ?
                                "checked" : "";?> <?= $LOCKED ? "disabled" : ""; ?>>
                        <label class="form-check-label" for="privacy_policy">
                            Agreed to Privacy Policy
                        </label>
                    </div>
                </div>
                <hr class="mb-4">
                <div class="mb-3">
                    <label for="uuid">UUID</label>
                    <input type="text" class="form-control" id="uuid" name="uuid"
                           value="<?= $row['uuid'];?>" disabled>
                    <input type="hidden" class="form-control" id="uuid" name="uuid"
                           value="<?= $row['uuid'];?>">
                </div>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-md-9">
                        <button class="btn btn-outline-secondary btn-lg btn-block" type="submit" name="submit" value="back">Back</button>
                        <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="verify"
                            <?= $LOCKED ? "disabled" : ""; ?>>Verify</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-danger btn-lg btn-block" style="float: right;" type="submit" name="submit"
                                value="invalidate" <?= $LOCKED ? "disabled" : ""; ?>>Invalidate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<?php require_once HERE."/include/inc_post_content.php"?>
</body>
</html>

