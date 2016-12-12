(function($){

    $(function(){

        $('#fit-spokane-program-is-recurring').change(function(){
            fitSpokaneChangeRecurring();
        });

        $('#fit-spokane-upload-logo').click(function(e){

            e.preventDefault();

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Insert Images',
                button: {
                    text: 'Insert'
                },
                multiple: true  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {

                var selection = file_frame.state().get('selection');

                selection.map( function( attachment ) {

                    attachment = attachment.toJSON();
                    $('#fit-spokane-company-logo').html('<img src="' + attachment.url + '">');
                    $('#fit_spokane_company_logo').val(attachment.url);
                    $('#fit-spokane-remove-logo').show();

                });
            });

            // Finally, open the modal
            file_frame.open();
        });

        $('#fit-spokane-remove-logo').click(function(e){

            e.preventDefault();
            $('#fit-spokane-company-logo').html('');
            $('#fit_spokane_company_logo').val('');
            $('#fit-spokane-remove-logo').hide();

        });

    });

})(jQuery);

function fitSpokaneChangeRecurring()
{
    var tr = jQuery('#tr-recur-period');
    if ( jQuery('#fit-spokane-program-is-recurring').val() == '1' ){
        tr.show();
    } else {
        tr.hide();
    }
}
