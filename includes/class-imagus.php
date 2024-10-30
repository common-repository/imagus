<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/sonirico
 * @since      1.0.0
 *
 * @package    Imagus
 * @subpackage Imagus/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Imagus
 * @subpackage Imagus/includes
 * @author     Marcos <marsanben92@gmail.com>
 */
class Imagus {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Imagus_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'IMAGUS_VERSION' ) ) {
			$this->version = IMAGUS_VERSION;
		} else {
			$this->version = '0.8.0';
		}
		$this->plugin_name = 'imagus';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Imagus_Loader. Orchestrates the hooks of the plugin.
	 * - Imagus_i18n. Defines internationalization functionality.
	 * - Imagus_Admin. Defines all hooks for the admin area.
	 * - Imagus_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-imagus-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-imagus-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-imagus-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/ImagusApi.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/AdminSettings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/MediaManager.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/ImagusResponse.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/Exceptions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

		$this->loader = new Imagus_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Imagus_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Imagus_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		global $pagenow;
		$plugin_admin = new Imagus_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		if ( get_option( $this->plugin_name . '_automatic_compression' ) ) {
			$this->loader->add_action( 'add_attachment', $plugin_admin, 'optimize_image_automatic' );
		}

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'settings_page' );
		if ( 'upload.php' === $pagenow ) {
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'upload_page' );
		}

		$this->loader->add_action( 'admin_post_imagus_update', $plugin_admin, 'settings_page' );
		$this->loader->add_action( 'wp_ajax_imagus_optimize_single', $plugin_admin, 'optimize_image_single' );
		$this->loader->add_action( 'wp_ajax_imagus_get_settings', $plugin_admin, 'get_settings' );
		$this->loader->add_action( 'wp_ajax_imagus_recover_image', $plugin_admin, 'recover_backup_image' );

		$this->loader->add_filter( 'manage_media_columns', $plugin_admin, 'add_media_columns', 10 );
		$this->loader->add_filter( 'manage_media_custom_column', $plugin_admin, 'fill_compress_button', 10, 2 );
		$this->loader->add_filter( 'bulk_actions-edit-attachment', $plugin_admin, 'manage_bulk_action');
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Imagus_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

}
