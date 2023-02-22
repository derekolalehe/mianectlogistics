jQuery(document).ready(function(){

    if( jQuery('#pending-customers').get().length > 0 ){

        jQuery.ajax({

            type: "POST",
            url: mianect_urls.ajaxurl,
            data: {
                action: 'fetch_pending_customers',
            },
            success: function(data) {  
    
                jQuery('#pending-customers').DataTable({
                    pageLength: 25,
                    data: JSON.parse(data),
                    columns: [
                        { "data" : "first_name" },
                        { "data" : "last_name" }, 
                        { "data" : "middle_name" },
                        { "data" : "user_email" }, 
                        { "data" : "dob" },
                        { "data" : "uid" }, 
                        { "data" : "onboard_date" }, 
                        { "data" : "row_num",
                            "render": function(data, type, row){
                                return '<span class="onboard-action onboard-approve dashicons dashicons-yes" data-row="' + data + '"></span>' + 
                                '&nbsp;&nbsp;&nbsp;<span class="onboard-action onboard-reject dashicons dashicons-no" data-row="' + data + '"></span>';
                            }
                        },
                    ]
                });
                                
            },
            error: function(){                
                
            }
    
        });


    }

});