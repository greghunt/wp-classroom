<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      2.2.7
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/admin
 */

require_once( WP_CLASSROOM_PLUGIN_DIR . '/includes/class-woocommerce-purchase.php' );

class WP_Classroom_Admin {

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
	 * Plugin Name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $plugin_name;

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id;

	/**
	 * Key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $WP_Classroom       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $WP_Classroom, $version ) {

		$this->WP_Classroom = WP_CLASSROOM_NAMESPACE;
		$this->plugin_name = WP_CLASSROOM_NAMESPACE;
		$this->version = WP_CLASSROOM_VERSION;
		$this->key = $this->plugin_name;
		$this->metabox_id = $this->key . '_mb';

	}

	/**
	 * Method for getting plugin options
	 *
	 * @since    1.0.0
	 */
	 public function getOption($option_name) {
		 $option = get_option(WP_CLASSROOM_NAMESPACE);
		 if( isset($option[$option_name]) )
 			 return $option[$option_name];
 		else
 			return FALSE;
	 }

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->WP_Classroom, plugin_dir_url( __FILE__ ) . 'css/wp-classroom-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->WP_Classroom, plugin_dir_url( __FILE__ ) . 'js/wp-classroom-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Create Classroom custom post type
	 *
	 * @since    1.0.0
	 */
	public function wp_classroom_post_type() {

		$labels = array(
			'name'                  => _x( 'Classes', 'Post Type General Name', WP_CLASSROOM_NAMESPACE ),
			'singular_name'         => _x( 'Class', 'Post Type Singular Name', WP_CLASSROOM_NAMESPACE ),
			'menu_name'             => __( 'Classroom', WP_CLASSROOM_NAMESPACE ),
			'name_admin_bar'        => __( 'Classroom', WP_CLASSROOM_NAMESPACE ),
			'archives'              => __( 'Class Archives', WP_CLASSROOM_NAMESPACE ),
			'parent_item_colon'     => __( 'Parent Class:', WP_CLASSROOM_NAMESPACE ),
			'all_items'             => __( 'All Classes', WP_CLASSROOM_NAMESPACE ),
			'add_new_item'          => __( 'Add New Class', WP_CLASSROOM_NAMESPACE ),
			'add_new'               => __( 'Add New', WP_CLASSROOM_NAMESPACE ),
			'new_item'              => __( 'New Class', WP_CLASSROOM_NAMESPACE ),
			'edit_item'             => __( 'Edit Class', WP_CLASSROOM_NAMESPACE ),
			'update_item'           => __( 'Update Class', WP_CLASSROOM_NAMESPACE ),
			'view_item'             => __( 'View Class', WP_CLASSROOM_NAMESPACE ),
			'search_items'          => __( 'Search Classes', WP_CLASSROOM_NAMESPACE ),
			'not_found'             => __( 'Not found', WP_CLASSROOM_NAMESPACE ),
			'not_found_in_trash'    => __( 'Not found in Trash', WP_CLASSROOM_NAMESPACE ),
			'featured_image'        => __( 'Featured Image', WP_CLASSROOM_NAMESPACE ),
			'set_featured_image'    => __( 'Set featured image', WP_CLASSROOM_NAMESPACE ),
			'remove_featured_image' => __( 'Remove featured image', WP_CLASSROOM_NAMESPACE ),
			'use_featured_image'    => __( 'Use as featured image', WP_CLASSROOM_NAMESPACE ),
			'insert_into_item'      => __( 'Insert into class', WP_CLASSROOM_NAMESPACE ),
			'uploaded_to_this_item' => __( 'Uploaded to this class', WP_CLASSROOM_NAMESPACE ),
			'items_list'            => __( 'Classroom', WP_CLASSROOM_NAMESPACE ),
			'items_list_navigation' => __( 'Classroom navigation', WP_CLASSROOM_NAMESPACE ),
			'filter_items_list'     => __( 'Filter classroom', WP_CLASSROOM_NAMESPACE ),
		);
		$rewrite = array(
			'slug'                  => 'classroom',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Class', WP_CLASSROOM_NAMESPACE ),
			'description'           => __( 'Classroom', WP_CLASSROOM_NAMESPACE ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-welcome-learn-more',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => 'classroom',
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'post',
		);
		register_post_type( WP_CLASSROOM_CLASS_POST_TYPE, $args );

	}

	/**
	 * Create Classroom course taxonomy
	 *
	 * @since    1.0.0
	 */
	 // Register Custom Taxonomy
	 public function wp_classroom_taxonomy() {

	 	$labels = array(
	 		'name'                       => _x( 'Courses', 'Taxonomy General Name', 'wp-classroom' ),
	 		'singular_name'              => _x( 'Course', 'Taxonomy Singular Name', 'wp-classroom' ),
	 		'menu_name'                  => __( 'Course', 'wp-classroom' ),
	 		'all_items'                  => __( 'All Courses', 'wp-classroom' ),
	 		'parent_item'                => __( 'Parent Course', 'wp-classroom' ),
	 		'parent_item_colon'          => __( 'Parent Course:', 'wp-classroom' ),
	 		'new_item_name'              => __( 'New Course Name', 'wp-classroom' ),
	 		'add_new_item'               => __( 'Add New Course', 'wp-classroom' ),
	 		'edit_item'                  => __( 'Edit Course', 'wp-classroom' ),
	 		'update_item'                => __( 'Update Course', 'wp-classroom' ),
	 		'view_item'                  => __( 'View Course', 'wp-classroom' ),
	 		'separate_items_with_commas' => __( 'Separate courses with commas', 'wp-classroom' ),
	 		'add_or_remove_items'        => __( 'Add or remove courses', 'wp-classroom' ),
	 		'choose_from_most_used'      => __( 'Choose from the most used', 'wp-classroom' ),
	 		'popular_items'              => __( 'Popular Courses', 'wp-classroom' ),
	 		'search_items'               => __( 'Search Courses', 'wp-classroom' ),
	 		'not_found'                  => __( 'Not Found', 'wp-classroom' ),
	 		'no_terms'                   => __( 'No Courses', 'wp-classroom' ),
	 		'items_list'                 => __( 'Course list', 'wp-classroom' ),
			'items_list_navigation'      => __( 'Course list navigation', 'wp-classroom' ),
	 	);
		$rewrite = array(
			'slug'                       => 'course',
			'with_front'                 => true,
			'hierarchical'               => false,
		);
	 	$args = array(
	 		'labels'                     => $labels,
	 		'hierarchical'               => true,
	 		'public'                     => true,
	 		'show_ui'                    => true,
	 		'show_admin_column'          => true,
	 		'show_in_nav_menus'          => true,
	 		'show_tagcloud'              => true,
	 		'rewrite'              			 => $rewrite,
	 	);
	 	register_taxonomy( WP_CLASSROOM_COURSE_TAXONOMY, array( WP_CLASSROOM_CLASS_POST_TYPE ), $args );

	 }

	/**
	 * Registers the Classroom Options
	 * @uses CMB2
	 * @return [type] [description]
	 */
	function classroom_register_metabox() {

		$prefix = 'wp_classroom_';

		/**
		 * Metabox to be displayed on a single page ID
		 */
		$cmb_classroom = new_cmb2_box( array(
			'id'           => $prefix . 'options',
			'title'        => __( 'Classroom Options', 'wp_classroom' ),
			'object_types' => array( 'wp_classroom', ), // Post type
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		) );

		$cmb_classroom->add_field( array(
			'name' => __( 'Video', 'wp_classroom' ),
			'desc' => __( 'The main class video.', 'wp_classroom' ),
			'id'   => $prefix . 'video',
			'type' => 'oembed',
		) );

		$cmb_classroom->add_field( array(
			'name' => __( 'Redirect', 'wp_classroom' ),
			'desc' => __( 'Redirect unauthorized users to this page ID.', 'wp_classroom' ),
			'id'   => $prefix . 'redirect',
			'type' => 'text_small',
		) );
		
	}

	/**
	 * Associates the Course with Teachers
	 * @uses CMB2
	 * @return [type] [description]
	 */
	function associate_course_teachers() {

		$prefix = '_wp_course_';

		/**
		 * Metabox to be displayed on a single page ID
		 */
		$cmb_term = new_cmb2_box( array(
			'id'               => $prefix . 'edit', 
 			'title'            => esc_html__( 'Course Teachers', 'cmb2' ), // Doesn't output for term boxes 
 			'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta 
 			'taxonomies'       => array( 'wp_course' ), // Tells CMB2 which taxonomies should have these fields 
		) );

		$cmb_term->add_field( array(
			'name'     => __( 'Teachers', $this->plugin_name ),
			'id'       => $prefix . 'teacher',
			'type'    => 'multicheck',
			'select_all_button' => false,
			'options' => $this->teacherOptions(),
		) );

		$cmb_term->add_field( array(
			'name'     => __( 'Course Redirect', $this->plugin_name ),
			'id'       => $prefix . 'course_redirect',
			'type'    => 'text',
			'object_types'     => array( 'term' ),
			'taxonomies'  => array( 'wp_course' ),
		));
	}	

	/**
	* add order column to admin listing screen for classes
	*/
	function add_new_classes_column($columns) {
		$new_item['menu_order'] = "Order";
		return array_slice($columns, 0, 1, true) + $new_item + array_slice($columns, 1, count($columns) - 1, true) ;
	}

	/**
	* show custom order column values
	*/
	function show_order_column($name){
	  global $post;

	  switch ($name) {
	    case 'menu_order':
	      $order = $post->menu_order;
	      echo $order;
	      break;
	   default:
	      break;
	   }
	}

	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	  $columns['menu_order'] = 'menu_order';
	  return $columns;
	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$title = 'Settings';
		$this->options_page = add_submenu_page( 'edit.php?post_type=wp_classroom', $title, $title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	public function add_product_purchase_metabox() {
		WP_Classroom_Woocommerce_Purchase::add_product_metabox();
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	public function add_options_page_metabox() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		$cmb->add_field( array(
			'name' => __( 'Use Frontend Styles', $this->plugin_name ),
			'id'   => 'frontend-styles',
			'type' => 'checkbox',
		) );

		$cmb->add_field( array(
			'name' => __( 'Class Count', $this->plugin_name ),
			'id'   => 'class-count',
			'type' => 'radio',
			'options' => array(
				'global' => 'Global',
				'course' => 'Course',
			)
		) );

		$cmb->add_field( array(
			'name' => __( 'Add Email to URL', $this->plugin_name ),
			'desc' => __( 'This is useful for Wistia embeds, which will pass the email add allow you to track video usage per user.', $this->plugin_name ),
			'id'   => 'add-email-url',
			'type' => 'checkbox',
		) );

		$cmb->add_field( array(
			'name' => __( 'Access Redirect', $this->plugin_name ),
			'desc' => __( 'Add the ID of the page you want to send users who are not logged in. If left blank they will be sent to the default login. If specified on a course or class, that will be used in priority.', $this->plugin_name ),
			'id'   => 'unauthorized-redirect',
			'type' => 'text_small',
		) );

	}

	/**
	 * Add metabox to the user profile
	 * Used for assigning access to Classes and/or Courses.
	 * @since  1.1.0
	 */
	public function add_user_access_metabox() {
		$prefix = $this->metabox_id . '_user_';
		/**
		 * Metabox
		 */
		$cmb_user = new_cmb2_box( array(
			'id'               => $prefix . 'edit',
			'title'            => __( 'Classroom', $this->plugin_name ), // Doesn't output for user boxes
			'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
			'show_names'       => true,
			'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
		) );
		/**
		 * Metabox Fields
		 */
		$cmb_user->add_field( array(
			'name'     => __( 'Class Access', $this->plugin_name ),
			'desc'     => __( 'Choose what individual classes and/or courses this user can access. Select and drag to reorder.', $this->plugin_name ),
			'id'       => $prefix . 'access_title',
			'type'     => 'title',
			'on_front' => false,
		) );

		$cmb_user->add_field( array(
			'name'     => __( 'Courses', $this->plugin_name ),
			'id'       => $prefix . 'course_access',
			'type'    => 'pw_multiselect',
			'options' => $this->courseOptions(),
		) );

		$cmb_user->add_field( array(
			'name'    => __( 'Classes', $this->plugin_name ),
			'id'      => $prefix . 'class_access',
			'type'    => 'pw_multiselect',
			'options' => $this->classOptions()
		) );

		$cmb_user->add_field( array(
			'name'    => __( 'Teacher', $this->plugin_name ),
			'id'      => $prefix . 'teacher',
			'type'    => 'title',
			'show_on_cb' => array($this, 'show_only_for_teachers')
		) );

		$cmb_user->add_field( array(
			'name'    => __( 'Title', $this->plugin_name ),
			'id'      => $prefix . 'teacher_title',
			'type'    => 'text',
			'show_on_cb' => array($this, 'show_only_for_teachers')
		) );

	}

	/**
	 * Only display a metabox for teachers
	 * @param  object $cmb CMB2 object
	 * @return bool        True/false whether to show the metabox
	 */
	function show_only_for_teachers( $user ) {
		return user_can( $user->object_id(), 'can_teach' );
	}

	/**
	 * Get course options for select field
	 * @return [type] [description]
	 */
	private function courseOptions() {
		$terms = get_terms('wp_course', array('hide_empty' => false) );
		return wp_list_pluck( $terms, 'name', 'term_id' );
	}

	private function teacherOptions() {
		$users = get_users();
		$teachers = [];
		
		foreach ($users as $user) {
			if($user->roles[0] == "teacher" || $user->roles[0] == "administrator" ) {
				$teachers[$user->ID] = $user->display_name;
			}
		}
		
		return $teachers;
	}
	/**
	 * Get Class Options for select field.
	 * @return [type] [description]
	 */
	private function classOptions() {
		$defaults = array(
			'post_type' => 'wp_classroom',
			'posts_per_page' => -1
		);
		$query = new WP_Query( $defaults );
		return wp_list_pluck( $query->get_posts(), 'post_title', 'ID' );
	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}


}
