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
 	 * Adds a settings page link to a menu
 	 *
 	 * @since 		1.0.0
 	 * @return 		void
 	 */
 	public function add_menu() {
		// add_submenu_page ( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
		add_submenu_page(
			'edit.php?post_type=wp_classroom',
			__('Classroom Settings', $this->plugin_name),
			__('Settings', $this->plugin_name),
			'edit_posts',
			$this->plugin_name,
			array( $this, 'options_page' )
		);

 	} // add_menu()

 	/**
 	 * Creates the options page
 	 *
 	 * @since 		1.0.0
 	 * @return 		void
 	 */
 	public function options_page() {
 		?><h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 		<form method="post" action="options.php"><?php
 		settings_fields( $this->plugin_name );
 		do_settings_sections( $this->plugin_name );
 		submit_button( 'Save Settings' );
 		?></form><?php
 	} // options_page()

 	/**
 	 * Registers plugin settings, sections, and fields
 	 *
 	 * @since 		1.0.0
 	 * @return 		void
 	 */
 	public function register_settings() {
 		// register_setting( $option_group, $option_name, $sanitize_callback );
 		register_setting(
 			$this->plugin_name,
 			$this->plugin_name
 		);

 		// add_settings_section( $id, $title, $callback, $menu_slug );
 		add_settings_section(
 			$this->plugin_name . '-display-options',
 			apply_filters( $this->plugin_name . '-display-section-title', __( 'Front End', $this->plugin_name ) ),
 			NULL, //array( $this, 'display_options_section' ),
 			$this->plugin_name
 		);

 		// add_settings_field( $id, $title, $callback, $menu_slug, $section, $args );
 		add_settings_field(
 			'frontend-styles',
 			apply_filters( $this->plugin_name . '-frontend-styles-label', __( 'Use Frontend Styles', $this->plugin_name ) ),
 			array( $this, 'frontend_styles_field' ),
 			$this->plugin_name,
 			$this->plugin_name . '-display-options'
 		);


 		// add_settings_field( $id, $title, $callback, $menu_slug, $section, $args );
 		add_settings_field(
 			'frontend-class-count',
 			apply_filters( $this->plugin_name . '-frontend-class-count-label', __( 'Class Count', $this->plugin_name ) ),
 			array( $this, 'class_count_field' ),
 			$this->plugin_name,
 			$this->plugin_name . '-display-options'
 		);

 	} // register_settings()

 	/**
 	 * Creates a settings section
 	 *
 	 * @since 		1.0.0
 	 * @param 		array 		$params 		Array of parameters for the section
 	 * @return 		mixed 						The settings section
 	 */
 	public function display_options_section( $params ) {
 		echo '<p>' . $params['title'] . '</p>';
 	} // display_options_section()

 	/**
 	 * Creates a settings field
 	 *
 	 * @since 		1.0.0
 	 * @return 		mixed 			The settings field
 	 */
 	public function frontend_styles_field() {
 		$options 	= get_option( $this->plugin_name );
 		$option 	= 0;
 		if ( ! empty( $options['frontend-styles'] ) ) {
 			$option = $options['frontend-styles'];
 		}
 		?>
		<input type="checkbox" id="<?php echo $this->plugin_name ?>[frontend-styles]" name="<?php echo $this->plugin_name ?>[frontend-styles]" value="1" <?php checked( 1, $option ); ?> />
		<?php
 	} // display_options_field()

 	/**
 	 * Creates a settings field
 	 *
 	 * @since 		1.0.0
 	 * @return 		mixed 			The settings field
 	 */
 	public function class_count_field() {
 		$options 	= get_option( $this->plugin_name );
 		$option 	= 0;
 		if ( ! empty( $options['class-count'] ) ) {
 			$option = $options['class-count'];
 		}
 		?>
		<input type="radio" id="<?php echo $this->plugin_name ?>[class-count]" name="<?php echo $this->plugin_name ?>[class-count]" value="global" <?php checked( 'global', $option ); ?> /> <?php _e('Global', $this->plugin_name) ?><br>
		<input type="radio" id="<?php echo $this->plugin_name ?>[class-count]" name="<?php echo $this->plugin_name ?>[class-count]" value="course" <?php checked( 'course', $option ); ?> /> <?php _e('Course', $this->plugin_name) ?>
		<?php
 	} // display_options_field()

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

		$cmb_classroom->add_field( array(
			'name' => __( 'Video', 'wp_classroom' ),
			'desc' => __( 'The main class video.', 'wp_classroom' ),
			'id'   => $prefix . 'video',
			'type' => 'oembed',
		) );

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
	
			/*
			'strings'      => array(
				'page_title'                      => __( 'Install Required Plugins', 'wp-classroom' ),
				'menu_title'                      => __( 'Install Plugins', 'wp-classroom' ),
				/* translators: %s: plugin name. * /
				'installing'                      => __( 'Installing Plugin: %s', 'wp-classroom' ),
				/* translators: %s: plugin name. * /
				'updating'                        => __( 'Updating Plugin: %s', 'wp-classroom' ),
				'oops'                            => __( 'Something went wrong with the plugin API.', 'wp-classroom' ),
				'notice_can_install_required'     => _n_noop(
					/* translators: 1: plugin name(s). * /
					'This theme requires the following plugin: %1$s.',
					'This theme requires the following plugins: %1$s.',
					'wp-classroom'
				),
				'notice_can_install_recommended'  => _n_noop(
					/* translators: 1: plugin name(s). * /
					'This theme recommends the following plugin: %1$s.',
					'This theme recommends the following plugins: %1$s.',
					'wp-classroom'
				),
				'notice_ask_to_update'            => _n_noop(
					/* translators: 1: plugin name(s). * /
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
					'wp-classroom'
				),
				'notice_ask_to_update_maybe'      => _n_noop(
					/* translators: 1: plugin name(s). * /
					'There is an update available for: %1$s.',
					'There are updates available for the following plugins: %1$s.',
					'wp-classroom'
				),
				'notice_can_activate_required'    => _n_noop(
					/* translators: 1: plugin name(s). * /
					'The following required plugin is currently inactive: %1$s.',
					'The following required plugins are currently inactive: %1$s.',
					'wp-classroom'
				),
				'notice_can_activate_recommended' => _n_noop(
					/* translators: 1: plugin name(s). * /
					'The following recommended plugin is currently inactive: %1$s.',
					'The following recommended plugins are currently inactive: %1$s.',
					'wp-classroom'
				),
				'install_link'                    => _n_noop(
					'Begin installing plugin',
					'Begin installing plugins',
					'wp-classroom'
				),
				'update_link' 					  => _n_noop(
					'Begin updating plugin',
					'Begin updating plugins',
					'wp-classroom'
				),
				'activate_link'                   => _n_noop(
					'Begin activating plugin',
					'Begin activating plugins',
					'wp-classroom'
				),
				'return'                          => __( 'Return to Required Plugins Installer', 'wp-classroom' ),
				'plugin_activated'                => __( 'Plugin activated successfully.', 'wp-classroom' ),
				'activated_successfully'          => __( 'The following plugin was activated successfully:', 'wp-classroom' ),
				/* translators: 1: plugin name. * /
				'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'wp-classroom' ),
				/* translators: 1: plugin name. * /
				'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'wp-classroom' ),
				/* translators: 1: dashboard link. * /
				'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'wp-classroom' ),
				'dismiss'                         => __( 'Dismiss this notice', 'wp-classroom' ),
				'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'wp-classroom' ),
				'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'wp-classroom' ),
	
				'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
			),
			*/
		);
	
		tgmpa( $plugins, $config );
	}

}
