<?php
if(!defined("INCLUDED"))
    die();

//chip ; Vorname ; Nachname ; Strasse ; PLZ ; Stadt ; tel ; mail ; valid recovery date

require_once dirname(__FILE__)."/../../config.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$inputs = filter_input_array(INPUT_POST, array(
        'from' => FILTER_SANITIZE_STRING,
        'to' => FILTER_SANITIZE_STRING,
        'vv' => FILTER_SANITIZE_NUMBER_INT,
        'rv' => FILTER_SANITIZE_NUMBER_INT,
        'format' => FILTER_SANITIZE_STRING
));

$stmt = $mysqli->prepare(
"SELECT *, ce.chip as real_chip FROM attendees a 
    LEFT JOIN check_events ce on a.id = ce.aid 
    LEFT JOIN verification_data vd on a.id = vd.aid
    WHERE ce.time BETWEEN ? AND ?"
);
try {
    $from = (new DateTime($inputs['from']))->format(DATE_ATOM);
} catch (Exception $e) {
    $from = "1970-01-01T00:00:00+0000";
}
try {
    $to = (new DateTime($inputs['to']))->format(DATE_ATOM);
} catch (Exception $e) {
    $to = (new DateTime("now"))->format(DATE_ATOM);
}

$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$result = $stmt->get_result();

//error_log(print_r([$to, $from], true));

$data = $result->fetch_all(MYSQLI_BOTH);

error_log(print_r($inputs, true));

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
        <img class="d-block mx-auto mb-4" src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/img/logo-startpage.png" alt="" height="72">
        <h2>Verification form</h2>
        <p class="lead">
            <i class="bi bi-person-check" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        <?php require_once HERE."/include/inc_sidebar.php"?>
        <div class="col-md-8 order-md-1">
            <form class="" novalidate="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?xsrf=<?php echo XSRF_TOKEN;?>">
                <div class="form-row row">
                    <div class="form-group col-md-6">
                        <label class="" for="from">From</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="bi bi-calendar-range"></i></div>
                            <input type="datetime-local" class="form-control" id="from" name="from" placeholder="yesterday">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="" for="to">To</label>
                        <div class="input-group">
                            <div class="input-group-text"><i class="bi bi-calendar-range"></i></div>
                            <input type="datetime-local" class="form-control" id="to" name="to" placeholder="today">
                        </div>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="form-group col-md-6">
                        <label class="" for="vv">Vaccination Validity (s)</label>
                        <div class="input-group">
                            <input type="number" min="-1" class="form-control" id="vv" name="vv" value="<?= $inputs['vv']?>" title="-1 for indefinitely">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="" for="rv">Recovery Validity (s)</label>
                        <div class="input-group">
                            <input type="number" min="-1" class="form-control" id="rv" name="rv" title="-1 for indefinitely" value="<?= $inputs['rv']?>">
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
                                <option value="csv" <?= $inputs['format'] == "csv" ? "selected" : ""?>>Comma Seperated Value</option>
                                <option value="show" <?= $inputs['format'] == "show" ? "selected" : ""?>>Show</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="submit">Export</button>
            </form>
            <hr class="mb-4">
            <?php if($inputs['format'] == "csv") {?>
                <textarea readonly class="form-text col-md-12"
                          style="white-space: pre; min-height: 30vh; height: auto;"><?php foreach ($data as $row) { if($row['event'] != "checkin") continue;
                    $v_to = PHP_INT_MAX/2;
                    if($inputs["vv"] > 0 && !empty($row['vaccination_date'])) {
                        try {
                            $vd = new DateTime($row['vaccination_date']);
                        } catch (Exception $e) {
                            $vd = new DateTime("now");
                        }
                        try {
                            $vd->add(new DateInterval("PT" . $inputs["vv"] . "S"));
                        } catch (Exception $e) {
                            //pass

                        }
                        $v_to = $vd->getTimestamp();
                    }
                    $r_to = PHP_INT_MAX/2;
                    if($inputs["rv"] > 0 && !empty($row['recovery_date'])) {
                        try {
                            $vd = new DateTime($row['recovery_date']);
                        } catch (Exception $e) {
                            $vd = new DateTime("now");
                        }
                        try {
                            $vd->add(new DateInterval("PT" . $inputs["rv"] . "S"));
                        } catch (Exception $e) {
                            //pass

                        }
                        $r_to = $vd->getTimestamp();
                    }

                    $f_to = min($r_to, $v_to);
                    $date = new DateTime();
                    $date->setTimestamp($f_to);
                    if(intval($date->format("Y")) > 9999){
                        $date->setDate(9999, 12, 31);
                    }
                    $date_string = $date->format("Y/m/d");
                    //chip ; Vorname ; Nachname ; Strasse ; PLZ ; Stadt ; tel ; mail ; valid recovery date
                    printf("%d;%s;%s;%s %s;%s;%s;%s;%s;%s\n",
                        $row['real_chip'], $row['given_name'], $row['surname'],
                        $row['street'], $row['house_nr'], $row['zip_code'], $row['city'],
                        $row['phonenumber'] ?: "n/a", $row['email'] ?: "n/a", $date_string
                    );
                } ?>
                </textarea>
            <?php } else if($inputs['format'] == "show") {?>
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th class="col col-sm-4" >Name</th>
                        <th class="col col-sm-4" colspan="2">Address</th>
                        <th class="col col-sm-4">Contact Info</th>
                    </tr>
                    <tr>
                        <th class="col col-sm-4" colspan="1">V. Status</th>
                        <th class="col col-sm-8" colspan="3">Vaccination Date</th>
                    </tr>
                    <tr>
                        <th class="col col-sm-4" colspan="1">R. Status</th>
                        <th class="col col-sm-7" colspan="3">Recovery Date</th>
                    </tr>
                    <tr>
                        <th class="col col-sm-3" >T. Status</th>
                        <th class="col col-sm-3" >Test Date</th>
                        <th class="col col-sm-3" >Test Type</th>
                        <th class="col col-sm-3" >Test Agency</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $counter = 0;
                    foreach ($data as $row){ ?>
                        <tr <?php echo $counter % 2 ? "class='bg-white'" : ""; ?>>
                            <td><?php printf("%s, %s",$row['surname'], $row['given_name']); ?></td>
                            <td colspan="2"><?php printf("%s %s, %s %s %s, %s", $row['street'], $row['house_nr'], $row['zip_code'], $row['city'], $row['state'], $row['country']); ?></td>
                            <td><?php printf("%s, %s", $row['email'], $row['phonenumber']); ?></td>
                        </tr>
                        <tr <?php echo $counter % 2 ? "class='bg-white'" : ""; ?>>
                            <td colspan="1"><?= $row['vaccination_status'] ? "vaccinated" : "no" ;?></td>
                            <td colspan="3"><?= $row['vaccination_date'];?></td>
                        </tr>
                        <tr <?php echo $counter % 2 ? "class='bg-white'" : ""; ?>>
                            <td colspan="1"><?= $row['recovery_status'] ? "recovered" : "no" ;?></td>
                            <td colspan="3"><?= $row['recovery_date'];?></td>
                        </tr>
                        <tr <?php echo $counter % 2 ? "class='bg-white'" : ""; ?>>
                            <td colspan="1"><?= $row['test_status'] ? "tested" : "no" ;?></td>
                            <td colspan="1"><?= $row['test_datetime'];?></td>
                            <td colspan="1"><?= $row['test_type'];?></td>
                            <td colspan="1"><?= $row['test_agency'];?></td>
                        </tr>
                        <?php
                        $counter++;
                    } ?>
                    </tbody>
                </table>
            <?php }?>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/bootstrap.bundle.min.js"></script>

<!--suppress JSUnresolvedVariable -->
<script>
    jQuery(function ($) {

        $('#to').val(new Date("<?= $inputs['to'] ?>").toISOString().slice(0,-1));
        $('#from').val((new Date("<?= $inputs['from'] ?>")).toISOString().slice(0,-1));

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

