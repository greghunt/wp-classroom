<?php
/**
 * Classroom Teacher User
 *
 * @link       https://wordpress.org/plugins/classroom
 * @since      2.0.3
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/includes
 */
class WP_Classroom_Teacher extends WP_User
{

  private $user_meta_key = 'wp-classroom_mb_user_teacher';

  public function getClasses( $num = null )
  {
    global $wp_query;

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $args = array(
      'post_type' => 'wp_classroom',
      'orderby' => 'date',
      'order' => 'DESC',
      'author' => $this->ID,
      'paged' => $paged,
      'posts_per_page' => $num
    );

    $posts = query_posts(array_merge($args, $wp_query->query));

    return $posts;
  }

  public function getMeta( $key ) {
    return get_user_meta( $this->ID, $this->user_meta_key . '_' . $key, true );
  }

}
