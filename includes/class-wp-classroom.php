<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 * @author     Greg Hunt <freshbrewedweb@gmail.com>
 */
class WP_Classroom {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Classroom_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $WP_Classroom    The string used to uniquely identify this plugin.
	 */
	protected $WP_Classroom;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->WP_Classroom = 'wp-classroom';
		$this->version = WP_CLASSROOM_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_global_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Classroom_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Classroom_i18n. Defines internationalization functionality.
	 * - WP_Classroom_Admin. Defines all hooks for the admin area.
	 * - WP_Classroom_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-i18n.php';

		/**
		 * Extended WP_User for teachers
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-teacher.php';

		/**
		 * The class responsible for user restriction and access
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-users.php';

		/**
		 * The class responsible for classroom videos
		 */
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-video.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-classroom-admin.php';

		/**
		 * The class responsible for loading template views
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-template-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-classroom-public.php';

		/**
		 * Helpers for the classroom
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-class.php';

		/**
		 * The class responsible for purchasing integration.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-classroom-purchase-handler.php';

		/**
		 * Helper template functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/helpers.php';


		$this->loader = new WP_Classroom_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Classroom_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_Classroom_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to both admin and frontend.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_global_hooks() {
		$video = new WP_Classroom_Video();
		$this->loader->add_action( 'init', $video, 'add_wistia_provider' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_Classroom_Admin( $this->get_WP_Classroom(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_classroom_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_classroom_taxonomy' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'classroom_register_metabox' );
		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'associate_course_teachers' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );

		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'add_options_page_metabox' );
		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'add_user_access_metabox' );

		$purchasable = new WP_Classroom_Woocommerce_Purchase;
		$purchasable->init();

		//Add order to classroom table
		$this->loader->add_action( 'manage_edit-wp_classroom_columns', $plugin_admin, 	'add_new_classes_column' );
		$this->loader->add_action( 'manage_wp_classroom_posts_custom_column', $plugin_admin, 	'show_order_column' );
		$this->loader->add_filter( 'manage_edit-wp_classroom_sortable_columns', $plugin_admin, 	'order_column_register_sortable' );


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_Classroom_Public( $this->get_WP_Classroom(), $this->get_version() );
		$user_public = new WP_Classroom_User( $this->get_WP_Classroom(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'single_template', $plugin_public, 'get_wp_classroom_template' );
		$this->loader->add_filter( 'get_previous_post_sort', $plugin_public, 'adjacent_post_sort' );
		$this->loader->add_filter( 'get_next_post_sort', $plugin_public, 'adjacent_post_sort' );
		$this->loader->add_filter( 'get_next_post_where', $plugin_public, 'order_adjacent_post_where' );
		$this->loader->add_filter( 'get_previous_post_where', $plugin_public, 'order_adjacent_post_where' );
		$this->loader->add_filter( 'body_class', $plugin_public, 'add_body_class' );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_action( 'init', $plugin_public, 'add_email_to_url' );

		$this->loader->add_action( 'init', $user_public, 'teacher_profile_route' );
		$this->loader->add_action( 'init', $user_public, 'custom_rewrite_tags' );
		$this->loader->add_filter( 'parse_request', $user_public, 'custom_request' );
		$this->loader->add_filter( 'template_include', $plugin_public, 'get_teacher_template' );


		$this->loader->add_action( 'wp_ajax_complete_class', $plugin_public, 'complete_class' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'restrict_access' );

		/**
		 * Action instead of template tag.
		 *
		 * do_action( 'course_list' );
		 *
		 * @link 	http://nacin.com/2010/05/18/rethinking-template-tags-in-plugins/
		 */
		$this->loader->add_action( 'course_list', $plugin_public, 'course_list_shortcode' );
		$this->loader->add_action( 'courses', $plugin_public, 'courses_shortcode' );
		$this->loader->add_action( 'student_profile', $plugin_public, 'student_profile_shortcode' );
		$this->loader->add_action( 'complete_class', $plugin_public, 'complete_class_shortcode' );
		$this->loader->add_action( 'course_progress', $plugin_public, 'course_progress_shortcode' );
		$this->loader->add_action( 'classroom_login', $plugin_public, 'classroom_login_shortcode' );

		/**
		* Execute shortcodes in widgets
		*
		*/
		add_filter('widget_text', 'do_shortcode');

		//Override Groups Restrict Categories function that hides terms
		add_filter( 'list_terms_exclusions', '__return_false', 99, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_WP_Classroom() {
		return $this->WP_Classroom;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Classroom_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
