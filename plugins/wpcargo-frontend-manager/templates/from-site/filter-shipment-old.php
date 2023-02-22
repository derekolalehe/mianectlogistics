<form id="wpcfe-filters" action="<?php echo $page_url; ?>" class="form-inline col-md-9 mt-0">
	<div class="row">
		<?php if( !empty( $wpcargo->status ) ): ?>
		<div class="form-group wpcfe-filter status-filter col-md-3">
			<label class="sr-only" for="status"><?php esc_html_e('Status', 'wpcargo-frontend-manager' ); ?></label>
			<select id="status" name="status" class="form-control md-form wpcfe-select">
				<option value=""><?php echo esc_html__('All Status', 'wpcargo-frontend-manager' ); ?></option>
				<?php 
				foreach ( $wpcargo->status as $status ) {
					?><option value="<?php echo $status; ?>"><?php echo $status; ?></option><?php
				}
				?>
			</select>
		</div>
		<?php endif; ?>
		<div class="form-group wpcfe-filter shipper-filter col-md-3">
			<label class="sr-only" for="shipper"><?php echo $shipper_data['label']; ?></label>
			<select id="shipper" name="shipper" class="form-control md-form wpcfe-select-ajax" data-filter="shipper">
				<option value=""><?php echo esc_html__('All', 'wpcargo-frontend-manager' ).' '.$shipper_data['label']; ?></option>
			</select>
		</div>
		<div class="form-group wpcfe-filter receiver-filter col-md-3">
			<label class="sr-only" for="receiver"><?php echo $receiver_data['label']; ?></label>
			<select id="receiver" name="receiver" class="form-control md-form wpcfe-select-ajax" data-filter="receiver">
				<option value=""><?php echo esc_html__('All', 'wpcargo-frontend-manager' ).' '.$receiver_data['label']; ?></option>
			</select>
		</div>
		<div class="form-group submit-filter col-md-3">
			<button id="wpcfe-submit-filter" type="submit" class="btn btn-primary btn-fill btn-sm"><?php esc_html_e('Filter', 'wpcargo-frontend-manager' ); ?></button>
		</div>
	</div>
</form>
<form id="wpcfe-search" action="<?php echo $page_url; ?>" class="form-inline col-md-3 mt-0">
	<div class="form-sm">
		<label for="search-shipment" class="sr-only"><?php esc_html_e('Shipment Number', 'wpcargo-frontend-manager' ); ?></label>
		<input type="text" class="form-control form-control-sm" name="wpcfes" id="search-shipment" placeholder="<?php esc_html_e('Shipment Number', 'wpcargo-frontend-manager' ); ?>">
	</div>
	<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light"><?php esc_html_e('Search', 'wpcargo-frontend-manager' ); ?></button>
</form>