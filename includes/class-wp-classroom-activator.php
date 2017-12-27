<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.3
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 * @author     Greg Hunt <freshbrewedweb@gmail.com>
 */
class WP_Classroom_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$admin = new WP_Classroom_Admin( 'wp-classroom', '2.0.3' );
		$users = new WP_Classroom_User( 'wp-classroom', '2.0.3' );
		$admin->wp_classroom_post_type();
		$admin->wp_classroom_taxonomy();
		$users->add_teacher_role();
		flush_rewrite_rules();
	}

}
