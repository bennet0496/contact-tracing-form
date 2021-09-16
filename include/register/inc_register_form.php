<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";

$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
error_log($locale);

(include_once HERE."/locale/".preg_replace("/[\/\\\]/","",$locale).".php") ?: include_once HERE."/locale/default.php";

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
    <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

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
    <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/css/form-validation.css" rel="stylesheet">
    <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH; ?>" alt="" height="72">
        <h2><?= LANG("Registration form") ?></h2>
        <p class="lead">
            <i class="bi bi-person-plus" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        <div class="col-md-12 order-md-1">
            <h4 class="mb-3"><?= LANG("Core data") ?>&nbsp;<span></span></h4>
            <form class="needs-validation" novalidate="" method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName"><?= LANG("First name");?><span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="firstName" name="given_name" required>
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName"><?= LANG("Last name");?><span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="lastName" name="surname" required>
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="street"><?= LANG("Street");?><span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="street" name="street" required>
                        <div class="invalid-feedback">
                            Valid street is required.
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="houseNr"><?= LANG("House number");?><span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="houseNr" name="house_nr" required>
                        <div class="invalid-feedback">
                            Valid House number is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="zipCode"><?= LANG("ZIP code");?><span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="zipCode" name="zip_code" required>
                        <div class="invalid-feedback">
                            Valid ZIP Code is required.
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="city"><?= LANG("City");?><span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="city" name="city" required>
                        <div class="invalid-feedback">
                            Valid City is required.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state"><?= LANG("State/Province");?></label>
                        <input type="text" class="form-control" id="state" name="state">
                        <div class="invalid-feedback">
                            Valid State is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country"><?= LANG("Country");?><span style="color: red;">*</span></label>
                        <select class="form-select" id="country" name="country">
                        <?php
                        $f = fopen(ISO_CODES, 'r');
                        while(($csv = fgetcsv($f, 0, ";"))){
                            ?>
                            <option value="<?php echo $csv[0]; ?>" <?php if("DEU" == $csv[0]) echo "selected"?>><?= LANG($csv[1]); ?></option>
                            <?php
                        }
                        ?>
                        </select>
                        <div class="invalid-feedback">
                            Valid Country is required.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" title="Email is required"><?= LANG("Email");?><span style="color: red;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phonenumber"><?= LANG("Phonenumber");?></label>
                        <input type="text" class="form-control" id="phonenumber" name="phonenumber"
                               pattern="^(((\+|00)[0-9][0-9]?[0-9]?)|0)([^0][1-9])[0-9]{3,}$">
                        <div class="invalid-feedback">
                            Please enter a valid phone number.
                        </div>
                    </div>
                </div>
                <hr class="mb-4">
                <!--<div class="mb-3">
                    <label for="chip">
                        Chip number
                    </label>
                    <input type="text" class="form-control" id="chip" name="chip" pattern="^[0-9]*"/>
                    <i>If you already have a chip to enter the MPI PKS, the chip number is the 5-digit number printed on it. If you don't already have a chip or your chip number is not readable anymore, you can leave this empty</i>
                    <div class="invalid-feedback">
                        The chip number only contains digits
                    </div>
                </div>
                <hr class="mb-4">-->
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="privacy_policy" name="privacy_policy" required>
                        <label class="form-check-label" for="privacy_policy">
                            <?php echo preg_replace("/\|(.*)?\|/",
                            "<a href=\"#\" data-toggle=\"modal\" data-target=\"#privacy_policy_modal\">$1</a>",
                                LANG("I read the |Privacy Policy| and agree to the data processing"));?>

                        </label>
                    </div>
                </div>
                <hr class="mb-4">
                <div class="mb-3">
                    <i>
                        <?= LANG("In the next step your are required to verify your email-address or phonenumber. After that your access code is generated which is used to verify your data before entering the location.");?>
                    </i>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="register"><?= LANG("Register");?></button>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="privacy_policy_modal" tabindex="-1" role="dialog" aria-labelledby="privacy_policy_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacy_policy_modal_label"><?= LANG("Privacy Policy");?></h5>
                </div>
                <div class="modal-body">
                    <?php echo PRIVACY_POLICY;?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= LANG("Close");?></button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/bootstrap.bundle.min.js"></script>

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
<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/form-validation.js"></script>

</body>
</html>

