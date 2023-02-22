<?php

global $wpdb;

$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$username = $current_user->user_login;

$roles = ( array ) $current_user->roles;
$role = $roles[0];	

$results = $wpdb->get_results("SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'registered_shipper' AND meta_value = '" . $user_id . "';");

$_shipment_titles = [];
$_tracking_nums = [];

foreach( $results as $result ):

    array_push( $_shipment_titles, $result->post_id );

endforeach;

$_shipment_titles_str = implode( ",", $_shipment_titles );

$results = $wpdb->get_results("SELECT post_title FROM " . $wpdb->prefix . "posts WHERE id IN (" . $_shipment_titles_str . ");");

foreach( $results as $result ):

    array_push( $_tracking_nums, $result->post_title );

endforeach;

if( isset( $_POST['update-tk-inv'] ) ):

    if( str_replace(" ", "", $_POST['wpcfe_new_tracking_number'] != '' ) ):

        $tk = $_POST['wpcfe_new_tracking_number'];

        $wpdb->update( 
            $wpdb->prefix . "posts", 
            array( 
                'post_title' => $_POST['wpcfe_new_tracking_number']
            ), 
            array( 'post_title' => $_POST['wpcfe_shipment_select'] ), 
            array( 
                '%s'
            ), 
            array( '%s' ) 
        );

        $invoice_files = scandir( $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username . "/" );

        for( $j=0;$j<sizeof( $invoice_files );$j++ ):

            if( explode( '.', $invoice_files[$j] )[0] == $_POST['wpcfe_shipment_select'] ):

                $_extension = explode( '.', $invoice_files[$j] )[1];

                rename( $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username . "/" . $_POST['wpcfe_shipment_select'] . '.' . $_extension, $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username . "/" . $_POST['wpcfe_new_tracking_number'] . '.' . $_extension );

            endif;

        endfor;

    else:

        $tk = $_POST['wpcfe_shipment_select'];

    endif;

endif;

?>

<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <label class="sr-only" for="wpcfe_shipment_title">New Tracking Number</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa fa-feed mr-3"></i>New Tracking Number
                    </div>
                </div> 
                <input type="text" class="form-control py-0" id="wpcfe_new_tracking_number" 
                name="wpcfe_new_tracking_number" value="">
            </div>            
            <div class="mt-5"></div>      
            <div class="text-center"><button name="add_tracking" type="submit" class="btn btn-success">Change</button></div>      
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa fa-barcode mr-3"></i>Select Shipment
                    </div>
                </div>
                <select style="display: inline !important;" class="form-control py-0" id="wpcfe_shipment_select" 
                name="wpcfe_shipment_select">
                    <?php

                        for ($i=0;$i<sizeof( $_tracking_nums );$i++ ):?>

                            <option value="<?php echo $_tracking_nums[$i];?>">
                                <?php echo $_tracking_nums[$i];?>
                            </option>

                        <?php endfor;

                    ?>
                </select> 
            </div>
        </div>
    </div>
</div>