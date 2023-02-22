jQuery(document).ready(function(){

    jQuery('#wpcfe_pk_tracking_number').click();

    jQuery('#submit-registration').click(function(){

        jQuery.ajax({

            type: "POST",
            url: mianect_urls.ajaxurl,
            data: {
                action: 'register_client',
                fname: jQuery('#r-fname').val(),
                lname: jQuery('#r-lname').val(), 
                mi: jQuery('#r-mi').val(), 
                email: jQuery('#r-email').val(),
                dob: jQuery('#r-dob').val(), 
                uid: jQuery('#r-uid').val(),

            },
            success: function(data) {  
                                               
            },
            error: function(){                
                
            }
    
        });


    });

});