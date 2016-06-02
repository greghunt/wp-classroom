<?php

/**
 * Single Class Template File
 *
 * This file is used to markup the class view.
 *
 * @link       https://github.com/greghunt/wp-classroom
 * @since      1.0.0
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/templates
 */
?>
<?php get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<article <?php post_class(); ?>>
		  <header>
		    <h1 class="entry-title"><?php the_title(); ?></h1>
		    
			<div class="wpclr-class__actions">
			    <?php if($next = get_next_post()): ?>
			    <a href="<?php echo get_permalink($next->ID) ?>"><?php _e('Skip Class', 'wp-classroom') ?></a>
			    <?php endif; ?>
			
			    <?= apply_filters(
			      'complete_class',
			      array(
			        'redirect' => get_permalink($next->ID),
			      )
			    ) ?>
			</div>
		    <div class="wpclr-class__meta">
			    <?php echo get_the_term_list( $post->ID, 'wp_course', 'Course: ', ', ' ); ?>
			</div>
			
		  </header>
		  
		  <div class="wpclr-class__multimedia">
		  <?php
		    $video = get_post_meta(get_the_ID(), 'wp_classroom_video', TRUE);
		    echo wp_oembed_get( $video );
		  ?>
		  </div>
		  <nav class="class-course-nav">
		    <?= apply_filters( 'courses', NULL) ?>
		  </nav>
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
		</article>

	</main><!-- .site-main -->

	<?php get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
