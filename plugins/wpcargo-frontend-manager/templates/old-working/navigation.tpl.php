<!--Main Navigation-->
<header>
    <!-- Navbar -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-light white scrolling-navbar">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand waves-effect d-sm-inline-block d-md-inline-block d-lg-none" href="<?php echo bloginfo('url'); ?>">
                <img src="<?php echo wpcfe_dashboard_logo_url(); ?>" class="img-fluid" alt="<?php esc_html_e( 'Site Logo', 'wpcargo-frontend-manager' ); ?>" style="width: 85%;" />
            </a>
            <!-- Collapse -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Links -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            	<?php if( is_user_logged_in() ): ?>
					<div class="nav-section search-nav mr-auto w-50">
						<!-- Search form -->
						<form class="form-inline md-form form-sm active-cyan-2 my-0" method="GET" action="<?php echo $page_url; ?>">
							<i class="fa fa-search" aria-hidden="true"></i>
							<input type="hidden" name="wpcfe" value="track">
							<input class="form-control form-control-sm my-0 ml-2 w-75" type="text" name="num" placeholder="<?php echo apply_filters('wpcfe_track_shipment',esc_html__('Track Shipment', 'wpcargo-frontend-manager') ); ?>"
							aria-label="<?php echo apply_filters('wpcfe_track_shipment',esc_html__('Track Shipment', 'wpcargo-frontend-manager') ); ?>">					  
						</form>
					</div>
		        <?php endif; ?>
                <?php
					$wpcfe_top_menu_args = array(
						'echo' 			 => FALSE,
						'theme_location' => 'wpcfe-dashboard-top-menu',
						'menu_class'     => 'nav navbar-nav nav-flex-icons ml-auto',
						'link_before'    => '',
						'link_after'     => '',
						'walker'        => new WPCFE_Dashboard_Top_Menu(),
						'fallback_cb'   => false,
						'container'     => ''
					);
					echo wp_nav_menu( $wpcfe_top_menu_args );
	            ?>
                <div class="nav-section mobile-sidebar-menu d-sm-inline-block d-md-inline-block d-lg-none">
					<?php
						if( wpcfe_admin_page() ){
							$user_roles = wpcfe_current_user_role();
							?>
							<?php if( !wpcfe_add_shipment_deactivated() ): ?>
								<?php if( !( in_array( 'cargo_agent', (array)$user_roles ) ) && !( in_array( 'wpcargo_driver', (array)$user_roles ) ) ): ?>
									<a href="<?php echo get_the_permalink( wpcfe_admin_page() ); ?>/?wpcfe=add" class="dashboard-page-menu list-group-item list-group-item-action waves-effect menu-item"> <i class="fa fa-plus mr-md-3 d-none d-lg-inline-block d-xl-inline-block"></i><?php echo apply_filters( 'wpcfe_create_shipment', esc_html__('Create Shipment', 'wpcargo-frontend-manager') ); ?> </a>
								<?php endif; ?>
							<?php endif; ?>
							<?php
						}
						do_action( 'wpcfe_after_add_shipment' );
						if( !empty( wpcfe_after_sidebar_menu_items() ) ){
							foreach( wpcfe_after_sidebar_menu_items() as $item => $additional_items ){
								?>
								<a href="<?php echo $additional_items['permalink']; ?>" class="dashboard-page-menu list-group-item list-group-item-action waves-effect menu-item <?php echo $item; ?>"> 
									<?php if( !empty( $additional_items['icon'] ) ): ?>
										<i class="fa <?php echo $additional_items['icon']; ?> mr-3"></i>
									<?php endif; ?>
									<?php echo $additional_items['label']; ?> 
								</a>
								<?php
							}
						}
					?>       
					<?php
						$wpcfe_sidebar_menu_args = array(
							'theme_location' => 'wpcfe-dashboard-sidebar-menu',
							'menu_class' 	 => 'list-group list-group-flush',
							'link_before'  	 => '',
							'link_after' 	 => '',
							'walker' 		=> new WPCFE_Dashboard_Sidebar_Menu(),
							'fallback_cb'   => false,
						);
						wp_nav_menu( $wpcfe_sidebar_menu_args );
						if( !empty( wpcfe_after_sidebar_menus() ) ){
							foreach( wpcfe_after_sidebar_menus() as $item => $additional_items ){
								?>
								<a href="<?php echo $additional_items['permalink']; ?>" class="list-group-item waves-effect <?php echo $item; ?>"> 
									<?php if( !empty( $additional_items['icon'] ) ): ?>
										<i class="fa <?php echo $additional_items['icon']; ?> mr-3"></i>
									<?php endif; ?>
									<?php echo $additional_items['label']; ?> 
								</a>
								<?php
							}
						}
					?>
		        </div>
		        <?php if( is_user_logged_in() ): ?>
					<div class="nav-section nav-account-dropdown <?php if( empty( wp_nav_menu( $wpcfe_top_menu_args ) ) ) { echo 'ml-auto'; } ?>">
						<?php
							$fullname = $wpcargo->user_fullname( get_current_user_id() );
							$user_avatar = wpcfe_user_avatar_url() ? '<img src="'.wpcfe_user_avatar_url().'" width="30" height="30">' : '<i class="fa fa-user-circle text-primary" style="font-size:30px;vertical-align: middle;"></i>' ;
						?>
						<a href="#" class="nav-wpcfe-account">
							<?php echo $user_avatar; ?>
							<span class="account-label"><?php echo $fullname; ?></span>
						</a>
						<ul class="account-dropdown">
							<li>
								<a href="<?php echo get_the_permalink( wpc_profile_get_frontend_page() ); ?>"><?php esc_html_e( 'My Profile', 'wpcargo-frontend-manager' ); ?></a>
							</li>
							<!--<li><a href="#"><?php esc_html_e( 'Notifications', 'wpcargo-frontend-manager' ); ?></a></li>-->
							<?php do_action( 'wpcfe_after_profile_dropdown', get_current_user_id() ); ?>
							<li><a href="<?php echo wp_logout_url( home_url() ); ?>"><?php esc_html_e( 'Logout', 'wpcargo-frontend-manager' ); ?></a></li>
						</ul>
					</div>
		        <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Navbar -->
    <!-- Sidebar -->
    <div class="sidebar-fixed position-fixed">
        <a class="logo-wrapper waves-effect" href="<?php echo bloginfo('url'); ?>">
        	<img src="<?php echo wpcfe_dashboard_logo_url(); ?>" class="img-fluid" alt="<?php esc_html_e( 'Site Logo', 'wpcargo-frontend-manager' ); ?>" style="width: 85%;" />
        </a>
        <div class="list-group list-group-flush">
			<?php
				if( wpcfe_admin_page() ){
					$user_roles = wpcfe_current_user_role();
					$active_class = ( get_the_ID() == wpcfe_admin_page() && isset($_GET['wpcfe']) ) ? 'active' : '' ;
					if( !wpcfe_add_shipment_deactivated() ):
						if( can_wpcfe_add_shipment() ): ?>
							<a href="<?php echo get_the_permalink( wpcfe_admin_page() ); ?>?wpcfe=add" class="list-group-item waves-effect <?php echo $active_class; ?>"> 
								<i class="fa fa-plus mr-md-3 d-none d-lg-inline-block d-xl-inline-block"></i><?php echo apply_filters( 'wpcfe_create_shipment', esc_html__('Create Shipment', 'wpcargo-frontend-manager') ); ?> 
							</a>
						<?php endif;
					endif;
					do_action( 'wpcfe_after_add_shipment' );
					if( !empty( wpcfe_after_sidebar_menu_items() ) ){
						foreach( wpcfe_after_sidebar_menu_items() as $item => $additional_items ){
							$page_id = array_key_exists( 'page-id', $additional_items ) ? $additional_items['page-id'] : 0;
							$active_class = '';
							if( !isset($_GET['wpcfe']) && get_the_ID() == $page_id ){
								$active_class = 'active';
							}
							?>
							<a href="<?php echo $additional_items['permalink']; ?>" class="list-group-item waves-effect <?php echo $item.' '.$active_class; ?>"> 
								<?php if( !empty( $additional_items['icon'] ) ): ?>
									<i class="fa <?php echo $additional_items['icon']; ?> mr-3"></i>
								<?php endif; ?>
								<?php echo $additional_items['label']; ?> 
							</a>
							<?php
						}?>
						<!-- TW Custom: 7/12/2020 -->
						<?php 
							$current_user = wp_get_current_user();
							$roles = ( array ) $current_user->roles;
							$role = $roles[0];						
						
							if( $role == 'wpcargo_client' || $role == 'administrator'):

						?>
							<a href="/shipments-dashboard/?wpcfe=upltrack" class="list-group-item waves-effect shipments-menu"> 
								<i class="fa fa-feed mr-3"></i>
								Add New Tracking# 
							</a>
							<a href="/shipments-dashboard/?wpcfe=edittrack" class="list-group-item waves-effect shipments-menu"> 
								<i class="fa fa-feed mr-3"></i>
								Edit Tracking# 
							</a>
						<!-- -->
						<?php endif;
						if( $role == 'administrator' ):?>
							<a href="/shipments-dashboard/?wpcfe=pkgreceive" class="list-group-item waves-effect shipments-menu"> 
								<i class="fa fa-codepen mr-3"></i>
								Receive Packages 
							</a>
						<?php endif;
						//if( $role == 'wpcargo_client' ):?>
							<!-- <a href="/shipments-dashboard/?wpcfe=uplinv" class="list-group-item waves-effect shipments-menu"> 
								<i class="fa fa-dollar mr-3"></i>
								Upload an Invoice 
							</a> -->
						<?php //endif;
					}
				}
			?>
			<?php do_action( 'wpcfe_before_sidebar_custom_menu' ); ?>
			<?php
				$wpcfe_menu_args = array(
					'theme_location' => 'wpcfe-dashboard-sidebar-menu',
					'menu_class' 	 => 'list-group list-group-flush',
					'link_before'  	 => '',
					'link_after' 	 => '',
					'walker' 		=> new WPCFE_Dashboard_Sidebar_Menu(),
					'fallback_cb'   => false,
				);
				wp_nav_menu( $wpcfe_menu_args );
				if( !empty( wpcfe_after_sidebar_menus() ) ){
					foreach( wpcfe_after_sidebar_menus() as $item => $additional_items ){
						?>
						<a href="<?php echo $additional_items['permalink']; ?>" class="list-group-item waves-effect <?php echo $item; ?>"> 
							<?php if( !empty( $additional_items['icon'] ) ): ?>
								<i class="fa <?php echo $additional_items['icon']; ?> mr-3"></i>
							<?php endif; ?>
							<?php echo $additional_items['label']; ?> 
						</a>
						<?php
					}
				}
			?>
			<?php do_action( 'wpcfe_after_sidebar_custom_menu' ); ?>
        </div>
    </div>
    <!-- Sidebar -->
</header>
<!--Main Navigation-->