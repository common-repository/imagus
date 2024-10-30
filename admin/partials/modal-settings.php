<div id="imagus-optimize-dialog" class="hidden">
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="imagus-image-quality"><?php _e( 'Quality percentage', IMAGUS_DOMAIN ) ?></label>
			</th>
			<td>
				<input type="number" min="10" max="100" id="imagus-image-quality"
				       value="<?php echo $settings['quality_image'] ?>" name="imagus-image-quality"
				       class="small-text">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="imagus-original-copies"><?php _e( 'Leave original copies in media folder', IMAGUS_DOMAIN ) ?></label>
			</th>
			<td>
				<input type="checkbox" id="imagus-original-copies"
				       value="<?php echo $settings['original_local_copy'] ?>"
				       name="imagus-original-copies" <?php echo( $settings['original_local_copy'] ? 'checked' : '' ) ?>>
			</td>
			<td>
				<input type="hidden" id="imagus-image-id" value="" name="imagus-image-id">
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" name="imagus-single-submit" id="imagus-single-submit" class="button button-primary"
				       value="<?php _e( 'Optimize', IMAGUS_DOMAIN ) ?>">
			</td>
		</tr>
	</table>
</div>
