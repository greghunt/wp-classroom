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

  public function getClasses()
  {
    $posts = query_posts(array(
      'post_type' => 'wp_classroom',
      'author' => $this->ID,
    ));

    return $posts;
  }

  public function getMeta( $key ) {
    return get_user_meta( $this->ID, $this->user_meta_key . '_' . $key, true );
  }

}
