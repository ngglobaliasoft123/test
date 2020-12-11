require([
    "jquery",
    'mage/translate'
], function ($) {
    $(document).ready(function() {
        $('.button-next').click(function() {
            $('.button-next').addClass('disabled');
        });
    });
});
