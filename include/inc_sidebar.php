<?php
if (!defined("INCLUDED"))
    die();

?>
<div class="col-md-4 order-md-2 mb-4">
    <p class="text-muted text-small px-1">Logged in as <?php echo $_SESSION['userdata']['common_name'];?><br /><a href="logout.php">Logout</a></p>
    <div class="list-group mb-3">
        <a class="list-group-item d-flex justify-content-between lh-condensed" href="record.php">
            <div>
                <h6 class="my-0">New Person</h6>
                <small class="text-muted">Create new Person to login</small>
            </div>
            <span class="text-muted"><i class="bi bi-person-plus" style="font-size: 32px"></i></span>
        </a>
        <a class="list-group-item d-flex justify-content-between lh-condensed" href="verify.php">
            <div>
                <h6 class="my-0">Scan QR</h6>
                <small class="text-muted">Verify Person with QR Code</small>
            </div>
            <span class="text-muted"><i class="bi bi-key" style="font-size: 32px"></i></span>
        </a>
        <a class="list-group-item d-flex justify-content-between lh-condensed" href="search.php">
            <div>
                <h6 class="my-0">Search</h6>
                <small class="text-muted">Search Person to verify by Name or Chipnumber</small>
            </div>
            <span class="text-muted"><i class="bi bi-search" style="font-size: 28px"></i></span>
        </a>
        <a class="list-group-item d-flex justify-content-between lh-condensed" href="audit.php?xsrf=<?php echo XSRF_TOKEN;?>">
            <div>
                <h6 class="my-0">Audit</h6>
                <small class="text-muted">Open Audit Log</small>
            </div>
            <span class="text-muted"><i class="bi bi-card-list" style="font-size: 28px"></i></span>
        </a>
        <a class="list-group-item d-flex justify-content-between lh-condensed" href="export.php?xsrf=<?php echo XSRF_TOKEN;?>">
            <div>
                <h6 class="my-0">Export</h6>
                <small class="text-muted">Show or Export attendees</small>
            </div>
            <span class="text-muted"><i class="bi bi-share" style="font-size: 28px"></i></span>
        </a>
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