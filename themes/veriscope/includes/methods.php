<?php

function fetch_pending_customers(){

    global $wpdb;

    $results = $wpdb->get_results(
        "SELECT * FROM " . $wpdb->prefix . 'onboard_existing_users'
    );

    echo json_encode( $results );

    wp_die();

}

function register_client(){

    global $wpdb;

    $dobArr = explode( '-', $_POST['dob']);

    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];
    $middle_name = $_POST['mi'];
    $user_login = $first_name[0] . $middle_name . $last_name[0] . $dobArr[1] . $dobArr[2];
    $user_email = $_POST['email'];
    $uid = str_replace( ' ', '', $_POST['uid'] );

    if( $uid != '' && $uid != null ):

        //Store all info in pending db
        $wpdb->replace( 
            $wpdb->prefix . 'onboard_existing_users' , 
            array( 
                'first_name' => $first_name,
                'last_name' => $last_name, 
                'middle_name' => $middle_name, 
                'user_email' => $user_email,
                'dob' => $_POST['dob'], 
                'uid' => $uid,
                'onboard_date' => date('Y-m-d'),
            ), 
            array( 
                '%s',
                '%s', 
                '%s', 
                '%s',
                '%s', 
                '%s',
                '%s',
            ) 
        );

        //Send email for admins to check
        $message =  'Account verification required for ' . $first_name . ' ' . $last_name .
                    '( ' . $uid . ' ). ' . '<a href="">Click here to review account</a>';

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: MIANECT LOGISTICS <admin@mianectlogistics.com>';
        $headers[] = 'Cc: Russell Jones <russell@mianectlogistics.com>';

        wp_mail( 'gloria@mianectlogistics.com', 'Existing User Account Approval Request - ' . $uid, $message, $headers );

        //page in admin to approve and create user as below
        //delete from pending db
    else:
        $args = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_login' => $user_login,
            'user_email' => $user_email,
            'cjl_birthday' => $dobArr[2],
            'cjl_birthmonth' => str_replace( '0', "", $dobArr[1] ),
            'user_pass' => wp_generate_password(8),
            'role' => 'wpcargo_client'
        );

        $result = wp_insert_user( $args );

        wp_new_user_notification( $result );
    endif;

    wp_die();

}

?>