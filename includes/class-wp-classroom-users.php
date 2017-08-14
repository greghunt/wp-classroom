<?php
/**
 * User functionality that includes restricting
 * access to courses and classes.
 *
 * @link       https://wordpress.org/plugins/classroom
 * @since      1.1.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 */
class WP_Classroom_User {

	private $access;

	private $class_access_key;

	private $course_access_key;

	private $message;

	private $user;

	public function __construct() {
		$this->class_access_key = 'wp-classroom_mb_user_class_access';
		$this->course_access_key = 'wp-classroom_mb_user_course_access';
	}

	/**
	 * Method for getting plugin options
	 *
	 * @since    1.0.0
	 */
	public function getOption($option_name) {
		$option = get_option('wp-classroom');
		if( isset($option[$option_name]) )
			return $option[$option_name];
		else
			return FALSE;
	}

	public function can_access() {
		$this->user = wp_get_current_user();
		$this->access = $this->set_access();

		if( is_post_type_archive( WP_CLASSROOM_CLASS_POST_TYPE ) ) {
			$this->message = __("You shouldn't access this archive");
			return false;
		}

		if( is_tax( WP_CLASSROOM_COURSE_TAXONOMY ) ) {
			$this->message = __("You shouldn't access this course");
			return $this->course_accessible();
		}

		$this->message = __("You shouldn't access this class");
		return $this->class_accessible();
	}

	/**
	 * Checks whether the class is accessible
	 * Can either be directly accessible, or
	 * belong to a course that is accessible.
	 *
	 * @return [type] [description]
	 */
	private function class_accessible() {
		$classes = array_intersect($this->get_class_courses(), $this->access['courses']);
		return
			in_array( $this->get_class(), $this->access['classes'] ) ||
			!empty($classes);
	}

	private function course_accessible() {
		return in_array( $this->get_course(), $this->access['courses'] );
	}

	private function default_user_meta( $key = NULL ) {
		$default = array(
			'classes' => array(),
			'courses' => array(),
		);

		return isset($default[$key]) ? $default[$key] : $default;
	}

	/**
	 * Handle forbidden access
	 * @return void
	 */
	public function forbidden() {
		// $this->forbiddenMessage();
		$this->redirect();
	}

	public function forbiddenMessage() {
		echo '<div style="padding:1em;background:red;color:white">'.$this->message.'</div>';
	}

	public function redirect() {
		global $post;
		$url = wp_login_url();

		if( $postRedirect = get_post_meta($post->ID, 'wp_classroom_redirect', TRUE) ) {
			$url = get_permalink($postRedirect);
		} elseif( $globalRedirect = $this->getOption('unauthorized-redirect') ) {
			$url = get_permalink($globalRedirect);
		}

		wp_redirect($url);
	}

	public function get_access() {
		return $this->access;
	}

	private function set_access() {
		return array(
			'classes' => is_user_logged_in() ? get_user_meta($this->user->ID, $this->class_access_key, true) : $this->default_user_meta('classes'),
			'courses' => is_user_logged_in() ? get_user_meta($this->user->ID, $this->course_access_key, true) : $this->default_user_meta('courses'),
		);
	}

	private function get_class() {
		$post = get_post();
		return $post->ID;
	}

	private function get_course() {
		if( is_tax('wp_course') ) {
			return get_queried_object()->term_id;
		}

		return false;
	}

	private function get_class_courses() {
		$courses = wp_get_post_terms($this->get_class(), 'wp_course');
		return wp_list_pluck($courses, 'term_id', 'name');
	}

	public static function remove_access( $user_id, $access, $type = 'class' ) {
		$key = 'wp-classroom_mb_user_'.$type.'_access';
		$current_access = get_post_meta($user_id, $key, TRUE);
		if( isset( $access[$type] ) ) {
			$updated_access = array_diff( $current_access, $access[$type] );
			update_user_meta( $user_id, $key, $updated_access);
		}
	}

	public static function remove_class_access( $user_id, $access ){
		self::remove_access( $user_id, $access, 'class' );
	}

	public static function remove_course_access( $user_id, $access ){
		self::remove_access( $user_id, $access, 'course' );
	}

	public static function update_access( $user_id, $access, $type = 'class' ) {
		$key = 'wp-classroom_mb_user_'.$type.'_access';
		$current_access = get_post_meta($user_id, $key, TRUE);
		if( isset( $access[$type] ) ) {
			$updated_access = array_diff( $current_access, $access[$type] );
			update_user_meta( $user_id, $key, $updated_access);
		}
	}

	public static function update_class_access( $user_id, $access ){
		self::remove_access( $user_id, $access, 'class' );
	}

	public static function update_course_access( $user_id, $access ){
		self::remove_access( $user_id, $access, 'course' );
	}

}
