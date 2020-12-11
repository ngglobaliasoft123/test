require([
    "jquery"
    ], function($) {
        $(document).ready(function() {
            // Basic configuration for IOvation
            var io_bbout_element_id = 'ioBlackBox';
            var io_install_stm = false;
            var io_exclude_stm = 12;
            var io_install_flash = false;
            var io_enable_rip = true;

            var deviceScriptUrl = 'https://mpsnare.iesnare.com/snare.js';
            var script = document.createElement("script");
            script.setAttribute('defer', '');
            script.setAttribute('async', '');
            script.setAttribute("type", "text/javascript");
            script.setAttribute("src", deviceScriptUrl);
            document.body.appendChild(script);

            $('.required-entry').addClass('select select admin__control-select');
            $('#accountWith90Days').click(function() {
                if ($(this).is(':checked')) {
                    if ($( ".accountWith90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWith90Days_show" ).removeClass( "hidden" );
                    }
                    if (!$( ".accountWithOut90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOut90Days_show" ).addClass( "hidden" );
                    }
                    if (!$( ".accountWithOutNumber_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOutNumber_show" ).addClass( "hidden" );
                    }

                    $('.accountWithOut90Days_show input').removeAttr('data-validate');
                    $('.accountWith90Days_show input').each(function() {
                        $(this).attr('data-validate', $(this).attr('data-validate-hidden'));
                    });
                }
            });
            var optradiovalue = $("#optradiovalue").val();
                if (optradiovalue == 1) {
                    if ($( ".accountWith90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWith90Days_show" ).removeClass( "hidden" );
                    }
                    if (!$( ".accountWithOut90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOut90Days_show" ).addClass( "hidden" );
                    }
                    if (!$( ".accountWithOutNumber_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOutNumber_show" ).addClass( "hidden" );
                    }
                } else if (optradiovalue == 2) {
                    if ($( ".accountWithOut90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOut90Days_show" ).removeClass( "hidden" );
                    }
                    if (!$( ".accountWith90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWith90Days_show" ).addClass( "hidden" );
                    }
                    if (!$( ".accountWithOutNumber_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOutNumber_show" ).addClass( "hidden" );
                    }
                } else {
                    if ($( ".accountWithOutNumber_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOutNumber_show" ).removeClass( "hidden" );
                    }
                    if (!$( ".accountWith90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWith90Days_show" ).addClass( "hidden" );
                    }
                    if (!$( ".accountWithOut90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOut90Days_show" ).addClass( "hidden" );
                    }
                }

            $('#accountWithOut90Days').click(function() {
                if ($(this).is(':checked')) {
                    if ($( ".accountWithOut90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOut90Days_show" ).removeClass( "hidden" );
                    }
                    if (!$( ".accountWith90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWith90Days_show" ).addClass( "hidden" );
                    }
                    if (!$( ".accountWithOutNumber_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOutNumber_show" ).addClass( "hidden" );
                    }

                    $('.accountWith90Days_show input').removeAttr('data-validate');
                    $('.accountWithOut90Days_show input').each(function() {
                        $(this).attr('data-validate', $(this).attr('data-validate-hidden'));
                    });
                }
            });

            $('#notAccount').click(function() {
                if ($(this).is(':checked')) {
                    if ($( ".accountWithOutNumber_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOutNumber_show" ).removeClass( "hidden" );
                    }
                    if (!$( ".accountWith90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWith90Days_show" ).addClass( "hidden" );
                    }
                    if (!$( ".accountWithOut90Days_show" ).hasClass( "hidden" )) {
                        $( ".accountWithOut90Days_show" ).addClass( "hidden" );
                    }

                    $('.accountWith90Days_show input').removeAttr('data-validate');
                    $('.accountWithOut90Days_show input').removeAttr('data-validate');
                }
            });
        });
    });

require([
    "jquery",
    "mage/calendar"
], function($) {
    $('#datepicker').datepicker().datepicker("setDate", new Date());

    $('#datepicker').each(function() {
        var currentValue = $(this).val();
        if (currentValue)
            $(this).datepicker('setDate', new Date(currentValue));

    });
    var invoiceDateValue = $("#invoicedatevalue").val();
    if (invoiceDateValue != "") {
        $('#datepicker').datepicker('setDate', invoiceDateValue);
    }
    $('#datepicker').keydown(function(event) {
        if (event.keyCode != 8) {
            event.preventDefault();
        }
        document.getElementById("datepicker").style.display="initial";
    });
});

require([
    "jquery",
    'mage/translate'
], function($) {
    upsShippingValidate('#form-account', $("#accountvalidatemessage").val(), function(error, element) {});
});

// Basic configuration for IOvation
var io_bbout_element_id = 'ioBlackBox';
var io_install_stm = false;
var io_exclude_stm = 12;
var io_install_flash = false;
var io_enable_rip = true;

var deviceScriptUrl = 'https://mpsnare.iesnare.com/snare.js';
var script = document.createElement("script");
script.setAttribute('defer', '');
script.setAttribute('async', '');
script.setAttribute("type", "text/javascript");
script.setAttribute("src", deviceScriptUrl);
document.body.appendChild(script);
