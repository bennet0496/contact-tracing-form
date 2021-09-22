<?php


require_once __DIR__."/../config.php";
?>
<footer class="my-5 pt-5 text-muted text-center text-small">
    <ul class="list-inline">
        <li class="list-inline-item"><a href="<?= PRIVACY_URL ?>">Privacy</a></li>
        <li class="list-inline-item"><a href="<?= IMPRINT_URL ?>">Imprint</a></li>
        <li class="list-inline-item"><a href="<?= SUPPORT_URL ?>">Support</a></li>
    </ul>
    <p class="mb-1">© 2020-<?= date('Y') ?> <?= ORGANISATION ?>, developed by <a href="https://github.com/bennet0496" class="text-muted">Bennet Becker</a></p>
</footer>

<a class="github-fork-ribbon right-bottom fixed d-print-none" href="https://github.com/bennet0496/contact-tracing-form" data-ribbon="Fork me on GitHub" title="Fork me on GitHub">Fork me on GitHub</a>
