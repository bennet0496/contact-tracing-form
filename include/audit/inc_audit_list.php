<?php


require_once __DIR__."/../../config.php";

$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$page = max(1, filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT));

$offset = ($page - 1) * 100;

$num = $mysqli->query("SELECT count(*) FROM audit_log")->fetch_row()[0];

$stmt = $mysqli->prepare("SELECT audit_log.id as eid, u.id as uid, 
time, action, data, username, CONCAT(given_name, CONCAT(' ', surname)) AS common_name 
FROM audit_log LEFT JOIN users u on u.id = audit_log.user ORDER BY audit_log.time DESC LIMIT ?,100");

$stmt->bind_param("i", $offset);
$stmt->execute();

$result = $stmt->get_result();


?>
<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"; ?>

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
            while (($row = $result->fetch_assoc())) {
                $row = array_map(function ($e) {
                    return htmlentities($e);
                }, $row);
                if (empty($row['uid'])) {
                    $row['common_name'] = "<i>Anonymous</i>";
                }
                ?>
                <tr <?= $counter % 2 ? "class='bg-white'" : ""; ?>>
                   <td><?= $row['common_name']; ?></td>
                   <td><?= $row['action']; ?></td>
                   <td><?= $row['time']; ?></td>
                </tr>
                <tr <?= $counter % 2 ? "class='bg-white'" : ""; ?>>
                    <td colspan="3"><code><pre><?= htmlentities(json_encode(json_decode(html_entity_decode($row['data'])), JSON_PRETTY_PRINT)); ?></pre></code></td>
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
                <a class="page-link" href="?xsrf=<?= XSRF_TOKEN;?>&page=1" aria-label="First">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">First</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?xsrf=<?= XSRF_TOKEN;?>&page=<?= max(1, $page - 1)?>" aria-label="Previous">
                    <span aria-hidden="true">&lt;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            <?php for ($i = max(1, $page - 3); $i <= min($page + 7, ceil($num / 100)); $i++) {?>
                <li class="page-item <?= $page == $i ? "active" : "";?>"><a class="page-link" href="?xsrf=<?= XSRF_TOKEN;?>&page=<?= $i; ?>"><?= $i; ?></a></li>
            <?php } ?>
            <li class="page-item">
                <a class="page-link" href="?xsrf=<?= XSRF_TOKEN;?>&page=<?= min($page + 1, ceil($num / 100))?>" aria-label="Next">
                    <span aria-hidden="true">&gt;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?xsrf=<?= XSRF_TOKEN;?>&page=<?= ceil($num / 100)?>" aria-label="Last">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Last</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<?php require_once HERE."/include/inc_post_content.php"?>
</body>
</html>

