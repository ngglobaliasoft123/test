require([
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'mage/translate',
        'mage/url',
    ], function ($,
            quote,
            checkoutData,
            rateRegistry,
            customerAddressProcessor,
            newAddressProcessor,
            $t,
            urlBuilder
    ) {
        var pathImage = $("#imageLinkString").val();
        var pathAddressImage = $("#addressImageString").val();
        var map, searchManager, indexMap = [];
        var shippingServicesHtml = '<tr class="service_option_ups">' +
            '<td colspan="4" style="width: unset;">' +
                '<div class="accessPointBlock">' +
                    '<h4><p><img class="logoCss" src="'+$("#upsLogoString").val()+'" width="100" />'
                    +'<strong>'+$("#anAccessPointProcess").val()+'</strong></p></h4>'
                    + '<p>'+$("#deliveredAccessPointProcess").val()+'</p>'
                    + '<br /><div id="listServiceUPSAP"></div>'
                    + '<div class="searchAP">' +
                        '<h4>'+$("#searchAccessPointProcess").val()+'</h4>' +
                        '<p class="hiddenMessage overHidden"><span class="floatNear">'
                        + $("#nearTitle").val() + ':</span> <a href="javascript:void(0);" class="useAddress">'
                        + $("#mydeliveryaddress").val() + '</a></p>' +
                        '<div>' +
                            '<div class="overHidden">' +
                                '<div class="searchAddress"><input type="text" id="searchAddress" maxlength="250"'
                                +' placeholder="' + $("#searchPlaceholder").val() + '"></div>'
                                + '<div class="divSearch"><button type="button" class="searchMap">'
                                + $("#searchTitle").val() + '</button></div>' +
                            '</div>' +
                            '<div class="overHidden">' +
                                '<div class="countryAddress"><input type="text" id="countryAddress" '
                                + ' value="" readonly></div>' +
                            '</div>' +
                        '</div>' +
                        '<br>' +
                        '<div class="bingMap">' +
                            '<div id="upsMap" style="height:400px"></div>' +
                            '<h4 class="results">'+$("#resultsTitle").val()+'</h4>' +
                            '<div id="selectAddress"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="addressBlock">' +
                    '<h4><p><img class="logoAddress" src="'+$("#upsLogoString").val()+'" width="100" />'
                    + '<trong>'+$("#deliveraddresstitle").val()+'</strong></p></h4>'
                    + '<p>'+$("#deliveredaddress").val()+'</p><br />'
                    + '<div id="listServiceUPSADD"></div>' +
                '</div>' +
                '<input type="hidden" id="selectedAPAddress" value="">' +
                '<input type="hidden" id="selectAddressinFor" value="">' +
                '<input type="hidden" id="selectedShippingServiceId" value="">' +
            '</td>' +
        '</tr>';
        var shippingServicesHtmlUS = '<tr class="service_option_ups">' +
            '<td colspan="4" style="width: unset;">' +
                '<div class="addressBlock">' +
                    '<h4><p><img class="logoAddress" src="'+$("#upsLogoString").val()+'" width="100" />'
                    + '<trong>'+$("#deliveraddresstitle").val()+'</strong></p></h4>'
                    + '<p>'+$("#deliveredaddress").val()+'</p><br />'
                    + '<div id="listServiceUPSADD"></div>' +
                '</div><br />' +
                '<div class="accessPointBlock">' +
                    '<h4><p><img class="logoCss" src="'+$("#upsLogoString").val()+'" width="100" />'
                    +'<strong>'+$("#anAccessPointProcess").val()+'</strong></p></h4>'
                    + '<p>'+$("#deliveredAccessPointProcess").val()+'</p>'
                    + '<br /><div id="listServiceUPSAP"></div>'
                    + '<div class="searchAP">' +
                        '<h4>'+$("#searchAccessPointProcess").val()+'</h4>' +
                        '<p class="hiddenMessage overHidden"><span class="floatNear">'
                        + $("#nearTitle").val() + ':</span> <a href="javascript:void(0);" class="useAddress">'
                        + $("#mydeliveryaddress").val() + '</a></p>' +
                        '<div>' +
                            '<div class="overHidden">' +
                                '<div class="searchAddress"><input type="text" id="searchAddress" maxlength="250"'
                                +' placeholder="' + $("#searchPlaceholder").val() + '"></div>'
                                + '<div class="divSearch"><button type="button" class="searchMap">'
                                + $("#searchTitle").val() + '</button></div>' +
                            '</div>' +
                            '<div class="overHidden">' +
                                '<div class="countryAddress"><input type="text" id="countryAddress" '
                                + ' value="" readonly></div>' +
                            '</div>' +
                        '</div>' +
                        '<br>' +
                        '<div class="bingMap">' +
                            '<div id="upsMap" style="height:400px"></div>' +
                            '<h4 class="results">'+$("#resultsTitle").val()+'</h4>' +
                            '<div id="selectAddress"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<input type="hidden" id="selectedAPAddress" value="">' +
                '<input type="hidden" id="selectAddressinFor" value="">' +
                '<input type="hidden" id="selectedShippingServiceId" value="">' +
            '</td>' +
        '</tr>';
        var selectedCountry = $("#selectedCountry").val();
        if ('us' == selectedCountry) {
            shippingServicesHtml = shippingServicesHtmlUS;
        }
        var upsServiceOption = {
            tableClass: '.table-checkout-shipping-method',
            methodId: $("#ordershippingmethod").val(),
            flagShippingFinish: $("#getFlagShippingFinishList").val(),
            identify: 'service_option_ups',
            mapHtml: '',
            selectedServiceId: 0,
            selectedCountryName: '',
            serviceUrl: urlBuilder.build('upsshipping/eshopper/shippingService'),
            appendHtml: shippingServicesHtml,
            messageNote: '',
            clearSession: '0',
            shippingServiceType: '',
            backendCountryCode: $("#backendCountryCodeKey").val(),
            getAddressFull: function() {
                var addressObject = {};
                // get address for locator api
                var addressForm = checkoutData.getShippingAddressFromData();
                var address = quote.shippingAddress();
                var newfullAddress = '';
                var regionAddress = '', firstnameAddress = '' , lastnameAddress = '', address1Address = '', address2Address = '', address3Address = '', cityAddress = '',
                postcodeAddress = '', stateCodeAddress = '', fullAddressAddress = '', countryCodeAddress = '', phoneNumberAddress = '';
                var firstnameForm = '', lastnameForm = '', address1Form = '', address2Form = '', address3Form = '', cityForm = '', regionForm = '',
                postcodeForm = '', stateCodeForm = '', fullAddressForm = '', countryCodeForm = '', phoneNumberForm = '', regionAddress = '';

                if (address) {
                    firstnameAddress = address.firstname;
                    lastnameAddress = address.lastname;
                    if (address.street) {
                        address1Address = address.street[0];
                        fullAddressAddress = address.street[0];
                        for (var i = 1; i < address.street.length; i++) {
                            var address1 = address.street[i];
                            if (i == 1)
                                address2Address = address1;
                            else
                                address3Address = address1;
                            fullAddressAddress += (address1) ? ' ' + address1: '';
                        }
                    }
                    cityAddress = (address.city) ? address.city : '';
                    postcodeAddress = (address.postcode) ? address.postcode : '';
                    regionAddress = (address.region) ? address.region : '';
                    stateCodeAddress = (address.regionId)? address.regionId : '';
                    countryCodeAddress = (address.countryId) ? address.countryId : '';
                    phoneNumberAddress = (address.telephone) ? address.telephone : '';
                }

                if (addressForm) {
                    firstnameForm = addressForm.firstname;
                    lastnameForm = addressForm.lastname;
                    if (addressForm.street) {
                        address1Form = addressForm.street[0];
                        fullAddressForm = addressForm.street[0];
                        for (var i = 1; i < addressForm.street.length; i++) {
                            var address1 = addressForm.street[i];
                            if (i == 1)
                                address2Form = address1;
                            else
                                address3Form = address1;
                            fullAddressForm += (address1) ? ' ' + address1 : '';
                        }
                    }
                    cityForm = (addressForm.city) ? addressForm.city : '';
                    postcodeForm = (addressForm.postcode) ? addressForm.postcode : '';
                    regionForm = (addressForm.region) ? addressForm.region : '';
                    stateCodeForm = (addressForm.region_id) ? addressForm.region_id : '';
                    countryCodeForm = (addressForm.country_id) ? addressForm.country_id : '';
                    phoneNumberForm = (addressForm.telephone) ? addressForm.telephone : '';
                }

                newfullAddress += ('' != fullAddressForm) ? fullAddressForm : fullAddressAddress;
                newfullAddress += ('' != newfullAddress) ? ', ' : '';
                newfullAddress += ('' != cityAddress) ? cityAddress : cityForm;
                newfullAddress += ('' != newfullAddress) ? ', ' : '';
                newfullAddress += ('' != regionAddress) ? regionAddress : regionForm;
                newfullAddress += ('' != newfullAddress) ? ', ' : '';
                newfullAddress += ('' != postcodeAddress) ? postcodeAddress : postcodeForm;

                addressObject.firstname = ('' != firstnameAddress) ? firstnameAddress : firstnameForm;
                addressObject.lastname = ('' != lastnameAddress) ? lastnameAddress : lastnameForm;
                addressObject.address1 = ('' != address1Address) ? address1Address : cityForm;
                addressObject.address2 = ('' != address2Address) ? address2Address : address2Form;
                addressObject.address3 = ('' != address3Address) ? address3Address : address3Form;
                addressObject.city = ('' != cityAddress) ? cityAddress : address1Form;
                addressObject.postcode = ('' != postcodeAddress) ? postcodeAddress : postcodeForm;
                addressObject.stateCode = ('' != stateCodeAddress) ? stateCodeAddress : stateCodeForm;
                addressObject.fullAddress = newfullAddress;
                addressObject.countryCode = ('' != countryCodeAddress) ? countryCodeAddress : countryCodeForm;
                addressObject.telephone = ('' != phoneNumberAddress) ? phoneNumberAddress : phoneNumberForm;
                return addressObject;
            },
            showShippingService: function() {
                var addressFull = upsServiceOption.getAddressFull();
                var checkStateCountry = true;
                if (addressFull.countryCode == 'US' && addressFull.stateCode == '') {
                    checkStateCountry = false;
                }
                if (addressFull.postcode && checkStateCountry && addressFull.countryCode) {
                    $.ajax({
                        showLoader: true,
                        type: 'POST',
                        url: upsServiceOption.serviceUrl,
                        data: {
                            method: 'loadDefault',
                            countryCode: addressFull.countryCode,
                            clearSession: upsServiceOption.clearSession
                        },
                        dataType: 'json',
                        success: function(resp, textStatus, jqXHR) {
                            if (resp && upsServiceOption.flagShippingFinish == '1') {
                                var checkedString =  '';
                                    // list access point services
                                    if (resp['serviceAP'] && resp['serviceAP'].length !== 0) {
                                    var listServiceAPHtml ='';
                                    $.each(resp['serviceAP'], function( index, value ) {
                                        checkedString = '';
                                        if (resp['selectedService'] == value.id) {
                                            checkedString =  'checked=""';
                                        }
                                        listServiceAPHtml+=upsServiceOption.getFreeValueHtml(value, checkedString);
                                    });
                                    $("#listServiceUPSAP").html(listServiceAPHtml);
                                }
                                // list address services
                                if (resp['serviceADD'] && resp['serviceADD'].length !== 0) {
                                    var listServiceADDHtml = '';
                                    $.each(resp['serviceADD'], function( index, value ) {
                                        checkedString = '';
                                        if (resp['selectedService'] == value.id) {
                                            checkedString =  'checked=""';
                                        }
                                        listServiceADDHtml+=upsServiceOption.getFreeValueHtml(value, checkedString);
                                    });
                                    $("#listServiceUPSADD").html(listServiceADDHtml);
                                    if (!$(".messageNoticeNoService").hasClass("d-none")) {
                                        $(".messageNoticeNoService").addClass("d-none");
                                    }
                                    $(".addressBlock").show();

                                } else {
                                    $(".addressBlock").hide();
                                }
                                // set selected Address null when loading
                                $("#selectedAPAddress").val('');
                                // filter shipping services on AP
                                if (resp['serviceAP'] && resp['serviceAP'].length == 0) {
                                    $(".accessPointBlock").hide();
                                    if (!$('.messageNoticeNoService').length) {
                                        $("#shipping-method-buttons-container").before('<div role="alert"'
                                        +' class="message notice messageNoticeNoService d-none">'
                                        +' <span id="showMessageNoteNoService"></span></div>');
                                    }
                                    var messageNoService = $("#aboveaddress").val();
                                    $("#showMessageNoteNoService").html(messageNoService);
                                    if ($(".messageNoticeNoService").hasClass("d-none")) {
                                        $(".messageNoticeNoService").removeClass("d-none");
                                    }
                                    if ($(".messageNoticeNoService").length) {
                                        $('html, body').animate({
                                            scrollTop: $("#shipping-method-buttons-container").offset().top
                                        }, 200);
                                    }
                                } else {
                                    if (resp['selectedServiceType'] != 'AP') {
                                        $(".searchAP").hide();
                                    } else {
                                        upsServiceOption.shippingServiceType = 'AP';
                                        $(".searchAP").show();
                                    }
                                    $(".accessPointBlock").show();
                                }
                                // hide UPS Shipping Service
                                if ((resp['serviceADD'] && resp['serviceADD'].length != 0
                                && resp['enableServiceADD'] == '1') || (resp['serviceAP']
                                && resp['serviceAP'].length != 0 && resp['enableServiceAP'] == '1')) {
                                    upsServiceOption.shippingServiceType = resp['selectedServiceType'];
                                    $('#label_method_upsshipping_upsshipping').parent().show();
                                    if (!$(".messageNoticeNoService").hasClass("d-none")) {
                                        $(".messageNoticeNoService").addClass("d-none");
                                    }
                                } else {
                                    $('#label_method_upsshipping_upsshipping').parent().hide();
                                    if (!$(".messageNoticeNoService").hasClass("d-none")) {
                                        $(".messageNoticeNoService").addClass("d-none");
                                    }
                                }
                                upsServiceOption.selectedCountryName = resp['countryName'];
                                $("#countryAddress").val(resp['countryName']);
                                // results
                                if (!$( ".results" ).hasClass("d-none")) {
                                    $( ".results" ).addClass("d-none");
                                }
                                upsServiceOption.show();
                                if (resp['serviceAP'] && (resp['serviceAP'].length == 0
                                || resp['enableServiceAP'] == '0')) {
                                    upsServiceOption.shippingServiceType = 'ADD';
                                }
                                if (resp['selectedService'] != 0 && $("#radio_"
                                + resp['selectedService']).val() == '') {
                                    $("#radio_" + resp['selectedService']).trigger("click");
                                }
                            } else {
                                $('#label_method_upsshipping_upsshipping').parent().hide();
                            }
                            // add Logo
                            if (!$( "#label_method_upsshipping_upsshipping" ).find('img').length) {
                                $('#label_method_upsshipping_upsshipping').prepend('<img class="logoUPSCss" '
                                + 'src="'+$("#upsLogoString").val()+'" width="32" />');
                            }
                        }
                    });
                }
            },
            getFreeValueHtml: function(value, checkedString) {
                var serviceString = '';
                if (value.service_name == 'UPS Access Point Economy') {
                    serviceString = 'UPS Access Point' + '™' + ' Economy';
                } else if (value.service_name == 'UPS Standard') {
                    serviceString = 'UPS' + '®' + ' Standard';
                } else if (value.service_name == 'UPS Express 12:00') {
                    serviceString = 'UPS Express 12:00';
                } else if (value.service_name == 'UPS Ground') {
                    serviceString = 'UPS' + '®' + ' Ground';
                } else if (value.service_name == 'UPS Next Day Air Early') {
                    serviceString = 'UPS Next Day Air' + '®' + ' Early';
                } else if (value.service_name == 'UPS Standard - Saturday Delivery') {
                    serviceString = 'UPS' + '®' + ' Standard - Saturday Delivery';
                } else if (value.service_name == 'UPS Express - Saturday Delivery') {
                    serviceString = 'UPS Express' + '®' + ' - Saturday Delivery';
                } else {
                    serviceString = value.service_name + '®';
                }
                return '<div class="full-width one-row">'
                        + '<input type="radio" name="serviceups" id="radio_'
                        + value.id + '" value="' + value.id + '"' + 'service-key="' + value.service_key + '"' + checkedString + '>'
                        + '<div class="row full-95-width one-row">'
                        +    '<div class="one-row full-50-width">'
                        +     '<span><strong>'
                        +        serviceString
                        +     '</strong></span></div>'
                        +    '<div class="full-45-width one-row col-right">'
                        +     '<span><strong>' + value.splitShippingFee
                        +     '</strong></span></div>'
                        + '</div><div class="row margin-date">'
                        +    value.shippingFeeString
                        + '</div>'
                        + '</div>'
            },
            append: function() {
                if (!upsServiceOption.checkExist()) {
                    var addressForm = checkoutData.getShippingAddressFromData();
                    var address = quote.shippingAddress();
                    if (address || addressForm) {
                        $("input[value='upsshipping_upsshipping']").trigger("click");
                        if ($("input[value='upsshipping_upsshipping']:checked").length === 1) {
                            $('#label_method_upsshipping_upsshipping').parent().after(upsServiceOption.appendHtml);
                            upsServiceOption.getMap();
                            upsServiceOption.hide();
                            $(upsServiceOption.tableClass + ' tr').each(function() {
                                if (!$(this).hasClass(upsServiceOption.identify)) {
                                    input = $(this).find('input');
                                    if (!$('.messageNotice').length) {
                                        $(".hiddenMessage").after('<div role="alert"'
                                        +' class="message notice messageNotice d-none">'
                                        +'<span id="showMessageNote"></span></div>');
                                    }
                                    if (input.val() == upsServiceOption.methodId
                                    && input.attr('checked') == 'checked') {
                                        upsServiceOption.showShippingService();
                                    }
                                }
                            });
                        }
                    }
                }
            },
            checkExist: function() {
                return $(upsServiceOption.tableClass).find('.'+upsServiceOption.identify).length;
            },
            isSelected: function() {
                if ($(upsServiceOption.tableClass).find('.'+upsServiceOption.identify).css('display') != 'none') {
                    return true;
                }
                return false;
            },
            validate: function() {
                upsServiceOption.removeMotice();
                if (upsServiceOption.isSelected()) {
                    if ($(upsServiceOption.tableClass+' .'
                    +upsServiceOption.identify).find('input[name=serviceups]:checked').length == 0) {
                        upsServiceOption.messageNote = $("#specifyservicemethod").val();
                        upsServiceOption.addNotice();
                        return false;
                    }
                }
                return true;
            },
            addNotice: function() {
                $("#showMessageNote").html(upsServiceOption.messageNote);
                if ($(".messageNotice").hasClass("d-none")) {
                    $(".messageNotice").removeClass("d-none");
                }
                if ($(".hiddenMessage").length) {
                    $('html, body').animate({
                        scrollTop: $(".hiddenMessage").offset().top
                    }, 200);
                }
            },
            removeMotice: function() {
                $("#showMessageNote").html();
                if (!$(".messageNotice").hasClass("d-none")) {
                    $(".messageNotice").addClass("d-none");
                }
            },
            show: function() {
                $(upsServiceOption.tableClass).find('.' + upsServiceOption.identify).show();
            },
            hide: function() {
                $(upsServiceOption.tableClass).find('.' + upsServiceOption.identify).hide();
            },
            toggle: function(_this) {
                if (!$(_this).hasClass(upsServiceOption.identify)) {
                    if ($(_this).find('input').val() == upsServiceOption.methodId) {
                        upsServiceOption.showShippingService();
                    } else {
                        if (typeof $(_this).find('input').val() !== 'undefined') {
                            upsServiceOption.hide();
                        }
                    }
                }
            },
            search: function() {
                var addressFull = upsServiceOption.getAddressFull();
                var searchAddress = $("#searchAddress").val();
                searchAddress = searchAddress.trim();
                var selectedService =  $(upsServiceOption.tableClass+' .'+upsServiceOption.identify).find('input[name=serviceups]:checked');
                var selectedServiceId = '';
                if (selectedService.val() !== 'undefined') {
                    selectedServiceKey = selectedService.attr('service-key');
                }
                $("#selectAddress").html('');
                if (searchAddress) {
                    $.ajax({
                        showLoader: true,
                        type: 'POST',
                        url: upsServiceOption.serviceUrl,
                        data: {
                            method: 'locatorAPI',
                            fullAddress: searchAddress,
                            countryCode: addressFull.countryCode,
                            selectedService: selectedServiceKey
                        },
                        dataType: 'json',
                        success: function(resp, textStatus, jqXHR) {
                            if (resp.description == 'Success') {
                                if ($( "#selectAddress" ).hasClass("d-none")) {
                                    $( "#selectAddress" ).removeClass("d-none");
                                }
                                // results
                                if ($( ".results" ).hasClass("d-none")) {
                                    $( ".results" ).removeClass("d-none");
                                }
                                var arrayLocatorAll = resp.data.infor;
                                var html = '';
                                if (arrayLocatorAll) {
                                    $("#selectAddressinFor").val(resp.data.selectAddress);
                                    arrayLocatorAll.forEach(function(locatorAddress, index) {
                                        index++;
                                        var operatingHours = ((locatorAddress.operatingHours)
                                        ? locatorAddress.operatingHours : '');
                                        html += '<div class="addressShow"><div class="addressTable">'
                                        + '<p class="pAddress"><strong>'
                                        + index +') '+ locatorAddress.name + '</strong><br />'
                                        + locatorAddress.address + '<br />'
                                        + $("#operatingHours").val() +':' + operatingHours + '</div>'
                                        + '<div class="pAddressDiv" id="pAddressDiv">'
                                        +'<i class="fas fa-map-marker-alt "></i><span id="nearbySpace">'
                                        + locatorAddress.distance + ' ' + locatorAddress.unit + '</span><br />'
                                        + '<input type="button" data-address="' + arrayLocatorAll.length
                                        + '" data-index="' + index + '" id="btnSelect_' + index
                                        + '" class="btnclass" value="'+$("#selectTitle").val()+'" /></div></div>';
                                    });
                                    $("#selectAddress").html(html);
                                }
                                //Remove all data from the map.
                                map.entities.clear();
                                var arrayLocatorInfo = resp.data.arrGeoCode;
                                if (arrayLocatorInfo) {
                                    //Make a request to geocode New York, NY.
                                    searchAddress += ', ' + addressFull.countryCode;
                                    geocodeQuery(searchAddress);
                                    arrayLocatorInfo.forEach(function (value, index) {
                                        var streetaddress= value.substr(0, value.indexOf(','))*1;
                                        streetaddress = streetaddress.toFixed(6);
                                        indexMap.push({
                                            value : streetaddress,
                                            index: index
                                        });
                                        //Create the geocode request.
                                        var geocodeRequest = {
                                            where: value,
                                            callback: getBoundary,
                                            errorCallback: function (e) {}
                                        };
                                        //Make the geocode request.
                                        searchManager.geocode(geocodeRequest);
                                    });
                                }
                                upsServiceOption.removeMotice();
                            } else {
                                upsServiceOption.messageNote = $("#locationMessage").val();
                                upsServiceOption.addNotice();
                                upsServiceOption.getMap();
                                return false;
                            }
                        }
                    });
                } else {
                    upsServiceOption.messageNote = $("#requiredAddress").val();
                    upsServiceOption.addNotice();
                    return false;
                }
            },
            select: function() {
                upsServiceOption.clearSession = '0';
                var addressShipping = quote.shippingAddress();
                // reload address information
                // addressShipping.trigger_reload = new Date().getTime();

                // clearing cached rates to retrieve new ones
                rateRegistry.set(addressShipping.getKey(), null);
                rateRegistry.set(addressShipping.getCacheKey(), null);

                var type = quote.shippingAddress().getType();
                if (type == 'new-customer-address') {
                    newAddressProcessor.getRates(addressShipping);
                } else {
                    customerAddressProcessor.getRates(addressShipping);
                }
            },
            selectShipHere: function() {
                $.ajax({
                    showLoader: true,
                    type: 'POST',
                    url: upsServiceOption.serviceUrl,
                    async: false,
                    data: {
                        method: 'shipHere'
                    },
                    dataType: 'json',
                    success: function(resp, textStatus, jqXHR) {
                    }
                });
            },
            useAddress: function() {
                var addressFull = upsServiceOption.getAddressFull();
                $("#searchAddress").val(addressFull.fullAddress);
            },
            getMap: function() {
                map = new Microsoft.Maps.Map('#upsMap', {zoom: 8});
                Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
                    var arrayLocation1 = $("#arrayLocation1").val();
                    var arrayLocation2 = $("#arrayLocation2").val();
                    var searchManager = new Microsoft.Maps.Search.SearchManager(map);
                    var requestOptions = {
                        bounds: map.getBounds(),
                        where: upsServiceOption.backendCountryCode,
                        callback: function (answer, userData) {
                            map.setView({ center: new Microsoft.Maps.Location(arrayLocation1, arrayLocation2), zoom: 8 });
                            map.entities.push(new Microsoft.Maps.Pushpin(answer.results[0].location));
                        }
                    };
                    searchManager.geocode(requestOptions);
                });
            },
            loadMap: function() {
                if (!map) {
                    var mapScriptUrl = 'https://www.bing.com/api/maps/mapcontrol?key='+ $("#bingMapKey").val();
                    var script = document.createElement("script");
                    script.setAttribute('defer', '');
                    //script.setAttribute('async', '');
                    script.setAttribute("type", "text/javascript");
                    script.setAttribute("src", mapScriptUrl);
                    document.body.appendChild(script);
                }
            }
        };

        $(document).ready(function() {
            upsServiceOption.loadMap();
            setInterval(function() { upsServiceOption.append();}, 500);

            $(document).on('click', upsServiceOption.tableClass +' tr', function() {
                // show/hide UPS service option
                upsServiceOption.toggle(this);
            });

            // use address
            $(document).on('click', '.useAddress', function() {
                upsServiceOption.useAddress();
            });

            // selectAddress
            $(document).on('click', '.action-select-shipping-item', function() {
                upsServiceOption.selectShipHere();
                upsServiceOption.select();
                upsServiceOption.showShippingService();
            });

            // action-save-address, action-show-popup
            $(document).on('click', '.action-save-address', function() {
                upsServiceOption.selectShipHere();
            });

            // search map
            $(document).on('click', '.searchMap', function() {
                upsServiceOption.search();
            });

            $(document).on('click', '#pAddressDiv .btnclass', function() {
                upsServiceOption.removeMotice();
                var locatorAddressLength = $(this).data("address");
                var addressInfo = $(this).data("index");
                var arrSelectAddress = JSON.parse($("#selectAddressinFor").val());
                // save selected address
                $("#selectedAPAddress").val(JSON.stringify(arrSelectAddress[addressInfo-1]));
                for (var i = 1; i < (locatorAddressLength + 1); i++) {
                    if (i == addressInfo) {
                        $("#btnSelect_" + i).css('background-color', 'paleturquoise');
                    } else {
                        $("#btnSelect_" + i).css('background-color', 'lightgrey');
                    }
                }
            });

            $(document).on('change', 'input[type=radio][name=serviceups]', function() {
                upsServiceOption.removeMotice();
                $("#selectedAPAddress").val('');
                $.ajax({
                    showLoader: true,
                    type: 'POST',
                    url: upsServiceOption.serviceUrl,
                    data: {
                        method: 'selectedService',
                        selectedShippingService: this.value
                    },
                    dataType: 'json',
                    success: function(resp, textStatus, jqXHR) {
                        upsServiceOption.shippingServiceType = resp['shippingServiceType'];
                        upsServiceOption.select();
                    }
                });
            });

            $(document).on('click', '#shipping-method-buttons-container .continue', function() {
                var saveSession = true;
                var selectedAPAdress = $("#selectedAPAddress").val();
                var checkSelectedAPAddress = true;
                if (upsServiceOption.shippingServiceType == 'AP'
                && $("input[value='upsshipping_upsshipping']:checked").length === 1) {
                    if (selectedAPAdress) {
                        $.ajax({
                            showLoader: true,
                            type: 'POST',
                            url: upsServiceOption.serviceUrl,
                            async: false,
                            data: {
                                method: 'selectedAPAddress',
                                selectedAPAddress: selectedAPAdress
                            },
                            dataType: 'json'
                        }).done(function() {
                            saveSession = true;
                        })
                        .fail(function() {
                            saveSession = false;
                        });
                    } else {
                        checkSelectedAPAddress = false;
                        upsServiceOption.messageNote = $("#accessPointProcess").val();
                        upsServiceOption.addNotice();
                        return false;
                    }
                }
                return saveSession && checkSelectedAPAddress && upsServiceOption.validate();
            });
        });

        function getBoundary(geocodeResult) {
            var labelMap = '';
            var str = geocodeResult.results[0].address.formattedAddress;
            var streetaddress= str.substr(0, str.indexOf(','))*1;
            streetaddress = streetaddress.toFixed(6);
            indexMap.forEach(function (value, index) {
                if (value.value == streetaddress) {
                    var num = value.index*1 + 1
                    labelMap = num.toString();
                }
            });
            //Add the first result to the map and zoom into it.
            if (geocodeResult && geocodeResult.results && geocodeResult.results.length > 0) {
                //Zoom into the location.
                map.setView({ center: geocodeResult.results[0].location, zoom: 11});
                //Create the request options for the GeoData API.
                var geoDataRequestOptions = {
                    lod: 1,
                    getAllPolygons: true
                };

                //Verify that the geocoded location has a supported entity type.
                switch (geocodeResult.results[0].entityType) {
                    case "CountryRegion":
                    case "AdminDivision1":
                    case "AdminDivision2":
                    case "Postcode1":
                    case "Postcode2":
                    case "Postcode3":
                    case "Postcode4":
                    case "Neighborhood":
                    case "PopulatedPlace":
                        geoDataRequestOptions.entityType = geocodeResult.results[0].entityType;
                        break;
                    default:
                        //Display a pushpin if GeoData API does not support EntityType.
                        var pin = new Microsoft.Maps.Pushpin(geocodeResult.results[0].location, {
                                text: labelMap,
                                icon: pathImage,
                            });
                        map.entities.push(pin);
                        return;
                }

                //Use the GeoData API manager to get the boundaries of the zip codes.
                Microsoft.Maps.SpatialDataService.GeoDataAPIManager.getBoundary(
                    geocodeResult.results[0].location,
                    geoDataRequestOptions,
                    map,
                    function (data) {
                        //Add the polygons to the map.
                        if (data.results && data.results.length > 0) {
                            map.entities.push(data.results[0].Polygons);
                        } else {
                            //Display a pushpin if a boundary isn't found.
                            var center = map.getCenter();

                            //Create custom Pushpin
                            var pin = new Microsoft.Maps.Pushpin(center, {
                                text: labelMap,
                                icon: pathImage,
                            });
                            map.entities.push(pin);
                        }
                });
            }
        }

        function geocodeQuery(query) {
            //If search manager is not defined, load the search module.
            if (!searchManager) {
                //Create an instance of the search manager and call the geocodeQuery function again.
                Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
                    searchManager = new Microsoft.Maps.Search.SearchManager(map);
                    geocodeQuery(query);
                });
            } else {
                var searchRequest = {
                    where: query,
                    callback: function (r) {
                        //Add the first result to the map and zoom into it.
                        if (r && r.results && r.results.length > 0) {
                            var pin = new Microsoft.Maps.Pushpin(r.results[0].location, {
                                text: '',
                                icon: pathAddressImage,
                            });
                            map.entities.push(pin);
                        }
                    },
                    errorCallback: function (e) {
                        //If there is an error, alert the user about it.
                        alert("No results found.");
                    }
                };

                //Make the geocode request.
                searchManager.geocode(searchRequest);
            }
        }
    });
