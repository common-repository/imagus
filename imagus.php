<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/sonirico
 * @since             0.8.0
 * @package           Imagus
 *
 * @wordpress-plugin
 * Plugin Name:       Imagus
 * Plugin URI:        https://katodia.com/webtools/imagus
 * Description:       Ultimate (and magic) image compressor
 * Version:           0.8.0
 * Author:            Katodia
 * Author URI:        katodia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       imagus-wordpress-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'IMAGUS_VERSION', '0.8.0' );
define( 'IMAGUS_PLUGIN_NAME', 'imagus' );
define( 'IMAGUS_PREFIX', 'imagus_' );
define( 'IMAGUS_BACKUP_FOLDER', IMAGUS_PREFIX.'image_backups' );
define( 'IMAGUS_HAS_COPY', IMAGUS_PREFIX.'has_copy');
define( 'IMAGUS_DOMAIN', 'imagus-wordpress-plugin');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-imagus-activator.php
 */
function activate_imagus() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-imagus-activator.php';
	Imagus_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-imagus-deactivator.php
 */
function deactivate_imagus() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-imagus-deactivator.php';
	Imagus_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_imagus' );
register_deactivation_hook( __FILE__, 'deactivate_imagus' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-imagus.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.8.0
 */
function run_imagus() {

	$plugin = new Imagus();
	$plugin->run();

}

run_imagus();
