<?php
/**
 * class-groups-ws-product.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author Karim Rahimpur
 * @package groups-woocommerce
 * @since groups-woocommerce 1.0.0
 */

/**
 * Product extension to integrate with Groups.
 */
class Groups_WS_Product {

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

	/**
	 * Groups tab title.
	 */
	public static function product_write_panel_tabs() {
		echo
			'<li class="attributes_tab attribute_options">' .
			'<a href="#woocommerce_groups">' .
			__( 'Groups', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</a>' .
			'</li>';
	}

	/**
	 * Groups tab content.
	 */
	public static function product_write_panels() {

		global $post, $wpdb, $woocommerce;

		echo '<div id="woocommerce_groups" class="panel woocommerce_options_panel" style="padding: 1em;">';
		
		if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $post->ID ) ) {
			echo '<p>' . __( 'The customer will be a member of the selected groups as long as the subscription is active. The customer will be removed from the selected groups once the subscription is active.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		} else {
			echo '<p>' . __( 'The customer will be added to or removed from the selected groups when purchasing this product.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		}

		$product_groups        = get_post_meta( $post->ID, '_groups_groups', false );
		$product_groups_remove = get_post_meta( $post->ID, '_groups_groups_remove', false );

		$group_table = _groups_get_tablename( "group" );
		$groups = $wpdb->get_results( "SELECT * FROM $group_table ORDER BY name" );

		$n = 0;
		if ( count( $groups ) > 0 ) {
			echo '<table class="widefat" style="margin:1em;width:50%;">';
			echo '<thead>';
			echo '<tr>';
			echo '<th style="width:50%">' . __( 'Group', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
			echo '<th style="width:25%">' . __( 'Add', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
			echo '<th style="width:25%">' . __( 'Remove', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach( $groups as $group ) {
				if ( $group->name !== Groups_Registered::REGISTERED_GROUP_NAME ) {
					echo '<tr>';
					echo '<th>' . wp_filter_nohtml_kses( $group->name ) . '</th>';
					echo '<td>';
					woocommerce_wp_checkbox(
						array(
							'id'    => '_groups_groups-' . esc_attr( $group->group_id ), // field name is derived from this, can't indicate name="_groups_groups[]"
							'label' => '',
							'value' => in_array( $group->group_id, $product_groups ) ? 'yes' : ''
						)
					);
					echo '</td>';
					echo '<td>';
					woocommerce_wp_checkbox(
						array(
							'id'    => '_groups_groups_remove-' . esc_attr( $group->group_id ),
							'label' => '',
							'value' => in_array( $group->group_id, $product_groups_remove ) ? 'yes' : ''
						)
					);
					echo '</td>';
					echo '</tr>';
					$n++;
				}
			}
			echo '</tbody>';
			echo '</table>';
			
			if ( !class_exists( 'WC_Subscriptions_Product' ) || !WC_Subscriptions_Product::is_subscription( $post->ID ) ) {

				$duration     = get_post_meta( $post->ID, '_groups_duration', true );
				$duration_uom = get_post_meta( $post->ID, '_groups_duration_uom', true );
				if ( empty( $duration_uom ) ) {
					$duration_uom = 'month';
				}
				switch( $duration_uom ) {
					case 'second' :
						$duration_uom_label = _n( 'Second', 'Seconds', $duration, GROUPS_WS_PLUGIN_DOMAIN );
						break;
					case 'minute' :
						$duration_uom_label = _n( 'Minute', 'Minutes', $duration, GROUPS_WS_PLUGIN_DOMAIN );
						break;
					case 'hour' :
						$duration_uom_label = _n( 'Hour', 'Hours', $duration, GROUPS_WS_PLUGIN_DOMAIN );
						break;
					case 'day' :
						$duration_uom_label = _n( 'Day', 'Days', $duration, GROUPS_WS_PLUGIN_DOMAIN );
						break;
					case 'week' :
						$duration_uom_label = _n( 'Week', 'Weeks', $duration, GROUPS_WS_PLUGIN_DOMAIN );
						break;
					case 'year' :
						$duration_uom_label = _n( 'Year', 'Years', $duration, GROUPS_WS_PLUGIN_DOMAIN );
						break;
					default :
						$duration_uom_label = _n( 'Month', 'Months', $duration, GROUPS_WS_PLUGIN_DOMAIN );
						break;
				}

				$duration_help =
					__( 'Leave the duration empty unless you want memberships to end after a certain amount of time.', GROUPS_WS_PLUGIN_DOMAIN ) .
					' ' .
					__( 'If the duration is empty, the customer will remain a member of the selected groups forever, unless removed explicitly.', GROUPS_WS_PLUGIN_DOMAIN ) .
					' ' .
					__( 'If the duration is set, the customer will only belong to the selected groups during the specified time, based on the <em>Duration</em> and the <em>Time unit</em>.', GROUPS_WS_PLUGIN_DOMAIN );

				$duration_help_icon = '<img class="help_tip" data-tip="' . esc_attr( $duration_help ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

				woocommerce_wp_text_input(
					array(
						'id'          => '_groups_duration',
						'label'       => sprintf( __( 'Duration', GROUPS_WS_PLUGIN_DOMAIN ), $duration_help_icon ),
						'value'       => $duration,
						'description' => sprintf( __( '%s (as chosen under <em>Time unit</em>)', GROUPS_WS_PLUGIN_DOMAIN ), $duration_uom_label ),
						'placeholder' => __( 'unlimited', GROUPS_WS_PLUGIN_DOMAIN )
					)
				);

				// data-tip is filtered out now, append it where we want it
				echo '<script type="text/javascript">';
				echo 'if (typeof jQuery !== "undefined"){';
				echo 'var _groups_duration_field = jQuery("._groups_duration_field");';
				echo '}';
				echo 'if (typeof _groups_duration_field !== "undefined"){';
				echo 'jQuery(_groups_duration_field).append(\'' . $duration_help_icon . '\');';
				echo '} else {';
				echo 'document.write(\'<p>' . $duration_help . '</p>\')';
				echo '}';
				echo '</script>';

				woocommerce_wp_select(
					array(
						'id'          => '_groups_duration_uom',
						'label'       => __( 'Time unit', GROUPS_WS_PLUGIN_DOMAIN ),
						'value'       => $duration_uom,
						'options'     => array(
							'second' => __( 'Seconds', GROUPS_WS_PLUGIN_DOMAIN ),
							'minute' => __( 'Minutes', GROUPS_WS_PLUGIN_DOMAIN ),
							'hour'   => __( 'Hours', GROUPS_WS_PLUGIN_DOMAIN ),
							'day'    => __( 'Days', GROUPS_WS_PLUGIN_DOMAIN ),
							'week'   => __( 'Weeks', GROUPS_WS_PLUGIN_DOMAIN ),
							'month'  => __( 'Months', GROUPS_WS_PLUGIN_DOMAIN ),
							'year'   => __( 'Years', GROUPS_WS_PLUGIN_DOMAIN ),
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
		}
		if ( $n == 0 ) {
			echo '<p>' . __( 'There are no groups available to select. At least one group (other than <em>Registered</em>) must be created.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		}
		echo '<p>' . __( 'Note that all users belong to the <em>Registered</em> group automatically.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';

		echo '<br/>';
		echo '</div>';
	}

	/**
	 * Register groups for a product.
	 * @param int $post_id product ID
	 * @param object $post product
	 */
	public static function process_product_meta( $post_id, $post ) {
		global $wpdb;

		// refresh groups, clear all, then assign checked
		delete_post_meta( $post_id,'_groups_groups' );
		delete_post_meta( $post_id,'_groups_groups_remove' );

		// iterate over groups, could also try to find these in $_POST
		// but would normally be more costly
		$group_table = _groups_get_tablename( "group" );
		$groups = $wpdb->get_results( "SELECT group_id FROM $group_table" );
		if ( count( $groups ) > 0 ) {
			foreach( $groups as $group ) {
				if ( !empty( $_POST['_groups_groups-'.$group->group_id] ) ) {
					add_post_meta( $post_id, '_groups_groups', $group->group_id );
				} else {
					if ( !empty( $_POST['_groups_groups_remove-'.$group->group_id] ) ) {
						add_post_meta( $post_id, '_groups_groups_remove', $group->group_id );
					}
				}
			}
		}

		// duration
		delete_post_meta( $post_id, '_groups_duration' );
		delete_post_meta( $post_id, '_groups_duration_uom' );
		$duration  = !empty( $_POST['_groups_duration'] ) ? intval( $_POST['_groups_duration'] ) : null;
		if ( $duration <= 0 ) {
			$duration = null;
		}
		if ( $duration !== null ) {
			$duration_uom = !empty( $_POST['_groups_duration_uom'] ) ? $_POST['_groups_duration_uom'] : null;
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
			add_post_meta( $post_id, '_groups_duration', $duration );
			add_post_meta( $post_id, '_groups_duration_uom', $duration_uom );
		}
	}
	
	/**
	 * Add duration info on prices.
	 * @param string $price
	 * @param WC_Product $product
	 */
	public static function woocommerce_get_price_html( $price, $product ) {
		$options = get_option( 'groups-woocommerce', null );
		$show_duration = isset( $options[GROUPS_WS_SHOW_DURATION] ) ? $options[GROUPS_WS_SHOW_DURATION] : GROUPS_WS_DEFAULT_SHOW_DURATION;
		if ( $show_duration ) {
			$duration     = get_post_meta( $product->id, '_groups_duration', true );
			if ( !empty( $duration ) ) {
				$duration_uom = get_post_meta( $product->id, '_groups_duration_uom', true );
				switch( $duration_uom ) {
					case 'second' :
						$price = sprintf( _n( '%s for 1 second', '%s for %d seconds', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'minute' :
						$price = sprintf( _n( '%s for 1 minute', '%s for %d minutes', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'hour' :
						$price = sprintf( _n( '%s for 1 hour', '%s for %d hours', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'day' :
						$price = sprintf( _n( '%s for 1 day', '%s for %d days', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'week' :
						$price = sprintf( _n( '%s for 1 week', '%s for %d weeks', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'year' :
						$price = sprintf( _n( '%s for 1 year', '%s for %d years', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					default :
						$price = sprintf( _n( '%s for 1 month', '%s for %d months', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
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
		$duration = get_post_meta( $product->id, '_groups_duration', true );
		return $duration > 0;
	}

	/**
	 * Returns the duration of membership in seconds.
	 * @param WC_Product $product
	 * @return duration in seconds or null if there is none defined
	 */
	public static function get_duration( $product ) {
		$result = null;
		$duration     = get_post_meta( $product->id, '_groups_duration', true );
		if ( !empty( $duration ) ) {
			$duration_uom = get_post_meta( $product->id, '_groups_duration_uom', true );
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
}
Groups_WS_Product::init();
