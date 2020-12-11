require([
    "jquery",
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: $("#termconditionTitle").val(),
        buttons: [{
            text: $("#okMessage").val(),
            class: 'primary',
            click: function () {
                this.closeModal();
            }
        }]
    };

    var popup = modal(options, $('#term_condition_popup'));
});
