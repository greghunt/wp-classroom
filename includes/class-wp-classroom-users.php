<?php
/**
 * User functionality that includes restricting
 * access to courses and classes.
 *
 * @link       https://wordpress.org/plugins/classroom
 * @since      2.2.7
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 */
class WP_Classroom_User
{

    private $access;

    private $class_access_key;

    private $course_access_key;

    private $message;

    private $user;

    public function __construct()
    {
        $this->class_access_key = 'wp-classroom_mb_user_class_access';
        $this->course_access_key = 'wp-classroom_mb_user_course_access';
    }

    /**
     * Method for getting plugin options
     *
     * @since    1.0.0
     */
    public function getOption($option_name)
    {
        $option = get_option('wp-classroom');
        if (isset($option[$option_name])) {
            return $option[$option_name];
        } else {
            return false;
        }
    }

    public function get_user() {
      return wp_get_current_user();
    }

    public function can_access()
    {
        global $post;
        $this->user = wp_get_current_user();

        // Authors can see their own posts.
        if( in_array( 'administrator', (array) $this->user->roles ) )
          return TRUE;

        // Authors can see their own posts.
        if( $post->post_author == $this->user->ID )
          return TRUE;

        $this->set_access();

        if (is_post_type_archive( WP_CLASSROOM_CLASS_POST_TYPE )) {
            // $this->message = __("You shouldn't access this archive");
            return true;
        }

        if (is_tax( WP_CLASSROOM_COURSE_TAXONOMY )) {
            $this->message = __("You shouldn't access this course");
            return $this->course_accessible();
        }

        $this->message = __("You shouldn't access this class");

        return $this->class_accessible();
    }

    public function belongs_to_course( $courses )
    {
      $course_ids = [];
      foreach( $courses as $slug )
        $course_ids[] = $this->get_course_id_by_slug( $slug );

      return $this->course_accessible( $course_ids );
    }

    /**
     * Checks whether the class is accessible
     * Can either be directly accessible, or
     * belong to a course that is accessible.
     *
     * @return [type] [description]
     */
    private function class_accessible()
    {
        $classes = array_intersect($this->get_class_courses(), $this->access['courses']);
        return
            in_array( $this->get_class(), $this->access['classes'] ) ||
            !empty($classes);
    }

    private function get_course_id_by_slug( $slug )
    {
      return get_term_by('slug', $slug, 'wp_course')->term_id;
    }

    private function course_accessible( $course = null )
    {
        $this->user = wp_get_current_user();
        $this->set_access();

        if( $course == null )
          $course = $this->get_course();

        if( is_array($course) )
          return array_intersect( $course, $this->access['courses'] ) == $course;

        return in_array( $course, $this->access['courses'] );
    }

    private function default_user_meta($key = null)
    {
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
    public function forbidden()
    {
        // $this->forbiddenMessage();
        $this->redirect();
    }

    public function forbiddenMessage()
    {
        echo '<div style="padding:1em;background:red;color:white">'.$this->message.'</div>';
    }

    public function redirect()
    {
        global $post;
        $url = wp_login_url();

        // remove get_taxonomies();
        $terms = get_the_terms($post->ID, 'wp_course');
        $terms_array = [];
        
        // push all redirect links to $terms_array[]. Only add to array if there is a link in that field
        foreach ($terms as $term) {
            $term = get_term_meta($term->term_id, '_wp_course_course_redirect', true);
            if ($term) {
                array_push($terms_array, $term); 
            }
        }

        if ($postRedirect = get_post_meta($post->ID, 'wp_classroom_redirect', true)) {
            $url = get_permalink($postRedirect);
        } // we can obviously only redirect once, and since the above array descends from parent to child, we want to give the lowest level descendent priority over its ancestors. 
        elseif ($taxonomyRedirect = array_pop($terms_array)) { 
            $url = $taxonomyRedirect;
        } elseif ($globalRedirect = $this->getOption('unauthorized-redirect')) {
            $url = get_permalink($globalRedirect);
        }
        
        wp_redirect($url);
    }

    public function get_access()
    {
        return $this->access;
    }

    private function set_access()
    {
        if( !is_null($this->access) )
          return;

        $this->access = array(
            'classes' => is_user_logged_in() ? get_user_meta($this->user->ID, $this->class_access_key, true) : $this->default_user_meta('classes'),
            'courses' => is_user_logged_in() ? get_user_meta($this->user->ID, $this->course_access_key, true) : $this->default_user_meta('courses'),
        );
    }

    private function get_class()
    {
        $post = get_post();
        return $post->ID;
    }

    private function get_course()
    {
        if (is_tax('wp_course')) {
            return get_queried_object()->term_id;
        }

        return false;
    }

    private function get_class_courses()
    {
        $courses = wp_get_post_terms($this->get_class(), 'wp_course');
        return wp_list_pluck($courses, 'term_id', 'name');
    }

    public static function remove_access($user_id, $access, $type = 'class')
    {
        $key = 'wp-classroom_mb_user_'.$type.'_access';
        $current_access = get_post_meta($user_id, $key, true);
        if (isset( $access[$type] )) {
            $updated_access = array_diff( $current_access, $access[$type] );
            update_user_meta( $user_id, $key, $updated_access);
        }
    }

    public static function remove_class_access($user_id, $access)
    {
        self::remove_access( $user_id, $access, 'class' );
    }

    public static function remove_course_access($user_id, $access)
    {
        self::remove_access( $user_id, $access, 'course' );
    }

    public static function update_access($user_id, $access, $type = 'class')
    {
        $key = 'wp-classroom_mb_user_'.$type.'_access';
        $current_access = get_post_meta($user_id, $key, true);
        if (isset( $access[$type] )) {
            $updated_access = array_diff( $current_access, $access[$type] );
            update_user_meta( $user_id, $key, $updated_access);
        }
    }

    public static function update_class_access($user_id, $access)
    {
        self::remove_access( $user_id, $access, 'class' );
    }

    public static function update_course_access($user_id, $access)
    {
        self::remove_access( $user_id, $access, 'course' );
    }

    public function add_teacher_role()
    {
        // Create Teacher Role
        $teacher_role = add_role(
            'teacher',
            __( 'Teacher' ),
            array(
                'read'         => true,  // true allows this capability
                'edit_posts'   => true,
								'can_teach' => true
            )
        );

        // Get roles that can teach by default
        $roles = array();
        ( $role = get_role('teacher') ) ? $roles[] = $role : null;
        ( $role = get_role('administrator') ) ? $roles[] = $role : null;
        ( $role = get_role('editor') ) ? $roles[] = $role : null;
        ( $role = get_role('author') ) ? $roles[] = $role : null;
        ( $role = get_role('contributor') ) ? $roles[] = $role : null;

        foreach ($roles as $role) {
            if ($role) {
                // add a new capability
                $role->add_cap('can_teach', true);
            }
        }
    }

    public static function get_teachers()
    {
      global $wpdb;
  		$users = $wpdb->get_results("SELECT post_author, count(*) AS post_count FROM wp_posts WHERE post_type =
  		'wp_classroom' GROUP BY post_author ORDER BY post_count DESC");
      $teachers = array();
      foreach( $users as $user ) {
        $teachers[] = get_user_by('id', $user->post_author);
      }

      return $teachers;
    }

    public function teacher_profile_route()
    {
        add_rewrite_rule(
        'teacher/([^/]*)/?',
        'index.php?teacher_username=$matches[1]',
        'top'
        );
    }

    public function custom_rewrite_tags($vars)
    {
        add_rewrite_tag('%teacher_username%', '([^&]+)');
    }

    public function custom_request( $query )
    {
			if( isset($query->query_vars['teacher_username']) ) {
				$user = get_user_by('slug', $query->query_vars['teacher_username']);
				if( user_can( $user, 'can_teach' ) )
					$query->query_vars[ 'teacher' ] = new WP_Classroom_Teacher( $user );
			}

			return $query;
    }

}
