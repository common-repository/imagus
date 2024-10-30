<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/sonirico
 * @since      0.8.0
 *
 * @package    Imagus
 * @subpackage Imagus/admin
 */

use Admin\Includes\AdminSettings;
use Admin\Includes\ImagusApi;
use Admin\Includes\ImagusAPIException;
use Admin\Includes\ImagusException;
use Admin\Includes\MediaManager;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Imagus
 * @subpackage Imagus/admin
 * @author     katodia
 */
class Imagus_Admin {
	const IMAGUS_ICON_PATH = 'admin/assets/magick-trick.svg';
	/**
	 * The ID of this plugin.
	 *
	 * @since    0.8.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.8.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    0.8.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.8.0
	 */
	public function enqueue_styles() {
		global $pagenow;
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/imagus-admin.css', array(), $this->version, 'all' );

		if ( 'upload.php' === $pagenow ) {
			$wp_scripts = wp_scripts();
			wp_enqueue_style( $this->plugin_name . 'jquery-ui',
			plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css',
				[],
				$this->version,
				'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.8.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Imagus_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Imagus_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $pagenow;

		if ( $pagenow === 'upload.php' ) {
			wp_enqueue_script( IMAGUS_PREFIX . 'modal', plugin_dir_url( __FILE__ ) . 'js/form-image-single.js', [ 'jquery-ui-dialog' ], $this->version, false );
		}

		wp_enqueue_script( IMAGUS_PREFIX . 'admin', plugin_dir_url( __FILE__ ) . 'js/imagus-admin.js', [], $this->version, false );

		if ( $pagenow === 'upload.php') {
			wp_localize_script(IMAGUS_PREFIX . 'admin', IMAGUS_PREFIX . 'admin_vars', array(
					'optimized' => __('Optimized!', IMAGUS_DOMAIN),
					'zero_saved' => __('0 bytes saved. Choose another quality ratio (lower)', IMAGUS_DOMAIN),
					'recover_backup' => __('Recover backup', IMAGUS_DOMAIN),
					'compression' => __('Imagus compression!', IMAGUS_DOMAIN)
				)
			);
		}
	}

	public function settings_page() {
		$settings = new AdminSettings();
		//form submitted
		if ( $settings->is_update_post() ) {
			$settings->update( $_POST );
			wp_safe_redirect( admin_url( 'options-general.php?page=imagus_settings' ) );

			return;
		}
		add_options_page( $this->plugin_name, 'Imagus', 'manage_options',
			'imagus_settings', [ $settings, 'edit' ] );
	}

	public function fill_compress_button( $column_name, $image_id ) {
		if ( $column_name == IMAGUS_PREFIX . 'optimize' ) {
			if ( false === strpos(get_post_mime_type( $image_id ), 'image' ) ) {
				return;
			}

			if ( !empty(get_post_meta ( $image_id,IMAGUS_PREFIX.'is_copy_of'))) {
				return;
			}

			$is_optimized = !empty( get_post_meta( $image_id, IMAGUS_PREFIX . 'optimized', true ) );
			$optimized_try = get_post_meta( $image_id, IMAGUS_PREFIX . 'bytes_saved', true ) === '0';
			echo '<div data-optimized="'.esc_html(($is_optimized ? "true" : "false" )).'" data-loading="false" class="imagus-media-column" id="imagus-media-column-' . esc_html($image_id) . '">';

			if ( !$is_optimized ) {
				$imagus_icon = plugin_dir_url( dirname( __FILE__ ) ) . 'admin/assets/magick-trick.svg';

				echo '<span id="imagus-spinner-' . esc_html($image_id) . '" class="imagus-spinner spinner is-active imagus-display-none"></span>';
				echo '<button id="imagus-optimizer-' . esc_html($image_id) . '" type="button" data-imagus="' . esc_html($image_id) . '" class="imagus-optimizer button-primary">';
				_e('Imagus compression!',IMAGUS_DOMAIN);
				echo '<img class="imagus-roll-icon" src="'.esc_html($imagus_icon).'">';
				echo '</button>';
				if ($optimized_try){
					echo '<div>';
					_e('0 bytes saved. Choose another quality ratio (lower)', IMAGUS_DOMAIN);
					'</div>';
				}
			} else {
				echo '<span class="imagus-label success"><small>';
				_e('Already optimized', IMAGUS_DOMAIN);
				 echo '</small></span>';
				echo '<div class="imagus-bytes-saved"><small class="imagus-label other">' . esc_html(get_post_meta( $image_id, IMAGUS_PREFIX . 'human_bytes_saved', true )) . ' saved </small></div>';
				echo '<div class="imagus-percentage-saved"><small class="imagus-label other">' . esc_html(get_post_meta( $image_id, IMAGUS_PREFIX . 'bytes_percentage', true )) . '% saved</small></div>';
			}
			if (false != get_post_meta($image_id,IMAGUS_HAS_COPY)){
				echo '<div><small><a data-image="'.esc_html($image_id).'" id="recover-image-'.esc_html($image_id).'" href="#">';
				_e('Recover backup', IMAGUS_DOMAIN);
				echo '</a></small></div>';
			}
			echo '</div>';
		}
	}

	public function add_media_columns( $columns ) {
		$columns[ $this->plugin_name . '_optimize' ] = __( 'Imagus compression' );

		return $columns;
	}

	public function optimize_image_single() {
		$cdata = AdminSettings::validate_form_single( $_POST );
		if ( ! $cdata['ok'] ) {
			echo json_encode( [ 'ok' => false ] );

			return;
		}
		$request_settings = $cdata['data'];
		$default_settings = AdminSettings::get_settings_options();
		$merged_settings  = array_merge( $default_settings, $request_settings );

		try {
			$stats = $this->optimize_image( $merged_settings );
			echo json_encode( [
				'ok'      => true,
				'message' => $stats
			] );
		} catch ( Exception $e ) {
			echo json_encode( [ 'ok' => false, 'message' => $e->getMessage(), 'code' => $e->getCode() ] );
		} finally {
			wp_die();
		}
	}

	public function recover_backup_image(){
		if (!isset($_POST['image_id'])){
			echo json_encode( [ 'ok' => false ] );
			return;
		}

		$image_id = (int)$_POST['image_id'];
		$filename = get_post_meta($image_id,IMAGUS_HAS_COPY, true);

		if ( $filename ){
			$upload     = wp_upload_dir();
			$upload_dir = $upload['basedir'];
			$upload_dir = $upload_dir . '/'.IMAGUS_BACKUP_FOLDER;
			$tmp = $upload_dir.'/'.$filename;
			$compressed_img_url=get_attached_file( $image_id );

			if (!$compressed_img_url){
				echo json_encode( [ 'ok' => false ] );
			}

			if (copy( $tmp, $compressed_img_url )){
				$media = new MediaManager($image_id);
				unlink( $tmp );
				$media->delete_compression_meta();
				echo json_encode( [ 'ok' => true ] );
			}

			echo json_encode( [ 'ok' => false ] );
		}
	}

	public function get_settings() {
		$default_settings = AdminSettings::get_settings_options();

		try {
			echo json_encode( [
				'ok'      => true,
				'message' => $default_settings
			] );
		} catch ( Exception $e ) {
			echo json_encode( [ 'ok' => false, 'message' => $e->getMessage(), 'code' => $e->getCode() ] );
		} finally {
			wp_die();
		}
	}

	public function optimize_image_automatic( $image_id ){
		remove_action( 'add_attachment', [ $this, 'optimize_image_automatic' ] );
		$settings = AdminSettings::get_settings_options();
		$curated_settings = [
			'id' => $image_id,
			'quality_image' => (int)$settings['quality_image'],
			'original_local_copy' => '1' === $settings['original_local_copy']
		];
		$this->optimize_image( $curated_settings );
	}

	public function optimize_image( $settings ) {

		if ( ! wp_attachment_is_image( $settings['id'] ) ) {
			throw new ImagusException( "Only images" );
		}
		$image_path = get_attached_file( $settings['id'] );
		$media      = new MediaManager( IMAGUS_PLUGIN_NAME, $settings['id'] );
		$media->create_backup( $image_path );
		$image_quality = $settings['quality_image'];
		$api           = new ImagusApi();

		try {
			$res = $api->optimize( $image_path, $image_quality );

			if ( ! $res->is_ok() ) {
				throw new ImagusAPIException( $res->get_body(), $res->get_status_code() );
			}

			file_put_contents( $media->get_backup(), $res->get_body() );
			$saved_bytes = $media->get_saved_bytes();
			$optimized = $media->is_optimized();

			if ( $optimized ) {
				$media->attach_optimized_image( $settings );
			}

			update_post_meta( $settings['id'], IMAGUS_PREFIX . 'optimized', $optimized);
			update_post_meta( $settings['id'], IMAGUS_PREFIX . 'bytes_saved', $saved_bytes['bytes'] );
			update_post_meta( $settings['id'], IMAGUS_PREFIX . 'human_bytes_saved', $saved_bytes['human_bytes']);
			update_post_meta( $settings['id'], IMAGUS_PREFIX . 'bytes_percentage', $saved_bytes['percentage'] );

			return [
				'optimized'         => $optimized,
				'bytes_saved'       => $saved_bytes['bytes'],
				'human_bytes_saved' => $saved_bytes['human_bytes'],
				'bytes_percentage'  => $saved_bytes['percentage']
			];
		} catch ( Exception $e ) {
			throw $e;
		} finally {
			unlink( $media->get_backup() );
		}

	}

	public function upload_page() {
		$settings = new AdminSettings();
		$settings->edit( 'modal-settings.php' );
	}

}
