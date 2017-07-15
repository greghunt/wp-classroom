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
	protected $theme_template_directory = 'classroom';

	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 *
	 * In this case, `WP_CLASSROOM_PLUGIN_DIR` would be defined in the root plugin file as:
	 *
	 * ~~~
	 * define( 'WP_CLASSROOM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	 * ~~~
	 *
	 * @since 1.0.0
	 * @type string
	 */

	protected $plugin_directory = WP_CLASSROOM_PLUGIN_DIR;

}
