<?php

/**

* veriscope header file

*

*/

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

    <head>

        <meta charset="<?php bloginfo( 'charset' ); ?>" />

        <meta name='viewport' 

        content='width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=yes, initial-scale=1'/>

        <title><?php wp_title( '|', true, 'right' ); ?></title>

        <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,400;0,700;1,400;1,700&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"/> 
                
        <?php wp_head(); ?>        
    
    </head>



    <body <?php body_class(); ?>>
                  
    <div id="site-header">
    
        <?php             
    
            $custom_logo_id = get_theme_mod( 'custom_logo' );

            $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );            

        ?>

        <div id="header-top-bar"></div>

        <img id="mobile-menu-icon" src="<?php echo get_template_directory_uri() . "/assets/icons/mobile-menu-icon.png";?>"/>
        
        <a href="<?php echo home_url();?>">

            <img class="header-logo" alt="<?php echo get_bloginfo(); ?>" src="<?php echo esc_url( $logo[0] ); ?>"/>

        </a>

        <?php wp_nav_menu( array(

                    'menu' => 'main-menu', 

                    'container' => ''

                )

            ); 

        ?>                      
        
    </div>