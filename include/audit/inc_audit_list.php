<?php
if(!defined("INCLUDED"))
    die();

require_once dirname(__FILE__)."/../../config.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$page = max(1, filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT));

$num = $mysqli->query("SELECT count(*) FROM audit_log")->fetch_row()[0];

$stmt = $mysqli->prepare("SELECT audit_log.id as eid, u.id as uid, 
time, action, data, username, CONCAT(given_name, CONCAT(' ', surname)) AS common_name 
FROM audit_log LEFT JOIN users u on u.id = audit_log.user ORDER BY audit_log.time DESC LIMIT ?,100");

$stmt->bind_param("i", $page);
$stmt->execute();

$result = $stmt->get_result();




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
        <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH ?>" alt="" height="72">
        <h2>AUDIT LOG</h2>
        <p class="lead">
            <i class="bi bi-card-list" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        <?php require_once HERE."/include/inc_sidebar.php"?>
        <div class="col-md-8 order-md-1">
            <hr class="mb-4">
            <div class=" table table-responsive w-100 d-block d-md-table">
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th class="col col-sm-4">User</th>
                        <th class="col col-sm-4">Action</th>
                        <th class="col col-sm-4">Datetime</th>
                    </tr>
                    <tr>
                        <th class="col col-sm-12" colspan="3">Detail</th>
                    </tr>
                    </thead>
                    <tbody>
            <?php
            $counter = 0;
            while (($row = $result->fetch_assoc())){
                $row = array_map(function($e){ return htmlentities($e); }, $row);
                if(is_null($row['uid']) || empty($row['uid'])) {
                    $row['common_name'] = "<i>Anonymous</i>";
                }
                ?>
                <tr <?php echo $counter % 2 ? "class='bg-white'" : ""; ?>>
                   <td><?php echo $row['common_name']; ?></td>
                   <td><?php echo $row['action']; ?></td>
                   <td><?php echo $row['time']; ?></td>
                </tr>
                <tr <?php echo $counter % 2 ? "class='bg-white'" : ""; ?>>
                    <td colspan="3"><code><pre><?php echo htmlentities(json_encode(json_decode(html_entity_decode($row['data'])),JSON_PRETTY_PRINT)); ?></pre></code></td>
                </tr>
            <?php
            $counter++;
            } ?>
                    </tbody>
                    </table>
            </div>
        </div>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="?xsrf=<?php echo XSRF_TOKEN;?>&page=1" aria-label="First">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">First</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?xsrf=<?php echo XSRF_TOKEN;?>&page=<?php echo max(1,$page - 1)?>" aria-label="Previous">
                    <span aria-hidden="true">&lt;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            <?php for ($i = max(1, $page - 3); $i <= min($page + 7,$num % 100); $i++) {?>
                <li class="page-item <?php echo $page == $i ? "active" : "";?>"><a class="page-link" href="?xsrf=<?php echo XSRF_TOKEN;?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php } ?>
            <li class="page-item">
                <a class="page-link" href="?xsrf=<?php echo XSRF_TOKEN;?>&page=<?php echo min($page + 1, $num % 100)?>" aria-label="Next">
                    <span aria-hidden="true">&gt;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?xsrf=<?php echo XSRF_TOKEN;?>&page=<?php echo $num % 100?>" aria-label="Last">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Last</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php require_once HERE."/include/inc_footer.php"; ?>
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
</script>
<script src="<?php echo rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/js/form-validation.js"></script>

</body>
</html>

