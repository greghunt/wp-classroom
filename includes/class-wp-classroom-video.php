<?php
/**
 * Classroom Videos
 *
 * @link       https://wordpress.org/plugins/classroom
 * @since      2.0.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 */
class WP_Classroom_Video {

	public function add_wistia_provider()
	{
		return wp_oembed_add_provider( '/https?:\/\/(.+)?(wistia.com|wi.st)\/(medias|embed)\/.*/', 'http://fast.wistia.com/oembed', true);
	}

}
