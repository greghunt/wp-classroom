<?php
/**
 * WP Classroom
 *
 * @package   WP_Classroom
 * @author    Greg Hunt
 * @link      https://github.com/greghunt/wp-classroom
 */

if( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require plugin_dir_path( __FILE__ ) . 'class-gamajo-template-loader.php';
}

/**
 * Template loader for WP Classroom.
 *
 * Only need to specify class properties here.
 *
 * @package WP_Classroom
 * @author  Gary Jones
 */
class WP_Classroom_Template_Loader extends Gamajo_Template_Loader {

	/**
	 * Prefix for filter names.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $filter_prefix = 'WP_Classroom';

	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $theme_template_directory = 'wp-classroom';

	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 *
	 * In this case, `WP_Classroom_PLUGIN_DIR` would be defined in the root plugin file as:
	 *
	 * ~~~
	 * define( 'WP_Classroom_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	 * ~~~
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $plugin_directory = WP_Classroom_PLUGIN_DIR;

	// public function get_template_path( $slug, $name = NULL ) {
	// 	// Execute code for this part
	// 	do_action( 'get_template_part_' . $slug, $slug, $name );
	//
	// 	// Get files names of templates, for given slug and name.
	// 	$template_names = $this->get_template_file_names( $slug, $name );
	//
	// 	// No file found yet
	// 	$located = false;
	//
	// 	// Remove empty entries
	// 	$template_names = array_filter( (array) $template_names );
	// 	$template_paths = $this->get_template_paths();
	//
	// 	// Try to find a template file
	// 	foreach ( $template_names as $template_name ) {
	// 		// Trim off any slashes from the template name
	// 		$template_name = ltrim( $template_name, '/' );
	//
	// 		// Try locating this template file by looping through the template paths
	// 		foreach ( $template_paths as $template_path ) {
	// 			if ( file_exists( $template_path . $template_name ) ) {
	// 				$located = $template_path . $template_name;
	// 				break 2;
	// 			}
	// 		}
	// 	}
	//
	// 	if ( $load && $located ) {
	// 		load_template( $located, $require_once );
	// 	}
	//
	// 	return $located;
	// }

}
