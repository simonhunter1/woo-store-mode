<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://remedyone.com
 * @since      1.0.0
 *
 * @package    Rm_Woo_Store_Mode
 * @subpackage Rm_Woo_Store_Mode/admin/partials
 */
// echo "<pre>";
// var_dump( $options );
// echo "</pre>";
// var_dump( get_current_screen() );
?>


<div class="wrap">
	
	<h1 class="wp-heading-inline">Woo Store Mode</h1>
	<hr>
	<h2>Current Status</h2>
	<p><strong>Mode:</strong> <?php echo ucfirst( rm_get_current_mode() ); ?>, <strong>Status:</strong> <?php echo rm_store_status(); ?></p>
	<p><strong>Store Time:</strong> <?php echo date('H:i', current_time('timestamp') ); ?></p>
	<h2>Enable/Disable Modes</h2>
	<form id="rm-woo-store-options" method="post">
		<?php if( $modes ): ?>
			<table class="wp-list-table widefat">
				<thead>
					<tr>
						<th>Mode</th>
						<th>Start Time</th>
						<th>End Time</th>
						<th>Enable/Disable</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach ($modes as $mode): 
							$open = isset( $options['modes'][$mode->slug]['open'] ) ? $options['modes'][$mode->slug]['open'] : '';
							$close = isset( $options['modes'][$mode->slug]['close'] ) ? $options['modes'][$mode->slug]['close'] : '';
					?>
					<tr>
						<td><?php echo $mode->name; ?></td>
						<td>
							<input type="text" name="modes[<?php echo $mode->slug; ?>][open]" value="<?php echo $options['modes'][$mode->slug]['open']; ?>" class="timepicker">							
						</td>
						<td>
							<input type="text" name="modes[<?php echo $mode->slug; ?>][close]" value="<?php echo $options['modes'][$mode->slug]['close']; ?>" class="timepicker">
						</td>
						<td>
							<?php $checked = isset( $options['modes'][$mode->slug]['status'] ) ? 'checked' : ''; ?>
							<input type="checkbox" name="modes[<?php echo $mode->slug; ?>][status]" value="1" <?php echo $checked; ?>>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h2>Working Hours</h2>
		<div id="days-type">
			<p>
				<label>
					<input type="radio" name="days" value="all" <?php echo ( $options['days'] == 'all') ? 'checked="checked"' : ''; ?>>
					Same All Days
				</label>
			</p>
			<p>
				<label>
					<input type="radio" name="days" value="individual" <?php echo ( $options['days'] == 'individual') ? 'checked="checked"' : ''; ?>>
					Different Individual Days
				</label>
			</p>
		</div>

		<div id="working-hours-template">
			
		</div>

		<h2>Checkout Page Alternate Text</h2>
		
		<?php 
			
			$checkout_content = isset( $options['rm_woo_store_checkout_content'] ) ? $options['rm_woo_store_checkout_content'] : '';
			
			wp_editor( $checkout_content, 'rm_woo_store_checkout_content' );

			wp_nonce_field( 'rm_woo_store_nonce', '_wpnonce', false, true );

		?>

		<input type="submit" name="save_rm_woo_store_options" value="Save">

	</form>
	<h2>Shortcode</h2>
	<p>[wsm-store-mode-change]</p>

	<h2>PHP Code</h2>
	<p>&lt?php echo wsm_store_mode_change(); ?&gt</p>
</div>

<script type="text/javascript">
	/* <![CDATA[ */
	var rm_store_options = <?php echo json_encode( rm_get_options() ); ?>;
	var mode_terms = <?php echo json_encode( $modes ); ?>;
	/* ]]> */
</script>

<script type="text/html" id="tmpl-working-hours">
	<# _.each( data, function( row, i ) { #>
		<# if( row != 'all' ) { #>
			<h2>{{{ row }}}</h2>
		<# } #>
		<table class="wp-list-table widefat">
			<thead>
				<tr>
					<th>Mode</th>
					<th>Open</th>
					<th>Close</th>
				</tr>
			</thead>
			<tbody>
				<# _.each( mode_terms, function( term, termIndex ) { 
					
					
					var termSlug = term.slug;
					var openTime = '00:00';
					var closeTime = '00:00';
					if( typeof rm_store_options['store'][row] != 'undefined' ) {
						openTime = rm_store_options.store[row][termSlug].open;
						closeTime = rm_store_options.store[row][termSlug].close;
					}
				#>

				<!-- if( rm_store_options.store[row][termSlug] ) {
						openTime = rm_store_options.store[row][termSlug].open;
						closeTime = rm_store_options.store[row][termSlug].close;
					} -->
				<tr>
					<th>{{{ term.name }}}</th>
					<td>
						<input type="text" name="store[{{{row}}}][{{{term.slug}}}][open]" class="timepicker" value="{{{ openTime }}}">
					</td>
					<td>
						<input type="text" name="store[{{{row}}}][{{{term.slug}}}][close]" class="timepicker" value="{{{ closeTime }}}">
					</td>
				</tr>
				<# }) #>
			</tbody>
		</table>		
	<# }) #>
</script>


<style type="text/css">
	h2 {
		text-transform: capitalize;
	}
</style>