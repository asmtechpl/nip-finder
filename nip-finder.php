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
 * Plugin Name:       Nip  Finder
 * Plugin URI:        https://nip-finder.pl
 * Description:
Wtyczka automatycznie pobiera dane firmowe z GUS po numerze NIP podczas zakupu, przyspieszając proces składania zamówień dla firm.
 * Version:           1.3.0
 * Author:            CodePress
 * Author URI:        https://code-press.pl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nip-finder
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 */
require 'lib/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

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
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'NIP_FINDER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nip-finder-activator.php
 */
function activate_nip_finder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nip-finder-activator.php';
	Nip_Finder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nip-finder-deactivator.php
 */
function deactivate_nip_finder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nip-finder-deactivator.php';
	Nip_Finder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nip_finder' );
register_deactivation_hook( __FILE__, 'deactivate_nip_finder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-nip-finder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_nip_finder() {

	$plugin = new Nip_Finder();
	$plugin->run();

}

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/asmtechpl/nip-finder/',
    __FILE__,
    'nip-finder'
);

$updateChecker->getVcsApi()->enableReleaseAssets();

run_nip_finder();
