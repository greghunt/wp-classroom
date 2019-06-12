<?php

/**
 * Product & subscription handler.
 */
class Groups_Classroom_Purchase_Handler {

	/**
	 * Register action hooks.
	 */
	public static function init() {
		// normal products

		// the essentials for normal order processing flow
		add_action( 'woocommerce_order_status_cancelled',  array( __CLASS__, 'order_status_cancelled' ) );
		add_action( 'woocommerce_order_status_completed',  array( __CLASS__, 'order_status_completed' ) );
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'order_status_completed' ) );
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'order_status_processing' ) );
		add_action( 'woocommerce_order_status_refunded',   array( __CLASS__, 'order_status_refunded' ) );

		// these are of concern when manual adjustments are made (backwards in order flow)
		add_action( 'woocommerce_order_status_failed',     array( __CLASS__, 'order_status_failed' ) );
		add_action( 'woocommerce_order_status_on_hold',    array( __CLASS__, 'order_status_on_hold' ) );
		add_action( 'woocommerce_order_status_pending',    array( __CLASS__, 'order_status_pending' ) );

		// give products
		add_action( 'woocommerce_order_given', array( __CLASS__, 'order_status_completed' ) );

		// scheduled expirations
		add_action( 'groups_ws_subscription_expired', array( __CLASS__, 'subscription_expired' ), 10, 2 );

		// time-limited memberships
		add_action( 'groups_created_user_group', array( __CLASS__, 'groups_created_user_group' ), 10, 2 );
		add_action( 'groups_deleted_user_group', array( __CLASS__, 'groups_deleted_user_group' ), 10, 2 );

		// force registration at checkout
//		add_filter( 'option_woocommerce_enable_guest_checkout', array( __CLASS__, 'option_woocommerce_enable_guest_checkout' ) );
//		add_filter( 'option_woocommerce_enable_signup_and_login_from_checkout', array( __CLASS__, 'option_woocommerce_enable_signup_and_login_from_checkout' ) );

		// subscriptions
		// >= 2.x

		// do_action( 'woocommerce_subscription_status_updated', $this, $new_status, $old_status );
//		add_action( 'woocommerce_subscription_status_updated', array( __CLASS__, 'woocommerce_subscription_status_updated' ), 10, 3 );
		// do_action( 'woocommerce_subscription_trashed', $post_id );
//		add_action( 'woocommerce_subscription_trashed', array( __CLASS__, 'woocommerce_subscription_trashed' ), 10, 1 );
		// do_action( 'woocommerce_subscriptions_switched_item', $subscription, $new_order_item, WC_Subscriptions_Order::get_item_by_id( $new_order_item['switched_subscription_item_id'] ) );
//		add_action( 'woocommerce_subscriptions_switched_item', array( __CLASS__, 'woocommerce_subscriptions_switched_item' ), 10, 3 );

		add_action( 'woocommerce_scheduled_subscription_end_of_prepaid_term', array( __CLASS__, 'woocommerce_scheduled_subscription_end_of_prepaid_term' ), 10, 1 );

	}

	/**
	 * Cancel group memberships for the order.
	 * @param int $order_id
	 */
	public static function order_status_cancelled( $order_id ) {
		$order = WP_Classroom_Woocommerce_Purchase::get_order( $order_id );
		$cus_id = $order->user_id;
		$items = $order->get_items();
		$user = new WP_User($cus_id);

		$classes = array();
		$courses = array();

		foreach ($items as $item)
		{
			$product_id = $item->get_product_id();
			$courses = get_post_meta( $product_id, '_classroom_courses', true);
			$classes = get_post_meta( $product_id, '_classroom_classes', true);
		}

		$existing_classes = get_user_meta($user->ID , 'wp-classroom_mb_user_class_access', true );
		$existing_courses = get_user_meta($user->ID , 'wp-classroom_mb_user_course_access', true );

		if ( !empty($classes) )
		{
			$classes = array_unique($classes);
			if ( !empty($existing_classes) )
		    {
		    	$classes = array_unique(array_diff($existing_classes,$classes));
		    }
		    update_user_meta($user->ID , 'wp-classroom_mb_user_class_access' , $classes);
		}

		if ( !empty($courses) )
		{
			$courses = array_unique($courses);
			if ( !empty($existing_courses) )
		    {
		    	$courses = array_unique(array_diff($existing_courses,$courses));
		    }
		    update_user_meta($user->ID , 'wp-classroom_mb_user_course_access' , $courses);
		}
	}

	/**
	 * Updates user to access the product
	 * @param int $order_id
	 */
	public static function order_status_completed( $order_id ) {
		$order = WP_Classroom_Woocommerce_Purchase::get_order( $order_id );
		$cus_id = $order->user_id;
		$items = $order->get_items();
		$user = new WP_User($cus_id);

		//check is order is a course order
		// if(!WP_Classroom_Woocommerce_Purchase::is_course($order_id))
		// 	return;

		$classes = array();
		$courses = array();

		foreach ($items as $item)
		{
			$product_id = $item->get_product_id();
			$courses = get_post_meta( $product_id, '_classroom_courses', true);
			$classes = get_post_meta( $product_id, '_classroom_classes', true);
		}

		$existing_classes = get_user_meta($user->ID , 'wp-classroom_mb_user_class_access', true );
		$existing_courses = get_user_meta($user->ID , 'wp-classroom_mb_user_course_access', true );

		if ( !empty($classes) )
		{
			$classes = array_unique($classes);
			if ( !empty($existing_classes) )
		    {
		    	$classes = array_unique(array_merge($existing_classes,$classes));
		    }
		    update_user_meta($user->ID , 'wp-classroom_mb_user_class_access' , $classes);
		}

		if ( !empty($courses) )
		{
			$courses = array_unique($courses);
			if ( !empty($existing_courses) )
		    {
		    	$courses = array_unique(array_merge($existing_courses,$courses));
		    }
		    update_user_meta($user->ID , 'wp-classroom_mb_user_course_access' , $courses);
		}
	}

	/**
	 * Revokes group memberships for the order.
	 * @param int $order_id
	 */
	public static function order_status_refunded( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_failed( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_on_hold( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_pending( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_processing( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Handle subscription status updates.
	 * Added for subscriptions 2.x compatibility.
	 *
	 * @param WC_Subscription $subscription
	 * @param string $new_status
	 * @param string $old_status
	 */
	public static function woocommerce_subscription_status_updated( $subscription, $new_status, $old_status ) {

		switch( $new_status ) {

			case 'active' :
			case 'completed' :
				self::subscription_status_active( $subscription );
				break;

			case 'cancelled' :
				self::subscription_status_cancelled( $subscription );
				break;

			case 'pending' :
				self::subscription_status_pending( $subscription );
				break;

			case 'failed' :
			case 'on-hold' :
				self::subscription_status_on_hold( $subscription );
				break;

			case 'pending-cancel' :
				// nothing to do here, wait until cancelled
				break;

			case 'expired' :
				self::subscription_status_expired( $subscription );
				break;

			case 'switched' :
				self::subscription_status_switched( $subscription );
				break;
		}
	}

	/**
	 * Get the order ID for a subscription.
	 *
	 * @param WC_Subscription $subscription
	 * @return int order id
	 */
	private static function get_subscription_order_id( $subscription ) {
		$order_id = null;
		if ( method_exists( $subscription, 'get_parent' ) ) {
			if ( $order = $subscription->get_parent() ) {
				$order_id = $order->get_id();
			}
		} else {
			if ( !empty( $subscription->order ) ) {
				$order_id = $subscription->order->id;
			} else {
				$order_id = $subscription->id;
			}
		}
		return $order_id;
	}

	/**
	 * Get the user ID for a subscription.
	 *
	 * @param WC_Subscription $subscription
	 * @return int user ID
	 */
	private static function get_subscription_user_id( $subscription ) {
		$user_id = null;
		if ( method_exists( $subscription, 'get_user_id' ) ) {
			$user_id = $subscription->get_user_id();
		} else {
			$user_id  = $subscription->user_id;
		}
		return $user_id;
	}

	/**
	 * Invokes the handler for cancelled.
	 *
	 * @param WC_Subscription $subscription
	 * @uses Groups_WS_Handler::subscription_status_cancelled( $subscription )
	 * @since 1.9.0
	 */
	private static function subscription_status_expired( $subscription ) {
		self::subscription_status_cancelled( $subscription );
	}

	/**
	 * Invokes the handler for cancelled.
	 *
	 * @param WC_Subscription $subscription
	 * @uses Groups_WS_Handler::subscription_status_cancelled( $subscription )
	 * @since 1.9.0
	 */
	private static function subscription_status_switched( $subscription ) {
		self::subscription_status_cancelled( $subscription );
	}

	/**
	 * Invokes the handler for on-hold subscription status.
	 *
	 * @param WC_Subscription $subscription
	 * @uses Groups_WS_Handler::subscription_status_on_hold( $subscription )
	 * @since 1.9.0
	 */
	private static function subscription_status_pending( $subscription ) {
		self::subscription_status_on_hold( $subscription );
	}

	/**
	 * Immediately remove the user from the subscription product's related groups.
	 * This is called when a cancelled subscription paid up period ends.
	 * The cancelled_subscription hook cannot be used because subscription is
	 * already cleared when the action is triggered and the
	 * get_next_payment_date() method will not return a payment date that
	 * we could use.
	 * @since 1.3.4
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function subscription_end_of_prepaid_term( $user_id, $subscription_key ) {
		self::subscription_expired( $user_id, $subscription_key );
	}

	public static function option_woocommerce_enable_guest_checkout() {

	}
}
$class_purchase_handler = new Groups_Classroom_Purchase_Handler(); //instantiaite the class
$class_purchase_handler->init(); // actions and filters put into action
