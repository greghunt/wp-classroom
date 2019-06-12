<?php

class WP_Classroom_Class {

	public function videoEmbed() {
		$video = get_post_meta(get_the_ID(), 'wp_classroom_video', TRUE);
	  return wp_oembed_get( $video );
	}

	public function getCourse() {
		if( !get_post_type() == "wp_classroom" )
			return null;

		$course = wp_get_post_terms(get_the_id(), 'wp_course');
		if( empty($course) )
			return null;

		return $course[0];
	}

}
