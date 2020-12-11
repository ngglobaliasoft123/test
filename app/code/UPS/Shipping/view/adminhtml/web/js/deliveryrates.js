require([
    "jquery",
    'mage/translate'
], function ($) {
    upsShippingValidate('#deliveryRates', $("#noteMessage").val(), function(error, element) {});
    $(document).on('change', '.select-rate-type', function() {
        serviceRow = $(this).parents('.service-row');
        if ($(this).val() == 'flat_rate') {
            serviceRow.find('div.flat_rate').removeClass('hidden');
            serviceRow.find('div.real_time').addClass('hidden');
            serviceRow.find('div.text_real_time').addClass('hidden');

            serviceRow.find('div.real_time input').prop('disabled', true);
            serviceRow.find('div.flat_rate input').prop('disabled', false);
        } else {
            serviceRow.find('div.flat_rate').addClass('hidden');
            serviceRow.find('div.real_time').removeClass('hidden');
            serviceRow.find('div.text_real_time').removeClass('hidden');

            serviceRow.find('div.real_time input').prop('disabled', false);
            serviceRow.find('div.flat_rate input').prop('disabled', true);
        }
    });

    $(document).on('click', '.flat_rate .titleAdd', function() {
        flat_rate = $(this).parents('.flat_rate');
        rowRate = flat_rate.find('.row').first();
        appendRow = $('<div class="row can-remove">' + rowRate.html() + '</div>');
        appendRow.find('p').remove();
        appendRow.find('.min-value-flat-rate').val(0);
        appendRow.find('.rate-value-flat-rate').val(0);
        appendRow.find('.hidden').removeClass('hidden');
        appendRow.find('.admin__field-error').removeClass('admin__field-error');
        flat_rate.append(appendRow);
    });

    $(document).on('click', '.flat_rate .titleRemove', function() {
        $(this).parents('.can-remove').remove();
    });

    $('#button_save_delivery_rates, #button_next').click(function() {
        $('.min-value-flat-rate').removeClass('admin__field-error');
        $('.rate-value-flat-rate').removeClass('admin__field-error');
        haveDuplicate = false;
        $('.service-row').each(function() {
            if ($(this).find('.select-rate-type').val() == 'flat_rate') {
                arrayMinValue = [];
                flat_rate_row = $(this).find('.flat_rate');
                flat_rate_row.find('.min-value-flat-rate').each(function() {
                    arrayMinValue.push($(this).val());
                });
                for (i = 0; i < arrayMinValue.length; i++) {
                    for (j = i + 1; j<arrayMinValue.length; j++) {
                        if (parseFloat(arrayMinValue[i]) == parseFloat(arrayMinValue[j])) {
                            flat_rate_row.find('.min-value-flat-rate:eq(' + i + ')').addClass('admin__field-error').css({background: 'none'});
                            flat_rate_row.find('.min-value-flat-rate:eq(' + j + ')').addClass('admin__field-error').css({background: 'none'});
                            haveDuplicate = true;
                        }
                    }
                }
            }
        });
        $('#messages').remove();
        if (haveDuplicate) {
            $('#container').prepend('<div id="messages"><div class="messages">'
            +' <div class="message message-error error"><div data-ui-id="messages-message-error">'
            + $("#miniumMessage").val() + '</div></div></div></div>');
            return false;
        }
    });
});
