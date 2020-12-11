require([
    "jquery",
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: $("#popup-termcondition-config-title").val(),
        buttons: [{
            text: $("#btn-ok").val(),
            class: 'primary',
            click: function () {
                this.closeModal();
            }
        }]
    };

    var popup = modal(options, $('#popup_termcondition_config'));
});
