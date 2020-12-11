require([
    "jquery",
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: '<b>' + $("#processshipmentmessage").val() +'</b>',
        buttons: [
            {
                text: $("#canceleditshipmentmessage").val(),
                class: 'btnCancelEdit primary paddingbutton hidden',
                click: function (event) {
                    $("#rating").html('');
                    $("#messages").html('');
                    $("#messageFalse").html('');
                    $(".removeValuePackage").remove();
                    editShipment.clearContent();
                    this.closeModal();
                }
            },
            {
                text: $("#editshipmentmessage").val(),
                class: 'btnEdit primary paddingbutton',
                click: function (event) {
                    $("#rating").html('');
                    $("#messages").html('');
                    $("#messageFalse").html('');
                    $(".us_note_edit").addClass('d-none');
                    editShipment.clearMakedBorderRed();
                    var orderIdChecked = $('input[type=radio][name=createShipToChecked]:checked').attr("data-id");
                    editFlag = 1;
                    editShipment.fillShipToAddressValue(orderIdChecked);
                    editShipment.checkedShippingService(orderIdChecked);
                    editShipment.checkedAccessorialService(orderIdChecked);
                    editShipment.showEditShipmentToAddressInfo(orderIdChecked);
                    editShipment.validateStatus();
                    getPackageSelect();
                }
            },
            {
                text: $("#createshipmentmessage").val(),
                class: 'primary paddingbutton buttonCreate',
                click: function (event) {
                    $("#rating").html('');
                    $("#messages").html('');
                    $("#messageFalse").html('');
                    getPackageSelect();
                    packageValidate = popupValidate.packageValidationCheck(
                       $('.selectCusPackage').parents('.have-depend-on').find('.depend-on-custom-package:visible'));
                    if (editFlag) {
                        var orderIdChecked
                        = $('input[type=radio][name=createShipToChecked]:checked').attr("data-id");
                        ShippingType = editShipment.addNewShippingService(orderIdChecked);
                        AccessorialService = editShipment.addNewAccessorial();
                        COD = editShipment.addNewCOD();
                        editShipmentValidate = editShipment.editShipmentValidate();
                        if (orderIdChecked) {
                            if (typeof editShipment.data[orderIdChecked].service_type !== "undefined"
                            && editShipment.data[orderIdChecked].service_type == 'ADD') {
                                ShipTo = editShipment.addNewShipTo();
                                if (packageValidate && editShipmentValidate) validate = true;
                                else validate = false;
                            } else {
                                validate = packageValidate;
                            }
                        } else {
                            if (typeof editShipment.data.service_type !== "undefined"
                            && editShipment.data.service_type == 'ADD') {
                                ShipTo = editShipment.addNewShipTo();
                                if (packageValidate && editShipmentValidate) validate = true;
                                else validate = false;
                            } else {
                                validate = packageValidate;
                            }
                        }
                    } else {
                        validate = packageValidate;
                    }
                    if (validate) {
                        $(".buttonCreate").attr("disabled", true);
                        if (editFlag) {
                            $(".btnCancelEdit").attr("disabled", true);
                        } else {
                            $(".btnEdit").attr("disabled", true);
                        }
                        var oderIdMagento = $('input[type=radio][name=createShipToChecked]:checked').val();
                        $.ajax({
                            showLoader: true,
                            url: $("#urlcreateshipment").val(),
                            data: {
                                'ajax' : '1',
                                'method' : 'CreateAPI',
                                'shipfrom' : JSON.stringify(ShipFrom),
                                'shipto' : JSON.stringify(ShipTo),
                                'ShippingType' : JSON.stringify(ShippingType),
                                'Package' : JSON.stringify(Package),
                                'AccessorialService' : JSON.stringify(AccessorialService),
                                'idorder' : JSON.stringify(listOrder),
                                'COD' : JSON.stringify(COD),
                                'OrderValue' : OrderValue,
                                'OrderIdMagento' : oderIdMagento,
                                'editshipment' :editFlag
                            },
                            type: "POST",
                            dataType: 'json',
                        }).done(function (data) {
                            $("#messageFalse").html('');
                            if (data.check) {
                                window.location = $("#urlorder").val();
                            } else {
                                $(".buttonCreate").removeAttr("disabled");
                                if (editFlag) {
                                    $(".btnCancelEdit").removeAttr("disabled");
                                } else {
                                    $(".btnEdit").removeAttr("disabled");
                                }
                                $("#messageFalse").html('<div id="messages"><div class="messages">'
                                +'<div class="message message-error error">'
                                +'<div data-ui-id="messages-message-error">' + data.message
                                +'</div></div></div></div>');
                            }
                        }).error(function() {
                            $(".buttonCreate").removeAttr("disabled");
                            (editFlag) ? $(".btnCancelEdit").removeAttr("disabled")
                            : $(".btnEdit").removeAttr("disabled");
                            $("#messageFalse").html('<div id="messages"><div class="messages">'
                            +'<div class="message message-error error"><div data-ui-id="messages-message-error">'
                            + $("#apierrormessage").val()
                            +'</div></div></div></div>');
                        })
                    } else {
                        $(".buttonCreate").removeAttr("disabled");
                        if (editFlag) {
                            $(".btnCancelEdit").removeAttr("disabled");
                        } else {
                            $(".btnEdit").removeAttr("disabled");
                        }
                        var messageError = errorValidateMessage (editFlag, editShipmentValidate, packageValidate);
                        if (messageError) {
                            $("#messageFalse").html('<div id="messages"><div class="messages">'
                            +'<div class="message message-error error"><div data-ui-id="messages-message-error">'
                            + messageError +'</div></div></div></div>');
                        }
                    }
                }
            }
        ]
    };

    var listOrder = [];
    var ShipFrom = [];
    var ShipTo = [];
    var ShipToEShopper = [];
    var AccessorialService = [];
    var Package = [];
    var ShippingType = [];
    var editFlag = 0;
    var COD = 0;
    var OrderValue = 0;
    var editShipmentValidate = false;
    var packageValidate = false;
    var validate = false;

    function getPackageSelect() {
        Package = [];
        $('.have-depend-on').each(function() {
            if ($(this).find('select[name="Packaging"]').val() === "custom_package") {
                var weight = $(this).find('input[name="weight"]').val();
                var unit_weight = $(this).find('select[name="unit_weight"]').val();
                var length = $(this).find('input[name="length"]').val();
                var width = $(this).find('input[name="width"]').val();
                var height = $(this).find('input[name="height"]').val();
                var unit_dimension = $(this).find('select[name="unit_dimension"]').val();
                var jsonPackageCustom = '{"weight":"'+weight+'", "unit_weight":"'+unit_weight+'", "length":"'
                    +length+'", "width":"'+width+'", "height":"'+height+'", "unit_dimension":"'+unit_dimension+'"}';
                Package.push(jsonPackageCustom);
            } else {
                Package.push($(this).find('select[name="Packaging"]').val());
            };
        });
        var getIDListArray = $('input[type=radio][name=createShipToChecked]:checked').attr('data-id');
        if (typeof getIDListArray !== "undefined") {
            ShipTo = [];
            if (ShippingType[0] === 'AP') {
                ShipTo.push(ListArrayShipTo[getIDListArray].ap_name, ListArrayShipTo[getIDListArray].state,
                ListArrayShipTo[getIDListArray].telephone, ListArrayShipTo[getIDListArray].ap_address1,
                ListArrayShipTo[getIDListArray].ap_address2, ListArrayShipTo[getIDListArray].ap_address3,
                ListArrayShipTo[getIDListArray].ap_city, ListArrayShipTo[getIDListArray].ap_postcode,
                ListArrayShipTo[getIDListArray].CountryCode, ListArrayShipTo[getIDListArray].email,
                ListArrayShipTo[getIDListArray].ap_id);
            } else {
                var AddName = ListArrayShipTo[getIDListArray].firstname + ' '
                + ListArrayShipTo[getIDListArray].lastname;
                ShipTo.push(AddName, ListArrayShipTo[getIDListArray].state,
                ListArrayShipTo[getIDListArray].telephone, ListArrayShipTo[getIDListArray].ADDAdress1,
                ListArrayShipTo[getIDListArray].ADDAdress2,  ListArrayShipTo[getIDListArray].ADDAdress3,
                ListArrayShipTo[getIDListArray].city, ListArrayShipTo[getIDListArray].postcode,
                ListArrayShipTo[getIDListArray].CountryCode, ListArrayShipTo[getIDListArray].email,
                ListArrayShipTo[getIDListArray].ap_id);
            }
        }
    }

    $("#apiRating").click(function() {
        $("#rating").html('');
        $("#messages").html('');
        $("#messageFalse").html('');
        getPackageSelect();
        packageValidate = popupValidate.packageValidationCheck($('.selectCusPackage').parents('.have-depend-on').find('.depend-on-custom-package:visible'));
        /*if (packageValidate === false) {
            result.check = false;
            result.message.error.gtextError = gtextError;
        } else {
            result.check = true;
        }*/
        if (editFlag) {
            var orderIdChecked = $('input[type=radio][name=createShipToChecked]:checked').attr("data-id");
            ShippingType = editShipment.addNewShippingService(orderIdChecked);
            AccessorialService = editShipment.addNewAccessorial();
            COD = editShipment.addNewCOD();
            editShipmentValidate = editShipment.editShipmentValidate();
            if (orderIdChecked) {
                if (typeof editShipment.data[orderIdChecked].service_type !== "undefined"
                && editShipment.data[orderIdChecked].service_type == 'ADD') {
                    ShipTo = editShipment.addNewShipTo();
                    if (packageValidate && editShipmentValidate) {
                         validate = true;
                    } else {
                        validate = false;
                    }
                } else {
                    validate = packageValidate;
                }
            } else {
                if (typeof editShipment.data.service_type !== "undefined"
                && editShipment.data.service_type == 'ADD') {
                    ShipTo = editShipment.addNewShipTo();
                    if (packageValidate && editShipmentValidate) {
                        validate = true;
                    }
                    else {
                        validate = false;
                    }
                } else {
                    validate = packageValidate;
                }
            }
        } else {
            validate = packageValidate;
        }
        if (validate) {
            var oderIdMagento = $('input[type=radio][name=createShipToChecked]:checked').val();
            $.ajax({
                showLoader: true,
                url: $("#urlrateshipment").val(),
                data: {
                    'shipfrom' : JSON.stringify(ShipFrom),
                    'shipto' : JSON.stringify(ShipTo),
                    'ShippingType' : JSON.stringify(ShippingType),
                    'Package' : JSON.stringify(Package),
                    'AccessorialService' : JSON.stringify(AccessorialService),
                    'OrderIdMagento' : oderIdMagento,
                    'OrderValue' : OrderValue,
                    'COD' : JSON.stringify(COD),
                    'editshipment' : editFlag
                    },
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                if (data.check) {
                    $("#rating").text(data.result['TimeInTransit'] + ' ' + data.result['MonetaryValue'] + ' '
                    + data.result['CurrencyCode'] );
                } else {
                    $("#messageFalse").html('<div id="messages"><div class="messages">'
                    +'<div class="message message-error error"><div data-ui-id="messages-message-error"> '
                    + data.message +'</div></div></div></div>');
                }
            }).error(function (jqXHR, textStatus) {
                $("#messageFalse").html('<div id="messages"><div class="messages">'
                +'<div class="message message-error error"><div data-ui-id="messages-message-error">'
                + $("#apierrormessage").val()
                +'</div></div></div></div>');
            });
        } else {
            $(".buttonCreate").removeAttr("disabled");
            var messageError = errorValidateMessage (editFlag, editShipmentValidate, packageValidate);
            if (messageError) {
                $("#messageFalse").html('<div id="messages"><div class="messages">'
                +'<div class="message message-error error"><div data-ui-id="messages-message-error">'
                + messageError +'</div></div></div></div>');
            }
        }
    });

    function errorValidateMessage (editFlag, editShipmentValidate, packageValidate) {
        var messageError = '';
        if (editFlag && ShippingType[0] === 'ADD' && !editShipmentValidate) {
            $('#editShipTo').find('input.admin__control-text.errorInputField').each(function () {
                var name = $(this).attr('name');
                if (messageError.trim()) {
                    messageError += '<br />';
                }
                switch(name) {
                    case 'edit_name':
                        messageError += $("#nameempty").val();
                        break;
                    case 'edit_address1':
                        messageError += $("#addressempty").val();
                        break;
                    case 'edit_postcode':
                        messageError += $("#postalempty").val();
                        break;
                    case 'edit_city':
                        messageError += $("#cityempty").val();
                        break;
                    case 'edit_phone':
                        messageError += $("#phoneempty").val();
                        break;
                    case 'edit_email':
                        messageError += $("#emailempty").val();
                        break;
                }
            });
        }
        if (!packageValidate) {
            if (messageError.trim()) {
                messageError += '<br />';
            }
            messageError += $("#packagemessage").val();
        }
        return messageError;
    }

    var popup = modal(options, $('#popup_create_single_shipment'));
    $(document).on('click', '#create_single_shipment', function() {
        $.ajax({
            showLoader: true,
            type: 'POST',
            url: $("#urlordershipment").val(),
            data: {
                method: 'getStates'
            },
            dataType: 'json',
            success: function(resp, textStatus, jqXHR) {
                editShipment.listState = resp.states;
            }
        });
        if ($(".us_note_edit").hasClass('d-none')) {
            $(".us_note_edit").removeClass('d-none');
        }
        $("#rating").html('');
        editFlag = 0;
        editShipment.checkedSignatureAccessorial();
        $('.countrySelected').change(function () {
            if ($(this).val()) {
                var countryCode = $(this).val();
                editShipment.showState(countryCode, 0);
            }
        });
        $("#messages").remove();
        listOrder = [];
        $('.selectAll:checked').each(function() {
            listOrder.push($(this).val());
        });
        $('#popup_create_single_shipment').find('#messages').remove();
        $('#popup_create_single_shipment').find('.admin__field-error').removeClass('admin__field-error');
        var packageOrderId = listOrder[0];
        if (listOrder.length == 1) {
            var idOrther = listOrder[0];
        } else {
            var idOrther =  JSON.stringify(listOrder);
        }
        $.ajax({
            showLoader: true,
            url: $("#urlorderdetail").val(),
            data: 'ajax=1&method=getDetailOrder&id='+idOrther,
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
            var dataDetail = data.result;
            editShipment.data = data.result;
            OrderValue = dataDetail.orderValue;
            editShipment.showState(dataDetail.CountryCode, dataDetail.state);
            ShippingType = [];
            if (typeof dataDetail.accessorial_service === "undefined") {
                var accessorialService = JSON.parse(dataDetail[0].accessorial_service);
                var accessorialServiceTranslate = dataDetail[0].accessorial_service_translate;
                $("#accessorialservice").html('');
                $.each(accessorialServiceTranslate , function( key, value ) {
                    $("#accessorialservice").append("<p>"+ value +"</p>");
                });
                (dataDetail[0].service_type == 'AP') ? typeService = 'AP' : typeService = 'Address';
                $("#shippingserviceCreate").html('To '+ typeService + ' (' + dataDetail[0].service_name_info + ')');
                ShippingType.push(dataDetail[0].service_type, dataDetail[0].rate_code, dataDetail[0].idservice,
                dataDetail[0].service_name);
                (dataDetail[0].service_type === "AP") ? $("#createAccount").val($("#accountdefaultap").val())
                : $("#createAccount").val($("#accountdefaultadd").val());
                if (dataDetail[0].method === "cashondelivery") {
                    COD = 1;
                    if (dataDetail[0].service_type === "AP") {
                        $("#accessorialservice").append("<p>" + $("#shiptoservicenameap").val() +"</p>");
                    } else {
                        $("#accessorialservice").append("<p>" + $("#shiptoservicenameadd").val() +"</p>");
                    }
                } else {
                    COD = 0;
                }
            } else {
                var accessorialService = JSON.parse(dataDetail.accessorial_service);
                var accessorialServiceTranslate = dataDetail.accessorial_service_translate;
                $("#accessorialservice").html('');
                $.each(accessorialServiceTranslate , function( key, value ) {
                    $("#accessorialservice").append("<p>"+ value +"</p>");
                });
                (dataDetail.service_type == 'AP') ? typeService = 'AP' : typeService = 'Address';
                $("#shippingserviceCreate").html('To ' + typeService + ' (' + dataDetail.service_name_info + ')');
                ShippingType.push(dataDetail.service_type, dataDetail.rate_code, dataDetail.idservice,
                dataDetail.service_name);
                (dataDetail.service_type === "AP") ? $("#createAccount").val($("#accountdefaultap").val())
                : $("#createAccount").val($("#accountdefaultadd").val());
                if (dataDetail.method === "cashondelivery") {
                    COD = 1;
                    if (dataDetail.service_type === "AP") {
                        $("#accessorialservice").append("<p>" + $("#shiptoservicenameap").val() +"</p>");
                    } else {
                        $("#accessorialservice").append("<p>" + $("#shiptoservicenameadd").val() +"</p>");
                    }
                } else {
                    COD = 0;
                }
            }
            var contentPackage = '';
            var data_openorder = dataDetail.package;
            if (typeof dataDetail[0] !== 'undefined') {
                data_openorder = JSON.parse(dataDetail[0].package);
            }
            if (data_openorder) {
                var selectedpackage = 'Package_1 (' + data_openorder[0].length + 'x'+ data_openorder[0].width +'x'+ data_openorder[0].height + ' '+ data_openorder[0].unit_dimension + ', ' + data_openorder[0].weight + ' '+ data_openorder[0].unit_weight +')';
                $(".selectCusPackage option:first").html(selectedpackage);
                $(".selectCusPackage option:first").val(packageOrderId);
                var htmlOneSelectedPackage = $(".packageAddRow").html();
                $.each(data_openorder, function( key, value ) {
                    if (key > 0) {
                        contentPackage += '<div class="row admin__field field removeValuePackage removeRowPackage have-depend-on">' + htmlOneSelectedPackage + '</div>';
                    } else {
                        contentPackage += '<div class="row admin__field field packageAddRow have-depend-on">' + htmlOneSelectedPackage + '</div>';
                    }
                });
            }
            var htmlContent = '<div class="col-xs-12"><label class="label admin__field-label"><b>Packaging</b></label></div>' + contentPackage;
            $(".addPackage").html(htmlContent);
            $(".removeValuePackage #removePackage").removeClass('hidden');
            loadAutoIncement();
            AccessorialService = accessorialService;
            addShipTo(dataDetail);
            getInfoAccount($("#createAccount").val());
            $("select[name='Packaging']").val(packageOrderId);
            $('.have-depend-on').find('.depend-on-custom-package').addClass('hidden');
        })
    });
    //Auto check and disable saturday delivery accessorial
    $(document).on('change', 'input[type=radio][name="optradio"]', function () {
        var service_key = $('input[type=radio][name="optradio"]:checked').val();
        if (service_key.includes('SAT_DELI')) {
            $('input[value="UPS_ACSRL_STATURDAY_DELIVERY"]').prop("checked", true);
            $('input[value="UPS_ACSRL_STATURDAY_DELIVERY"]').attr("disabled", true);
        } else {
            $('input[value="UPS_ACSRL_STATURDAY_DELIVERY"]').attr("disabled", false);
        }
    });


    var ListArrayShipTo = [];
    function addShipTo(data) {
        ShipTo = [];
        ListArrayShipTo = [];
        var apAddress = '';
        if (typeof data.id !== "undefined") {
            if (data.ap_name == null) {
                apName = '';
            } else {
                apName = '<div class="col-xs-12 admin__field"><input type="radio" class="hidden"'
                +' name="createShipToChecked" value="'+ data.order_id_magento
                +'" checked ><label style="font-weight: bold" checked>'+ data.ap_name +'</label></div>';
            }
            if (data.ap_postcode == null) {
                apPostcode = '';
            } else {
                apPostcode = data.ap_postcode;
            }
            if (data.APAdressAll && data.APAdressAll.indexOf(", ")) {
                var splitAddress = data.APAdressAll.split(",");
                $.each(splitAddress, function (key, val) {
                    apAddress += '<div class="col-xs-12 admin__field"><label>'+ val +'</label></div>';
                });
            } else {
                apAddress = '<div class="col-xs-12 admin__field"><label>'+ data.APAdressAll +'</label></div>';
            }
            var state = '';
            if (data.CountryAPorADD == null) {
                CountryAPorADD = '';
            } else {
                CountryAPorADD = '<div class="col-xs-12 admin__field"><label>'+ data.CountryAPorADD
                +'</label></div>';
            }
            if (data.email == null) {
                apEmail = '';
            } else {
                apEmail = '<div class="col-xs-12 admin__field"><label>'+ data.email +'</label></div>';
            }
            if (data.telephone == null) {
                apPhone = '';
            } else {
                apPhone = '<div class="col-xs-12 admin__field"><label>'+ data.telephone +'</label></div>';
            }
            if (data.email == null) {
                apEmail = '';
            } else {
                apEmail = '<div class="col-xs-12 admin__field"><label>'+ data.email +'</label></div>';
            }
            if (data.service_type == 'AP') {
                var apPostcode = '';
                if (data.ap_postcode == null) {
                    apPostcode = '';
                } else {
                    apPostcode = data.ap_postcode;
                }
                if (data.ap_city == null) {
                    apCity = '';
                } else {
                    var apPostcodeAll = '';
                    if (data.ap_city != null && data.ap_city != '') {
                        apPostcodeAll += data.ap_city + ', ';
                    }
                    if (data.ap_state != null && data.ap_state != '') {
                        state = data.ap_state + ', ';
                        apPostcodeAll += data.stateName + ', ';
                    }
                    apPostcodeAll += apPostcode;
                    apCity = '<div class="col-xs-12 admin__field"><label>'+ apPostcodeAll +'</label></div>';
                }
                var dataAppend = apName + apAddress + apCity + CountryAPorADD + apEmail + apPhone;
                ShipTo.push(data.ap_name, data.ap_state, data.telephone, data.ap_address1, data.ap_address2,
                data.ap_address3, data.ap_city, data.ap_postcode, data.CountryCode, data.email, data.ap_id);
            } else {
                var apAddress = '';
                if (data.ADDAdressAll && data.ADDAdressAll.indexOf(",")) {
                    var splitAddress = data.ADDAdressAll.split(",");
                    $.each(splitAddress, function (key, val) {
                        if (key == 0) {
                            apAddress += '<div class="col-xs-12 admin__field" style="margin-top: 1.5rem;"><label>'+ val.trim() +'</label></div>';
                        } else {
                            apAddress += '<div class="col-xs-12"><label>'+ val.trim() +'</label></div>';
                        }
                    });
                } else {
                    apAddress = '<div class="col-xs-12 admin__field" style="margin-top: 1.5rem;"><label>'
                    + data.ADDAdressAll +'</label></div>';
                }

                var addName = data.firstname + ' ' + data.lastname;
                var dataApPostcode = '';
                if (data.city != null && data.city != '') {
                    dataApPostcode += data.city + ', ';
                }
                if (data.stateName != null && data.stateName != '') {
                    dataApPostcode += data.stateName + ', ';
                }
                dataApPostcode += data.postcode;
                var dataAppend = '<div class="col-xs-12 admin__field">'+
                        '<input type="radio" class="hidden" name="createShipToChecked" value="'
                        + data.order_id_magento +'" checked>'+
                        '<label style="font-weight: bold" >'+ addName +'</label>'+
                        '</div>'+ apAddress +
                        '<div class="col-xs-12 admin__field"><label>'+ dataApPostcode +'</label></div>'+
                        '<div class="col-xs-12 admin__field"><label>'+ data.CountryAPorADD +'</label></div>'+
                        '<div class="col-xs-12 admin__field"><label>'+ data.telephone +'</label></div>'+
                        '<div class="col-xs-12 admin__field"><label>'+ data.email +'</label></div>';
                ShipTo.push(addName, data.state, data.telephone, data.ADDAdress1, data.ADDAdress2, data.ADDAdress3,
                 data.city, data.postcode, data.CountryCode, data.email, data.ap_id);
            }
            $("#createShipTo").html(dataAppend);
        } else {
            var check = "";
            $("#createShipTo").html('');
            ListArrayShipTo = data;
            $.each( data, function( key, value ) {
                if (key != "orderValue") {
                    if (key == 0) {
                        check = 'checked';
                        stylePaddingRadio = "";
                    } else {
                        check = '';
                        stylePaddingRadio = "margin-top: 1.5rem;"
                    }
                    if (value.ap_name == null) {
                        apName = '';
                    } else {
                        apName = '<div class="col-xs-12" style="'+ stylePaddingRadio +'">'+
                            '<input class="admin__control-radio" '+ check
                            +' type="radio" name="createShipToChecked" id="RadioShipto'+value.id+'"  value="'
                            + value.order_id_magento +'" style="margin-right:5px" data-id="'+ key +'">'+
                            '<label class="admin__field-label" style="font-weight: bold" for="RadioShipto'
                            +value.id+'"><b>'+ value.ap_name +'</b></label>'+
                            '</div>';
                    }
                    var apAddress = '';
                    if (value.APAdressAll && value.APAdressAll.indexOf(", ")) {
                        var splitAddress = value.APAdressAll.split(",");
                        $.each(splitAddress, function (key, val) {
                            if (key == 0) {
                                apAddress += '<div class="col-xs-12 admin__field" style="margin-top: 1.5rem;"><label>'+ val.trim() +'</label></div>';
                            } else {
                                apAddress += '<div class="col-xs-12"><label>'+ val.trim() +'</label></div>';
                            }
                        });
                    } else {
                        apAddress = '<div class="col-xs-12 admin__field" style="margin-top: 1.5rem;"><label>'
                        + value.APAdressAll +'</label></div>';
                    }
                    var state = '';

                    if (value.CountryAPorADD == null) {
                        CountryAPorADD = '';
                    } else {
                        CountryAPorADD = '<div class="col-xs-12 admin__field"><label>'+ value.CountryAPorADD
                        +'</label></div>';
                    }
                    if (value.telephone == null) {
                        apPhone = '';
                    } else {
                        apPhone = '<div class="col-xs-12 admin__field"><label>'+ value.telephone +'</label></div>';
                    }
                    if (value.email == null) {
                        apEmail = '';
                    } else {
                        apEmail = '<div class="col-xs-12 admin__field"><label>'+ value.email +'</label></div>';
                    }
                    var dataAppend = '';
                    if (value.service_type == 'AP') {
                        var apPostcode = '';
                        if (value.ap_postcode == null) {
                            apPostcode = '';
                        } else {
                            apPostcode = value.ap_postcode;
                        }
                        if (value.ap_postcode == null) {
                            apCity = '';
                        } else {
                            var apPostcodeAll = '';
                            if (value.ap_city != null && value.ap_city != '') {
                                apPostcodeAll += value.ap_city + ', ';
                            }
                            if ((value.ap_state != null && value.ap_state != '')) {
                                apPostcodeAll += value.stateName + ', ';
                                state = value.ap_state;
                            }
                            apPostcodeAll += apPostcode;
                            apCity = '<div class="col-xs-12 admin__field"><label>'+ apPostcodeAll +'</label></div>';
                        }
                        dataAppend = apName + apAddress + apCity + CountryAPorADD + apEmail + apPhone;
                        if (key == 0) {
                            ShipTo.push(value.ap_name, value.ap_state, value.telephone, value.ap_address1,
                            value.ap_address2, value.ap_address3, value.ap_city, value.ap_postcode,
                            value.CountryCode, value.email, value.ap_id);
                        }
                    } else {
                        var apAddress = '';
                        if (value.ADDAdressAll && value.ADDAdressAll.indexOf(",")) {
                            var splitAddress = value.ADDAdressAll.split(",");
                            $.each(splitAddress, function (key, val) {
                                if (key == 0) {
                                    apAddress += '<div class="col-xs-12 admin__field" style="margin-top: 1.5rem;"><label>'+ val.trim() +'</label></div>';
                                } else {
                                    apAddress += '<div class="col-xs-12"><label>'+ val.trim() +'</label></div>';
                                }
                            });
                        } else {
                            apAddress = '<div class="col-xs-12 admin__field" style="margin-top: 1.5rem;"><label>'
                            + value.ADDAdressAll +'</label></div>';
                        }
                        var addName = value.firstname + ' ' + value.lastname;
                        var dataAddressPostCode = '';
                        if (value.city != null && value.city != '') {
                            dataAddressPostCode += value.city + ', ';
                        }
                        if (value.stateName != null && value.stateName != '') {
                            dataAddressPostCode += value.stateName + ', ';
                        }
                        dataAddressPostCode += value.postcode;
                        dataAppend = '<div class="col-xs-12" style="'+ stylePaddingRadio +'">'+
                            '<input class="admin__control-radio" '+ check +' type="radio" id="RadioShipto'
                            +value.id+'" name="createShipToChecked" value="'+ value.order_id_magento +'" data-id="'
                            + key +'" style="margin-right:5px">'+
                            '<label class="admin__field-label" for="RadioShipto'+value.id+'"><b>'+ addName
                            +'</b></label>'+
                            '</div>'+ apAddress +
                            '<div class="col-xs-12 admin__field"><label>'+ dataAddressPostCode +'</label></div>'+
                            '<div class="col-xs-12 admin__field"><label>'+ value.CountryAPorADD +'</label></div>'+
                            '<div class="col-xs-12 admin__field"><label>'+ value.telephone +'</label></div>'+
                            '<div class="col-xs-12 admin__field"><label>'+ value.email +'</label></div>';
                        if (key == 0) {
                            ShipTo.push(addName, value.state, value.telephone, value.ADDAdress1, value.ADDAdress2,
                            value.ADDAdress3, value.city, value.postcode, value.CountryCode, value.email,
                            value.ap_id);
                        }
                    }
                    $("#createShipTo").append(dataAppend);
                }
            });
        }
    }

    $(".buttonAddPackage").click(function() {
        $("#rating").html('');
        $("#messageFalse").html('');
        editShipment.clearMakedBorderRed();
        var html = $(".packageAddRow").html();
        var addPackageFormat
        = $('<div class="row admin__field field removeValuePackage removeRowPackage have-depend-on">' + html
        + '</div>');
        addPackageFormat.find('.removeRow').removeClass('hidden');
        addPackageFormat.find('.depend-on-custom-package ').addClass('hidden');
        $(".addPackage").append(addPackageFormat);
        loadAutoIncement();
    });

    $(document).on('click', '.removeRow', function() {
        $("#rating").html('');
        $("#messageFalse").html('');
        editShipment.clearMakedBorderRed();
        $(this).parents(".removeRowPackage").remove();
        loadAutoIncement();
    });

    $(".action-close").click(function() {
        $(".removeValuePackage").remove();
        $("#rating").html('');
        $("#messageFalse").html('');
        editShipment.clearContent();
    });

    $(document).on("change", ".selectCusPackage", function() {
        $("#rating").html('');
        $("#messageFalse").html('');
        editShipment.clearMakedBorderRed();
        if (this.value == 'custom_package') {
            $(this).parents('.have-depend-on').find('.depend-on-custom-package').removeClass('hidden');
            popupValidate.packageInputStatus($(this).parents('.have-depend-on').find('.depend-on-custom-package'), 'package');
        } else {
            $(this).parents('.have-depend-on').find('.depend-on-custom-package').addClass('hidden');
            $(this).parents('.have-depend-on').find('input').val('');
        }
    });

    function loadAutoIncement() {
        $(document).find('.label-auto-increment').each(function(index) {
            $(this).find('o').html(index+1);
        });
    }

    $("select[name='createAccount']").on("change",function() {
        $("#rating").html('');
        $("#messageFalse").html('');
        editShipment.clearMakedBorderRed();
        getInfoAccount($(this).val());
    });

    $(document).ready(function() {
        $("#selectAll").prop('disabled', false);
        $('.selectAll').each(function() {
            $(this).prop('disabled', false);
        });
    });

    function getInfoAccount(id) {
        $("#rating").html('');
        $("#messages").html('');
        $("#messageFalse").html('');
        $.ajax({
            showLoader: true,
            url: $("#urlcreateshipment").val(),
            data: 'ajax=1&method=getInfoAccount&id='+id,
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
            var dataDetail = data.result;
            var name = "";
            if (dataDetail.account_id !== "1") {
                CheckGetInfoAccount(dataDetail.ups_account_name, "CreateFullName");
                name = dataDetail.ups_account_name;
            } else {
                CheckGetInfoAccount(dataDetail.fullname, "CreateFullName");
                name = dataDetail.fullname;
            }
            var sringReplace = dataDetail.MergeAddress.replace(/\,/g, ' <br />');
            ShipFrom = [];
            ShipFrom.push(name, dataDetail.company, dataDetail.ups_account_number, dataDetail.phone_number,
            dataDetail.address_1, dataDetail.address_2, dataDetail.address_3, dataDetail.city,
            dataDetail.post_code, dataDetail.Macountry, dataDetail.state_province_code);
            CheckGetInfoAccount(sringReplace, "createAddress1");
            CheckGetInfoAccount(dataDetail.city, "createCity");
            if (dataDetail.state_province_code && dataDetail.state_province_code != 'XX') {
                dataDetail.post_code = dataDetail.stateNameDetail + ', ' + dataDetail.post_code;
            }
            CheckGetInfoAccount(dataDetail.post_code, "createPostCode");
            CheckGetInfoAccount(dataDetail.country, "createCountry");
            CheckGetInfoAccount(dataDetail.phone_number, "createPhone");
            $('#popup_create_single_shipment').modal('openModal').removeClass('hidden');
        });
    }

    function CheckGetInfoAccount(data, object) {
        (data != null && data != '') ? $("#"+ object).html(data).parent().show() : $("div #"
        + object).parent().hide();
    }
});
