<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/greghunt/wp-classroom
 * @since      1.0.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/public/partials
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php if($next = get_next_post()): ?>
      <a href="<?php echo get_permalink($next->ID) ?>"><?php _e('Skip Class', 'wp-classroom') ?></a>
      <?php endif; ?>
      <div class="meta"><?php echo get_the_term_list( $post->ID, 'wp_course', 'Course: ', ', ' ); ?></div>
    </header>
    <div class="class-multimedia">
    <?php
      $video = get_post_meta(get_the_ID(), 'class_media_video', TRUE);
      echo wp_oembed_get( $video );
    ?>
    </div>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
    <footer>
      <?= apply_filters(
        'course_list',
        array(
          'numbered'=>'true',
          'orderby'=>'menu_order'
        )
      ) ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
