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
<?php

    $video = get_post_meta(get_the_ID(), 'wp_classroom_video', TRUE);
    $options = get_option('wp-classroom');
	if( $options['video-host'] == "wistia" ) {
	    $defaults = wp_embed_defaults();
		$iframe_src = 'https://fast.wistia.net/embed/iframe/'. $video;
		$userEmail = NULL;

		if( is_user_logged_in() ) {
			$user = wp_get_current_user();
			if( isset($_GET['wemail']) ) {
				if( $_GET['wemail'] == $user->user_email )
					$userEmail = $user->user_email;
				else
					$userEmail = NULL;
			}
			if( !$userEmail ) {
				wp_redirect( add_query_arg( 'wemail', $user->user_email) );
				exit;
			}
		}
		$videoEmbed = '<iframe src="'.$iframe_src.'" frameborder=0 width='.$defaults['width'].' style="min-height:440px"></iframe>';
	} else {
		$videoEmbed = wp_oembed_get( $video );
	}

?>
<?php get_header(); ?>

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

switch ( $template ) {
	case 'twentyeleven' :
		echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
		break;
	case 'twentytwelve' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
		break;
	case 'twentythirteen' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
		break;
	case 'twentyfourteen' :
		echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwc">';
		break;
	case 'twentyfifteen' :
		echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
		break;
	case 'twentysixteen' :
		echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
		break;
	default :
		echo '<div id="container"><div id="content" role="main">';
		break;
}

?>
	<div class="wrap">		
		<article <?php post_class(); ?>>
			<header>
				<h1 class="entry-title"><?php the_title(); ?></h1>

				<div class="wpclr-class__actions">
					<?php if($next = get_next_post()): ?>
						<a href="<?php echo get_permalink($next->ID) ?>"><?php _e('Skip Class', 'wp-classroom') ?></a>
					<?php endif; ?>

					<?php
					$redirect = NULL;
					if( $next )
					$redirect = get_permalink($next->ID);
					?>
					<?= apply_filters(
						'complete_class',
						array(
							'redirect' => $redirect,
						)
						) ?>
					</div>
					<div class="wpclr-class__meta">
						<?php echo get_the_term_list( $post->ID, 'wp_course', 'Course: ', ', ' ); ?>
					</div>

				</header>

				<div class="wpclr-class__multimedia">
					<?php
					echo $videoEmbed;
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
	</div>

<?php
switch ( $template ) {
	case 'twentyeleven' :
		echo '</div>';
		get_sidebar();
		echo '</div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar();
		break;
	case 'twentyfifteen' :
		echo '</div></div>';
		break;
	case 'twentysixteen' :
		echo '</main></div>';
		break;
	default :
		echo '</div></div>';
		break;
}
?>

<?php get_footer(); ?>
