<?php
global $wpcargo;
$shipper_data   = wpcfe_table_header('shipper');
$receiver_data  = wpcfe_table_header('receiver');
$s_shipment     = isset( $_GET['wpcfes'] ) ? $_GET['wpcfes'] : '' ;
?>
<div class="col-lg-9 col-md-8 mt-0">
	<form id="wpcfe-filters" action="<?php echo $page_url; ?>" class="form-inline" style="width: 100%">
		<div class="row">
			<?php do_action( 'wpcfe_before_shipment_filters' ); ?>
			<?php if( !empty( $wpcargo->status ) ): ?>
				<div class="form-group wpcfe-filter status-filter p-0 mx-1">
					<label class="sr-only" for="status"><?php esc_html_e('Status', 'wpcargo-frontend-manager' ); ?></label>
					<select id="status" name="status" class="form-control md-form wpcfe-select">
						<option value=""><?php echo esc_html__('All Statuses', 'wpcargo-frontend-manager' ); ?></option>
						<?php 
							foreach ( $wpcargo->status as $status ) {
								?><option value="<?php echo $status; ?>"><?php echo $status; ?></option><?php
							}
						?>
					</select>
				</div>
			<?php endif; ?>
			<!--<div class="form-group wpcfe-filter shipper-filter p-0 mx-1">
				<label class="sr-only" for="shipper"><?php echo $shipper_data['label']; ?></label>
				<select id="shipper" name="shipper" class="form-control md-form wpcfe-select-ajax" data-filter="shipper">
					<option value=""><?php echo esc_html__('All', 'wpcargo-frontend-manager' ).' '.$shipper_data['label']; ?></option>
				</select>
			</div>-->
			<div class="form-group wpcfe-filter receiver-filter p-0 mx-1">
				<label class="sr-only" for="receiver"><?php echo $receiver_data['label']; ?></label>
				<select id="receiver" name="receiver" class="form-control md-form wpcfe-select-ajax" data-filter="receiver">
					<option value=""><?php echo esc_html__('All', 'wpcargo-frontend-manager' ).' '.$receiver_data['label']; ?></option>
				</select>
			</div>
			<div class="form-group wpcfe-filter receiverid-filter col-md-3">
				<?php 
					$user_ids_arr = [];
					$user_ids_results = $wpdb->get_results( "SELECT user_id FROM " . $wpdb->prefix . "usermeta WHERE meta_key = 'wpvj_capabilities' AND meta_value LIKE '%wpcargo_client%';" );
					for( $i=0;$i<sizeof( $user_ids_results );$i++):
						array_push( $user_ids_arr, $user_ids_results[$i]->user_id );
					endfor;
					$user_ids_str = implode( "','", $user_ids_arr );
					$user_ids_str = "'" . $user_ids_str . "'";
					$username_list = $wpdb->get_results( "SELECT meta_value FROM " . $wpdb->prefix . "usermeta WHERE meta_key = 'nickname' AND user_id IN (" . $user_ids_str . ");" );
				?>
			<label class="sr-only" for="receiverid">Receiver ID</label>
			<select id="receiverid" name="receiverid" class="form-control md-form" data-filter="receiverid">
				<option value=""><?php echo esc_html__('All', 'wpcargo-frontend-manager' ).' Receiver IDs'; ?></option>
				<?php
				for( $i=0;$i<sizeof( $username_list );$i++):?>
					<option value="<?php echo $username_list[$i]->meta_value;?>"><?php echo $username_list[$i]->meta_value;?></option>
				<?php 
				endfor;?>
			</select>
		</div>
			<?php do_action( 'wpcfe_after_shipment_filters' ); ?>
			<div class="form-group submit-filter p-0 mx-1">
				<button id="wpcfe-submit-filter" type="submit" class="btn btn-primary btn-fill btn-sm"><?php esc_html_e('Filter', 'wpcargo-frontend-manager' ); ?></button>
				<?php if(isset($_GET['status'])): ?>
				<a href="<?php echo $page_url; ?>" class="btn btn-secondary btn-fill btn-sm ml-4"><?php esc_html_e('Reset', 'wpcargo-frontend-manager' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</form>
</div>
<div class="col-lg-3 col-md-4 mt-0 p-0">
	<form id="wpcfe-search" class="float-md-none float-lg-right" action="<?php echo $page_url; ?>" method="get">
		<div class="form-sm">
			<label for="search-shipment" class="sr-only"><?php esc_html_e('Shipment Number', 'wpcargo-frontend-manager' ); ?></label>
			<input type="text" class="form-control form-control-sm" name="wpcfes" id="search-shipment" placeholder="<?php esc_html_e('Shipment Number', 'wpcargo-frontend-manager' ); ?>" value="<?php echo $s_shipment; ?>">
			<button type="submit" class="btn btn-primary btn-sm mx-md-0 ml-2"><?php esc_html_e('Search', 'wpcargo-frontend-manager' ); ?></button>
		</div>
	</form>
</div>