(function($){

    $(function(){

        $('.fit-spokane-submit').click(function(e){

            e.preventDefault();
            var id = $(this).data('id');
            var value = $('#fit-spokane-price-'+id).val();

            for (var h=0; h<fit_spokane_handlers.length; h++) {
                if (fit_spokane_handlers[h].id == id) {
                    fit_spokane_handlers[h].handler.open({
                        name: $(this).data('name'),
                        description: $(this).data('description'),
                        amount: value,
                        currency: $(this).data('currency'),
                        billingAddress: true,
                        shippingAddress: true
                    });
                }
            }
        });

        $('.fit-spokane-alert').each(function(index){
            if (index == 0) {
                var id = $(this).data('id');
                $('#'+id).trigger('click');
            }
        });

        if (window.location.protocol != 'https:') {
            $('.fit-spokane-ssl-check').each(function(){
                $(this).addClass('alert').addClass('alert-danger').html($(this).data('if-error-show'));
            });
        }
    });

})(jQuery);
