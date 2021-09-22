<script src="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/js/jquery-3.5.1.min.js"></script>

<script src="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/js/bootstrap.bundle.min.js"></script>

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
<script src="<?= rtrim(dirname(filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL)), "/"); ?>/node_modules/formvalidation/dist/js/formValidation.js"></script>