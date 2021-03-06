<?php


require_once __DIR__."/../../config.php";
require_once HERE."/include/functions.php";

$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$input = filter_input(INPUT_POST, "search", FILTER_SANITIZE_STRING);

$words = explode(" ", strtolower($input));

$prep = implode(", ", array_map(function () {
    return '?';
}, $words));
$bind = implode("", array_map(function () {
    return 's';
}, $words));

$num_key = array_search(true, array_map(function ($e) {
    return preg_match("/^[0-9]*$/", $e) === 1;
}, $words));


if ($num_key !== false) {
    //chip number found
    $chip = intval($words[$num_key]);
    if (count($words) == 1) {
        $stmt = $mysqli->prepare("SELECT * FROM attendees WHERE chip = ?");
        $stmt->bind_param("i", $chip);
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM attendees WHERE (LOWER(surname) IN (" . $prep . ") OR LOWER(given_name) IN (" . $prep . ")) AND chip = ?");
        $b = $bind . $bind . "i";
        call_user_func_array([$stmt, "bind_param"], array_merge([$b], $words, $words, [$chip]));
    }
} else {
    $stmt = $mysqli->prepare("SELECT * FROM attendees WHERE LOWER(surname) IN (" . $prep . ") OR LOWER(given_name) IN (" . $prep . ")");
    $b = $bind.$bind;
    $wps = array();
    $i = 0;
    foreach ($words as &$word) {
        $wps[$i++] = &$word;
    }
    $p = array_merge([$b], $wps, $wps);
    call_user_func_array([$stmt, "bind_param"], $p);
}

$stmt->execute();

$result = $stmt->get_result();


?>
<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"?>

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
            <form class="needs-validation" novalidate="" method="POST" action="<?= filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL); ?>?xsrf=<?= XSRF_TOKEN;?>">
                <div class="mb-3">
                    <label class="visually-hidden" for="search">Search Term</label>
                    <div class="input-group">
                        <div class="input-group-text"><i class="bi bi-search"></i></div>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Enter Name or Chipnumber" value="<?= htmlentities($input); ?>">
                    </div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit" value="submit">Search</button>
            </form>
            <hr class="mb-4">
            <div class="list-group mb-3">
                <form class="" novalidate="" method="POST" action="verify.php?xsrf=<?= XSRF_TOKEN;?>">
                    <input type="hidden" name="submit" value="submit" />
            <?php while (($row = $result->fetch_assoc())) {
                $row = array_map(function ($e) {
                    return htmlentities($e);
                }, $row);?>
                <button class="list-group-item d-flex justify-content-between lh-condensed" name="search" value="<?= $row['uuid']?>" style="width: 100%; text-align: left">
                    <span>
                        <span class="h6 my-0">
                            <?php
                            $LOCKED = false;
                            $verified = false;
                            try {
                                $vd = get_verification_data_by_user($mysqli, $row['id']);
                                $LOCKED = count($vd) > 0;
                                $details = get_detail_verification_by_user($mysqli, $row['id']);
                                $verified = array_search(true, array_map(function ($e) {
                                    return $e['credential'] == "CORE_DATA";
                                }, $details)) !== false;
                            } catch (Exception $e) {
                            }

                            if ($LOCKED) {
                                echo '<span class="text-muted">';
                            }
                            printf("%s, %s", $row['surname'], $row['given_name']);
                            if (isset($row['chip'])) {
                                printf(" (%s)", $row['uuid']);
                            }
                            if ($LOCKED) {
                                echo '</span>';
                            }

                            if ($LOCKED) {
                                echo '&nbsp;<i class="bi bi-lock"></i>';
                                if ($verified) {
                                    echo '&nbsp;<i class="bi bi-patch-check-fill" style="color: #0d6efd" title="core data is verified"></i>';
                                } else {
                                    echo '&nbsp;<i class="bi bi-patch-exclamation" style="color: #bd2130" title="core data is invalid"></i>';
                                }
                            }
                            ?>
                        </span>
                        <br/>
                        <small class="text-muted">
                            <?php printf("%s %s, %s %s, %s", $row['street'], $row['house_nr'], $row['zip_code'], $row['city'], $row['country']);?> <br />
                            <?php printf("%s, %s", $row['email'], $row['phonenumber']);?>
                        </small>
                    </span>
                </button>
            <?php } ?>
                    </form>
            </div>
        </div>
    </div>

    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<?php require_once HERE."/include/inc_post_content.php"?>
</body>
</html>

