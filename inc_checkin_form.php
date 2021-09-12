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
        <h2>Check-In form</h2>
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
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">QR Login</h6>
                        <small class="text-muted">Login Person with existing QR Code</small>
                    </div>
                    <span class="text-muted"><img src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/img/QR-Code-icon.png" width="32"/></span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Name Login</h6>
                        <small class="text-muted">Login existing Person by data</small>
                    </div>
                    <span class="text-muted"><img src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/img/Search-People-icon.png" width="32"/></span>
                </li>
            </ul>

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
            <h4 class="mb-3">Core data</h4>
            <form class="needs-validation" novalidate="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">First name</label>
                        <input type="text" class="form-control" id="firstName" name="given_name" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Last name</label>
                        <input type="text" class="form-control" id="lastName" name="surname" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" data-manyselect="contact" placeholder="you@example.com" required>
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="phonenumber">Phone number</label>
                    <input type="text" class="form-control" id="phonenumber" name="phonenumber" data-manyselect="contact" placeholder="0157355544432"
                           pattern="^(((\+|00)[0-9][0-9]?[0-9]?)|0)([^0][1-9])[0-9]{3,}$"
                           required>
                    <div class="invalid-feedback">
                        Please enter a valid phone number.
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

