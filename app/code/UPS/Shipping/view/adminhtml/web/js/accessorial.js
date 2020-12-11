require([
    "jquery"
], function ($) {
    $(document).ready(function() {
        check();
        $("input[type='checkbox']").on("change",function() {
            check(this);
        })
        function check(object) {
            var getName = $(object).attr("name");
            if (getName === "UPS_ACSRL_SIGNATURE_REQUIRED") {
                $("input[name='UPS_ACSRL_ADULT_SIG_REQUIRED']").prop('checked', false);
            }
            if (getName === "UPS_ACSRL_ADULT_SIG_REQUIRED") {
                $("input[name='UPS_ACSRL_SIGNATURE_REQUIRED']").prop('checked', false);
            }
        }
    })
});
