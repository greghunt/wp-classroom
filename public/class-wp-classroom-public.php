<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/greghunt/wp-classroom
 * @since      1.0.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/public
 * @author     Greg Hunt <freshbrewedweb@gmail.com>
 */

require_once( WP_CLASSROOM_PLUGIN_DIR . '/public/partials/shortcodes.php' );

class WP_Classroom_Public {

	use WP_Classroom_Shortcodes;

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
	 * CSS Class Prefix
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the plugin.
	 */
	public $prefix = 'wpclr';

	/**
	 * Users class
	 * @since    1.1.0
	 * @access   private
	 * @var WP_Classroom_Users
	 */
	private $user;

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
		$this->user = new WP_Classroom_User();

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

	/**
	 * Method for loading public templates
	 *
	 * @since    1.0.0
	 */
	public function get_template_path( $slug, $name = NULL, $load = FALSE ) {
		$wp_classroom_template_loader = new WP_Classroom_Template_Loader;
		return $wp_classroom_template_loader->get_template_part( $slug, $name, $load );
	}

	//Template for Single Course
	public function get_wp_classroom_template($single_template) {
		global $post;

		if ($post->post_type == 'wp_classroom') {
			add_filter('the_content', 'do_shortcode');
			$single_template = $this->get_template_path( 'single' );
		}

		return $single_template;

	}

	//Template for Teacher
	public function get_teacher_template($single_template) {

		if( $username = get_query_var( 'teacher_username', false ) ) {
			$single_template = $this->get_template_path( 'teacher' );
		}

		return $single_template;
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
		if( $this->getOption('frontend-styles') == 'on' ) {
			wp_enqueue_style( $this->WP_Classroom, plugin_dir_url( __FILE__ ) . 'css/wp-classroom-public.css', array(), $this->version, 'all' );
		}

	}

	public function restrict_access() {
		if( $this->is_classroom() && !$this->user->can_access() ) {
			$this->user->forbidden();
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

	public function get_course_class_list( array $args, $course = NULL, $reveal = FALSE ) {
		global $post;
		$return = '';

		if( $course ) {
			$course_terms[] = $course->slug;
		} else {
			//Current Courses
			$course_terms = array();
			$courses = wp_get_post_terms($post->ID, 'wp_course');
			foreach( $courses as $course ) {
				$course_terms[] = $course->slug;
			}
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
			'posts_per_page' => 100,
		);
		if( $reveal ) {
			$default_args['suppress_filters'] = TRUE;
		}

		//Combine defaults with passed args
		$args = array_merge($default_args, $args);

		$query = new WP_Query( $args );

		if ( 0 == $query->found_posts ) {
			$return = '<div class="alert alert-warning">' . __('There are currently no classes available.', 'wp-classroom') . '.</div>';
		} else {
			$classes = array();
			foreach( $query->posts as $class ) {
				$class->is_complete = FALSE;
				if( $completed_courses = $this->get_user_completed_courses() ) {
					foreach($completed_courses as $course) {
						if( in_array($class->ID, $course) )
							$class->is_complete = TRUE;
					}
				}
				$classes[] = $class;
			}

			$return = $classes;
		}

		return $return;
	}

	public function complete_class() {
		// Handle request then generate response using WP_Ajax_Response
		$user_id = get_current_user_id();

		if( $user_id !== 0 && ( $id = $_POST['class_id'] ) && $_POST['course'] ) {
			$courses = explode(',', $_POST['course']);
			$completed_courses = $this->get_user_completed_courses($user_id);

			if( !is_array($completed_courses) )
				$completed_courses = array();

			foreach( $courses as $course ) {
				if( isset($completed_courses[$course]) && in_array($id, $completed_courses[$course]) ) {
					continue;
				}
				$completed_courses[$course][] = $id;
			}
			update_user_meta( $user_id, 'wp_classroom_completed', $completed_courses );
		}

		if( $_POST['redirect'] != "" ) {
			$redirect = $_POST['redirect'];
		} elseif( $_POST['return'] != "" ) {
			$redirect = $_POST['return'];
		} else {
			$redirect = home_url();
		}

		wp_redirect($redirect);
		exit();

	}

	private function get_course_progress( $course ) {
		$total = $course->count;
		$completions = $this->get_user_completed_courses(get_current_user_id());

		if( isset($completions[$course->term_id]) )
			$course_completions = $completions[$course->term_id];
		else
			$course_completions = NULL;

		if( is_null($course_completions) )
			return 0;
		else
			$ratio = (count($course_completions) / $total) * 100;

		return number_format($ratio); //% completed
	}

	private function get_user_completed_courses( $user_id = NULL ) {
		if( !$user_id )
			$user_id = get_current_user_id();
		return get_user_meta($user_id, 'wp_classroom_completed', TRUE);
	}


	/**
	 * Customize Adjacent Post Link Order
	 * @param  string $sql SQL String
	 * @return string      Modified SQL
	 */
	public function order_adjacent_post_where($sql) {
		if ( !is_main_query() || !is_singular() )
			return $sql;

		$the_post = get_post( get_the_ID() );
		$patterns = array();
		$patterns[] = '/post_date/';
		$patterns[] = '/\'[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\'/';
		$replacements = array();
		$replacements[] = 'menu_order';
		$replacements[] = $the_post->menu_order;
		return preg_replace( $patterns, $replacements, $sql );
	}

	/**
	 * Sort Posts
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	public function adjacent_post_sort($sql) {
		if ( get_post_type() == "wp_classroom" ) {
			$pattern = '/post_date/';
			$replacement = 'menu_order';
			return preg_replace( $pattern, $replacement, $sql );
		}

		return $sql;

	}

	/**
	 * Add class to body.
	 * @param array $classes Array of classes
	 */
	public function add_body_class($classes) {

	  if ( $this->is_classroom() ) {
	      $classes[] = get_post_type();
	  }

		if( get_query_var('teacher') != '' ) {
				$classes[] = 'wp-classroom-teacher';
		}

	  return $classes;
	}

	/**
	 * Add user's email to URL
	 * @return  void
	 */
	public function add_email_to_url() {
	    if( $this->getOption('add-email-url') == "on" ) {
			$userEmail = NULL;

			if( is_user_logged_in() ) {
				$user = wp_get_current_user();
				if( isset($_GET['wemail']) ) {
					if( $_GET['wemail'] == $user->user_email )
						$userEmail = $user->user_email;
					else
						$userEmail = NULL;
				}

				if( !$userEmail ) {
					wp_redirect( add_query_arg( 'wemail', $user->user_email) );
					exit;
				}
			}
		}
	}

	private function is_classroom() {
		return get_post_type() == WP_CLASSROOM_CLASS_POST_TYPE;
	}

}
