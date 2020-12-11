require([
    "jquery",
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    var globalData = [];
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: '<b>' + $("#processshipmentmessage").val() + '</b>',
        buttons: [{
            text: $("#createshipmentmessage").val(),
            class: 'createbathshipment primary paddingbutton',
            click: function (event) {
                $("#messageBatch").html('');
                $.ajax({
                    showLoader: true,
                    url: $("#urlbathshipment").val(),
                    data: {
                        'shipfrom' : JSON.stringify(ShipFrom),
                        'idorder' : JSON.stringify(listOrder)
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    $('.action-close').attr('id', 'closeBatch');
                    globalData = data.listOrderFalse;
                    $("#listOrders").html('');
                    listOrder = [];
                    var countNo = 1;
                    check = true;
                    $.each(globalData, function(key, value) {
                        if (value[2] == 'false') {
                            $("#listOrders").append(countNo + '. ' + value[0] + ' &#10006;' + ' ' + value[3] + '&#13;&#10;');
                            listOrder.push(value[1]);
                            check = false;
                        } else {
                            $("#listOrders").append(countNo + '. ' + value[0] + ' &#10004;' + '&#13;&#10;');
                        }
                        countNo++;
                    });
                    $(".createbathshipment").attr("disabled", true);
                });
            }
        }]
    };

    $(document).ready(function() {
        $(document).on('click', '#closeBatch', function() {
            window.location = $("#urlorder").val();
        });
    });

    var ShipFrom = [];
    var listOrder = [];
    var popup = modal(options, $('#popup_create_batch_shipment'));

    $(document).on('click', '#create_batch_shipment', function() {
        $("#messages").remove();
        getInfoAccountBatch($("#createBatchAccount").val());
        listOrder = [];
        $('.selectAll:checked').each(function() {
            listOrder.push($(this).val());
        });
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
            var optionOrderID = '';
            var countNo = 1;
            $.map(dataDetail, function(value, index) {
                if (typeof value['increment_id'] !== "undefined") {
                    optionOrderID += countNo + '. ' + value['increment_id'] + '&#13;&#10;';
                    countNo++;
                }
            });
            $('#listOrders').html(optionOrderID);
            $('#popup_create_batch_shipment').modal('openModal').removeClass('hidden');
        });
    });

    $("#createBatchAccount").on("change", function() {
        if (globalData.length > 0) {
            listOrder = [];
            var countNo = 1;
            $("#listOrders").html('');
            $.each(globalData, function(key, value) {
                if (value[2] == 'false') {
                    $(".createbathshipment").attr("disabled", false);
                    $("#listOrders").append(countNo + '. ' + value[0] + '&#13;&#10;');
                    listOrder.push(value[1]);
                    countNo++;
                }
            });
        }
        getInfoAccountBatch($(this).val());
    });

    function getInfoAccountBatch(id) {
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
                name = dataDetail.ups_account_name;
            } else {
                name = dataDetail.fullname;
            }
            var sringReplace = dataDetail.MergeAddress.replace(/\,/g, ' <br />');

            ShipFrom = [];
            ShipFrom.push(name, dataDetail.company, dataDetail.ups_account_number,
            dataDetail.phone_number, dataDetail.address_1, dataDetail.address_2, dataDetail.address_3,
            dataDetail.city, dataDetail.post_code, dataDetail.Macountry, dataDetail.state_province_code);
        });
    }
});
