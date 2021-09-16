<?php
if(!defined("INCLUDED"))
    die();

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

    <!-- Custom styles for this template -->
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/css/form-validation.css" rel="stylesheet">
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH; ?>" alt="" height="72">
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
                    <label class="visually-hidden" for="search">Scan QR code</label>
                    <div class="input-group">
                        <div class="input-group-text"><i class="bi bi-search"></i></div>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Scan QR code">
                    </div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="submit">Open</button>
            </form>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/bootstrap.bundle.min.js"></script>

<!--suppress JSUnresolvedVariable -->
<!--suppress JSUnresolvedFunction -->
<script>
    jQuery(function ($) {
        // get anything with the data-manyselect
        // you don't even have to name your group if only one group
        let $group = $("[data-manyselect]");

        $group.on('input', function () {
            let group = $(this).data('manyselect');
            // set required property of other inputs in group to false
            let allInGroup = $('*[data-manyselect="' + group + '"]');
            // Set the required property of the other input to false if this input is not empty.
            let oneSet = true;
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

