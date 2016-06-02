<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/freshbrewedweb/wp-classroom
 * @since             1.1.7
 * @package           WP_Classroom
 *
 * @wordpress-plugin
 * Plugin Name:       Classroom
 * Plugin URI:        https://github.com/greghunt/wp-classroom
 * Description:       Create a multimedia classroom within your WordPress site.
 * Version:           1.1.7
 * Author:            Fresh Brewed Web
 * Author URI:        https://freshbrewedweb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-classroom
 * Domain Path:       /languages
 * GitHub Plugin URI: freshbrewedweb/wp-classroom
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_Classroom_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-classroom-activator.php
 */
function activate_WP_Classroom() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-classroom-activator.php';
	WP_Classroom_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-classroom-deactivator.php
 */
function deactivate_WP_Classroom() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-classroom-deactivator.php';
	WP_Classroom_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WP_Classroom' );
register_deactivation_hook( __FILE__, 'deactivate_WP_Classroom' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-classroom.php';

/**
 * Require third party plugins
 *
 * @link {https://wordpress.org/plugins/wp-term-images/}
 */

//Custom Meta Box 2
if ( file_exists( WP_Classroom_PLUGIN_DIR . 'vendor/CMB2/init.php' ) ) {
	require_once WP_Classroom_PLUGIN_DIR . 'vendor/CMB2/init.php';
}

//Plugin Acvtivator Class
require_once WP_Classroom_PLUGIN_DIR . 'vendor/TGM-Plugin-Activation/class-tgm-plugin-activation.php';


//WP Term Images
require WP_Classroom_PLUGIN_DIR . 'vendor/wp-term-images/wp-term-images.php';

//Groups WooCommerce
define('GROUPS_WS_CORE_DIR', WP_Classroom_PLUGIN_DIR . 'vendor/groups-woocommerce' );
require GROUPS_WS_CORE_DIR . '/groups-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_WP_Classroom() {

	$plugin = new WP_Classroom();
	$plugin->run();

}
run_WP_Classroom();
//Execute shortcodes in widgets
