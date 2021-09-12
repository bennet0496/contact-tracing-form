<?php
if(!defined("INCLUDED"))
    die();
?>
<?php require_once dirname(__FILE__)."/config.php"; ?>
<?php require_once dirname(__FILE__)."/vendor/autoload.php"; ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>Checkout example · Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/css/bootstrap.min.css" rel="stylesheet">

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
        @page {
            size: 105mm 148mm;
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/css/form-validation.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container-sm">
    <div class="py-sm-5 row">
        <div class="col-md-12 col-6 text-center">
            <img class="d-block mx-auto mb-4" src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/img/006.svg" alt="" height="72">
            <h2><?php /** @noinspection PhpUndefinedVariableInspection */
                echo $EVENT_NAME; ?></h2>
            <p class="lead">Attendance Document</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-6">
            <div class="row">
                <div class="col mb-3 text-center">
                    <img src="<?php /** @noinspection PhpUndefinedVariableInspection */
                    echo (new \chillerlan\QRCode\QRCode())->render($uuid) ?>" />
                    <p class="text-muted"><?php echo $uuid;?></p>
                </div>
            </div>

            <div class="row">
                <div class="col-5">
                    <p>
                        <b>Name</b><br />
                        <?php /** @noinspection PhpUndefinedVariableInspection */
                        echo $inputs["given_name"]." ".$inputs["surname"]; ?>
                    </p>
                    <p>
                        <b>Email</b><br />
                        <?php echo $inputs['email'] != "" ? $inputs['email'] : "N/A" ;?>
                    </p>
                    <p>
                        <b>Phone number</b><br />
                        <?php echo $inputs['phonenumber'] != "" ? $inputs['phonenumber'] : "N/A" ;?>
                    </p>
                    <p>
                        <b>Admission time</b><br />
                        <?php echo date("d.m.Y H:i"); ?>
                    </p>
                </div>
                <div class="col-7">
                    <p>If you get test COVID-19 positive within 14-days of the admission time, please tell us and the
                        health authorities, that you attended this event.</p>
                    <p><small><?php /** @noinspection PhpUndefinedVariableInspection */
                            echo $RESPONSIBLE; ?></small></p>
                </div>
            </div>
            <hr class="mb-4 d-print-none">
            <button class="btn btn-primary btn-lg btn-block d-print-none" onclick="window.print()">Print</button>
            <button class="btn btn-secondary btn-lg btn-block d-print-none" onclick="window.location.assign(window.location.href)">Done</button>
        </div>
    </div>
</div>
<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/bootstrap.bundle.min.js"></script>

</body>
</html>

