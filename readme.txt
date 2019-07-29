=== Classroom ===
Contributors: freshbrewedweb
Donate link: https://freshbrewedweb.com
Tags: classroom, education, school, woocommerce
Requires at least: 3.7
Tested up to: 5.2
Stable tag: 2.2.7
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

Most of the time, you'll want to sell private access to your classes. This can be accomplished by installing [WooCommerce](https://en-ca.wordpress.org/plugins/woocommerce/). Then simply create a product and specify the classes or courses to which purchasing the product should grant access.

If a user is not logged in or doesn't have access to your class or course, you can specify what page they should be redirected to.

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


= 1.0 =
Hello, this is the first version.

== Changelog ==

= 2.2.6 =
* Added ability to define redirects on course level.

= 2.1.6 =
* Updated CMB2

= 2.1.4 =
* Added Teachers to Courses

= 2.0.4 =
* Fixed bug with purchase handler not adding access.

= 2.0.1 =
* Fixed PHP version compatibility

= 2.0.0 =
* We moved to our own access restriction system. Previously, we recommended users to use Groups plugin. This is no longer needed.
* Removed Wistia integration
* Added redirect settings
* Replaced Groups plugin dependency

= 1.1 =
* Fixed rewrite rules not flushing on activation.
* Removes required plugins.

= 1.0 =
* Start of a simple plugin.
