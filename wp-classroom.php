<?php
/**
* Plugin Name: Classroom
* Author: Fresh Brewed Web
* Author URI: https://freshbrewedweb.com
* Version: 2.2.7
* Description: Create a digital video based classroom in WordPress. This plugin gives you the ability to publish classes. It's flexible enough to combine with other well known WordPress plugins to enhance the functionality.
* Tags: classroom, education, school, woocommerce, video
* License: GPL v3 or later
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_CLASSROOM_VERSION', '2.2.7' );
define( 'WP_CLASSROOM_NAMESPACE', 'wp-classroom' );
define( 'WP_CLASSROOM_CLASS_POST_TYPE', 'wp_classroom' );
define( 'WP_CLASSROOM_COURSE_TAXONOMY', 'wp_course' );

define( 'WP_CLASSROOM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_CLASSROOM_VENDOR_DIR', WP_CLASSROOM_PLUGIN_DIR . 'vendor' );

$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ));

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
if ( file_exists( WP_CLASSROOM_VENDOR_DIR . '/CMB2/init.php' ) ) {
	require_once WP_CLASSROOM_VENDOR_DIR . '/CMB2/init.php';
	require_once WP_CLASSROOM_VENDOR_DIR . '/cmb-field-select2/cmb-field-select2.php';
}

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
