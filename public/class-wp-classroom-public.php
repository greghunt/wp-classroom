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
	 * CSS Class Prefix
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the plugin.
	 */
	public $prefix = 'wpclr';
	
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
		add_shortcode( 'student_profile', array( $this, 'student_profile_shortcode' ) );
		add_shortcode( 'complete_class', array( $this, 'complete_class_shortcode' ) );
		add_shortcode( 'course_progress', array( $this, 'course_progress_shortcode' ) );
		add_shortcode( 'classroom_login', array( $this, 'classroom_login_shortcode' ) );
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
		$queried_object = get_queried_object();
		$id = NULL;
		if( get_class($queried_object) == "WP_Term" ) {
			$id = $queried_object->term_id;
		} elseif( get_class($queried_object) == "WP_Post" ) {
			$id = $queried_object->ID;
		}
		
		$defaults = array(
			'orderby' => 'date',
			'numbered' => 'false',
			'course' => NULL,
			'thumbnail' => false,
			'thumbnail_size' => 'thumbnail',
			'thumbnail_class' => $this->prefix.'-course-list__thumb',
			'class' => $this->prefix.'-course-list',
			'count_class' => $this->prefix.'-course-list__num',
			'reveal' => FALSE,
		);
		
		$args		= shortcode_atts( $defaults, $atts, 'course_list' );
		
		if( strtolower($args['reveal']) == 'true' ) {
			$args['reveal'] = TRUE;
		}
		
		$classes 	= $this->get_course_class_list( $args, get_term_by('slug', $args['course'], 'wp_course'), $args['reveal'] );
		
		if( is_string($classes) ) {
			return $classes;
		} else {
			$html = '<ol class="'.$args['class'].'">';
			$count = 1;
			foreach( $classes as $class ) {
				if( $this->getOption('class-count') == "course" ) {
					$order_num = $count;
				} else {
					$order_num = $class->menu_order;
				}
				
				$html .= '<li class="'.$this->prefix.'-cl-item';
				if( $id == $class->ID ) {
					$html .= ' '. $this->prefix.'-cl-item--current';
				}
				if( $class->is_complete == TRUE ) {
					$html .= ' '. $this->prefix.'-cl-item--completed';
				}
				$html .= '"><a href="'. get_permalink($class) .'">';
				if( $args['numbered'] == "true" ) {
					$html .= '<span class="'.$args['count_class'].'">'.$order_num.'</span> ';
				}
				if( $args['thumbnail'] == "true" ) {
					$html .= '<span class="'.$args['thumbnail_class'].'">';
					$html .= get_the_post_thumbnail($class, $args['thumbnail_size']);
					$html .= '</span>';
				}
				$html .= '<span class="'.$args['class'].'__text">' . $class->post_title . '</span>';
				$html .= '</a></li>';
				$count++;
			}
			$html .= '</ol>';
		}
		
		return $html;
	} // shortcode()
	
	/**
	 * Processes student_profile shortcode
	 *
	 * @param   array	$atts		The attributes from the shortcode
	 *
	 * @return	str	$html		Output the HTML
	 */
	public function student_profile_shortcode( $atts ) {
		$defaults['orderby'] 		= 'date';
		$args			= shortcode_atts( $defaults, $atts, 'student_profile' );
		
		$student = wp_get_current_user();
		
		//print_r($student);
		$html = '<figure class="'.$this->prefix.'-student-profile">';
		$html .= get_avatar($student->ID, 300);
		$html .= '<figcaption>'.$student->user_nicename.'</figcaption>';
		$html .= '</figure>';
		
		return $html;
	} // shortcode()
	
	/**
	 * Outputs Button to complete class
	 *
	 * @param   array	$atts		The attributes from the shortcode
	 *
	 * @return	str	$html		Output the HTML
	 */
	public function complete_class_shortcode( $atts ) {
		
		$completed = FALSE;
		$defaults['class'] 		= 'wpclr-complete-class';
		$defaults['button_text'] = __('Complete Class', 'wp_classroom');
		$defaults['redirect'] = home_url();
		$defaults['course'] = '';
		
		$completed_courses = $this->get_user_completed_courses(get_current_user_id());
		
		if( $courses = wp_get_post_terms(get_the_ID(), 'wp_course') ) {
			foreach( $courses as $course ) {
				
				if(
					isset($completed_courses[$course->term_id]) &&
					in_array(get_the_ID(), $completed_courses[$course->term_id])
				) {
					$completed = TRUE;
				}
				
				$defaults['course'] .= $course->term_id;
				if( end($courses) !== $course )
					$defaults['course'] .= ',';
			}
		}
		
		$args	= shortcode_atts( $defaults, $atts, 'complete_class' );
		
		if( $completed ) {
			$html = '<button type="button" class="'.$args['class'].'" disabled>' . __('Completed', 'wp-classroom') . '</button>';
		} else {
			$html = '<form class="wpclr-complete-class-form" action="'.admin_url('admin-ajax.php').'" method="POST">';
			$html .= '<input type="hidden" name="action" value="complete_class">';
			$html .= '<input type="hidden" name="course" value="'. $args['course'] .'">';
			$html .= '<input type="hidden" name="return" value="'. get_permalink(get_the_ID()) .'">';
			$html .= '<input type="hidden" name="redirect" value="'. $args['redirect'] .'">';
			$html .= '<input type="hidden" name="class_id" value="'. get_the_ID() .'">';
			$html .= '<button type="submit" class="'.$args['class'].'">' . $args['button_text'] . '</button>';
			$html .= '</form>';
		}
		
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
	public function courses_shortcode( $atts = [] ) {
		global $wp_query;
		$defaults['orderby']	= 'date';
		$defaults['active']	= NULL;
		$defaults['class']	= $this->prefix.'-courses';
		$defaults['thumbnail'] = "true";
		$args		= shortcode_atts( $defaults, $atts, 'courses' );
		$courses 	= get_terms( 'wp_course', array('hide_empty' => 0) );
		
		if( $args['active'] ) {
			$current_course = get_term_by('slug', $args['active'], 'wp_course');
		} else {
			$wp_object = $wp_query->get_queried_object();
			if( get_class($wp_object) == "WP_Term" ) {
				$current_course = $wp_object;
			} else {
				$current_course = array();
				foreach( wp_get_post_terms($wp_object->ID, 'wp_course')  as $course ) {
					$current_course[$course->slug] = $course->term_id;
				}
			}
		}
		
		$html = '<ul class="'. $args['class'] .'">';
		foreach( $courses as $course ) {
			$term_meta = get_term_meta($course->term_id);
			$html .= '<li class="'.$this->prefix.'-course ';
			$html .= $this->prefix.'-course--'.$course->slug;
			
			if( is_array($current_course) && in_array($course->term_id, $current_course) ) {
				$html .= ' ' . $this->prefix.'-course--active';
			} elseif( $current_course == $course )  {
				$html .= ' ' . $this->prefix.'-course--active';
			}
			$html .= '" data-course="'.trim($course->slug);
			$html .= '"><a href="'. get_term_link($course) .'">';
			
			if( isset($term_meta['image'][0]) && $args['thumbnail'] === "true" ) {
				$term_image = wp_get_attachment_image($term_meta['image'][0], 'medium');
				$html .= '<span class="'.$this->prefix.'-course__img">' . $term_image . '</span>';
			}
			
			$html .= '<span class="'.$this->prefix.'-course__title">' . $course->name . '</span>';
			$html .= '</a></li>';
		}
		$html .= '</ul>';
		
		return $html;
	} // shortcode()
	
	
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
		
		if( $_POST['redirect'] ) {
			$redirect = $_POST['redirect'];
		} elseif( $_POST['return'] ) {
			$redirect = $_POST['redirect'];
		} else {
			$redirect = home_url();
		}
		
		wp_redirect($redirect);
		exit();
		
	}
	
	public function course_progress_shortcode() {
		$terms = wp_get_post_terms( get_the_ID(), 'wp_course' );
		$course = $terms[0];
		$completed = $this->get_course_progress( $course );
		
		$html = '<h5 class="' . $this->prefix .'-progress-txt">' . $course->name . ' ' .$completed.' % Completed</h5>';
		$html .= '<div class="' . $this->prefix .'-progress-bar">';
		$html .= '<span class="' . $this->prefix .'-progress-indicator" style="width:'.$completed.'%"></span>';
		$html .= '</div>';
		
		return $html;
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
	 * classroom_login_shortcode
	 */
	public function classroom_login_shortcode( $atts, $content = NULL ) {
		if ( is_user_logged_in() )
			return '<div class="alert aler-warning">You\'re already logged in.</div>';
		
		$html = '';
		if( !is_null($content) ) {
			$html .= $content;
		}
		
		$class = get_posts(array(
			'post_type' => 'wp_classroom',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'posts_per_page' => 1
		));
		
		$html .= wp_login_form( array(
			'redirect' => ( isset($class[0]) ? get_permalink($class[0]) : NULL),
			'echo' => false
		) );
		return $html;
		
	}
	
	
	/**
	 * Customize Adjacent Post Link Order
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
	
	public function adjacent_post_sort($sql) {
		if ( get_post_type() == "wp_classroom" ) {
			$pattern = '/post_date/';
			$replacement = 'menu_order';
			return preg_replace( $pattern, $replacement, $sql );
		}
		
		return $sql;
		
	}
	
	public function add_body_class($classes) {
	  if (get_post_type() == "wp_classroom" ) {
	      $classes[] = get_post_type();
	  }
	  // return the $classes array
	  return $classes;
	}
	
}
