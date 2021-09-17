<?php
if(!defined("INCLUDED"))
    die();
require_once dirname(__FILE__)."/../../config.php";
?>
<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"; ?>

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
            <?php if(defined("ERROR")){ ?>
                <div class="alert alert-danger" role="alert">
                    There was an error retrieving the entry. Either because it does not exists or the UUID was malformed!
                </div>
            <?php } ?>
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
<?php require_once HERE."/include/inc_post_content.php"?>
</body>
</html>

