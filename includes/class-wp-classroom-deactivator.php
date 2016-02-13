<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 * @author     Your Name <email@example.com>
 */
class WP_Classroom_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		//TODO: Doesn't seem to be working
		flush_rewrite_rules();
	}

}
