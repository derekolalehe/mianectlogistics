<?php include('header.php'); ?>
<?php
global $wpcargo, $WPCCF_Fields, $wpcargo_print_admin;
$receiver_id = '';
$user_info          = wp_get_current_user();
$class_not_logged   = 'not-logged';
$wpcfesort_list     = array( 10, 25, 50, 100 );
$wpcfesort          = isset( $_GET['wpcfesort'] ) && in_array( $_GET['wpcfesort'], $wpcfesort_list ) ? (int)$_GET['wpcfesort'] : 10 ;
$page_url           = get_the_permalink( wpcfe_admin_page() );
$p0 = '';
if( is_user_logged_in() ){
	require_once( wpcfe_include_template( 'navigation.tpl' ) );
    $class_not_logged  = '';
}
if( isset( $_GET['wpcfe'] ) && $_GET['wpcfe'] == 'update' ){
	$p0 = 'p-0';
}
?>
<!--Main layout-->
<main class="pt-5 mx-lg-5 <?php echo $class_not_logged; ?>">
    <div id="content-container" class="container-fluid my-5 <?php echo $p0; ?>">
        <?php do_action( 'wpcfe_dashboard_before_content', get_the_id() ); ?>
        <?php
        if( !is_user_logged_in() ){
            $redirect_to = get_the_permalink( get_the_id() );
            include_once('login.php');
            require_once( WPCFE_PATH.'templates/registration.tpl.php');
        }elseif( !can_wpcfe_access_dashboard() ){
			?>
			<div class="col-md-12 text-center">
				<section class="card">
					<div class="card-body">    
						<?php require_once( WPCFE_PATH.'templates/restricted.tpl.php'); ?>
					</div>
				</section>
			</div>
			<?php
        }else{
            if( $post->ID == wpcfe_admin_page() ){
                do_action( 'wpcfe_before_admin_page_load' );
                if( isset( $_GET['wpcfe'] ) && $_GET['wpcfe'] == 'track' && isset( $_GET['num'] ) ){
                    $shipment_id = wpcfe_shipment_id( $_GET['num'] );
                    if( $shipment_id && is_user_shipment( $shipment_id ) ){
                        $shipment_detail                = new stdClass;
                        $shipment_detail->ID            = $shipment_id;
                        $shipment_detail->post_title    = get_the_title( $shipment_id );
                        $template_path = apply_filters( 'wpcfe_track_shipment_template', WPCFE_PATH.'templates/track-shipment.php', $shipment_id, $WPCCF_Fields );
                    }else{
                         $template_path = WPCFE_PATH.'templates/no-shipment.php';
                    }          
                    require_once( $template_path );
                //<!-- TW Custom: 7/12/2020 -->
                }elseif( isset( $_GET['wpcfe'] ) && $_GET['wpcfe'] == 'upltrack' ){
                    $template_path = apply_filters( 'wpcfe_add_shipment_template', WPCFE_PATH.'templates/add-tracking.php' );
                    require_once( $template_path );
                }elseif( isset( $_GET['wpcfe'] ) && $_GET['wpcfe'] == 'edittrack' ){
                    $template_path = apply_filters( 'wpcfe_add_shipment_template', WPCFE_PATH.'templates/edit-tracking.php' );
                    require_once( $template_path );
                }elseif( isset( $_GET['wpcfe'] ) && $_GET['wpcfe'] == 'uplinv' ){
                    $template_path = apply_filters( 'wpcfe_add_shipment_template', WPCFE_PATH.'templates/add-invoice.php' );
                    require_once( $template_path );
                }elseif( isset( $_GET['wpcfe'] ) && $_GET['wpcfe'] == 'pkgreceive' ){
                    $template_path = apply_filters( 'wpcfe_add_shipment_template', WPCFE_PATH.'templates/receive-packages.php' );
                    require_once( $template_path );
                //<!-- -->
                }elseif( isset( $_GET['wpcfe'] ) && $_GET['wpcfe'] == 'add' && !wpcfe_add_shipment_deactivated() && can_wpcfe_add_shipment() ){        
                    $template_path = apply_filters( 'wpcfe_add_shipment_template', WPCFE_PATH.'templates/add-shipment.php' );
                    require_once( $template_path );
                }elseif( isset( $_GET['wpcfe'] ) &&  $_GET['wpcfe'] == 'update' && isset( $_GET['id'] ) && is_wpcfe_shipment( $_GET['id'] ) && can_wpcfe_update_shipment() && is_user_shipment( (int)$_GET['id'] ) ){
                    $shipment_id = (int)$_GET['id'];
                    $template_path = apply_filters( 'wpcfe_update_shipment_template', WPCFE_PATH.'templates/update-shipment.php', $shipment_id, $WPCCF_Fields );
                    require_once( $template_path );
                }elseif( isset( $_GET['wpcfe-print'] ) && is_wpcfe_shipment( $_GET['wpcfe-print'] ) && can_wpcfe_update_shipment() && is_user_shipment( (int)$_GET['wpcfe-print'] ) ){
                    $shipment_id 	        = $_GET['wpcfe-print'];
                    $shipmentID             = $shipment_id;
                    $custom_template_path   = get_stylesheet_directory().'/wpcargo/waybill.tpl.php';
                    $mp_settings 		= get_option('wpc_mp_settings');
                    $setting_options 	= get_option('wpcargo_option_settings');
                    $packages 			= maybe_unserialize( get_post_meta( $shipmentID,'wpc-multiple-package', TRUE) );
                    $logo 				= '';
                    if( !empty( $setting_options['settings_shipment_ship_logo'] ) ){
                        $logo 		= '<img style="width: 180px;" src="'.$setting_options['settings_shipment_ship_logo'].'">';
                    }
                    if( get_option('wpcargo_label_header') ){
                        $siteInfo = get_option('wpcargo_label_header');
                    }else{
                        $siteInfo  = $logo;
                        $siteInfo .= '<h2 style="margin:0;padding:0;">'.get_bloginfo('name').'</h2>';
                        $siteInfo .= '<p style="margin:0;padding:0;font-size: 14px;">'.get_bloginfo('description').'</p>';
                        $siteInfo .= '<p style="margin:0;padding:0;font-size: 10px;">'.get_bloginfo('wpurl').'</p>';
                    }
                    $shipmentDetails 	= array(
                        'shipmentID'	=> $shipment_id,
                        'barcode'		=> $wpcargo->barcode( $shipment_id ),
                        'packageSettings'	=> $mp_settings,
                        'cargoSettings'	=> $setting_options,
                        'packages'		=> $packages,
                        'logo'			=> $logo,
                        'siteInfo'		=> $siteInfo
                    );
                    if( file_exists( $custom_template_path ) ){
                        $template_path = $custom_template_path;
                    }else{
                        $template_path  = apply_filters( 'label_template_url', $wpcargo_print_admin->print_label_template_callback(), $shipmentDetails );
                    }
                    ?>
                    <script type="text/javascript">
                        function wpcargo_print(wpcargo_class) {
                            var printContents = document.getElementById(wpcargo_class).innerHTML;
                            var originalContents = document.body.innerHTML;
                            document.body.innerHTML = printContents;
                            window.print();
                            document.body.innerHTML = originalContents;
                            location.reload(true);
                        }
                    </script>
                    <div id="actions" style="margin-bottom: 12px; text-align: right;">
                        <a href="#" class="btn btn-primary btn-sm print" onclick="wpcargo_print('print-label')"><i class="fa fa-print text-white"></i> <?php esc_html_e('Print Label', 'wpcargo-frontend-manager'); ?></a>
                        <a href="<?php echo get_the_permalink(); ?>?wpcfe-waybill=<?php echo $shipment_id; ?>" class="btn btn-secondary btn-sm"><i class="fa fa-file-pdf text-white"></i> <?php esc_html_e('Download File', 'wpcargo-frontend-manager'); ?></a>
                    </div>  
                    <div id="print-label" style="background-color:#fff;">
                        <style type="text/css">
                            div.copy-section {
                                border: 2px solid #000;
                                margin-bottom: 18px;
                            }
                            .copy-section table {
                                border-collapse: collapse;
                            }
                            .copy-section table td.align-center{
                                text-align: center;
                            }
                            .copy-section table td {
                                border: 1px solid #000;
                            }
                            table tr td{
                                padding:6px;
                            }
                            @media screen, print{
                            }
                        </style>
                            <?php include_once( $template_path ); ?>
                        </div>
                    </div><?php
                }else{
                    $shipper_data   = wpcfe_table_header('shipper');
                    $receiver_data  = wpcfe_table_header('receiver');
                    $paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                    $s_shipment     = isset( $_GET['wpcfes'] ) ? $_GET['wpcfes'] : '' ;
                    // Custom meta query
                    $meta_query   = array();
                    if( isset($_GET['status']) && !empty( $_GET['status'] ) ){
                        $meta_query[] = array(
                            'key' => 'wpcargo_status',
                            'value' => urldecode( $_GET['status'] ),
                            'compare' => '='
                        );
                    }
                    if( isset($_GET['shipper']) && !empty( $_GET['shipper'] ) ){
                        $meta_query[] = array(
                            'key' => $shipper_data['field_key'],
                            'value' => urldecode( $_GET['shipper'] ),
                            'compare' => '='
                        );
                    }
                    if( isset($_GET['receiver']) && !empty( $_GET['receiver'] ) ){
                        $meta_query[] = array(
                            'key' => $receiver_data['field_key'],
                            'value' => urldecode( $_GET['receiver'] ),
                            'compare' => '='
                        );
                    }
                    if( isset($_GET['receiverid']) && !empty( $_GET['receiverid'] ) ){
                        $receiver_id = $_GET['receiverid'];
                    }
                    $meta_query = apply_filters( 'wpcfe_dashboard_meta_query', $meta_query );
                    $args           = array(
                        'post_type'         => 'wpcargo_shipment',
                        'post_status'       => 'publish',
                        'posts_per_page'    => $wpcfesort,
                        'paged'             => get_query_var('paged'),
                        's'                 => $s_shipment,
                        'meta_query' => array(
                            'relation' => 'AND',
                            $meta_query
                        )
                    );
							
                    $wpc_shipments  = new WP_Query( $args );
                    $number_records = $wpc_shipments->found_posts;
                    $paged          = get_query_var('paged') <= 1 ? 1 : get_query_var('paged');
					$basis          = $paged * $wpcfesort;
					if( $number_records < $basis ){
						$record_end = $number_records ;
					}else{
						$record_end = $basis;
					}
                    $record_start  = $basis - ( $wpcfesort - 1 );
					$custom_template_path   = get_stylesheet_directory().'/wpcargo/wpcargo-frontend-manager/shipments.php';
					if( file_exists( $custom_template_path ) ){
						require_once( $custom_template_path );
					}else{
						require_once( WPCFE_PATH.'templates/shipments.php');
					}
                    wp_reset_postdata();
                }
                do_action( 'wpcfe_after_admin_page_load' );
            }else{
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <section class="card mb-4">
                            <div class="card-body">
                            <?php
                            while ( have_posts() ) : the_post();
                                the_content();
                            endwhile;
                            ?>
                            </div>
                        </section>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php do_action( 'wpcfe_dashboard_after_content', get_the_id() ); ?>
</main>
<!--Main layout-->
<!--Footer-->
<footer class="page-footer font-small primary-color-dark darken-2 mt-4 wow fadeIn fixed-bottom <?php echo $class_not_logged; ?>">
    <?php do_action( 'wpcfe_dashboard_before_footer', get_the_id() ); ?>
    <!--Copyright-->
    <div class="footer-copyright py-3 text-center">
        <?php echo apply_filters( 'wpcfe_footer_credits', '&copy; '.date('Y-m-d').' '.__('Copyright','wpcargo-frontend-manager').': <a href="'.home_url().'">'.get_bloginfo('name').'</a>' ); ?>
    </div>
    <!--/.Copyright-->
</footer>
<?php include('footer.php'); ?>