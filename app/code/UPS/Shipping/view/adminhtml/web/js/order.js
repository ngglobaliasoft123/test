require([
    "jquery",
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm'
], function ($, modal, confirm) {
    $('.open-order-grid-view').click(function() {
        var orderId = $(this).parent().find('input').val();
        $.ajax({
            showLoader: true,
            url: $("#urlorderDetail").val(),
            data: 'ajax=1&method=getDetailOrder&id='+orderId,
            type: "POST",
            dataType: 'json'
        }).done(function(data) {
            $('#orderID').html(data.result.increment_id);
            $('#dateTime').html(data.result.created_at);
            $('#customer').html(data.result.firstname + ' ' + data.result.lastname);
            $('#product').html(data.result.productName);
            $('#address').html(data.result.address);
            $('#phoneNumber').html(data.result.telephone);
            $('#email').html(data.result.email);
            (data.result.service_type == 'AP') ? typeService = 'AP' : typeService = 'Address';
            $('#shippingservice').html('To ' + typeService + ' (' + data.result.service_name_info + ')');
            $('#address1').html(data.result.address_ap);
            var accessorialService = data.result.accessorial_service_translate;
            var listAccessorial = '';
            $("#accessorial1").html('');
            $.each(accessorialService , function( key, value ) {
                if (value != '' && value != null) {
                    listAccessorial += value + '<br />';
                }
            });
            if (data.result.method =='cashondelivery') {
                if (data.result.service_type == 'AP') {
                    listAccessorial +='<?php echo __(\UPS\Shipping\Helper\Config::SHIP_TO_SERVICE_AP) ?>'+'<br />';
                } else {
                    listAccessorial +='<?php echo __(\UPS\Shipping\Helper\Config::SHIP_TO_SERVICE_ADD) ?>'+'<br />';
                }
            }
            $("#accessorial1").append(listAccessorial);
            $('#orderValue').html(data.result.currency_symbol+data.result.orderValueFM);
            $('#status').html(data.result.label);
            $('#popup_detail_order').modal('openModal').removeClass('hidden');
        });
    });

    $('#selectAll').click(function() {
        $('.selectAll').prop('checked', $(this).prop('checked'));
        if ($(this).prop('checked'))
        {
            var countopenorder = $("#countopenorder").val();
            if (countopenorder >= 1) {
                $("#orders_data_ddmmyy").prop('disabled',false);
                $("#updateArchiveOrder").prop('disabled',false);
                if (countopenorder > 1) {
                    $("#create_batch_shipment").prop('disabled',false);
                }
                $(".list-data-table>tbody>tr").each(function() {
                    $(this).addClass('selected');
                })
            }
            checkButtonSingleBatch();
        } else {
            $("#orders_data_ddmmyy").prop('disabled',true);
            $("#updateArchiveOrder").prop('disabled',true);
            $("#create_batch_shipment").prop('disabled',true);
            $("#create_single_shipment").prop('disabled',true);
            $(".list-data-table>tbody>tr").each(function() {
                $(this).removeClass('selected');
            })
        }
    });

    function checkButtonSingleBatch() {
        var listService = [];
        $(".selectAll:checked").each(function() {
            listService.push($(this).attr('service'));
        })
        var AP = 0;
        var ADD = 0;
        $.each(listService , function( key, value ) {
            if (value == 'AP') {
                AP ++;
            } else {
                ADD ++;
            }
        });
        var countopenorder = $("#countopenorder").val();
        if (countopenorder >= 1) {
            if (parseInt(AP) > 0 && parseInt(ADD) > 0) {
                $("#create_single_shipment").prop('disabled',true);
            } else {
                $("#create_single_shipment").prop('disabled',false);
            }
        }
    }

    $(".selectAll").click(function() {
        if ($('.selectAll:checked').length > 0) {
            $("#orders_data_ddmmyy").prop('disabled',false);
            $("#updateArchiveOrder").prop('disabled',false);
            if ($('.selectAll:checked').length == 1) {
                $("#create_single_shipment").prop('disabled',false);
                $("#create_batch_shipment").prop('disabled',true);
            }else {
                $("#create_batch_shipment").prop('disabled',false);
            }
            if ($('.selectAll:checked').length === $('.selectAll').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
            checkButtonSingleBatch();
        }
        else{
            $('#selectAll').prop('checked', false);
            $("#orders_data_ddmmyy").prop('disabled',true);
            $("#updateArchiveOrder").prop('disabled',true);
            $("#create_batch_shipment").prop('disabled',true);
            $("#create_single_shipment").prop('disabled',true);
        }
    });

    $('#orders_data_ddmmyy').click(function() {
        var orderIds = [];
        $('.selectAll:checked').each(function() {
            orderIds.push($(this).val());
        });
        $('#form-orders_data_ddmmyy').find('input[name=orderIds]').val(JSON.stringify(orderIds));
        $('#form-orders_data_ddmmyy').find('input[name=ExportType]').trigger('click');
    });

    $('#export-all-orders').click(function() {
        $('#form-orders_data_ddmmyy').find('input[name=orderIds]').val('');
        $('#form-orders_data_ddmmyy').find('input[name=ExportType]').trigger('click');
    });

    $('#updateArchiveOrder').click(function() {
        var orderIds = [];
        $('.selectAll:checked').each(function() {
            orderIds.push($(this).val());
        });
        $('#updateValue').find('input[name=orderIds]').val(JSON.stringify(orderIds));
        $('#updateValue').find('input[name=updateStatus]').trigger('click');
    });
});
