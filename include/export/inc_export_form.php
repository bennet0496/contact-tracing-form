<?php

require_once __DIR__."/../../config.php";

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
            <form class="" novalidate="" method="POST" action="<?= filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL); ?>?xsrf=<?= XSRF_TOKEN;?>">
                <div class="form-row row">
                    <div class="form-group col-md-6">
                        <label class="" for="from">From</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="bi bi-calendar-range"></i></div>
                            <input type="datetime-local" class="form-control" id="from" name="from">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="" for="to">To</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="bi bi-calendar-range"></i></div>
                            <input type="datetime-local" class="form-control" id="to" name="to">
                        </div>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="form-group col-md-6">
                        <label class="" for="vv">Vaccination Validity (s)</label>
                        <div class="input-group">
                            <input type="number" min="-1" class="form-control" id="vv" name="vv" value="-1" title="-1 for indefinitely">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="" for="rv">Recovery Validity (s)</label>
                        <div class="input-group">
                            <input type="number" min="-1" class="form-control" id="rv" name="rv" value="14515200" title="-1 for indefinitely">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <small><i>86400 = 1d, 604800 = 1w, 2419200 = 28d = 1m, 14515200 = 6 * 28d = 6m</i></small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <label class="" for="format">Format</label>
                        <div class="input-group">
                            <select name="format" id="format" class="form-select">
                                <option value="csv" selected>Comma Seperated Value</option>
                                <option value="show">Show</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="submit">Export</button>
            </form>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<?php require_once HERE."/include/inc_post_content.php"?>
<script>
    jQuery(function ($) {

        $('#to').val(new Date().toISOString().slice(0,-1));
        $('#from').val((new Date(Date.now() - 86400000)).toISOString().slice(0,-1));

    });
</script>
</body>
</html>

