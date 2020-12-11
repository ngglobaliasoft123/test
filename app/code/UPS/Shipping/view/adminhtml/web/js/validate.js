/**
 * _USER_TECHNICAL_AGREEMENT
 *
 * @category  UPS eCommerce Integrations
 * @package   UPS Shipping and UPS Access Point™ : Official Extension for OpenCart
 * @author    United Parcel Service of America, Inc.
 * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved
 * @license   This work is Licensed under the License and Data Service Terms available
 * at: https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf
 * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page
 */

//General Param
require(
    ["jquery"],
    function ($) {
        rulePattern = {
            'alphanum': /^[a-zA-Z0-9]*$/,
            'alpha': /^[a-zA-Z]*$/,
            'name': /^[\D]*$/,
            'number': /^\s*-?\d*(\.\d*)?\s*$/,
            'numberInt': /^\d+$/,
            'numberFloat': /^\d+(\.\d{1,2})?$/,
            'email': /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/,
            'emailEdit': /^[\S]+\@[\S]+\.[\S]{2,3}$/,
            'phone': /^[0|\+][0-9]+/,
            'phoneEdit': /^[0|\+|(0-9)]+[\)|0-9\s\-]+/,
            'postalCode': /[0-9]{2}[\-][0-9]{3}/,
            'postalCodeEdit': /^[0-9]/,
            'accountNumber': /[A-Za-z0-9]{6}/,
            'invoiceNumber': /^[^-\s][\w\s-]+$/,
            'invoiceAmount': /^[0-9]+\.?[0-9]*$/,
            'validateNull': /^\D+$/,
            'address': /^\D+$/,
            'matchAll': /^.+$/,
            'date': /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/,
            'vatNumber': /[A-Za-z0-9]/,
            'promoNumber': /[A-Za-z0-9]/
        };
        var countryCode = $('#countryCode').val();

        var gtextError = $("#commonvalidatemessage").val();
        var textErrorWeight = $("#weightvalidatemessagemax").val();
        var textWarningWeight = $("#messageWeightManagerWarning").val();
        var textWarningWeight = $("#weightwarningmessage").val();
        var textWarningSize = $("#packagwarningmessage").val();
        var textErrorSize = $("#packageerrormessage").val();
        var textWarningSizeExceeds = $("#packagsideuswarning").val();
        var textErrorSizeExceeds = $("#packagesideuserror").val();
        var textErrorSizeNotUs = $("#packageerrormessage").val();
        var textWarningSizeNotUs = $("#packagwarningmessage").val();

        packageValidation = {
            validateNumberOfItem: function(numberOfItemElm, result) {
                $(numberOfItemElm).removeClass('has-error');
                var chkVal = $(numberOfItemElm).val();
                var check = packageValidation.checkEmpty(chkVal);
                var check_duplication = packageValidation.checkDuplication(chkVal, result.arrItems);
                if (check === true) {
                    if(Math.sign(chkVal) !== 1) {
                        check = false
                    }
                }
                if (check === false || check_duplication === false) {
                    result.check = false;
                    result.message.error.gtextError = gtextError;
                    $(numberOfItemElm).addClass('has-error');
                }
            },
            validatePackageName: function(packageNameElm, result) {
                $(packageNameElm).removeClass('has-error');
                var chkVal = $(packageNameElm).val();
                var check = packageValidation.checkEmpty(chkVal);
                var check_duplication = packageValidation.checkDuplication(chkVal, result.arrItemNames);
                if (check === false || check_duplication === false) {
                    result.check = false;
                    result.message.error.gtextError = gtextError;
                    $(packageNameElm).addClass('has-error');
                }
            },
            validateDimension: function(lengthElm, widthElm, heightElm, unitElm, result) {
                // Clear error class
                $(lengthElm).removeClass('has-error');
                $(widthElm).removeClass('has-error');
                $(heightElm).removeClass('has-error');
                // Define validate variable
                var check = true;
                var checkDimensionElm = [lengthElm, widthElm, heightElm];
                // Loop dimension element
                checkDimensionElm.forEach(
                    function (chkElm) {
                        var chkVal = $(chkElm).val();
                        // Check empty
                        check = packageValidation.checkEmpty(chkVal);
                        // Check range and valid number of each item
                        if (check === true) {
                            check = popupValidate.checkPackageValue(chkVal, 0.01, 9999.99, 3);
                        }
                        if (check === false) {
                            $(chkElm).addClass('has-error');
                        }
                    }
                );
                if (check === false) {
                    result.message.error.gtextError = gtextError;
                    result.check = false;
                    return;
                }
                // Define dimension value
                var length = $(lengthElm).val();
                var width = $(widthElm).val();
                var height = $(heightElm).val();
                var unit = $(unitElm).val();
                var tag;
                var checkWarning = true;
                // Check unit is chosen and validate
                switch (unit) {
                    case "cm":
                        var size = parseFloat(length) + 2 * parseFloat(width) + 2 * parseFloat(height);
                        var minSize = 330;
                        var maxSize = 400;
                        if (size > minSize && size <= maxSize) {
                            checkWarning = false;
                            result.message.warning.textWarningSize = textWarningSizeNotUs;
                        } else if (size > maxSize) {
                            check = false;
                            result.message.error.textErrorSize = textErrorSizeNotUs;
                        }
                        break;
                    case "inch":
                        var check = true;
                        var size = parseFloat(length) + 2 * parseFloat(width) + 2 * parseFloat(height);
                        var mediumSize = 129.92;
                        var maxSize = 157.48;
                        var mediumLength = 100;
                        var maxLength = 100;
                        if (countryCode == "us") {
                            mediumSize = 130;
                            maxSize = 165;
                            maxLength = 108;
                            mediumLength = 38;
                        }
                        if (size > mediumSize && size <= maxSize) {
                            checkWarning = false;
                            tag = 'size';
                            result.message.warning.textWarningSize = textWarningSize;
                        } else if (size > maxSize) {
                            check = false;
                            tag = 'size';
                            result.message.error.textErrorSize = textErrorSize;
                        }
                        if (countryCode == "us" && check) {
                            var max_side = packageValidation.getLongestSide(length, width, height);
                            if (max_side.max > mediumLength && max_side.max <= maxLength) {
                                checkWarning = false;
                                tag = max_side.tag;
                                result.message.warning.textWarningSizeExceeds = textWarningSizeExceeds;
                            } else if (max_side.max > maxLength) {
                                check = false;
                                tag = max_side.tag;
                                result.message.error.textErrorSizeExceeds = textErrorSizeExceeds;
                            }
                        }
                        break;
                    default:
                        check = false;
                        result.message.error.gtextError = gtextError;
                        break;
                }

                if (checkWarning === false) {
                    switch (tag) {
                        case "length":
                            $(lengthElm).addClass('has-warning');
                            break;
                        case "width":
                            $(widthElm).addClass('has-warning');
                            break;
                        case "height":
                            $(heightElm).addClass('has-warning');
                            break;
                        default:
                            $(lengthElm).addClass('has-warning');
                            $(widthElm).addClass('has-warning');
                            $(heightElm).addClass('has-warning');
                            break;
                    }
                }

                if (check === false) {
                    result.check = false;
                    switch (tag) {
                        case "length":
                            $(lengthElm).addClass('has-error');
                            break;
                        case "width":
                            $(widthElm).addClass('has-error');
                            break;
                        case "height":
                            $(heightElm).addClass('has-error');
                            break;
                        default:
                            $(lengthElm).addClass('has-error');
                            $(widthElm).addClass('has-error');
                            $(heightElm).addClass('has-error');
                            break;
                    }
                }
            },
            validateWeight: function(weightElm, unitElm, result) {
                // Clear error class
                $(weightElm).removeClass('has-error');
                // Define validate variable
                var weight = $(weightElm).val();
                var unit = $(unitElm).val();
                // Check empty
                var check = packageValidation.checkEmpty(weight);
                var checkWarning = true;
                var mediumWeight = 44.09;
                var maxWeight = 154.32;
                if (countryCode == "us") {
                    mediumWeight = 44;
                    maxWeight = 150;
                }
                // Check range and valid number of each item
                if (check === true) {
                    check = popupValidate.checkPackageValue(weight, 0.01, 9999.99, 3);
                }
                if (check === false) {
                    result.message.error.gtextError = gtextError;
                    result.check = false;
                    $(weightElm).addClass('has-error');
                    return;
                }
                switch (unit) {
                    case "kgs":
                        if (parseFloat(weight) > 20 && parseFloat(weight) <= 70) {
                            checkWarning = false;
                            result.message.warning.textWarningWeight = textWarningWeight;
                        } else if (parseFloat(weight) > 70) {
                            check = false;
                            result.message.error.textErrorWeight = textErrorWeight;
                        }
                        break;
                    case "lbs":
                        if (parseFloat(weight) > mediumWeight && parseFloat(weight) <= maxWeight) {
                            checkWarning = false;
                            result.message.warning.textWarningWeight = textWarningWeight;
                        } else if (parseFloat(weight) > maxWeight) {
                            check = false;
                            result.message.error.textErrorWeight = textErrorWeight;
                        }
                        break;
                    default:
                        check = false;
                        result.message.error.gtextError = gtextError;
                        break;
                }
                if (checkWarning == false) {
                    $(weightElm).addClass('has-warning');
                }
                if (check == false) {
                    result.check = false;
                    $(weightElm).addClass('has-error');
                }
            },
            getLongestSide: function(length, width, height) {
                var result = {};
                result.max = parseFloat(length);
                result.tag = "length";
                if (parseFloat(width) > result.max) {
                    result.max = parseFloat(width);
                    result.tag = "width";
                }
                if (parseFloat(height) > result.max) {
                    result.max = parseFloat(height);
                    result.tag = "width";
                }
                return result;
            },
            checkEmpty: function (chkVal) {
                if (typeof chkVal == 'undefined' ) {
                    return false;
                }
                var check = 0;
                var val = chkVal.trim();
                if (val == '') {
                    check += 1;
                }
                if (check > 0) {
                    return false;
                }
                return true;
            },
            checkDuplication: function (chkVal, arrItem) {
                if (arrItem.indexOf(chkVal) > -1) {
                    return false;
                } else {
                    arrItem.push(chkVal);
                }
                return true;
            }
        };
        popupValidate = {
            makeBorderRed: function (element) {
                if ($(element).hasClass('errorInputField') == false) {
                    $(element).addClass('errorInputField').css({background: "none"});
                }
            },
            clearBorderRed: function (element) {
                if ($(element).hasClass('errorInputField') == true) {
                    $(element).removeClass('errorInputField');
                }
            },
            validation: function (element, pattern) {
                var validation = false;
                var value = $(element).val().trim();
                if (value) {
                    validation = rulePattern[pattern].test(value);
                }
                if (validation) {
                    popupValidate.clearBorderRed(element);
                } else {
                    popupValidate.makeBorderRed(element);
                }
                return validation;
            },
            validateInputStatus: function (element, pattern) {
                $(element).keyup(
                    function () {
                        var value = $(element).val().trim();
                        var name = $(element).attr('name');
                        if (value === '' && (name != 'edit_address2' && name != 'edit_address3')) {
                            popupValidate.makeBorderRed(element);
                        } else {
                            if (rulePattern[pattern].test(value)) {
                                popupValidate.clearBorderRed(element);
                            } else {
                                popupValidate.makeBorderRed(element);
                            }
                        }
                    }
                );
            },
            packageValidationCheck: function (element) {
                var error = 0;
                $(element).find('input').each(
                    function () {
                        var value = $(this).val();
                        if (value === '') {
                            error++;
                            popupValidate.makeBorderRed(this);
                        } else {
                            if (popupValidate.checkPackageValue(value, 0, 9999.99, 3)) {
                                popupValidate.clearBorderRed(this);
                            } else {
                                error++;
                                popupValidate.makeBorderRed(this);
                            }
                        }
                    }
                );
                if (error == 0) {
                    return true;
                } else {
                    return false;
                }
            },
            packageInputStatus: function (element) {
                $(element).find('input').each(
                    function () {
                        $(this).keyup(
                            function () {
                                var value = $(this).val().trim();
                                if (value === '') {
                                    popupValidate.makeBorderRed(this);
                                } else {
                                    if (popupValidate.checkPackageValue(value, 0, 9999.99, 3)) {
                                        popupValidate.clearBorderRed(this);
                                    } else {
                                        popupValidate.makeBorderRed(this);
                                    }
                                }
                            }
                        );
                    }
                );
            },
            showMessage: function (msg, flg = true) {
                if (flg === true) {
                    $('#error-notice').show();
                    $('#error-notice').html('<div id="messages"><div class="messages"><div class="message message-error error"><div data-ui-id="messages-message-error">' + msg + '</div></div></div></div>');
                } else {
                    $('#error-notice').hide();
                }
            },
            checkPackageValue: function (value, min, max, length) {
                var strRegex = rulePattern['number'];
                if (strRegex.test(value)) {
                    if (value.indexOf('.') == -1) {
                        if (parseFloat(value) >= min && parseFloat(value) <= max) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        if ((value.substring(value.indexOf('.')).length > length)) {
                            return false;
                        } else {
                            if (parseFloat(value) >= min && parseFloat(value) <= max) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        };
        window.upsShippingValidate = function (formId, message, callback) {
            clearMessage = function () {
                $('#messages').remove();
                $('#container').find('#messages').remove();
            };
            showMessage = function (type) {
                clearMessage();
                $('#container').prepend('<div id="messages"><div class="messages"><div class="message message-' + type + ' ' + type + '"><div data-ui-id="messages-message-' + type + '">' + message + '</div></div></div></div>');
            };

            makeBorderRed = function (element) {
                element.addClass('admin__field-error').css({background: "none"});
            };

            clearBorderRed = function (element) {
                element.removeClass('admin__field-error');
            }

            clearBorderRedAll = function () {
                $('.admin__field-error').removeClass('admin__field-error');
            }

            $(formId).submit(
                function () {
                    error = 0;
                    errorElement = [];
                    clearBorderRedAll();
                    $(formId).find('input, select, textarea').each(
                        function () {
                            element = $(this);
                            dataValidate = element.attr('data-validate');
                            idElement = element.attr('id');
                            if (typeof dataValidate !== 'undefined' && !element.prop('disabled')) {
                                dataValidate = dataValidate.replace(/'/g, '"');
                                objectValidate = JSON.parse(dataValidate);
                                $.each(
                                    objectValidate,
                                    function (key, value) {
                                        v = element.val();
                                        if (v.trim().length == 0) {
                                            error++;
                                            makeBorderRed(element);
                                            errorElement.push(idElement);
                                        }
                                        switch(key) {
                                        case "required":
                                            if (typeof value === 'boolean' && value == true && v == '') {
                                                error++;
                                                makeBorderRed(element);
                                                errorElement.push(idElement);
                                            }
                                            break;
                                        case "validate-pattern":
                                            if (typeof value === 'string' && value != '') {
                                                re = new RegExp(value);
                                                if (!re.test(v)) {
                                                    error++;
                                                    makeBorderRed(element);
                                                    errorElement.push(idElement);
                                                }
                                            }
                                            break;
                                        case "validate-rule":
                                            if (typeof value === 'string' && value != '') {
                                                if (!rulePattern[value].test(v)) {
                                                    error++;
                                                    makeBorderRed(element);
                                                    errorElement.push(idElement);
                                                }
                                            }
                                            break;
                                        case "required-if-specified":
                                            if (typeof value === 'string' && value != '') {
                                                if ($(value).val().length > 0) {
                                                    error++;
                                                    makeBorderRed(element);
                                                    errorElement.push(idElement);
                                                }
                                            }
                                            break;
                                        case "validate-min-max":
                                            if (typeof value === 'string' && value != '') {
                                                numberMinMax = value.split("-");
                                                v = parseFloat(v);
                                                if (v > numberMinMax[1] || v < numberMinMax[0]) {
                                                    error++;
                                                    makeBorderRed(element);
                                                    errorElement.push(idElement);
                                                }
                                            }
                                            break;
                                        }
                                    }
                                );
                            }
                        }
                    );
                    if (typeof callback === 'function') {
                        onlyUnique = function (value, index, self) {
                            return self.indexOf(value) === index;
                        }
                        callback(error, errorElement.filter(onlyUnique));
                    }
                    if (!error) {
                        clearMessage();
                        return true;
                    }
                    showMessage('error');
                    return false;
                }
            );
        }
    }
);
