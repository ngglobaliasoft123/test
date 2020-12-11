require([
    "jquery"
], function ($) {
    $(document).ready(function() {
        var showButton = $("#showbuttontitle").val();
        $("#accept_term_condition").click(function()
        {
            if (showButton) {
                var arrSelected = $('#accept_term_condition:checked');
                if ($('#accept_term_condition:checked').length) {
                    $("#button_save_term_condition").prop('disabled', false);
                } else {
                    $("#button_save_term_condition").prop('disabled', true);
                }
            }
        });

        $(document).on('click', '.show-popup-termcondition', function()
        {
            window.open('https://www.ups.com/assets/resources/media/ups-license-and-data-service-terms.pdf', '_blank');
        });
    });

    $("#btnPrint").on("click", function () {
        var popupContent = '';
        popupContent += '<img src="'+$('#image_print_1').attr('src')+'">';
        popupContent += '<h2>'+$('#title_print_1').html()+'</h2>';
        popupContent += '<div>'+$('#content_print_1').html()+'</div>';
        var printWindow = window.open('', '', 'height=800,width=1000,scrollbars=yes,resizable');
        printWindow.document.write('<html><head><title>'+$("#upstermcondition").val()+'</title>');
        printWindow.document.write('</head><body style="text-align: left;">');
        printWindow.document.write(popupContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
});
