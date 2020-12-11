require([
    "jquery"
    ], function($) {
        clearOpenAccountMessage = function () {
            $('#messages').remove();
            $('#container').find('#messages').remove();
        };
        showOpenAccountMessage = function (type, message) {
            clearOpenAccountMessage();
            $('#container').prepend('<div id="messages"><div class="messages"><div class="message message-' + type + ' ' + type + '"><div data-ui-id="messages-message-' + type + '">' + message + '</div></div></div></div>');
        };
        var errorOpenAccountMessage = $("#errorMessage").val();
        if (errorOpenAccountMessage != '') {
            showOpenAccountMessage('error', errorOpenAccountMessage);
        } else {
            var successOpenAccountMessage = $("#successMessage").val();
            if (successOpenAccountMessage !='') {
                showOpenAccountMessage('success', successOpenAccountMessage);
            }
        }

        $('#addAccount').click(function() {
            $('#addAccountSuccess').slideToggle(500);
            if ($(".button-save-account").hasClass("hidden")) {
                $('.button-save-account').removeClass("hidden");
            } else {
                $('.button-save-account').addClass("hidden");
            }
        });
        var invoicedatevalue = $("#accountnamevalue").val();
        if (invoicedatevalue != "") {
            $('#addAccountSuccess').slideToggle(500);
        }

        $("#nextPage").on("click",function() {
            window.location = $("#urlshippingservice").val();
        })

        $(document).ready(function() {
            $('#accountWith90Days').click(function() {
                if ($(this).is(':checked')) {
                    $( ".accountWith90Days_show" ).removeClass( "hidden" );
                    $( ".accountWithOut90Days_show" ).addClass( "hidden" );

                    $('.accountWithOut90Days_show input').removeAttr('data-validate');
                    $('.accountWith90Days_show input').each(function() {
                        $(this).attr('data-validate', $(this).attr('data-validate-hidden'));
                    });
                }
            });
            $('#accountWithOut90Days').click(function() {
                if ($(this).is(':checked')) {
                    $( ".accountWithOut90Days_show" ).removeClass( "hidden" );
                    $( ".accountWith90Days_show" ).addClass( "hidden" );

                    $('.accountWith90Days_show input').removeAttr('data-validate');
                    $('.accountWithOut90Days_show input').each(function() {
                        $(this).attr('data-validate', $(this).attr('data-validate-hidden'));
                    });
                }
            });
        });
    });

    require([
        "jquery",
        'Magento_Ui/js/modal/confirm',
        "mage/calendar"
    ], function($, confirm) {
        $('#datepicker').datepicker().datepicker("setDate", new Date()).attr('readonly','readonly');
        $('#datepicker').each(function() {
            var currentValue = $(this).val();
            if (currentValue)
                $(this).datepicker('setDate',new Date(currentValue)).attr('readonly','readonly');

        });
        var invoicedatevalue = $("#invoicedatevalue").val();
        if (invoicedatevalue != "") {
            $('#datepicker').datepicker('setDate', invoicedatevalue).attr('readonly','readonly');
        }

        $('#datepicker').keydown(function(event) {
            if (event.keyCode != 8) {
                event.preventDefault();
            }
            document.getElementById("datepicker").style.display="initial";
        });

        $('.removeAccount').click(function() {
            var account_id = $(this).attr('data-id');
            confirm({
                title: $("#removeaccountmessage").val(),
                content: $("#confirmaccountsuccessmassage").val(),
                buttons: [{
                    text: $("#cancelaccountsuccessmassage").val(),
                    click: function (event) {
                        this.closeModal(event);
                    }
                    }, {
                    text: $("#okMessage").val(),
                    class: 'primary',
                    click: function (event) {
                        $.ajax({
                        showLoader: true,
                        url: $("#urlsaveaccountsuccess").val(),
                        data: 'ajax=1&method=deleteAccount&account_id='+account_id,
                        type: "POST",
                        dataType: 'json'
                    }).done(function (data) {
                        location.reload();
                    });
                    }
                }]
            });
        });
    });

    require([
        "jquery",
        'mage/translate'
    ], function ($) {
        upsShippingValidate('#form-accountsuccess', $("#accountsuccessvalidatemessage").val(), function(error, element) {});
    });
