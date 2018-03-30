<?php

trait WP_Classroom_Shortcodes {

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
		add_shortcode( 'course_show', array( $this, 'course_show_shortcode' ) );
		add_shortcode( 'classroom_breadcrumb', array( $this, 'classroom_breadcrumb_shortcode' ) );
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
	 * Include content for specific courses
	 *
	 * @param   array	$atts		The attributes from the shortcode
	 *
	 * @return	str	$html		Output the HTML
	 */
	public function course_show_shortcode( $atts, $content = NULL ) {
		$defaults['in'] = null;
		$defaults['not'] = null;
		$args = shortcode_atts( $defaults, $atts, 'course_show' );
		extract($args);

		$in = explode(',', trim($in));
		$not = explode(',', trim($not));

		$student = wp_classroom_student();

		if( $student->belongs_to_course($in) && !$student->belongs_to_course($not) ) {
			return $content;
		}
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
	 * Classroom breadcrumb
	 */
	public function classroom_breadcrumb_shortcode( $atts ) {
		$defaults['submenu'] = false;
		$args	= shortcode_atts( $defaults, $atts, 'classroom_breadcrumb' );

		if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb("<div class=\"{$this->prefix}-breadcrumb\">","</div>");
		}
	}

}
