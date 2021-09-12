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
    </style>
    <!-- Custom styles for this template -->
    <link href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/css/form-validation.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/img/006.svg" alt="" height="72">
        <h2>Check-Out form</h2>
        <p class="lead"></p>
    </div>

    <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
            <div>
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Number of Guests</span>
                    <span class="badge badge-secondary badge-pill" id="guest_counter">3</span>
                </h4>
                <h6 class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">As of: </small>
                    <small class="text-muted" id="guest_time">now</small>
                </h6>
            </div>

            <!--<form class="card p-2">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Promo code">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary">Redeem</button>
                    </div>
                </div>
            </form>-->
        </div>
        <div class="col-md-8 order-md-1">
            <?php
            /** @noinspection PhpUndefinedVariableInspection */
            if(isset($_POST['submit']) && !$error): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Successfully checked out <strong><?php /** @noinspection PhpUndefinedVariableInspection */ echo $common_name; ?></strong> (<?php /** @noinspection PhpUndefinedVariableInspection */ echo $uuid; ?>)
            </div>
            <?php
            elseif (isset($_POST['submit']) && $error):?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                        There was an error in your request:
                        <?php
                        /** @noinspection PhpUndefinedVariableInspection */
                        echo join("<br/>", $errors)?>
                </div>
            <?php endif; ?>
            <h4 class="mb-3">Checkout</h4>
            <form class="needs-validation" novalidate="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="mb-3">
                    <img src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/img/QR-Code-icon.png"
                         width="128" style="position: relative;left: calc(50% - 64px);"/>
                </div>
                <div class="mb-3">
                    <label for="uuid">Attendee Id</label>
                    <input type="text" class="form-control" id="uuid" name="uuid" placeholder="8521aec1-c1f7-11eb-90e4-945f1f7d15f4" required>
                    <div class="invalid-feedback">
                        Please enter a valid UUID.
                    </div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="submit">Continue</button>
            </form>
        </div>
    </div>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">© 2020-2020 Bennet Becker, Die H&uuml;tte Boxdorf</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
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

        $(".alert-success").delay(5000).slideUp(200, function () {
            $(this).alert('close');
        });
    });
    const evtSource = new EventSource("counter.php");
    evtSource.addEventListener("ping", function(event) {
        const elementC = document.getElementById("guest_counter");
        const elementT = document.getElementById("guest_time");

        const data = JSON.parse(event.data);

        elementC.innerHTML = data.guests;
        elementT.innerText = data.time;

        if(data.limit * 0.8 <= data.guests) {
            elementC.classList.remove("badge-secondary");
            elementC.classList.remove("badge-warning");
            elementC.classList.remove("badge-danger");
            elementC.classList.add("badge-warning");
        } else if(data.limit <= data.guests) {
            elementC.classList.remove("badge-secondary");
            elementC.classList.remove("badge-warning");
            elementC.classList.remove("badge-danger");
            elementC.classList.add("badge-danger");
        } else {
            elementC.classList.remove("badge-secondary");
            elementC.classList.remove("badge-warning");
            elementC.classList.remove("badge-danger");
            elementC.classList.add("badge-secondary");
        }
    });
</script>
<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/form-validation.js"></script>

</body>
</html>

