<?php $wpcfe_print_options = wpcfe_print_options();
global $wpdb;
?>
<div id="shipment-filters" class="filters-card mb-4">
	<div class="filters-body row wpcfe-filter">
		<?php require_once( WPCFE_PATH.'templates/filter-shipment.php'); ?>
	</div>
</div>
<div class="shipments-wrapper mb-4" style="visibility: visible; animation-name: fadeIn;">
    <div class="shipments-body">
		<div id="shipments-table-list" class="content">
			<?php if ( $wpc_shipments->have_posts() ) : ?>
			<div class="table-top">
				<form id="shipment-sort" class="float-right mr-4" action="<?php echo $page_url; ?>" method="get">
					<select name="wpcfesort" class="mdb-select" style="width: 38px; display: inline-block;">
						<option ><?php echo __('Show entries', 'wpcargo-frontend-manager' ); ?></option>
						<?php foreach( $wpcfesort_list as $list ): ?>
						<option value="<?php echo $list ?>" <?php echo $list == $wpcfesort ? 'selected' : '' ;?>><?php echo $list ?> <?php echo __('entries', 'wpcargo-frontend-manager' ); ?></option>
						<?php endforeach; ?>
					</select>
				</form>
				<button class="donwloadWaybill btn btn-secondary btn-sm"><i class="fa fa-file-pdf text-white"></i> <?php esc_html_e('Print Selected Waybills', 'wpcargo-frontend-manager'); ?></button>
				<?php if( can_wpcfe_delete_shipment() ): ?>
					<button class="remove-shipments btn btn-danger btn-sm"><i class="fa fa-trash text-white"></i> <?php esc_html_e('Delete', 'wpcargo-frontend-manager'); ?></button>
				<?php endif; ?>
				<?php do_action( 'wpcfe_before_after_shipment_table' ); ?>
			</div>
			<div class="card">
				<div class="card-body table-responsive">
					<table id="shipment-list" class="table table-hover table-sm">
						<thead>
							<tr>
								<th class="form-check">
									<input class="form-check-input " id="wpcfe-select-all" type="checkbox"/>
									<label class="form-check-label" for="materialChecked2"></label>
								</th>
								<th><?php echo apply_filters( 'wpcfe_shipment_number_label', __('Tracking Number', 'wpcargo-frontend-manager' ) ); ?></th>
								<th>Invoice</th>
								<th class="no-space"><?php echo apply_filters( 'wpcfe_shipper_table_header_label', $shipper_data['label'] ); ?></th>
								<th class="no-space"><?php echo apply_filters( 'wpcfe_receiver_table_header_label', $receiver_data['label'] ); ?>	</th>
								<?php do_action( 'wpcfe_shipment_table_header' ); ?>
								<th>ID</th>
								<th><?php esc_html_e('Status', 'wpcargo-frontend-manager' ); ?></th>
								<?php do_action( 'wpcfe_shipment_table_header_action' ); ?>
								<th class="text-center"><?php esc_html_e('View', 'wpcargo-frontend-manager' ); ?></th>
								<?php if( !empty( $wpcfe_print_options ) ): ?>
									<th class="text-center"><?php esc_html_e('Print', 'wpcargo-frontend-manager' ); ?></th>
								<?php endif; ?>
								<?php if( can_wpcfe_update_shipment() ): ?>
									<th class="text-center"><?php esc_html_e('Update', 'wpcargo-frontend-manager'); ?></th>
								<?php endif; ?>
								<?php if( can_wpcfe_delete_shipment() ): ?>
									<th class="text-center"><?php esc_html_e('Delete', 'wpcargo-frontend-manager'); ?></th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php					
							while ( $wpc_shipments->have_posts() ) {
								$wpc_shipments->the_post();
								$result = $wpdb->get_row( "SELECT post_title FROM " . $wpdb->prefix . "posts WHERE ID = " . get_the_ID() );
								$shipment_title = $result->post_title;
								 
								$current_user = wp_get_current_user();
								$roles = ( array ) $current_user->roles;
								if( $roles[0] == 'wpcargo_client' ):

									$username = $current_user->user_login;

									$user_is_admin = false;

								elseif( $roles[0] == 'administrator' ):

									$data = $wpdb->get_row( "SELECT meta_value FROM " . $wpdb->prefix . "postmeta WHERE post_id = " . get_the_ID() . " AND meta_key = 'registered_shipper';" );

									$user_id = $data->meta_value;

									$shipment_user = get_user_by( 'ID', $user_id );

									$username = $shipment_user->user_login;

									$user_is_admin = true;

								endif;
								

								//$shipment_title = apply_filters( 'wpcfe_shipment_number', get_the_title(), get_the_ID() );
								$shipper_meta 	= apply_filters( 'wpcfe_shipper_table_cell_data', get_post_meta( get_the_ID(), $shipper_data['field_key'], true ), get_the_ID() );
								$receiver_meta 	= apply_filters( 'wpcfe_receiver_table_cell_data', get_post_meta( get_the_ID(), $receiver_data['field_key'], true ), get_the_ID() );
								$status 		= get_post_meta( get_the_ID(), 'wpcargo_status', true );
								$shipment_type 	= get_post_meta( get_the_ID(), '__shipment_type', true ) ? get_post_meta( get_the_ID(), '__shipment_type', true ) : '';
								$shipment_type_list 	= wpcfe_shipment_type_list();
								$get_shipment_type_label = isset( $shipment_type_list[$shipment_type] ) ? $shipment_type_list[$shipment_type] : __('Default', 'wpcargo-frontend-manager');
								$has_invoice = false;
								$invoice_files = scandir( $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username . "/" );
								for( $j=0;$j<sizeof( $invoice_files );$j++ ):

									if( explode( '.', $invoice_files[$j] )[0] == $shipment_title ):

										$has_invoice = true;
										$invoice_file_name = $invoice_files[$j];

									endif;

								endfor;
								
								
								if( $receiver_id == '' || $receiver_id == $username ):
								?>
								<tr id="shipment-<?php echo get_the_ID(); ?>" class="shipment-row <?php echo wpcfe_to_slug( $status ); ?>">
									<td class="form-check">
									  <input class="wpcfe-shipments form-check-input " type="checkbox" name="wpcfe-shipments[]" value="<?php echo get_the_ID(); ?>" data-number="<?php echo $shipment_title; ?>">
									  <label class="form-check-label" for="materialChecked2"></label>
									</td>
									<td><a href="<?php echo $page_url; ?>?wpcfe=track&num=<?php echo $shipment_title; ?>" class="text-primary font-weight-bold"><?php echo $shipment_title; ?></a></td>
									<td>
										<?php if( $has_invoice ): ?>
											<a target="_blank" href="<?php echo get_template_directory_uri() . "/invoices/" . $username . "/" . $invoice_file_name;?>"><img src="https://mianectlogistics.com/wp-content/uploads/2020/07/invoice-icon.png" alt="shipment invoice"/></a>
										<?php elseif( !$has_invoice && $user_is_admin ):?>
											<img src="https://mianectlogistics.com/wp-content/uploads/2020/07/awaiting-invoice-icon.png" alt="shipment invoice"/>
										<?php else: ?>
											<form method="post" action="https://mianectlogistics.com/shipments-dashboard/?wpcfe=uplinv">
												<input type="hidden" name="wpcfe_new_tracking_number" value="<?php echo $shipment_title; ?>"/>
												<button style="cursor: pointer; background-color: transparent; border: none;" type="submit" name="inv_tracking"><img src="https://mianectlogistics.com/wp-content/uploads/2020/07/upload-invoice-icon.png" alt="shipment invoice"/></button>
											</form>
											<!-- <a href="https://mianectlogistics.com/shipments-dashboard/?wpcfe=uplinv"><img src="https://mianectlogistics.com/wp-content/uploads/2020/07/upload-invoice-icon.png" alt="shipment invoice"/></a> -->
										<?php endif; ?>	
									</td>							
									<td class="no-space"><?php echo $shipper_meta; ?></td>
									<td class="no-space"><?php echo $receiver_meta; ?></td>
									<?php do_action( 'wpcfe_shipment_table_data', get_the_ID() ); ?>
									<td><?php echo $username;?></td>
									<td class="shipment-status <?php echo wpcfe_to_slug( $status ); ?>"><?php echo $status; ?></td>
									<?php do_action( 'wpcfe_shipment_table_action', get_the_ID() ); ?>
									<td class="text-center">
										<a href="<?php echo $page_url; ?>?wpcfe=track&num=<?php echo $shipment_title; ?>" title="<?php echo __('View', 'wpcargo-shipment-rate' ); ?>">
											<i class="fa fa-list text-success"></i>
										</a>
									</td>
									<?php if( !empty( $wpcfe_print_options ) ): ?>
										<td class="text-center print-shipment">
											<div class="dropdown">
												<!--Trigger-->
												<button class="btn btn-default btn-sm dropdown-toggle m-0 py-1 px-2" type="button" id="dropdownPrint" data-toggle="dropdown"
													aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i></button>
												<!--Menu-->
												<div class="dropdown-menu dropdown-primary">
													<?php foreach( $wpcfe_print_options as $print_key => $print_label ): ?>
														<a class="dropdown-item print-<?php echo $print_key; ?> py-1" data-id="<?php echo get_the_ID(); ?>" data-type="<?php echo $print_key; ?>" href="#"><?php echo $print_label; ?></a>
													<?php endforeach; ?>
												</div>
											</div>
										</td>
									<?php endif; ?>
									<?php if( can_wpcfe_update_shipment() ): ?>
										<td class="text-center wpcfe-action">									
											<?php echo apply_filters( 'wpcfe_update_shipment_action', wpcfe_update_shipment_action( get_the_ID(), $page_url ), get_the_ID(), $page_url ); ?>									
										</td>
									<?php endif; ?>
									<?php if( can_wpcfe_delete_shipment() ): ?>
										<td class="text-center">
											<a href="#" class="wpcfe-delete-shipment" data-id="<?php echo get_the_ID(); ?>" title="<?php esc_html_e('Delete', 'wpcargo-frontend-manager'); ?>"><i class="fa fa-trash text-danger"></i></a>
										</td>	
									<?php endif; ?>						
								</tr>
								<?php
								endif;
							} // end while
							?>
						</tbody>
					</table>
				</div>
			</div>
			<button class="donwloadWaybill btn btn-secondary btn-sm"><i class="fa fa-file-pdf text-white"></i> <?php esc_html_e('Print Selected Waybills', 'wpcargo-frontend-manager'); ?></button>
			<?php if( can_wpcfe_delete_shipment() ): ?>
				<button class="remove-shipments btn btn-danger btn-sm"><i class="fa fa-trash text-white"></i> <?php esc_html_e('Delete', 'wpcargo-frontend-manager'); ?></button>
			<?php endif; ?>
			<?php do_action( 'wpcfe_before_after_shipment_table' ); ?>
			<div class="row">
				<section class="col-md-5">
					<?php
						printf(
							'<p class="note note-primary">Showing %s to %s of %s entries.</p>',
							$record_start,
							$record_end,
							number_format($number_records)
						);
					?>
				</section>
				<section class="col-md-7"><?php wpcfe_bootstrap_pagination( array( 'custom_query' => $wpc_shipments ) ); ?></section>
			</div>
			<?php else: ?>
				<i class="fa fa-inbox d-block p-2 text-center text-danger" style="font-size: 4rem;"></i>
				<h3 class="text-center text-danger"><?php esc_html_e('No shipment found!', 'wpcargo-frontend-manager' ); ?></h3>
			<?php endif; ?>			
		</div>
	</div>
</div>
<?php do_action('wpcfe_after_shipment_data'); ?>