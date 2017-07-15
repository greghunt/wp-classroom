<?php
/**
 * The woocommerce implementation to add the ability
 * to purchase a class from a product.
 *
 * @link       https://freshbrewedweb.com
 * @since      1.1.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 */

require_once( WP_CLASSROOM_PLUGIN_DIR . '/includes/interface-purchase.php' );

class WP_Classroom_Woocommerce_Purchase implements WP_Classroom_Purchase {

    /**
	 * Register own Groups tab and handle group association with products.
	 * Register price display modifier.
	 */
	public static function init() {
		if ( is_admin() ) {
			add_action( 'woocommerce_product_write_panel_tabs', array( __CLASS__, 'product_write_panel_tabs' ) );
			add_action( 'woocommerce_product_write_panels',	    array( __CLASS__, 'product_write_panels' ) );
			add_action( 'woocommerce_process_product_meta',	    array( __CLASS__, 'process_product_meta' ), 10, 2 );
		}
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'woocommerce_get_price_html' ), 10, 2 );
	}

    public function add_class_to_user() {
        return;
    }

    public function add_course_to_user() {
        return;
    }

    public static function get_classes() {
        return get_posts(array(
            'post_type' => 'wp_classroom',
            'posts_per_page' => 999
        ));
    }

    public static function get_courses() {
        return get_terms(array(
            'taxonomy' => 'wp_course',
            'show_empty' => true
        ));
    }

    public static function get_user_classes() {
        $user = wp_get_current_user();
        return get_user_meta($user->ID, 'wp-classroom_mb_user_class_access', TRUE);
    }

    public static function get_user_courses() {
        $user = wp_get_current_user();
        return get_user_meta($user->ID, 'wp-classroom_mb_user_course_access', TRUE);
    }

    /**
	 * Groups tab title.
	 */
	public static function product_write_panel_tabs() {
		echo
        '<li class="attributes_tab attribute_options">
            <a href="#woocommerce_classroom"><span>' . __( 'Classroom', WP_CLASSROOM_NAMESPACE ) . '</span></a>
        </li>';
	}


    /**
	 * Groups tab content.
	 */
	public static function product_write_panels() {

		global $post, $wpdb, $woocommerce;

		echo '<div id="woocommerce_classroom" class="panel woocommerce_options_panel" style="padding: 1em;">';

		if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $post->ID ) ) {
			echo '<p>' . __( 'The customer will be a member of the selected classroom as long as the subscription is active. The customer will be removed from the selected groups once the subscription is active.', WP_CLASSROOM_NAMESPACE ) . '</p>';
		} else {
			echo '<p>' . __( 'The customer will be added to or removed from the selected classrooms or courses when purchasing this product.', WP_CLASSROOM_NAMESPACE ) . '</p>';
		}

        $classes = get_post_meta( $post->ID, '_classroom_classes', true );
		$courses = get_post_meta( $post->ID, '_classroom_courses', true );

		echo '<table class="widefat" style="margin:1em;width:50%;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th style="width:50%">' . __( 'Classes', WP_CLASSROOM_NAMESPACE ) . '</th>';
		echo '<th style="width:50%">' . __( 'Courses', WP_CLASSROOM_NAMESPACE ) . '</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
        echo '<tr>';
        echo '<td>';
		foreach( get_posts(array('post_type' => 'wp_classroom')) as $class ) {
			if ( $class = get_post($class) ) {
				woocommerce_wp_checkbox(
					array(
						'id'    => '_classroom_classes['.esc_attr( $class->ID ).']',
						'label' => $class->post_title,
						'value' => in_array( $class->ID, $classes ) ? 'yes' : ''
					)
				);
			}
		}
        echo '</td>';
        echo '<td>';
		foreach( get_terms(array('taxonomy' => 'wp_course')) as $course ) {
			woocommerce_wp_checkbox(
				array(
                    'id'    => '_classroom_courses['.esc_attr( $course->term_id ).']',
                    'label' => $course->name,
                    'value' => in_array( $course->term_id, $courses ) ? 'yes' : ''
				)
			);
		}
        echo '</td>';
        echo '</tr>';
		echo '</tbody>';
		echo '</table>';

		if ( !class_exists( 'WC_Subscriptions_Product' ) || !WC_Subscriptions_Product::is_subscription( $post->ID ) ) {

			$duration     = get_post_meta( $post->ID, '_classroom_duration', true );
			$duration_uom = get_post_meta( $post->ID, '_classroom_duration_uom', true );
			if ( empty( $duration_uom ) ) {
				$duration_uom = 'month';
			}
			switch( $duration_uom ) {
				case 'second' :
					$duration_uom_label = _n( 'Second', 'Seconds', $duration, WP_CLASSROOM_NAMESPACE );
					break;
				case 'minute' :
					$duration_uom_label = _n( 'Minute', 'Minutes', $duration, WP_CLASSROOM_NAMESPACE );
					break;
				case 'hour' :
					$duration_uom_label = _n( 'Hour', 'Hours', $duration, WP_CLASSROOM_NAMESPACE );
					break;
				case 'day' :
					$duration_uom_label = _n( 'Day', 'Days', $duration, WP_CLASSROOM_NAMESPACE );
					break;
				case 'week' :
					$duration_uom_label = _n( 'Week', 'Weeks', $duration, WP_CLASSROOM_NAMESPACE );
					break;
				case 'year' :
					$duration_uom_label = _n( 'Year', 'Years', $duration, WP_CLASSROOM_NAMESPACE );
					break;
				default :
					$duration_uom_label = _n( 'Month', 'Months', $duration, WP_CLASSROOM_NAMESPACE );
					break;
			}

			$duration_help =
				__( 'Leave the duration empty unless you want memberships to end after a certain amount of time.', WP_CLASSROOM_NAMESPACE ) .
				' ' .
				__( 'If the duration is empty, the customer will remain a member of the selected groups forever, unless removed explicitly.', WP_CLASSROOM_NAMESPACE ) .
				' ' .
				__( 'If the duration is set, the customer will only belong to the selected groups during the specified time, based on the <em>Duration</em> and the <em>Time unit</em>.', WP_CLASSROOM_NAMESPACE );

			$duration_help_icon = '<img class="help_tip" data-tip="' . esc_attr( $duration_help ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

			woocommerce_wp_text_input(
				array(
					'id'          => '_classroom_duration',
					'label'       => sprintf( __( 'Duration', WP_CLASSROOM_NAMESPACE ), $duration_help_icon ),
					'value'       => $duration,
					'description' => sprintf( __( '%s (as chosen under <em>Time unit</em>)', WP_CLASSROOM_NAMESPACE ), $duration_uom_label ),
					'placeholder' => __( 'unlimited', WP_CLASSROOM_NAMESPACE )
				)
			);

			// data-tip is filtered out now, append it where we want it
			echo '<script type="text/javascript">';
			echo 'if (typeof jQuery !== "undefined"){';
			echo 'var _classroom_duration_field = jQuery("._classroom_duration_field");';
			echo '}';
			echo 'if (typeof _classroom_duration_field !== "undefined"){';
			echo 'jQuery(_classroom_duration_field).append(\'' . $duration_help_icon . '\');';
			echo '} else {';
			echo 'document.write(\'<p>' . $duration_help . '</p>\')';
			echo '}';
			echo '</script>';

			woocommerce_wp_select(
				array(
					'id'          => '_classroom_duration_uom',
					'label'       => __( 'Time unit', WP_CLASSROOM_NAMESPACE ),
					'value'       => $duration_uom,
					'options'     => array(
						'second' => __( 'Seconds', WP_CLASSROOM_NAMESPACE ),
						'minute' => __( 'Minutes', WP_CLASSROOM_NAMESPACE ),
						'hour'   => __( 'Hours', WP_CLASSROOM_NAMESPACE ),
						'day'    => __( 'Days', WP_CLASSROOM_NAMESPACE ),
						'week'   => __( 'Weeks', WP_CLASSROOM_NAMESPACE ),
						'month'  => __( 'Months', WP_CLASSROOM_NAMESPACE ),
						'year'   => __( 'Years', WP_CLASSROOM_NAMESPACE ),
					)
				)
			);

			echo
				'<noscript>' .
				'<p>' .
				$duration_help .
				'</p>' .
				'</noscript>';

		}

		echo '</div>';
	}


    /**
	 * Register groups for a product.
	 * @param int $post_id product ID
	 * @param object $post product
	 */
	public static function process_product_meta( $post_id, $post ) {
		global $wpdb;

		// refresh classrooms, clear all, then assign checked
		delete_post_meta( $post_id,'_classroom_classes' );
		delete_post_meta( $post_id,'_classroom_courses' );

        if ( !empty( $_POST['_classroom_classes'] ) ) {
            add_post_meta( $post_id, '_classroom_classes', array_keys($_POST['_classroom_classes']) );
        }

        if ( !empty( $_POST['_classroom_courses'] ) ) {
            add_post_meta( $post_id, '_classroom_courses', array_keys($_POST['_classroom_courses']) );
        }

		// duration
		delete_post_meta( $post_id, '_classroom_duration' );
		delete_post_meta( $post_id, '_classroom_duration_uom' );
		$duration  = !empty( $_POST['_classroom_duration'] ) ? intval( $_POST['_classroom_duration'] ) : null;
		if ( $duration <= 0 ) {
			$duration = null;
		}
		if ( $duration !== null ) {
			$duration_uom = !empty( $_POST['_classroom_duration_uom'] ) ? $_POST['_classroom_duration_uom'] : null;
			switch( $duration_uom ) {
				case 'second' :
				case 'minute' :
				case 'hour' :
				case 'day' :
				case 'week' :
				case 'year' :
					break;
				default :
					$duration_uom = 'month';
			}
			add_post_meta( $post_id, '_classroom_duration', $duration );
			add_post_meta( $post_id, '_classroom_duration_uom', $duration_uom );
		}
	}

	/**
	 * Add duration info on prices.
	 * @param string $price
	 * @param WC_Product $product
	 */
	public static function woocommerce_get_price_html( $price, $product ) {
		// $options = get_option( 'groups-woocommerce', null );
		// $show_duration = isset( $options[GROUPS_WS_SHOW_DURATION] ) ? $options[GROUPS_WS_SHOW_DURATION] : GROUPS_WS_DEFAULT_SHOW_DURATION;
		$show_duration = true;
		if ( $show_duration ) {
			$duration     = get_post_meta( $product->id, '_classroom_duration', true );
			if ( !empty( $duration ) ) {
				$duration_uom = get_post_meta( $product->id, '_classroom_duration_uom', true );
				switch( $duration_uom ) {
					case 'second' :
						$price = sprintf( _n( '%s for 1 second', '%s for %d seconds', $duration, WP_CLASSROOM_NAMESPACE ), $price, $duration );
						break;
					case 'minute' :
						$price = sprintf( _n( '%s for 1 minute', '%s for %d minutes', $duration, WP_CLASSROOM_NAMESPACE ), $price, $duration );
						break;
					case 'hour' :
						$price = sprintf( _n( '%s for 1 hour', '%s for %d hours', $duration, WP_CLASSROOM_NAMESPACE ), $price, $duration );
						break;
					case 'day' :
						$price = sprintf( _n( '%s for 1 day', '%s for %d days', $duration, WP_CLASSROOM_NAMESPACE ), $price, $duration );
						break;
					case 'week' :
						$price = sprintf( _n( '%s for 1 week', '%s for %d weeks', $duration, WP_CLASSROOM_NAMESPACE ), $price, $duration );
						break;
					case 'year' :
						$price = sprintf( _n( '%s for 1 year', '%s for %d years', $duration, WP_CLASSROOM_NAMESPACE ), $price, $duration );
						break;
					default :
						$price = sprintf( _n( '%s for 1 month', '%s for %d months', $duration, WP_CLASSROOM_NAMESPACE ), $price, $duration );
						break;
				}
			}
		}
		return $price;
	}

	/**
	 * Retruns true if the membership is limited.
	 * @param WC_Product $product
	 * @return boolean true if product group membership has duration defined, false otherwise
	 */
	public static function has_duration( $product ) {
		$duration = get_post_meta( $product->id, '_classroom_duration', true );
		return $duration > 0;
	}

	/**
	 * Returns the duration of membership in seconds.
	 * @param WC_Product $product
	 * @return duration in seconds or null if there is none defined
	 */
	public static function get_duration( $product ) {
		$result = null;
		$duration     = get_post_meta( $product->id, '_classroom_duration', true );
		if ( !empty( $duration ) ) {
			$duration_uom = get_post_meta( $product->id, '_classroom_duration_uom', true );
			$suffix = $duration > 1 ? 's' : '';
			$result = strtotime( '+' . $duration . ' ' . $duration_uom . $suffix ) - time();
		}
		return $result;
	}

	/**
	 * Calculate the duration in seconds.
	 *
	 * @param int|string $duration
	 * @param string $duration_uom
	 * @return seconds or null if $duration is empty
	 */
	public static function calculate_duration( $duration, $duration_uom ) {
		$result = null;
		if ( !empty( $duration ) ) {
			$suffix = $duration > 1 ? 's' : '';
			$result = strtotime( '+' . $duration . ' ' . $duration_uom . $suffix ) - time();
		}
		return $result;
	}
	
	/**
	 * Retrieve an order.
	 *
	 * @param int $order_id
	 * @return WC_Order or null
	 */
	public static function get_order( $order_id = '' ) {
		$result = null;
		if ( $order = wc_get_order( $order_id ) ) {
			if ( $order->get_id() ) {
				$result = $order;
			}
		}
		return $result;
	}
	
	/**
	 * Retrieve a product.
	 *
	 * @param mixed $the_product Post object or post ID of the product
	 * @param array $args retrieval arguments
	 * @return WC_Product or null
	 */
	public static function get_product( $the_product = false, $args = array() ) {
		$result = null;
		if ( function_exists( 'wc_get_product' ) ) {
			$result = wc_get_product( $the_product, $args );
		} else if ( function_exists( 'get_product' ) ) {
			$result = get_product( $the_product, $args );
		}
		return $result;
	}
	
	/**
	 * Returns the order status key for the $status given.
	 * Order status keys have changed in WC 2.2 and this is provided to make
	 * it easier to handle.
	 *
	 * @param string $status key, one of 'pending', 'failed', 'on-hold', 'processing', 'completed', 'refunded', 'cancelled'
	 * @return string status key for current WC
	 */
	public static function get_order_status( $status ) {
		$result = $status;
		if ( function_exists( 'wc_get_order_statuses' ) ) { // only from WC 2.2
			if ( in_array( 'wc-' . $status, array_keys( wc_get_order_statuses() ) ) ) {
				$result = 'wc-' . $status;
			}
		}
		return $result;
	}

}
