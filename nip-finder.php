<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://code-press.pl
 * @since             1.0.0
 * @package           Nip_Finder
 *
 * @wordpress-plugin
 * Plugin Name:       Nip Finder
 * Plugin URI:        https://nip-finder.pl
 * Description:       Wtyczka automatycznie pobiera dane firmowe z GUS po numerze NIP podczas zakupu, przyspieszając proces składania zamówień dla firm.
 * Version:           1.3.5
 * Author:            CodePress
 * Author URI:        https://code-press.pl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nip-finder
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Update this as you release new versions.
 */
define( 'NIPFI_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nip-finder-activator.php
 */
function nipfi_activate_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-nip-finder-activator.php';
    Nip_Finder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nip-finder-deactivator.php
 */
function nipfi_deactivate_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-nip-finder-deactivator.php';
    Nip_Finder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'nipfi_activate_plugin' );
register_deactivation_hook( __FILE__, 'nipfi_deactivate_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-nip-finder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point does not affect the page life cycle.
 *
 * @since    1.0.0
 */
function nipfi_run_plugin() {
    $plugin = new Nip_Finder();
    $plugin->run();
}

nipfi_run_plugin();
