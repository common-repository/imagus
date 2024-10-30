<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/sonirico
 * @since      0.8.0
 *
 * @package    Imagus
 * @subpackage Imagus/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.8.0
 * @package    Imagus
 * @subpackage Imagus/includes
 * @author     Marcos <marsanben92@gmail.com>
 */
class Imagus_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.8.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			IMAGUS_DOMAIN,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}


}
