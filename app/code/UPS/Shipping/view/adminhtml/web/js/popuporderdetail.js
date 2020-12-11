require([
    "jquery",
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: '<b style="opacity: 0;">.</b>',
        buttons: [{
            text: $("#okMessage").val(),
            class: 'primary',
            click: function () {
                this.closeModal();
            }
        }]
    };

    var popup = modal(options, $('#popup_detail_order'));
});
