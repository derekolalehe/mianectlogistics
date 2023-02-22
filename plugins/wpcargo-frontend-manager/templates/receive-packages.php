<?php

global $wpdb;

$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$username = $current_user->user_login;

$roles = ( array ) $current_user->roles;
$role = $roles[0];	

if( isset( $_POST['receive_package'] ) ):

    $tkid = '';
    $msg = '';

    $results = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_status = 'publish' AND post_title = '" . $_POST['wpcfe_pk_tracking_number'] . "'");
    $status = $wpdb->get_results("SELECT meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'wpcargo_status' AND post_id = " . $results[0]->id);
    
    if( $results != null):

        if(  $status[0]->meta_value != 'Arrived in Barbados' ):

            $tkid = $results[0]->id;
            
            $wpdb->update( 
                $wpdb->prefix . "postmeta", 
                array( 
                    'meta_value' => 'Arrived in Barbados'
                ), 
                array( 'meta_key' => 'wpcargo_status', 'post_id' => $tkid ), 
                array( 
                    '%s'
                ), 
                array( 
                    '%s',
                    '%s' 
                ) 
            );

            
            $arr = $wpdb->get_results("SELECT meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'wpcargo_shipments_update' AND post_id = " . $tkid);
        
            $arrTrim = rtrim( $arr[0]->meta_value, '}' );

            $arrTrim .= 'i:5;a:6:{s:4:"date";s:10:"2020-08-16";s:4:"time";s:5:"12:40";s:8:"location";s:0:"Barbados";s:6:"status";s:17:"Arrived in Barbados";s:7:"remarks";s:0:"";s:12:"updated-name";s:12:"ebiblogadmin";}}";';
            
            $wpdb->update( 
                $wpdb->prefix . "postmeta", 
                array( 
                    'meta_value' => $arrTrim
                ), 
                array( 'meta_key' => 'wpcargo_shipments_update', 'post_id' => $tkid ), 
                array( 
                    '%s'
                ), 
                array( 
                    '%s',
                    '%s' 
                ) 
            );

            $msg = '<div class="alert alert-success" role="alert">'
            . 'Shipment Received!'
            . '</div>';
        
        else:

            $msg = '<div class="alert alert-warning" role="alert">'
            . 'Shipment Already Arrived In Barbados!'
            . '</div>';

        endif;
            
    else:
        $msg = '<div class="alert alert-danger" role="alert">'
        . 'Shipment Not Found!'
        . '</div>';
    endif;

endif;

?>


<form method="post" action="" class="receive-packages">
    <div id="tracking-number" class="row text-center">
        <div class="col-lg-2">        
        </div>
        <div class="col-lg-8">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa fa-codepen mr-3"></i>Scan Package
                    </div>
                </div> 
                <input type="text" class="form-control py-0" id="wpcfe_pk_tracking_number" 
                name="wpcfe_pk_tracking_number" value="" autofocus>
            </div>
            <div class="mt-5"></div>
            <button name="receive_package" type="submit" class="btn btn-success">Receive</button>    
        </div>
        <div class="col-lg-2">        
        </div>
    </div>
    <div class="my-5"></div>
    <?php echo $msg;?>
</form>

<script type="text/javascript">

jQuery(document).ready(function(){

    jQuery('#wpcfe_pk_tracking_number').click();

});

</script>