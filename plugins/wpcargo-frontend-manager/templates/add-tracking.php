<?php

global $wpdb;

$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$userfname = $current_user->user_firstname;
$userlname = $current_user->user_lastname;
$useremail = $current_user->user_email;
$userID = $current_user->ID;

$roles = ( array ) $current_user->roles;
$role = $roles[0];	

$results = $wpdb->get_results("SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'registered_shipper' AND meta_value = '" . $user_id . "';");

$_shipment_titles = [];
$_tracking_nums = [];

foreach( $results as $result ):

    array_push( $_shipment_titles, $result->post_id );

endforeach;

$_shipment_titles_str = implode( "','", $_shipment_titles );

$results = $wpdb->get_results("SELECT post_title FROM " . $wpdb->prefix . "posts WHERE id IN (" . $_shipment_titles_str . ");");

foreach( $results as $result ):

    array_push( $_tracking_nums, $result->post_title );

endforeach;

if( isset( $_POST['add-tracking'] ) ):

    if( str_replace(" ", "", $_POST['wpcfe_new_tracking_number'] != '' ) ):

        $tk = $_POST['wpcfe_new_tracking_number'];

        $tk_exists = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . 
        "tracking_staging WHERE tracking_number = '" . $tk . "'");

        $shipment_created = $wpdb->get_var( "SELECT shipment_created FROM " . $wpdb->prefix . 
        "tracking_staging WHERE tracking_number = '" . $tk . "'" );

        if( intval($tk_exists) <= 0 )://Tracking# not yet entered
            if( $role == "administrator" ):
                $wpdb->insert( 
                    $wpdb->prefix . "tracking_staging", 
                    array( 
                        'tracking_number' => $tk
                    ), 
                    array( 
                        '%s'
                    )
                );
                //Send tk added awaiting client's confirmation
            elseif( $role == "wpcargo_client" ):
                $wpdb->insert( 
                    $wpdb->prefix . "tracking_staging", 
                    array( 
                        'tracking_number' => $tk,
                        'client' => $username,
                        'client_id' => $userID
                    ), 
                    array( 
                        '%s',
                        '%s',
                        '%s'
                    )
                );
                //Send tk added awaiting shipper's confirmation
            endif;
        elseif( intval($tk_exists) > 0 )://Tracking# already entered
            if( $role == "administrator" ):
                if( $shipment_created == 0 ):
                    //Update Shipment Created Status
                    $wpdb->update( 
                        $wpdb->prefix . "tracking_staging", 
                        array( 
                            'shipment_created' => 1
                        ), 
                        array( 
                            'tracking_number' => $tk
                        ),
                        array( 
                            '%d'
                        ),
                        array( 
                            '%s'
                        )
                    );
                    //Create Shipment
                    $uid = $wpdb->get_var( "SELECT client_id FROM " . $wpdb->prefix . "tracking_staging WHERE tracking_number = '" . $tk . "'" );
                    
                    $_user = get_user_by( 'ID', $uid );
                    $_userfname = $_user->user_firstname;
                    $_userlname = $_user->user_lastname;
                    $_useremail = $_user->user_email;
                    createShipment($tk, $_userfname, $_userlname, $_useremail, $uid);                    
                else:
                    //Send tk exists processing already started message
                endif;
            elseif( $role == "wpcargo_client" ):
                if( $shipment_created == 0 ):
                    //Update Shipment Created Status
                    $wpdb->update( 
                        $wpdb->prefix . "tracking_staging", 
                        array( 
                            'shipment_created' => 1,
                            'client' => $username,
                            'client_id' => $userID
                        ), 
                        array( 
                            'tracking_number' => $tk
                        ),
                        array( 
                            '%d',
                            '%s',
                            '%s'
                        ),
                        array( 
                            '%s'
                        )
                    );
                    //Create Shipment
                    createShipment($tk, $userfname, $userlname, $useremail, $userID);
                    //Send tk exists processing started
                else:
                    //Send tk exists processing already started message
                endif;
            endif;
        endif;

    else:

        //$tk = $_POST['wpcfe_shipment_select'];

    endif;

endif;

function createShipment($tk, $userfname, $userlname, $useremail, $uid){

    $post_id = wp_insert_post( array(
        'post_author' => 8,
        'post_title' => $tk,
        'post_name' => $tk,
        'post_status' => 'publish',
        'post_type' => "wpcargo_shipment",
    ) );

    update_post_meta( $post_id, 'wpcargo_status', 'Received in Miami' );
    update_post_meta( $post_id, 'wpcargo_shipper_name', 'MIANECT' );
    update_post_meta( $post_id, 'wpcargo_shipper_phone', '246-230-5115' );
    update_post_meta( $post_id, 'wpcargo_shipper_address', 'Miami' );
    update_post_meta( $post_id, 'wpcargo_receiver_name', 'MIANECT' );
    update_post_meta( $post_id, 'wpcargo_receiver_phone', '246-230-5115' );
    update_post_meta( $post_id, 'wpcargo_receiver_address', 'Miami' );
    update_post_meta( $post_id, 'wpcargo_receiver_name', $userfname . " " . $userlname );
    //update_post_meta( $post_id, 'wpcargo_receiver_phone', '246-230-5115' );
    update_post_meta( $post_id, 'wpcargo_receiver_email', $useremail );
    update_post_meta( $post_id, 'wpcargo_type_of_shipment', 'International Shipping' );
    update_post_meta( $post_id, 'wpcargo_courier', 'MIANECT' );
    update_post_meta( $post_id, 'wpcargo_origin_field', 'Miami, USA' );
    update_post_meta( $post_id, 'wpcargo_destination', 'Barbados' );
    update_post_meta( $post_id, 'registered_shipper', $uid );

}

?>

<div class="container">
    <form method="post" action="">
        <div class="row">            
            <div class="col-lg-3"></div>
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
                <div class="text-center"><button name="add-tracking" type="submit" class="btn btn-success">Add</button></div>    
            </div>
            <div class="col-lg-3"></div>              
        </div>
    </form>
</div>