(function($){
    $(function(){

        let donateSimpleFormNameLast;
        let donateSimpleFormCompany;
        let donateSimpleFormEmail;
        let donateSimpleFormPrice;
        let donateSimpleFormGateway;
        let alertMsj = $(".donate-simple-form-alert");

        $("form#donate_simple_form_frontend").trigger("reset");

        $("input[name='donate-simple-form-price']").change( function() {

            donateSimpleFormPrice = $("input[name='donate-simple-form-price']").val();

            V.init( {
                apikey: donatesimpleform.apikey,
                paymentRequest:{
                    currencyCode: donatesimpleform.currency,
                    total: donateSimpleFormPrice
                },
                settings: {
                    locale: donatesimpleform.locale
                }
            });

            V.on("payment.success", function(payment)
            {

                let currencyCode = payment.vInitRequest.paymentRequest.currencyCode;

                $.ajax({
                    type: 'POST',
                    url:  donatesimpleform.ajaxurl,
                    data: {action: 'donate_simple_form_dvsmp', currencyCode: currencyCode, price: donateSimpleFormPrice, name: donateSimpleFormNameLast, company: donateSimpleFormCompany, email: donateSimpleFormEmail, payment: 'Visa Checkout'},
                    beforeSend: function(){
                        $('.v-checkout-wrapper').hide();
                    },
                    success: function(r){
                        $(alertMsj).show();
                        $(alertMsj).append("<span style='color:green;font-size:18px'>"+donatesimpleform.successMsj+"<br></span>");
                        $(alertMsj).addClass( "alert-success" );
                    }
                });

            });
            V.on("payment.cancel", function(payment)
            {
                $('.v-checkout-wrapper').hide();
                $('button[type="submit"]').prop('disabled', false);
                $(alertMsj).show();
                $(alertMsj).append("<span style='color:orange;font-size:18px'>"+donatesimpleform.cancelMsj+"<br></span>");
                $(alertMsj).addClass( "alert-info" );
            });
            V.on("payment.error", function(payment, error)
            {
                $('.v-checkout-wrapper').hide();
                $('button[type="submit"]').prop('disabled', false);
                $(alertMsj).show();
                $(alertMsj).append("<span style='color:red;font-size:18px'>"+donatesimpleform.errorMsj+"<br></span>");
                $(alertMsj).addClass( "alert-danger" );
            });

        });



        $('form#donate_simple_form_frontend').submit(function (e){
            e.preventDefault();

            let msg = '';

            $(alertMsj).empty(msg);
            $(alertMsj).hide();

            donateSimpleFormNameLast = $("input[name='donate-simple-form-name-last']").val();
            donateSimpleFormCompany = $("input[name='donate-simple-form-company']").val();
            donateSimpleFormEmail = $("input[name='donate-simple-form-email']").val();
            donateSimpleFormGateway = $("input[name='gateway-payment']:checked").val();

            if (!donatesimpleform.apikey && donateSimpleFormGateway == 'visa')
                msg += donatesimpleform.apikeyMsj;

            if (msg != ''){
                $(alertMsj).append("<span style='color:red;font-size:18px'>"+msg+"<br></span>");
                return;
            }


            $('button[type="submit"]').prop('disabled', true);


            if(donateSimpleFormGateway == 'visa'){

                $('.v-checkout-wrapper').show();
                $('.v-button').click()

            }else{
                $("input[name='amount']").val(donateSimpleFormPrice);
                $("input[name='buyerEmail']").val(donateSimpleFormEmail);
                $("input[name='extra1']").val(donateSimpleFormNameLast);
                $("input[name='extra2']").val(donateSimpleFormCompany);
                $("input[name='extra3']").val(donateSimpleFormEmail);
                let URLactual = window.location;
                $("input[name='responseUrl']").val(URLactual);
                $.ajax({
                    type: 'POST',
                    url:  donatesimpleform.ajaxurl,
                    data: {action: 'donate_simple_form_dvsmp', payment: 'payu', price: donateSimpleFormPrice},
                    dataType: 'json',
                    success: function(r){
                        $("input[name='referenceCode']").val(r.reference);
                        $("input[name='signature']").val(r.signature);
                        $("form#donate-simple-form-frontend-payu").submit();

                    }
                });
            }
            
        });

    });
})(jQuery);