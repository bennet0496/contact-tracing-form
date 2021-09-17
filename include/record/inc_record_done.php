<html lang="en">
<?php require_once HERE."/include/inc_html_head.php"; ?>

<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= LOGO_WEB_PATH ?>" alt="" height="72">
        <h2>Record form</h2>
        <p class="lead">
            <i class="bi bi-person-check" style="font-size: 48px"></i>
        </p>
    </div>

    <div class="row">
        <?php require_once HERE."/include/inc_sidebar.php"?>
        <div class="col-md-8 order-md-1">
            <div class="alert alert-success" role="alert">
                Successfully recorded person. Refreshing in <span id="timer">5</span>...
            </div>
        </div>
    </div>

    <script>
        window.counter = 5;
        function countd() {
            if(counter === 0) {
                window.location.assign(window.location.href)
            } else {
                window.counter--;
                document.getElementById("timer").innerText = window.counter;
                setTimeout(countd, 1000);
            }
        }
        setTimeout(countd, 1000);
    </script>
    <?php require_once HERE."/include/inc_footer.php"; ?>
</div>
<?php require_once HERE."/include/inc_post_content.php"?>
</body>
</html>
