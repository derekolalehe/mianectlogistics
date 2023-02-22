<?php

/**

* veri functions and definitions

*

* @package veri

*/

if ( ! function_exists( 'veri_setup' ) ) :

    function veri_setup() {

        /**

        * Add default posts and comments RSS feed links to head

        */

        add_theme_support( 'automatic-feed-links' );

        /**

        * Enable support for Post Thumbnails

        */

        add_theme_support( 'post-thumbnails' );

        add_theme_support( "title-tag" );
        
        add_theme_support( "custom-header", null );

        add_theme_support( "custom-background", null );

    }

endif;

add_action( 'after_setup_theme', 'veri_setup' );

/**

* Enqueue scripts and styles

*/

function veri_scripts_and_styles() {
   
    wp_enqueue_script('jquery','', false, true );

    //Make ajax url available on the front end
    $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
        
    $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
        'home_url' => home_url(),
        'theme_url' => get_template_directory_uri(),
        'plugins_url' => plugins_url(),
    );
    
    wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js?v=' . (string)microtime(), false, true );

    wp_localize_script( 'main', 'mianect_urls', $params ); 

    wp_enqueue_style( 'style', get_stylesheet_uri() . '?v=' . (string)microtime(), array() );   
    	

}

add_action( 'wp_enqueue_scripts', 'veri_scripts_and_styles' );  


function login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url('<?php echo get_template_directory_uri() . '/assets/icons/main-logo.png';?>');
		    height:191px;
            width:200px;
            background-size: 320px 132px;
            background-repeat: no-repeat;
        	padding-bottom: 30px;
        }
    </style>
<?php }

add_action( 'login_enqueue_scripts', 'login_logo' );


/*For Admin*/

function veri_admin_scripts_and_styles() {   

    wp_enqueue_script('jquery','', false, true );

    //Make ajax url available on the front end
    $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
        
    $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
        'home_url' => home_url(),
        'theme_url' => get_template_directory_uri(),
        'plugins_url' => plugins_url(),
    );
    
    wp_enqueue_script( 'admin', get_template_directory_uri() . '/assets/js/admin.js?v=' . (string)microtime(), false, true );

    wp_localize_script( 'admin', 'mianect_urls', $params ); 

    wp_enqueue_style( 'admin-style', get_template_directory_uri() . '/assets/css/admin.css?v=' . (string)microtime(), array() );  
    
    wp_enqueue_style( 'data-tables-css', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
    wp_enqueue_script( 'data-tables-js', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', false, false );
        
}

add_action( 'admin_enqueue_scripts', 'veri_admin_scripts_and_styles' );



//Enable Custom Logo

add_theme_support( 'custom-logo' );


function themename_custom_logo_setup() {

    $defaults = array(

        'flex-height' => true,

        'flex-width'  => true

    );

    add_theme_support( 'custom-logo', $defaults );


}

add_action( 'after_setup_theme', 'themename_custom_logo_setup' );


//Enqueue the Dashicons script

add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );

function load_dashicons_front_end() {

    wp_enqueue_style( 'dashicons' );

}


/**

* Register menu locations

*/

register_nav_menus( array(

    'primary' => __( 'Primary Menu', 'veri' ),

) );


/**
* Custom Post Types Columns
*/

/*Partners*/
function veri_partners_custom_columns( $cols ) {
    $cols = array(
        'cb' => '<input type="checkbox" />',        
        'title' => __( 'Title', 'veri' ),
        'desc' => __( 'Partner Description', 'veri' ), 
        'active' => __( 'Active', 'veri' ),
        'logo' => __( 'Logo', 'veri' )
    );
    return $cols;
}

add_filter( "manage_veri_partners_posts_columns", "veri_partners_custom_columns" );

function veri_partners_custom_column_content( $column, $post_id ) {
    switch ( $column ) {
        case "desc":
        echo get_post_meta( $post_id, 'veri_partner_description', true);
        break;
        case "active":
        echo get_post_meta( $post_id, 'veri_partner_active', true);
        break;
        case "logo":
        get_post_meta( $post_id, the_post_thumbnail('thumbnail',''), true);
        break;
    }
}

add_action( "manage_veri_partners_posts_custom_column", "veri_partners_custom_column_content", 10, 2 );



/**
* Custom Post Types Metaboxes
*/

/*Patners*/

function veri_partners_meta_box () {
    add_meta_box (
        'veri_partners_meta',
        __('Partner Details', 'veri'),
        'veri_partners_meta_fields',
        'veri_partners',
        'normal',
        'core'
    );
}

add_action ('add_meta_boxes', 'veri_partners_meta_box');

function veri_partners_meta_fields ( $post ) {
        // Use nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'veri_partners_meta_noncename' );
        $active = get_post_meta( $post->ID, 'veri_partner_active', true );
        $desc = get_post_meta( $post->ID, 'veri_partner_description', true );
        
    ?>        
        <label for="veri_partner_description">Description</label><br />
        <textarea style="width: 100%; height: 150px;" name="veri_partner_description" 
        id="veri_partner_description"><?php echo $desc; ?></textarea><br/><br/>
        
        <label for="veri_partner_active">Active</label><br />
        <select name="veri_partner_active" id="veri_partner_active">
            <option <?php echo ( $active == 0 ? "selected" : "" ); ?>>0</option>
            <option <?php echo ( $active == 1 ? "selected" : "" ); ?>>1</option>
        </select>

    <?php
}

function veri_partner_meta_save ( $post_id ) {
    
    // verify if this is an auto save routine.
    // If it is the post has not been updated, so we don't want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    // verify this came from the screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['veri_partners_meta_noncename'] ) || !wp_verify_nonce( $_POST['veri_partners_meta_noncename'], plugin_basename( __FILE__ ) ) ) {
        return $post_id;
    }

    // Get the post type object.
    global $post;
    $post_type = get_post_type_object( $post->post_type );

    // Check if the current user has permission to edit the post.
    if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
        return $post_id;
    }

    // Get the posted data and pass it into an associative array for ease of entry
    $metadata['veri_partner_active'] = ( isset( $_POST['veri_partner_active'] ) ? $_POST['veri_partner_active'] : '' );
    $metadata['veri_partner_description'] = ( isset( $_POST['veri_partner_description'] ) ? $_POST['veri_partner_description'] : '' );
    
    // add/update record (both are taken care of by update_post_meta)
    foreach( $metadata as $key => $value ) {
        // get current meta value
        $current_value = get_post_meta( $post_id, $key, true);
        if ( $value && '' == $current_value ) {
            add_post_meta( $post_id, $key, $value, true );
        } elseif ( $value && $value != $current_value ) {
            update_post_meta( $post_id, $key, $value );
        } elseif ( '' == $value && $current_value ) {
            delete_post_meta( $post_id, $key, $current_value );
        }
    }
}

add_action ('save_post', 'veri_partner_meta_save');


/**

* Shortcodes

*/

function veri_setup_shortcodes () {

    add_shortcode('veri_heading', 'veri_heading_shortcode');

    add_shortcode('veri_button', 'veri_button_shortcode');

    add_shortcode('veri_submit_button', 'veri_submit_button_shortcode');

    add_shortcode('veri_reg_form', 'veri_registration_form_shortcode');

    add_shortcode('veri_contact_form', 'veri_contact_form_shortcode');

    add_shortcode('veri_icon_box', 'veri_icon_box_shortcode');

}

add_action( 'init', 'veri_setup_shortcodes' );


/*Heading*/

function veri_heading_shortcode($atts) {

    extract(shortcode_atts( array(

            'h_color' => "#333",
            's_color' => "#333",
            'b_color' => "#F2F2F2",
            'heading' => "heading",
            'sub_heading' => "sub heading"

            ), $atts ));

    $output =   '<div class="veri-heading-container">' . 
                    '<h1 style="color: ' . $b_color . ';">' . $heading . '</h1>' .
                    '<p style="color: ' . $h_color . ';">' . $heading . '</p>' .
                    '<h2 style="color: ' . $s_color . ';">' . $sub_heading . '</h2>' .
                '</div>';

    return $output;


}


/*Icon Box*/

function veri_icon_box_shortcode($atts) {

    extract(shortcode_atts( array(

            'color' => "#FFF",
            'bg_color' => "transparent",
            'icon' => "call",
            'heading' => "heading",
            'text' => 'text',
            'style' => 'v1'

            ), $atts ));

    $output = '';

    if ( $style == "v1" ):

        $output =   '<div class="veri-icon-box icon-box-v1" style="color: ' . $color . '; background-color: ' . $bg_color . 
                        ';">' .
                        '<img alt="icon" src="' . get_template_directory_uri() . '/assets/icons/icon-' .
                        $icon . '.png"/>' .
                        '<h6>' . $heading . '</h6>' .
                        '<p>' . $text . '</p>' .
                    '</div>';
    elseif ( $style == "v2" ):

        $output =   '<div class="veri-icon-box icon-box-v2" style="color: ' . $color . ';">' .
                        '<table><tr>' .
                        '<td><div style="background-color: ' . $bg_color . ';"><img alt="icon" src="' . get_template_directory_uri() . '/assets/icons/icon-' .
                        $icon . '.png"/></div></td>' .
                        '<td><h5>' . $heading . '</h5></td>' .
                        '</table></tr>' .
                        '<p>' . $text . '</p>' .
                        '<div class="icon-box-v2-accent"></div>' .
                    '</div>';

    endif;

    return $output;


}


/*Button*/

function veri_button_shortcode($atts) {
   
    extract(shortcode_atts( array(

        'text'  => "button",
        'icon' => 'submit',
        'bg_color' => '#00032E',
        'icon_bg_color' => '#000333',
        "txt_color" => "#FFF",        
        'text_align' => 'left',
        "path" => "#"

        ), $atts ));

    $output =   '<div class="veri-submit-button-container" style="text-align: ' . $text_align . ';">' .
                    '<div style="background-color: ' . $bg_color . ';"><img alt="submit" src="' . get_template_directory_uri() . '/assets/icons/icon-' . 
                    $icon . '.png"/></div>' . 
                    '<a href="' . $path . '" style="color: ' . $txt_color . '; background-color: ' . $bg_color . '">' . $text . 
                    '</a>' .
                '</div>';

    return $output;

}


/*Submit Button*/

function veri_submit_button_shortcode($atts) {
   
    extract(shortcode_atts( array(

            'text'  => "button",
            'icon' => 'submit',
            'bg_color' => '#00032E',
            'icon_bg_color' => '#000333',
            "txt_color" => "#FFF",
            "id" => "",

            ), $atts ));

    $output =   '<div class="veri-submit-button-container">' .
                    '<div style="background-color: ' . $icon_bg_color . ';"><img alt="submit" src="' . get_template_directory_uri() . '/assets/icons/icon-' . 
                    $icon . '.png"/></div>' . 
                    '<button id="' . $id . '" style="color: ' . $txt_color . '; background-color: ' . $bg_color . '">' . $text . 
                    '</button>' .
                '</div>';

    return $output;


}


/*Registration Form*/
function veri_registration_form_shortcode($atts) {

    $button = do_shortcode( '[veri_submit_button id="submit-registration" bg_color="#00032E" txt_color="#FFF" text="submit" icon="submit"]' );

    $output =   '<div id="veri-reg-form-container">' . 
                    '<h4>get a mianect mailbox</h4>' .
                    '<div class="field-container"><h6>first name</h6><input required type="text" id="r-fname" name="r-fname" maxlength="30"/></div>' . 
                    '<div class="field-container"><h6>middle initial</h6><input type="text" id="r-mi" name="r-mi" maxlength="1"/></div>' .
                    '<div class="field-container"><h6>last name</h6><input required type="text" id="r-lname" name="r-lname" maxlength="30"/></div>' .
                    '<div class="field-container"><h6>email address</h6><input required type="text" id="r-email" name="r-email"/></div>' .
                    '<div class="field-container"><h6>date of birth</h6><input required type="date" id="r-dob" name="r-dob"/></div>' .
                    '<div class="field-container"><h6>user id (existing)</h6><input pattern="(\w{2,3}\d{4,5})|(\s*)" type="text" id="r-uid" name="r-uid" maxlength="9"/></div>' .
                    '<div class="field-container"><h6>consent form</h6><input pattern="(\w{2,3}\d{4,5})|(\s*)" type="text" id="r-uid" name="r-uid" maxlength="9"/></div>' .
                    '<a id="ccpl-link" target="_blank" href="' . get_template_directory_uri() . 
                    '/assets/documents/PERMISSION_LETTER_CARGO_CLEARANCE.pdf"><img alt="cargo clearance permission" ' . 
                    'src="' . home_url() . '/wp-content/uploads/2020/10/CCPL.png"/></a>' .
                    $button .
                '</div>' ;

    echo $output;

}

/*Contact Form*/
function veri_contact_form_shortcode($atts) {

    $button = do_shortcode( '[veri_submit_button bg_color="#00032E" txt_color="#FFF" text="send" icon="send"]' );

    $output =   '<div id="veri-contact-form-container">' . 
                    '<h2>get in touch</h2>' .
                    '<form method="POST" action="' . get_template_directory_uri() . '/includes/contact.php">' .
                        '<div class="field-container"><input placeholder="name" type="text" name="c-name"/></div>' .
                        '<div class="field-container"><input placeholder="email" type="text" name="c-email"/></div>' .                         
                        '<div class="field-container"><input placeholder="subject" type="text" name="c-subject"/></div>' .
                        '<div class="field-container"><textarea class="field-container" placeholder="message" name="c-msg" maxlength=250></textarea></div>' .
                        $button .
                    '</form>' .
                '</div>' ;

    echo $output;

}


/**

 * Widget Areas

 *

*/

function footer_area_1_init() {



	register_sidebar( array(

		'name'          => 'Footer Area 1',

		'id'            => 'footer_area_1',

        'before_widget' => '',

        'after_widget' => ''

	) );



}

add_action( 'widgets_init', 'footer_area_1_init' );



function footer_area_2_init() {



	register_sidebar( array(

		'name'          => 'Footer Area 2',

		'id'            => 'footer_area_2',

        'before_widget' => '',

        'after_widget' => ''

	) );



}

add_action( 'widgets_init', 'footer_area_2_init' );



function footer_area_3_init() {



	register_sidebar( array(

		'name'          => 'Footer Area 3',

		'id'            => 'footer_area_3',

        'before_widget' => '',

        'after_widget' => ''

	) );



}

add_action( 'widgets_init', 'footer_area_3_init' );



/**

* Sidebars

*

**/

function veri_sidebars() {

    

    $args = array(

    'name' => __( 'Sidebar Right', 'veri' ),

    'id' => 'sidebar-right',

    'before_widget' => '<section class="widget">',

    'after_widget' => '</section>',

    'before_title' => '<h2 class="widget-title">',

    'after_title' => '</h2>'

    );

    register_sidebar($args);

}

add_action( 'widgets_init', 'veri_sidebars' );

// function meks_which_template_is_loaded() {
// 	global $wp_roles;

// $all_roles = $wp_roles->roles;
// $editable_roles = apply_filters('editable_roles', $all_roles);

// print_r( $editable_roles );
// }
 
// add_action( 'wp_footer', 'meks_which_template_is_loaded' );

// function meks_which_template_is_loaded() {
// 	//if ( is_super_admin() ) {
//     global $template;
   
//     print_r( $template );
// }
 
// add_action( 'wp_footer', 'meks_which_template_is_loaded' );

require_once( 'includes/methods.php' );

require_once( 'includes/admin-menus.php' );

add_action('wp_ajax_fetch_pending_customers', 'fetch_pending_customers');
add_action('wp_ajax_nopriv_fetch_pending_customers', 'fetch_pending_customers');

add_action('wp_ajax_register_client', 'register_client');
add_action('wp_ajax_nopriv_register_client', 'register_client');

?>