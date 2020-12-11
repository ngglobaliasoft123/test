require([
    "jquery"
], function($) {
    $(document).ready(function() {
        $("#to_ap_delivery").on("change",function() {
            $('.open-form-ap').slideToggle(500);
        });

        $("#to_address_delivery").on("change",function() {
            $('.open-form-add').slideToggle(500);
        })
        var toapdeliveryvalue = $("#toapdeliveryvalue").val();
        var toaddressdeliveryvalue = $("#toaddressdeliveryvalue").val();
        var selected_country = $("#selected_country").val();
        if (toapdeliveryvalue =="1" || (toapdeliveryvalue =='' && 'us' == selected_country)) {
            $(".open-form-ap").show();
        } else {
            $(".open-form-ap").hide();
        }

        if (toaddressdeliveryvalue == "1" || (toaddressdeliveryvalue =='' && 'us' == selected_country)) {
            $(".open-form-add").show();
        } else {
            $(".open-form-add").hide();
        }
    });
});
