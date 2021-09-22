<?php

require_once __DIR__."/../../config.php";
?>
<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"; ?>


<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH ?>" alt="" height="72">
        <h2>Record form</h2>
        <p class="lead">
            <i class="bi bi-person-check" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        <?php require_once HERE."/include/inc_sidebar.php"?>
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Core data&nbsp;<span></span></h4>
            <form class="needs-validation" novalidate="" method="POST" action="<?= filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL); ?>?xsrf=<?= XSRF_TOKEN;?>" autocomplete="off">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">First name<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="firstName" name="given_name" autocomplete="nope" required>
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Last name<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="lastName" name="surname" autocomplete="nope" required>
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="street">Street<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="street" name="street" autocomplete="nope" required>
                        <div class="invalid-feedback">
                            Valid street is required.
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="houseNr">House number<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="houseNr" name="house_nr" autocomplete="nope" required>
                        <div class="invalid-feedback">
                            Valid House number is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="zipCode">ZIP code<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="zipCode" name="zip_code" autocomplete="nope" required>
                        <div class="invalid-feedback">
                            Valid ZIP Code is required.
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="city">City<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="city" name="city" autocomplete="nope" required>
                        <div class="invalid-feedback">
                            Valid City is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state">State/Province</label>
                        <input type="text" class="form-control" id="state" autocomplete="nope" name="state">
                        <div class="invalid-feedback">
                            Valid State is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country">Country<span style="color: red;">*</span></label>
                        <select class="form-select" id="country" name="country" autocomplete="nope">
                            <?php
                            $f = fopen(ISO_CODES, 'r');
                            while (($csv = fgetcsv($f, 0, ";"))) {
                                ?>
                                <option value="<?= $csv[0]; ?>"
                                    <?php if ("DEU" == $csv[0]) {
                                        echo "selected";
                                    }?>>
                                    <?php printf("%s (%s)", $csv[1], $csv[3]); ?>
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

                <script>
                    function togglePhone(elm) {
                        console.log(elm);
                        document.getElementById("phonenumber").required = elm.value === "";
                    }
                    function toggleMail(elm) {
                        console.log(elm);
                        document.getElementById("email").required = elm.value === "";
                    }
                </script>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" title="Email or Phonenumber are required">Email<span style="color: orange;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" data-manyselect="contact" onchange="togglePhone(this)" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phonenumber" title="Email or Phonenumber are required">Phone number<span style="color: orange;">*</span></label>
                        <input type="text" class="form-control" id="phonenumber" name="phonenumber" data-manyselect="contact" autocomplete="nope"
                               pattern="^(((\+|00)[0-9][0-9]?[0-9]?)|0)([^0][1-9])[0-9]{3,}$" onchange="toggleMail(this)" required>
                        <div class="invalid-feedback">
                            Please enter a valid phone number.
                        </div>
                    </div>
                </div>
                <hr class="mb-4">
                <h3>Person Status</h3>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="dataCorrect" name="data_correct" autocomplete="nope">
                        <label class="form-check-label" for="dataCorrect">
                            Data is correct (checked with government ID)
                        </label>
                    </div>
                </div>
                <?php if (REQUIRE_VACCINATION_STATUS) { ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="vaccinated" name="vaccinated">
                            <label class="form-check-label" for="vaccinated" >
                                Valid (Full) Vaccination Certification shown<br/>
                                <small><i>1/1 for Janssen; 2/2 for Comirnaty (BioNTech), Spikevax (Moderna) and Vaxzevria (AstraZeneca)</i><br/>
                                    No other vaccine is considered Valid by <a href="https://www.pei.de/DE/arzneimittel/impfstoffe/covid-19/covid-19-node.html" target="_blank">STIKO and PEI</a></small>
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <?php if (REQUIRE_VACCINATION_DATE) { ?>
                    <div class="mb-3">
                        <label for="vdate">Date of Full Vaccination</label>
                        <input type="date" class="form-control" id="vdate" name="vdate">
                        <div class="invalid-feedback">
                            Please enter a valid date.
                        </div>
                    </div>
                <?php } ?>
                <?php if (REQUIRE_RECOVERY_STATUS) { ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="recovered" name="recovered" >
                            <label class="form-check-label" for="recovered">
                                Valid Recovery Certification shown
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <?php if (REQUIRE_RECOVERY_DATE) { ?>
                    <div class="mb-3">
                        <label for="rdate">Recovery Date</label>
                        <input type="date" class="form-control" id="rdate" name="rdate">
                        <div class="invalid-feedback">
                            Please enter a valid date.
                        </div>
                    </div>
                <?php } ?>
                <?php if (REQUIRE_TEST_STATUS) { ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tested" name="tested" >
                            <label class="form-check-label" for="tested">
                                Valid negative test shown
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <?php if (REQUIRE_TEST_DATE) { ?>
                    <div class="mb-3">
                        <label for="tdate">Test Date and Time</label>
                        <input type="datetime-local" class="form-control" id="tdate" name="tdate" >
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
                                   id="testAgency<?= $key; ?>" name="test_agency" value="<?= $agency; ?>">
                            <label class="form-check-label" for="testAgency<?= $key; ?>"><?= $agency; ?></label>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty(REQUIRE_TEST_TYPE_OF)) { ?>
                    <div class="mb-3">
                        <p>Test Type</p>
                        <?php foreach (REQUIRE_TEST_TYPE_OF as $key => $tt) { ?>
                            <input type="radio" class="form-check-input"
                                   id="testType<?= $key; ?>" name="test_type" value="<?= $tt; ?>">
                            <label class="form-check-label" for="testType<?= $key; ?>"><?= $tt; ?></label>
                        <?php } ?>
                    </div>
                <?php } ?>
                <!--<hr class="mb-4">
                <h3>Chip</h3>
                <div class="mb-3">
                    <label for="chip">Chip number</label>
                    <input type="text" class="form-control" id="chip" name="chip" autocomplete="nope">
                </div>-->
                <hr class="mb-4">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="privacy_policy" name="privacy_policy" autocomplete="nope">
                        <label class="form-check-label" for="privacy_policy">
                            Agreed to Privacy Policy
                        </label>
                    </div>
                </div>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-md-9">
                        <button class="btn btn-outline-secondary btn-lg btn-block" type="submit" name="submit" value="back">Back</button>
                        <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="verify">Save</button>
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

