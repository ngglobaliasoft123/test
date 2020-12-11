require([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "Magento_Ui/js/modal/confirm"
], function ($, modal , confirm) {
    $("#term-condition").on('click',function(){
        $("#term_condition_popup").modal("openModal").removeClass('hidden');
    });

    var cancelShipmentList = [];
    $('.list-data-table tbody tr').click(function() {
        var checked = $(this).find('input').prop("checked");
        var shipmentNumber = $(this).find('input[name=shipmentId]').attr('shipmentNumber');
        var trackingNumber = $(this).find('input[name=trackingNumber]').val();
        if (checked) {
            $(this).addClass('selected');
            cancelShipmentList.push({ 'shipment_number' : shipmentNumber, 'tracking_number' : trackingNumber});
        } else {
            $(this).removeClass('selected');
            cancelShipmentList.splice(cancelShipmentList.map(function(item) {
                return item.shipment_number; }).indexOf(shipmentNumber), 1);
        }
        checkselected();
    });
    $('.shipment-grid-view').click(function() {
        var trackingId = $(this).parent().find('input[name=trackingNumber]').attr('data-id');
        $.ajax({
            showLoader: true,
            url: $("#urlshipmentdetail").val(),
            data: 'ajax=1&method=getShipmentDetail&tracking_id='+trackingId,
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
            if (data && data.shipment) {
                if (data.shipment.id)
                $('.shipment-id').html($("#shipmenttitle").val() + data.shipment.shipment_number);
                if (data.shipment.date_created) $('.shipment-datetime').html(data.shipment.date_created);
                if (data.shipment.package_status) $('.shipment-package-status').html(data.shipment.package_status);
                if (data.shipment.increment_id) $('#detail_order_id').html(data.shipment.increment_id);
                if (data.shipment.tracking_number) $('#detail_tracking_number').html(data.shipment.tracking_number);
                if (data.shipment.name) $('#detail_customer_name').html(data.shipment.customername);
                if (data.product) {
                    var listProduct = getListProduct(data.product);
                    $('#detail_shipment_product').html(listProduct);
                }
                if (data.shipment.shipping_address) $('#detail_address').html(data.shipment.shipping_address);
                if (data.shipment.phone) $('#detail_phone_number').html(data.shipment.phone);
                if (data.shipment.email) $('#detail_customer_email').html(data.shipment.email);
                if (data.shipment.shipping_service)
                $('#detail_shipping_service').html(data.shipment.shipping_service);
                if (data.shipment.access_point_addr !== null && data.shipment.access_point_addr !== "undefined")
                $('#detail_access_point').html(data.shipment.access_point_addr);
                if (data.shipment.package_detail) {
                    $('#detail_package_info').html(data.shipment.package_detail);
                }
                var accessorialService = '';
                if (data.shipment.accessorial_service) {
                    accessorialService = getAccessorialService(data.shipment.accessorial_service);
                    if (data.shipment.cod == 1) {
                        if (data.shipment.service_type == 'AP') {
                            if (accessorialService) {
                                accessorialService = accessorialService + '<br/>' + $("#shiptoserviceap").val();
                            } else {
                                accessorialService = $("#shiptoserviceap").val();
                            }
                        } else {
                            if (accessorialService) {
                                accessorialService = accessorialService + '<br/>' + $("#shiptoserviceadd").val();
                            } else {
                                accessorialService =$("#shiptoserviceadd").val();
                            }
                        }
                    }
                } else {
                    if (data.shipment.service_type == 'AP') {
                        accessorialService = $("#shiptoserviceap").val();
                    } else {
                        accessorialService = $("#shiptoserviceadd").val();
                    }
                }
                $('#detail_accessorial_service').html(accessorialService);
                if (data.shipment.order_value)
                $('#detail_order_value').html(data.shipment.currency_symbol+data.shipment.order_value);
                if (data.shipment.shipping_fee)
                $('#detail_shipping_fee').html($("#apipolandcode").val() + data.shipment.shipping_fee);
                $('#popup_shipments_layout_details').modal('openModal').removeClass('hidden');
            }
        });
    });

    function getListProduct(productData) {
        var listProduct = '';
        var productDetail = [];
        productData.forEach(function (item) {
            if (!item['parent_item_id']) {
                productDetail.push(item);
            } else {
                productDetail.splice(productDetail.indexOf(item['parent_item_id']) , 1);
                productDetail.push(item);
            }
        });
        productDetail.forEach(function(item) {
            if (listProduct) { listProduct += '<br/>';}
            var quantity = parseInt(item['qty_ordered']);
            var product = quantity  +' x '+ item['name'];
            listProduct += product;
        });
        return listProduct;
    }

    function getAccessorialService(accessorialServiceData) {
        if (accessorialServiceData) {
            var listAccessorialService = $.map(JSON.parse(accessorialServiceData), function(value, index) {
                return [value]; });
            var accessorialService = '';
            listAccessorialService.forEach(function (item) {
                if (accessorialService != '') accessorialService += '<br/>';
                accessorialService += item;
            });
        }
        return accessorialService;
    }

    $(document).ready(function() {
        $("#selectAll").prop('disabled', false);
        $('.selectAll').each(function() {
            $(this).prop('disabled', false);
        });
    });

    $('#selectAll').click(function() {
        $('.selectAll').prop('checked', $(this).prop('checked'));
        if ($(this).prop('checked')) {
            $('.list-data-table tbody tr').addClass('selected');
        } else {
            $('.list-data-table tbody tr').removeClass('selected');
        }
        checkselected();
    });

    var listChecked = [];
    var listTracking = [];
    var listTrackingId = [];

    $("#exportData").click(function() {
        submitExport(1);
    })

    $("#printLabelPDF").click(function() {
        $("input[name='listCheckedLabel']").val(JSON.stringify(listTracking));
        $("input[name='labelFormat']").val('PDF');
        $("#formPrintLabel").submit();
    })
    
    $("#printLabelZPL").click(function() {
        $("input[name='listCheckedLabel']").val(JSON.stringify(listTracking));
        $("input[name='labelFormat']").val('ZPL');
        $("#formPrintLabel").submit();
    })

    function submitExport($status) {
        $("input[name='listChecked']").val(JSON.stringify(listTrackingId));
        $("input[name='statusExport']").val($status);
        $("#shipment").submit();
    }

    $("#cancelShipment").click(function() {
        confirm({
            title: $("#cancelshipmentmessage").val(),
            content: '<div style="margin:20px 0 20px 0">'
            + $("#confirmmessage").val() + '</div>',
            buttons: [{
                text: $("#cancelmessage").val(),
                class: 'paddingbutton',
                click: function (event) {
                    this.closeModal(event);
                }
                }, {
                text: $("#okmessage").val(),
                class: 'primary paddingbutton',
                click: function (event) {
                    cancelShipment();
                }
            }]
        });
    });

    function cancelShipment() {
        var cancelShipmentData = JSON.stringify(cancelShipmentList);
        $.ajax({
            showLoader: true,
            url: $("#urlshipmentdetail").val(),
            data: 'ajax=1&method=setCancelShipment&cancel_shipment_data='+cancelShipmentData,
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
            $(".modals-wrapper").remove();
            setTimeout(function() {
                confirm({
                    title: $("#cancelshipmentmessage").val(),
                    content: '<div class="popup-cancel">' + data.message +'</div>',
                    buttons: []
                });
                setTimeout(function() {
                    window.location = $("#urlindex").val();
                }, 10000);
            }, 300);
        });
    }

    function checkselected() {
        listChecked = [];
        listTracking = [];
        cancelShipmentList = [];
        listTrackingId = [];
        $(".selectAll:checked").each(function() {
            var id = $(this).val();
            if (Math.floor(id) == id && $.isNumeric(id))
                listChecked.push(id);
                listTracking.push($(this).attr("shipmentNumber"));
                listTrackingId.push($(this).attr("trackId"));
                cancelShipmentList.push({ 'shipment_number' : $(this).attr("shipmentNumber"),
                    'tracking_number' : $(this).attr('data-id') })
        })
        if (listChecked.length==0) {
            $("#exportData").prop( "disabled", true );
            $("#printLabelPDF").prop( "disabled", true );
            $("#printLabelZPL").prop( "disabled", true );
            $("#cancelShipment").prop( "disabled", true );
        } else {
            $("#exportData").prop( "disabled", false );
            $("#printLabelPDF").prop( "disabled", false );
            $("#printLabelZPL").prop( "disabled", false );
            $("#cancelShipment").prop( "disabled", false );
        }
        if (!$('.list-data-table tbody tr').hasClass('no_shipment_rows')) {
            if (listChecked.length == $('.selectAll').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        }
    }
});
