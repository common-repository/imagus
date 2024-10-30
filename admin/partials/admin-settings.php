<?php
$image = plugin_dir_url( dirname( __FILE__ ) ) . '/assets/magick-trick.svg';
?>
<div class="wrap">
	<h1><img class="imagus-icon-admin" src=<?php echo esc_html($image) ?>><?php _e( 'Imagus settings', IMAGUS_DOMAIN ) ?></h1>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<?php wp_nonce_field( 'imagus-settings' ); ?>
		<input type="hidden" name="action" value="imagus_update">
		<table class="form-table" role="presentation">
			<tbody>
			<tr>
				<th scope="row">
					<label for="image-quality"><?php _e( 'Quality percentage', IMAGUS_DOMAIN ) ?></label>
				</th>
				<td>
					<input type="number" min="10" max="100" id="image-quality"
					       value="<?php echo $settings['quality_image'] ?>" name="image-quality" class="small-text">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="automatic-compression"><?php _e( 'Automatic compression', IMAGUS_DOMAIN ) ?></label>
				</th>
				<td>
					<input type="checkbox" id="automatic-compression"
					       value="<?php echo $settings['automatic_compression'] ?>"
					       name="automatic-compression" <?php echo( $settings['automatic_compression'] ? 'checked' : '' ) ?>>
				</td>
			</tr>
			<tr>
				<?php if (!get_option( IMAGUS_PREFIX.'image_backups') ): ?>
					<div class="notice notice-warning inline">
						<p>
						<?php _e('Cannot create images backup folder. Check permissions in uploads folder path.', IMAGUS_DOMAIN); ?>
						</p>
					</div>
				<?php else: ?>
				<th scope="row">
					<label for="original-copies"><?php _e( 'Leave original copies in media folder', IMAGUS_DOMAIN ) ?></label>
				</th>
				<td>
					<input type="checkbox" id="original-copies" value="<?php echo $settings['original_local_copy'] ?>"
					       name="original-copies" <?php echo( $settings['original_local_copy'] ? 'checked' : '' ) ?>>
				</td>
				<?php endif; ?>
			</tr>
			<tr>
				<th scope="row">
					<label for="modal-window-options"><?php _e( 'Enable modal customized options window', IMAGUS_DOMAIN ) ?></label><br>
					<span class="description"><?php _e( 'This modal allows to set custom options when you do a single or raw compression in the media gallery.', IMAGUS_DOMAIN); ?></span><br>
				</th>
				<td>
					<input type="checkbox" id="modal-window-options"
					       value="<?php echo $settings['modal_window_options'] ?>"
					       name="modal-window-options" <?php echo( $settings['modal_window_options'] ? 'checked' : '' ) ?>>
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary"
			       value="<?php _e( 'Save changes', IMAGUS_DOMAIN ) ?>">
		</p>
	</form>
</div>
