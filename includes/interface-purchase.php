<?php
/**
 * The api interface for purchasing a product.
 *
 * @link       https://freshbrewedweb.com
 * @since      1.1.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 */

interface WP_Classroom_Purchase {

    public function add_class_to_user();

    public function add_course_to_user();

}
