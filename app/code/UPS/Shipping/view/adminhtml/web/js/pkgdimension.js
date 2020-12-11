require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm',
    'UPS_Shipping/js/validate'
], function($, modal, confirm) {
    $(document).ready(function() {
        $('#default_package_radio').click(
            function () {
                if ($(this).is(':checked')) {
                    $('.default-package-option .show-hide').removeClass("hidden");
                    $('.product-dimension-option .show-hide').addClass("hidden");
                    $('#product_dimension input').each(function () {
                        $(this).removeClass("has-error");
                    });
                    $('#backup_rate input').each(function () {
                        $(this).removeClass("has-error");
                    });
                    $('.validate-form-label').hide();
                }
            }
        );

        $('#product_dimension_radio').click(
            function () {
                if ($(this).is(':checked')) {
                    $('.product-dimension-option .show-hide').removeClass("hidden");
                    $('.default-package-option .show-hide').addClass("hidden");
                    $('#default_package input').each(function () {
                        $(this).removeClass("has-error");
                    });
                    $('.validate-form-label').hide();
                }
            }
        );

        $('input[name="package_setting_option"]').each(function () {
            if ($(this).is(':checked')) {
                $(this).closest('div').find('.show-hide').each(function () {
                    $(this).removeClass("hidden");
                })
            } else {
                $(this).closest('div').find('.show-hide').each(function () {
                    $(this).addClass("hidden");
                })
            }
        });

        var packageSettingOption = $("input[name='package_setting_option']:checked").val();

        $("input[name='package_setting_option']").change(function(){
            packageSettingOption = $(this).val();
        });

        $('.add-row').click (function () {
            var divId = $(this).attr('id').replace('add_row_', '');
            addInputRow(divId);
        });

        function addInputRow(divId) {
            var selected_country = $('#countryCode').val();
            if (divId !== 'default_package' && divId !== 'product_dimension' && divId !== 'backup_rate') {
                return;
            }
            // Get row new id
            var rowIds = [];
            var divElm = $('#' + divId);
            var rowIdPattern = divId + '_';
            divElm.find('.input-row').each(function () {
                rowIds.push(parseInt($(this).attr('id').replace(rowIdPattern, '')));
            });
            // Sort array id descending
            rowIds.sort(function(a, b){return b - a});
            var oldId = rowIds[0];
            var newId = oldId + 1;
            // Create new row
            var oldRow = $('#' + rowIdPattern + oldId);
            var newRow = $('<div class="row input-row" id="' + rowIdPattern + newId + '">' + oldRow.html() + '</div>');
            newRow.find('input, select').each(function () {
                var oldName = $(this).attr('name');
                var newName = oldName.replace(oldId.toString(), newId.toString());
                $(this).attr('name', newName);
            });
            newRow.find('input').each(function () {
                $(this).val('');
            });
            newRow.find('select').each(function () {
                if ('us' == selected_country) {
                    $(this).prop("selectedIndex", 1);
                } else {
                    $(this).prop("selectedIndex", 0);
                }
            });
            newRow.find('p').remove();
            newRow.find('.hidden').removeClass('hidden');
            newRow.find('.has-error').removeClass('has-error');
            divElm.append(newRow);
        }

        $('#btn_package_save').click(function () {
            $("input[name='btn_package']").val('save');
            $(".message-error").hide();
            $(".message-success").hide();
            validatePackageDimension();
        });

        $('#btn_package_next').click(function () {
            $("input[name='btn_package']").val('next');
            $(".message-error").hide();
            $(".message-success").hide();
            validatePackageDimension();
        });

        function validatePackageDimension() {
            if (packageSettingOption != 1 && packageSettingOption != 2) {
                return;
            }
            var result = {};
            result.check = true;
            result.arrItems = [];
            result.arrItemNames = [];
            result.message = {
                error:{},
                warning:{}
            };
            if (packageSettingOption == 1) {
                $('#default_package').find('.input-row').each( function () {
                    var packageId = $(this).attr('id');
                    checkPackageDimWeight('default_package', packageId, result);
                });
            } else {
                $('#product_dimension').find('.input-row').each( function () {
                    var packageId = $(this).attr('id');
                    checkPackageDimWeight('product_dimension', packageId, result);
                });
                checkBackupRate(result);
            }
            if (Object.keys(result.message.error).length > 0) {
                var msgError = Object.values(result.message.error).join('<br/>');
                popupValidate.showMessage(msgError);
            } else if (Object.keys(result.message.warning).length > 0) {
                var msgWarning = Object.values(result.message.warning).join('<br/>');
                $("#popup-text-warning").html(msgWarning);
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: $("#warningtitle").val(),
                    buttons: [{
                        text: $("#cancelmessage").val(),
                        class: 'paddingbutton',
                        click: function (event) {
                                this.closeModal(event);
                            }
                        }, {
                        text: $("#okmessage").val(),
                        class: 'primary',
                        click: function (event) {
                            $('#form_package_setting').submit();
                        }
                    }]
                };
                var popup = modal(options, $('#popup-modal'));
                $("#popup-text-warning").html(msgWarning);
                $('#popup-modal').removeClass('hidden').modal('openModal');
            } else {
                $(this).prop('disabled', true);
                popupValidate.showMessage('', false);
                $('#form_package_setting').submit();
            }
        }
    });

    $(document).on('click', '.btn-minus', function () {
            $(this).parents('.input-row').remove();
        }
    );

    $(document).on('change', 'input[type="text"]', function () {
            this.value = $.trim(this.value);
        }
    );

    $(document).on('click', '#fallback_rate input, #fallback_rate select, #product_dimension input, #default_package input', function () {
        $(this).removeClass('has-error has-warning');
    });

    function checkPackageDimWeight(prefix, packageId, result) {
        // Define html element
        var packageId       = '#' + packageId;
        var id              = packageId.replace('#' + prefix + '_', '');
        // Check valid number of item in order
        if (prefix == 'default_package') {
            var numberOfItemElm = "input[name='" + prefix + "[" + id + "][number_of_item]']";
            packageValidation.validateNumberOfItem(numberOfItemElm, result);
        }
        if (prefix == 'product_dimension') {
            var packageNameElm  = "input[name='" + prefix + "[" + id + "][package_name]']";
            packageValidation.validatePackageName(packageNameElm, result);
        }
        // Check valid dimension
        var lengthElm       = "input[name='" + prefix + "[" + id + "][length]']";
        var widthElm        = "input[name='" + prefix + "[" + id + "][width]']";
        var heightElm       = "input[name='" + prefix + "[" + id + "][height]']";
        var dimUnitElm      = "select[name='" + prefix + "[" + id + "][unit_dimension]']";
        packageValidation.validateDimension(lengthElm, widthElm, heightElm, dimUnitElm, result);
        // Check valid weight
        var weightElm       = "input[name='" + prefix + "[" + id + "][weight]']";
        var weightUnitElm   = "select[name='" + prefix + "[" + id + "][unit_weight]']";
        packageValidation.validateWeight(weightElm, weightUnitElm, result);
    }

    function checkBackupRate(result) {
        var gtextError = $("#commonvalidatemessage").val();
        var checkServiceId = true;
        var checkRate = true;
        var serviceIds = [];
        $('#backup_rate').find('select').each(function () {
            var serviceId = $(this).val();
            if (serviceIds.includes(serviceId)) {
                checkServiceId = false;
                $(this).addClass('has-error');
            } else {
                $(this).removeClass('has-error');
            }
            serviceIds.push(serviceId);
        });
        $('#backup_rate').find('input').each(function () {
            checkRate = popupValidate.checkPackageValue($(this).val(), 0, 9999.99, 3);
            if (checkRate == false) {
                $(this).addClass('has-error');
            } else {
                $(this).removeClass('has-error');
            }
        });
        if (checkServiceId === false || checkRate === false) {
            result.check = false;
            result.message.error.gtextError = gtextError;
        } else {
            result.check = true;
        }
    }
});
