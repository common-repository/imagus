<?php

namespace Admin\Includes;


class AdminSettings {
	const DEFAULT_SETTING_PAGE = 'admin-settings.php';

	public static function validate_form_single( $post_data ): array {
		//validate quality image
		// TODO wpnonce

		return [
			'ok'   => true,
			'data' => [
				'id'         => (int) $post_data['id'],
				'quality_image'    => (int) $post_data['quality_image'],
				'original_local_copy' => 'false' !== $post_data['original_local_copy']
			]
		];
	}

	public function edit( $partial ): void {
		if ( empty( $partial ) ) {
			$partial = self::DEFAULT_SETTING_PAGE;
		}
		ob_start();
		$settings = self::get_settings_options();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/' . $partial;
		echo ob_get_clean();
	}

	public static function get_settings_options(): array {
		$options_and_default_values = [
			'quality_image'         => 70,
			'automatic_compression' => 0,
			'original_local_copy'   => 0,
			'modal_window_options'   => 1
		];
		$settings                   = [];

		foreach ( $options_and_default_values as $option => $value ) {
			$settings[ $option ] = get_option( IMAGUS_PREFIX . $option, $value );
		}

		return $settings;
	}

	public function update( $post_data ) {
		$validation = $this->validate_form( $post_data );
		if ( ! $validation ) {
			return;
		}
		update_option( IMAGUS_PREFIX . 'quality_image', $post_data['image-quality'] );
		update_option( IMAGUS_PREFIX . 'automatic_compression', (int) isset( $post_data['automatic-compression'] ) );
		update_option( IMAGUS_PREFIX . 'original_local_copy', (int) isset( $post_data['original-copies'] ) );
		update_option( IMAGUS_PREFIX . 'modal_window_options', (int) isset( $post_data['modal-window-options'] ) );
	}

	private function validate_form( $post_data ): bool {
		//validate nonce
		if ( ! isset( $post_data['_wpnonce'] ) || ! wp_verify_nonce( $post_data['_wpnonce'], 'imagus-settings' ) ) {
			return false;
		}

		//validate quality image
		if ( ! isset( $post_data['image-quality'] ) || ! is_int( (int) $post_data['image-quality'] ) ) {
			return false;
		}

		if ( $post_data['image-quality'] < 10 || $post_data['image-quality'] > 100 ) {
			return false;
		}

		return true;
	}

	public function is_update_post(): bool {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset ( $_POST['action'] ) && $_POST['action'] == 'imagus_update' ) {
			return true;
		}

		return false;
	}
}
