<?php
header($_SERVER['SERVER_PROTOCOL']. " 500 Internal Server error", true, 500);
ob_flush();
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
    <link href="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

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
    <link href="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/css/form-validation.css" rel="stylesheet">
    <link href="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/css/style.css" rel="stylesheet">
    <link href="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/img/logo-startpage.png" alt="" height="72">
        <h2 style="font-family: Wingdings">ThErE-wAs_An'eRrOr</h2>
        <p class="lead">
            <i class="bi bi-bug-fill" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        </div>
        <div class="col-md-12 order-md-1">
            <div class="alert alert-danger" role="alert">
                Ooops, this should not have happend. But it did! <br />
                Please try again, later or ask someone* to debug the application
                <?php
                if (isset($ERROR_MSG)) {
                    echo "<br /><code>".$ERROR_MSG."</code>";
                }
                ?>
            </div>
        </div>
    </div>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">© 2020-2020 *Bennet Becker, MPI PKS Dresden</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
</div>
<script src="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/js/bootstrap.bundle.min.js"></script>

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
<script src="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/js/form-validation.js"></script>

</body>
</html>
