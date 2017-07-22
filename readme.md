=== Classroom ===
Contributors: freshbrewedweb
Donate link: https://freshbrewedweb.com
Tags: classroom, education, school, woocommerce
Requires at least: 3.7
Tested up to: 4.8
Stable tag: 2.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

Create a digital video based classroom in WordPress. This plugin gives you the ability to publish classes. It's flexible enough to combine with other well known WordPress plugins to enhance the functionality.

Want to sell your courses? Sell access to them with [WooCommerce](https://en-ca.wordpress.org/plugins/woocommerce/). Includes support for [subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/).

Organize your classes into Course groups? Try these helpers:
- [WP Term Images](https://wordpress.org/plugins/wp-term-images/)
- [WP Term Order](https://wordpress.org/plugins/wp-term-order/)


== Installation ==

1. Upload the `classroom` folder to the `/wp-content/plugins` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.

== 3rd Party Services ==

You have the option to use [Wistia](https://wistia.com) as a video provider. In order to do this, we need to communicate with their servers.

== Usage ==

Most of the time, you'll want to sell private access to your classes. This can be accomplished by installing [Groups](https://en-ca.wordpress.org/plugins/groups/) and [WooCommerce](https://en-ca.wordpress.org/plugins/woocommerce/).

Once installed, you can restrict access on a class or course basis by assigning them to a Group. Then, simply create a product and designate which Group they should be added to after purchase.

== Shortcodes ==
- [course_list]
- [courses]
- [student_profile]
- [complete_class]
- [course_progress]
- [classroom_login]

== Customization ==

This plugin was made to be particularly customizable and not too opinionated. To customize the classroom design, copy the `templates/single.php` to your theme's directory in `classroom/single.php`. From there, you can customize the output.

== Screenshots ==

1. Plugin banner

== Upgrade Notice ==

= 2.0.0 =
We moved to our own access restriction system. Previously, we recommended users to use Groups plugin. This is no longer needed.

= 1.0 =
Hello, this is the first version.

== Changelog ==

= 2.0.0 =
* Removed Wistia integration
* Added redirect settings
* Replaced Groups plugin dependency

= 1.1 =
* Fixed rewrite rules not flushing on activation.
* Removes required plugins.

= 1.0 =
* Start of a simple plugin.