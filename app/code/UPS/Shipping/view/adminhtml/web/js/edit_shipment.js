require(
    ["jquery",'Magento_Ui/js/modal/modal'],
    function ($, modal) {
        editShipment = {
            data: [],
            listState: {},
            listAccessorial: [],

            clearContent: function () {
                $('.btnEdit').addClass('primary').removeClass('hidden');
                $('.btnCancelEdit').addClass('hidden');
                $('#createShipTo').removeClass('hidden');
                $('#editShipTo').addClass('hidden');
                $('#shippingserviceCreate').removeClass('hidden');
                $('#accessorialservice').removeClass('hidden');
                $('#editAccessorialService').addClass('hidden');
                $('#editAccessorialService').removeClass('show-inline-grid');
                if (!$('#editShippingServiceToAp').hasClass('hidden')) {
                    $('#editShippingServiceToAp').addClass('hidden');
                }
                if (!$('#editShippingServiceToAdd').hasClass('hidden')) {
                    $('#editShippingServiceToAdd').addClass('hidden');
                }
                //form reset.
                var form = document.getElementById('formCreateShipment');
                if (form) {
                    form.reset();
                }
                this.clearMakedBorderRed();
                //clear state
                if (!$('.showListState').hasClass('hidden')) {
                    $('.showListState').addClass('hidden');
                }
                $('.stateSelected').html('');
            },
            clearMakedBorderRed: function () {
                var formInput = $('#formCreateShipment').find('input.admin__control-text');
                formInput.each(
                    function () {
                        popupValidate.clearBorderRed(this);
                    }
                );
            },
            fillShipToAddressValue: function (orderIdChecked) {
                if (orderIdChecked === undefined) {
                    if (typeof this.data.service_type !== 'undefined' && this.data.service_type == "ADD") {
                        var street = (this.data.street).split('\n');
                        $('.btnCancelEdit').removeClass('hidden');
                        $('#createShipTo').addClass('hidden');
                        $('#editShipTo').removeClass('hidden');
                        $('input[name="edit_name"]').val(this.data.firstname +' '+ this.data.lastname);
                        if (street[0]) {
                            $('input[name="edit_address1"]').val(street[0]);
                        } else {
                            $('input[name="edit_address1"]').val();
                        }
                        if (street[1]) {
                            $('input[name="edit_address2"]').val(street[1]);
                        } else {
                            $('input[name="edit_address2"]').val();
                        }
                        if (street[2]) {
                            $('input[name="edit_address3"]').val(street[2]);
                        } else {
                            $('input[name="edit_address3"]').val();
                        }
                        $('input[name="edit_postcode"]').val(this.data.postcode);
                        $('input[name="edit_city"]').val(this.data.city);
                        $('.countrySelected').val(this.data.CountryCode);
                        $('input[name="edit_phone"]').val(this.data.telephone);
                        $('input[name="edit_email"]').val(this.data.email);
                    }
                } else {
                    if (typeof this.data[orderIdChecked].service_type !== 'undefined'
                        && this.data[orderIdChecked].service_type == "ADD"
                    ) {
                        var orderCountryCode = this.data[orderIdChecked].CountryCode;
                        if (this.data[orderIdChecked].state) {
                            this.showState(orderCountryCode, this.data[orderIdChecked].state);
                        }
                        var street = (this.data[orderIdChecked].street).split('\n');
                        $('.btnCancelEdit').removeClass('hidden');
                        $('#createShipTo').addClass('hidden');
                        $('#editShipTo').removeClass('hidden');
                        var userData = this.data[orderIdChecked].firstname +' ' + this.data[orderIdChecked].lastname;
                        $('input[name="edit_name"]').val(userData);
                        if (street[0]) {
                            $('input[name="edit_address1"]').val(street[0]);
                        } else {
                            $('input[name="edit_address1"]').val();
                        }
                        if (street[1]) {
                            $('input[name="edit_address2"]').val(street[1]);
                        } else {
                            $('input[name="edit_address2"]').val();
                        }
                        if (street[2]) {
                            $('input[name="edit_address3"]').val(street[2]);
                        } else {
                            $('input[name="edit_address3"]').val();
                        }
                        $('input[name="edit_postcode"]').val(this.data[orderIdChecked].postcode);
                        $('input[name="edit_city"]').val(this.data[orderIdChecked].city);
                        $('.countrySelected').val(this.data[orderIdChecked].CountryCode);
                        $('input[name="edit_phone"]').val(this.data[orderIdChecked].telephone);
                        $('input[name="edit_email"]').val(this.data[orderIdChecked].email);
                    }
                }
                $('.btnEdit').removeClass('primary').addClass('hidden');
            },
            checkedShippingService: function (orderIdChecked) {
                if (orderIdChecked === undefined) {
                    $('input[type=radio][id="'+ this.data.shipping_service +'"]').prop('checked', true);
                } else {
                    $('input[type=radio][id="'+ this.data[orderIdChecked].shipping_service +'"]').prop('checked', true);
                }
            },
            checkedAccessorialService: function (orderIdChecked) {
                if (orderIdChecked === undefined) {
                    if (typeof this.data.accessorial_service !== 'undefined') {
                        this.listAccessorial = JSON.parse(this.data.accessorial_service);
                        for (var key in this.listAccessorial) {
                            $('input[value="' + key + '"]').prop('checked', true);
                            if (key == 'UPS_ACSRL_STATURDAY_DELIVERY' && this.data.service_key.includes('SAT_DELI')) {
                                $('input[value="' + key + '"]').attr("disabled", true);
                            }
                        }
                        if (this.data.method === "cashondelivery") {
                            if (this.data.service_type == "ADD") {
                                $('input[value="UPS_ACSRL_TO_HOME_COD"]').prop('checked', true);
                            } else {
                                $('input[value="UPS_ACSRL_ACCESS_POINT_COD"]').prop('checked', true);
                            }
                        }
                    }
                } else {
                    if (typeof this.data[orderIdChecked].accessorial_service !== 'undefined') {
                        this.listAccessorial = JSON.parse(this.data[orderIdChecked].accessorial_service);
                        for (var key in this.listAccessorial) {
                            $('input[value="' + key + '"]').prop('checked', true);
                            if (key == 'UPS_ACSRL_STATURDAY_DELIVERY' && this.data.service_key.includes('SAT_DELI')) {
                                $('input[value="' + key + '"]').attr("disabled", true);
                            }
                        }
                        if (this.data[orderIdChecked].method === "cashondelivery") {
                            if (this.data[orderIdChecked].service_type == "ADD") {
                                $('input[value="UPS_ACSRL_TO_HOME_COD"]').prop('checked', true);
                            } else {
                                $('input[value="UPS_ACSRL_ACCESS_POINT_COD"]').prop('checked', true);
                            }
                        }
                    }
                }
            },
            checkedSignatureAccessorial: function () {
                var signature
                    = $('#editAccessorialService').find('input[type=checkbox][value="UPS_ACSRL_SIGNATURE_REQUIRED"]');
                var adultSignature
                    = $('#editAccessorialService').find('input[type=checkbox][value="UPS_ACSRL_ADULT_SIG_REQUIRED"]');
                $(signature).change(
                    function () {
                        if ($(this).prop("checked")) {
                            adultSignature.prop("checked", false);
                        }
                    }
                );
                $(adultSignature).change(
                    function () {
                        if ($(this).prop("checked")) {
                            signature.prop("checked", false);
                        }
                    }
                );
            },
            showEditShipmentToAddressInfo: function (orderIdChecked) {
                $('#shippingserviceCreate').addClass('hidden');
                $('#accessorialservice').addClass('hidden');
                if (orderIdChecked === undefined) {
                    if (typeof this.data.service_type !== 'undefined' && this.data.service_type === 'AP') {
                        $('#editShippingServiceToAp').removeClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_TO_HOME_COD"]').parent().addClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_ACCESS_POINT_COD"]').parent().removeClass('hidden');
                    } else if (typeof this.data.service_type !== 'undefined' && this.data.service_type === 'ADD') {
                        $('#editShippingServiceToAdd').removeClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_ACCESS_POINT_COD"]').parent().addClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_TO_HOME_COD"]').parent().removeClass('hidden');
                    }
                } else {
                    if (typeof this.data[orderIdChecked].service_type !== 'undefined'
                        && this.data[orderIdChecked].service_type === 'AP'
                    ) {
                        $('#editShippingServiceToAp').removeClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_TO_HOME_COD"]').parent().addClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_ACCESS_POINT_COD"]').parent().removeClass('hidden');
                    } else if (typeof this.data[orderIdChecked].service_type !== 'undefined'
                        && this.data[orderIdChecked].service_type === 'ADD'
                    ) {
                        $('#editShippingServiceToAdd').removeClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_ACCESS_POINT_COD"]').parent().addClass('hidden');
                        $('input[type=checkbox][value="UPS_ACSRL_TO_HOME_COD"]').parent().removeClass('hidden');
                    }
                }
                $('#editAccessorialService').removeClass('hidden');
                $('#editAccessorialService').addClass('show-inline-grid');
            },
            addNewShipTo: function () {
                var shipTo = [];
                var name = $('input[name="edit_name"]').val();
                var state = $('.stateSelected').val();
                var address1 = $('input[name="edit_address1"]').val();
                var address2 = $('input[name="edit_address2"]').val();
                var address3 = $('input[name="edit_address3"]').val();
                var postcode = $('input[name="edit_postcode"]').val();
                var city = $('input[name="edit_city"]').val();
                var country_code = $('.countrySelected').val();
                var email = $('input[name="edit_email"]').val();
                var phone = $('input[name="edit_phone"]').val();
                if (!state) {
                    state = '';
                }
                shipTo.push(name, state, phone, address1, address2, address3, city, postcode, country_code, email);
                return shipTo;
            },
            addNewShippingService: function (orderIdChecked) {
                var shippingService = [];
                if (orderIdChecked) {
                    if (this.data[orderIdChecked].service_type == "ADD") {
                        var checkedShippingService = $('#editShippingServiceToAdd').find('input[type=radio]:checked');
                        var id = checkedShippingService.attr("id");
                        var service_type = checkedShippingService.attr("service-type");
                        var rate_code = checkedShippingService.data("ratecode");
                        var servicename = checkedShippingService.attr("service-name");
                        shippingService.push(service_type, rate_code, id, servicename);
                    } else {
                        var checkedShippingService = $('#editShippingServiceToAp').find('input[type=radio]:checked');
                        var id = checkedShippingService.attr("id");
                        var service_type = checkedShippingService.attr("service-type");
                        var rate_code = checkedShippingService.data("ratecode");
                        var servicename = checkedShippingService.attr("service-name");
                        shippingService.push(service_type, rate_code, id, servicename);
                    }
                } else {
                    if (this.data.service_type == "ADD") {
                        var checkedShippingService = $('#editShippingServiceToAdd').find('input[type=radio]:checked');
                        var id = checkedShippingService.attr("id");
                        var service_type = checkedShippingService.attr("service-type");
                        var rate_code = checkedShippingService.data("ratecode");
                        var servicename = checkedShippingService.attr("service-name");
                        shippingService.push(service_type, rate_code, id, servicename);
                    } else {
                        var checkedShippingService = $('#editShippingServiceToAp').find('input[type=radio]:checked');
                        var id = checkedShippingService.attr("id");
                        var service_type = checkedShippingService.attr("service-type");
                        var rate_code = checkedShippingService.data("ratecode");
                        var servicename = checkedShippingService.attr("service-name");
                        shippingService.push(service_type, rate_code, id, servicename);
                    }
                }
                return shippingService;
            },
            addNewAccessorial: function () {
                var accessorial = {};
                var listCheckedAccessorial = $('#editAccessorialService').find('input[type=checkbox]:checked');
                var accessorialKey = '';
                var accessorialName = '';
                listCheckedAccessorial.each(
                    function () {
                        accessorialKey = $(this).val();
                        accessorialName = $(this).parent().find('label').text();
                        if (accessorialKey !== 'UPS_ACSRL_ACCESS_POINT_COD'
                            && accessorialKey !== 'UPS_ACSRL_TO_HOME_COD'
                        ) {
                            accessorial[accessorialKey] = accessorialName.trim();
                        }
                    }
                );
                return accessorial;
            },
            addNewCOD: function () {
                var apCODChecked
                = $('#editAccessorialService').find('input[type=checkbox][value="UPS_ACSRL_ACCESS_POINT_COD"]').prop("checked");
                var addCODChecked
                = $('#editAccessorialService').find('input[type=checkbox][value="UPS_ACSRL_TO_HOME_COD"]').prop("checked");
                if (apCODChecked || addCODChecked) {
                    return 1;
                } else {
                    return 0;
                }
            },
            editShipmentValidate: function () {
                var checkName = popupValidate.validation('#editName', 'matchAll');
                var checkAddress1 = popupValidate.validation('#editAddress1', 'matchAll');
                var checkPostcode = popupValidate.validation('#editPostcode', 'matchAll');
                var checkCity = popupValidate.validation('#editCity', 'matchAll');
                var checkPhone = popupValidate.validation('#editPhone', 'matchAll');
                var checkEmail = popupValidate.validation('#editEmail', 'emailEdit');
                if (checkName && checkAddress1 && checkPostcode && checkCity && checkPhone && checkEmail) {
                    return true;
                } else {
                    return false;
                }
            },
            validateStatus: function () {
                popupValidate.validateInputStatus('#editName', 'matchAll');
                popupValidate.validateInputStatus('#editAddress1', 'matchAll');
                popupValidate.validateInputStatus('#editPostcode', 'matchAll');
                popupValidate.validateInputStatus('#editCity', 'matchAll');
                popupValidate.validateInputStatus('#editPhone', 'matchAll');
                popupValidate.validateInputStatus('#editEmail', 'emailEdit');
            },
            showState: function (countryCode, stateselected) {
                if ($('.showListState').hasClass('hidden')) {
                    $('.showListState').removeClass('hidden');
                }
                var listStateOfCountry = [];
                if (Array.isArray(this.listState)) {
                    this.listState.map(
                        function (value) {
                            if (value['country_code'] == countryCode) {
                                listStateOfCountry.push({'code': value['state_code'], 'name': value['state_name']});
                            }
                        }
                    );
                }
                showListStateValue = '';
                listStateOfCountry.forEach(
                    function (item) {
                        showListStateValue = showListStateValue + '<option value="'+ item['code'] +'"> '+ item['name'] +'</option>';
                    }
                );
                if (showListStateValue.length == 0) {
                    if (!$('.showListState').hasClass('hidden')) {
                        $('.showListState').addClass('hidden');
                    }
                    $('.stateSelected').html('');
                } else {
                    $('.stateSelected').html(showListStateValue);
                    if (stateselected) {
                        $('.stateSelected').val(stateselected);
                    }
                }
            }
        }
    }
);
