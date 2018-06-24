(function($){
    $(function(){
     $('form#donate-simple-form-config').submit(function (e){
         e.preventDefault();
         $.ajax({
             type: 'POST',
             url:  ajaxurl,
             data: $(this).serialize() + '&action=donate_simple_form_dvsmp',
             beforeSend: function(){
                 $('body').css('cursor','wait');
                 $('input[type="submit"]').prop('disabled', true);
                 console.log('enviando datos');
             },
             success: function(r){
                 $('body').css('cursor','default');
                 $('input[type="submit"]').prop('disabled', false);
             }
         });
     });

    });
})(jQuery);