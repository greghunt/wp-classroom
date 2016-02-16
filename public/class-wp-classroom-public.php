<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/greghunt/wp-classroom
 * @since      1.0.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/public
 * @author     Greg Hunt <freshbrewedweb@gmail.com>
 */
class WP_Classroom_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $WP_Classroom    The ID of this plugin.
	 */
	private $WP_Classroom;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $WP_Classroom       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $WP_Classroom, $version ) {

		$this->WP_Classroom = $WP_Classroom;
		$this->version = $version;

	}

	/**
	 * Method for getting plugin options
	 *
	 * @since    1.0.0
	 */
	 public function getOption($option_name) {
		 $option = get_option('wp-classroom');
		 return $option[$option_name];
	 }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Classroom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Classroom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if( $this->getOption('frontend-styles') == 1 ) {
			wp_enqueue_style( $this->WP_Classroom, plugin_dir_url( __FILE__ ) . 'css/wp-classroom-public.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Classroom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Classroom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->WP_Classroom, plugin_dir_url( __FILE__ ) . 'js/wp-classroom-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Registers all shortcodes at once
	 *
	 * @return [type] [description]
	 */
	public function register_shortcodes() {
		add_shortcode( 'course_list', array( $this, 'course_list_shortcode' ) );
		add_shortcode( 'courses', array( $this, 'courses_shortcode' ) );
	} // register_shortcodes()


	/**
	 * Processes course_list shortcode
	 *
	 * @param   array	$atts		The attributes from the shortcode
	 *
	 * @uses	get_course_class_list
	 *
	 * @return	str	$html		Output the HTML
	 */
	public function course_list_shortcode( $atts ) {
		$defaults['orderby'] 		= 'date';
		$args			= shortcode_atts( $defaults, $atts, 'course_list' );
		$classes 		= $this->get_course_class_list( $args );

		$html = '<ol>';
		foreach( $classes->posts as $class ) {
			$html .= '<li><a href="'. get_permalink($class) .'">';
			$html .= $class->post_title;
			$html .= '</a></li>';
		}
		$html .= '</ol>';

		return $html;
	} // shortcode()

	/**
	 * Processes courses shortcode
	 *
	 * @param   array	$atts		The attributes from the shortcode
	 *
	 *
	 * @return	str	$html		Output the HTML
	 */
	public function courses_shortcode( $atts ) {
		$defaults['orderby']	= 'date';
		$args			= shortcode_atts( $defaults, $atts, 'course_list' );
		$courses 	= get_terms( 'wp_course' );

		$html = '<ul class="wpclr-courses">';
		foreach( $courses as $course ) {
			$term_meta = get_term_meta($course->term_id);
			$html .= '<li class="wpclr-course"><a href="'. get_term_link($course) .'">';
			if( $term_meta['image'] ) {
				$term_image = wp_get_attachment_image($term_meta['image'][0], 'medium');
				$html .= '<span class="wpclr-course__img">' . $term_image . '</span>';
			}
			$html .= '<h2 class="wpclr-course__title">' . $course->name . '</h2>';
			$html .= '</a></li>';
		}
		$html .= '</ul>';

		return $html;
	} // shortcode()

	//Template for Single Course
	public function get_wp_classroom_template($single_template) {
     global $post;

     if ($post->post_type == 'wp_classroom') {
        $single_template = dirname( __FILE__ ) . '/partials/wp-classroom-public-display.php';
     }
     return $single_template;
	}


	public function get_course_class_list( array $args ) {
		global $post;
		$return = '';

		//Current Courses
		$course_terms = array();
		$courses = wp_get_post_terms($post->ID, 'wp_course');
		foreach( $courses as $course ) {
			$course_terms[] = $course->slug;
		}

		$default_args = array(
			'post_type' => 'wp_classroom',
			'tax_query' => array( //Only Include classes from current courses
				array(
					'taxonomy' => 'wp_course',
					'field'    => 'slug',
					'terms'    => $course_terms,
				),
			),
			'orderby' => 'date',
			'order' => 'ASC',
		);
		//Combine defaults with passed args
		$args = array_merge($default_args, $args);

		$query = new WP_Query( $args );

		if ( 0 == $query->found_posts ) {
			$return = '<div class="alert alert-warning">' . __('Thank you for your interest! There are no job openings at this time', 'wp-classroom') . '.</div>';
		} else {
			$return = $query;
		}

		return $return;
	}

}
