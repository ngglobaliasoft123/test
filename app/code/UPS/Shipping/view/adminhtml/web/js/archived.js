require([
    "jquery",
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm'
], function ($,modal, confirm) {
    $('.archived-order-grid-view').click(function() {
        var orderId = $(this).parent().find('input[name=orderId]').val();
        if ($(this).hasClass('no_archived_rows')) {
            $(this).prop("disabled", true);
        } else {
            $.ajax({
                showLoader: true,
                url: $("#urlorderdetail").val(),
                data: 'ajax=1&method=getDetailOrder&id='+orderId,
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                $('#orderID').html(data.result.increment_id);
                $('#dateTime').html(data.result.created_at);
                $('#customer').html(data.result.firstname + ' ' + data.result.lastname);
                $('#product').html(data.result.productName);
                $('#address').html(data.result.address);
                $('#phoneNumber').html(data.result.telephone);
                $('#email').html(data.result.email);
                (data.result.service_type == 'AP') ? typeService = 'AP' : typeService = 'Address';
                $('#shippingservice').html('To ' + typeService + ' (' +data.result.service_name_info + ')');
                $('#address1').html(data.result.address_ap);
                var accessorialService = data.result.accessorial_service_translate;
                var listAccessorial = '';
                $("#accessorial1").html('');
                $.each(accessorialService , function( key, value ) {
                    listAccessorial += value + '<br />';
                });
                if (data.result.method =='cashondelivery') {
                    if (data.result.service_type == 'AP') {
                        listAccessorial += $("#shiptoservicenameap").val() + '<br />';
                    } else {
                        listAccessorial += $("#shiptoservicenameadd").val() + '<br />';
                    }
                }
                $("#accessorial1").append(listAccessorial);
                $('#orderValue').html(data.result.currency_symbol+data.result.grand_total);
                $('#status').html($("#urlarchivedorder").val());
                $('#popup_detail_order').modal('openModal').removeClass('hidden');
            });
        }
    });

    $('#selectAll').click(function() {
        $('.selectAll').prop('checked', $(this).prop('checked'));
        if ($(this).prop('checked')) {
            if ($('.selectAll:checked').length > 0) {
                $("#updateUnArchiveOrder").prop('disabled', false);
            } else {
                $("#updateUnArchiveOrder").prop('disabled', true);
            }
        } else {
            $(".list-data-table>tbody>tr").each(function() {
                $(this).removeClass('selected');
            })
            $("#updateUnArchiveOrder").prop('disabled', true);
        }
    });

    $(".selectAll").click(function() {
        if ($('.selectAll:checked').length > 0) {
            $("#updateUnArchiveOrder").prop('disabled', false);
            if ($('.selectAll:checked').length === $('.selectAll').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        } else {
            $('#selectAll').prop('checked', false);
            $("#updateUnArchiveOrder").prop('disabled', true);
        }
    });

    $('#updateUnArchiveOrder').click(function() {
        var orderIds = [];
        $('.selectAll:checked').each(function() {
            orderIds.push($(this).val());
        });
        confirm({
            title: $("#unarchivingorder").val(),
            content: "<div style='margin:20px 0 20px 0'>" + $("#warningmessage").val() + "</div>",
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
                    $('#updateValue').find('input[name=orderIds]').val(JSON.stringify(orderIds));
                    $('#updateValue').find('input[name=updateUnArchivedStatus]').trigger('click');
                }
            }]
        });
    });
});
