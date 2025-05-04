<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://code-press.pl
 * @since      1.0.0
 *
 * @package    Nip_Finder
 * @subpackage Nip_Finder/includes
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
 * @package    Nip_Finder
 * @subpackage Nip_Finder/includes
 * @author     CodePress <kontakt@code-press.pl>
 */
class Nip_Finder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Nip_Finder_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
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
		if ( defined( 'NIP_FINDER_VERSION' ) ) {
			$this->version = NIP_FINDER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'nip-finder';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Nip_Finder_Loader. Orchestrates the hooks of the plugin.
	 * - Nip_Finder_i18n. Defines internationalization functionality.
	 * - Nip_Finder_Admin. Defines all hooks for the admin area.
	 * - Nip_Finder_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nip-finder-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nip-finder-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nip-finder-public.php';

		$this->loader = new Nip_Finder_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Nip_Finder_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action('woocommerce_settings_tabs_array', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('woocommerce_settings_tabs_nip_finder', $plugin_admin, 'register_settings_page');
        $this->loader->add_action('woocommerce_update_options_nip_finder', $plugin_admin, 'update_nip_finder_settings');
        $this->loader->add_action('woocommerce_settings_save_nip_finder', $plugin_admin, 'wc_settings_tab_save');
        $this->loader->add_action('admin_init', $plugin_admin, 'nip_finder_maybe_update_token');

        $this->loader->add_action('wp_ajax_nip_finder_check_status', $plugin_admin, 'check_status');
        $this->loader->add_action('wp_ajax_nip_finder_register', $plugin_admin, 'register');
        $this->loader->add_action('wp_ajax_nip_finder_register_subscription', $plugin_admin, 'register_subscription');
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Nip_Finder_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'nip_finder_enqueue_gus_script');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'nip_finder_enqueue_postalcode_script');

        $this->loader->add_action('wp_ajax_nip_finder_fetch_gus_data', $plugin_public, 'nip_finder_fetch_gus_data');
        $this->loader->add_action('wp_ajax_nopriv_nip_finder_fetch_gus_data', $plugin_public, 'nip_finder_fetch_gus_data');
        $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'nip_finder_save_nip_billing_field');
        $this->loader->add_action('woocommerce_admin_order_data_after_billing_address', $plugin_public, 'nip_finder_display_nip_in_admin_order_meta');
        $this->loader->add_action('woocommerce_checkout_process', $plugin_public, 'nip_finder_validate_nip_billing_field');
        $this->loader->add_action('wp_ajax_nip_finder_fetch_cities', $plugin_public, 'nip_finder_fetch_cities_by_postcode');
        $this->loader->add_action('wp_ajax_nopriv_nip_finder_fetch_cities', $plugin_public, 'nip_finder_fetch_cities_by_postcode');

        $this->loader->add_filter('woocommerce_checkout_fields', $plugin_public, 'nip_finder_add_nip_billing_field');

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
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Nip_Finder_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
