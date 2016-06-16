<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/admin
 * @author     Your Name <email@example.com>
 */
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

		$this->WP_Classroom = $WP_Classroom;
		$this->plugin_name = $WP_Classroom;
		$this->version = $version;
		$this->key = $this->plugin_name;
		$this->metabox_id = $this->key . '_mb';

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
			'name'                  => _x( 'Classes', 'Post Type General Name', 'wp-classroom' ),
			'singular_name'         => _x( 'Class', 'Post Type Singular Name', 'wp-classroom' ),
			'menu_name'             => __( 'Classroom', 'wp-classroom' ),
			'name_admin_bar'        => __( 'Classroom', 'wp-classroom' ),
			'archives'              => __( 'Class Archives', 'wp-classroom' ),
			'parent_item_colon'     => __( 'Parent Class:', 'wp-classroom' ),
			'all_items'             => __( 'All Classes', 'wp-classroom' ),
			'add_new_item'          => __( 'Add New Class', 'wp-classroom' ),
			'add_new'               => __( 'Add New', 'wp-classroom' ),
			'new_item'              => __( 'New Class', 'wp-classroom' ),
			'edit_item'             => __( 'Edit Class', 'wp-classroom' ),
			'update_item'           => __( 'Update Class', 'wp-classroom' ),
			'view_item'             => __( 'View Class', 'wp-classroom' ),
			'search_items'          => __( 'Search Classes', 'wp-classroom' ),
			'not_found'             => __( 'Not found', 'wp-classroom' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wp-classroom' ),
			'featured_image'        => __( 'Featured Image', 'wp-classroom' ),
			'set_featured_image'    => __( 'Set featured image', 'wp-classroom' ),
			'remove_featured_image' => __( 'Remove featured image', 'wp-classroom' ),
			'use_featured_image'    => __( 'Use as featured image', 'wp-classroom' ),
			'insert_into_item'      => __( 'Insert into class', 'wp-classroom' ),
			'uploaded_to_this_item' => __( 'Uploaded to this class', 'wp-classroom' ),
			'items_list'            => __( 'Classroom', 'wp-classroom' ),
			'items_list_navigation' => __( 'Classroom navigation', 'wp-classroom' ),
			'filter_items_list'     => __( 'Filter classroom', 'wp-classroom' ),
		);
		$rewrite = array(
			'slug'                  => 'classroom',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Class', 'wp-classroom' ),
			'description'           => __( 'Classroom', 'wp-classroom' ),
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
		register_post_type( 'wp_classroom', $args );

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
	 	register_taxonomy( 'wp_course', array( 'wp_classroom' ), $args );

	 }

	/**
	 * Registers the Classroom Metabox
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
		
		if( $this->getOption('video-host') == "wistia" ) {
			$cmb_classroom->add_field( array(
				'name' => __( 'Video (Wistia)', 'wp_classroom' ),
				'desc' => __( 'The main class video. Enter the Wistia Video ID.', 'wp_classroom' ),
				'id'   => $prefix . 'video',
				'type' => 'text',
			) );			
		} else {
			$cmb_classroom->add_field( array(
				'name' => __( 'Video', 'wp_classroom' ),
				'desc' => __( 'The main class video.', 'wp_classroom' ),
				'id'   => $prefix . 'video',
				'type' => 'oembed',
			) );
		}

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
	* Install dependent Plugins
	*/
	function wpclr_register_required_plugins() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(
	
			array(
				'name'         => 'Simple Page Ordering',
				'slug'         => 'simple-page-ordering',
				'required'     => false, 
			),

			array(
				'name'         => 'WP Term Order',
				'slug'         => 'wp-term-order',
				'required'     => false, 
			),

			array(
				'name'         => 'Woocommerce',
				'slug'         => 'woocommerce',
				'required'     => false, 
			),

			array(
				'name'               => 'Groups Restrict Categories',
				'slug'               => 'groups-restrict-categories', 
				'source'             => WP_Classroom_PLUGIN_DIR . 'vendor/groups-restrict-categories-1.4.1.zip', 
				'required'           => false,
			),

			array(
				'name'         => 'Groups',
				'slug'         => 'groups',
				'required'     => false, 
			),

			array(
				'name'         => 'Groups 404 Redirect',
				'slug'         => 'groups-404-redirect',
				'required'     => false, 
			),

		
			array(
				'name'      => 'GitHub Updater',
				'slug'      => 'github-updater',
				'source'    => 'https://github.com/afragen/github-updater/archive/develop.zip',
				'required'  => TRUE,
			),
	
		);
	
		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = array(
			'id'           => 'wp-classroom',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);
	
		tgmpa( $plugins, $config );
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

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

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
			'name' => __( 'Video Host', $this->plugin_name ),
			'id'   => 'video-host',
			'type' => 'radio',
			'default' => 'youtube',
			'options' => array(
				'youtube' => 'Youtube',
				'wistia' => 'Wistia',
			)
		) );

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