<?php

/**
 * Teacher Profile template
 *
 * @link       https://github.com/freshbrewedweb/wp-classroom
 * @since      2.0.3
 *
 * @package    WP_Classroom
 * @subpackage WP_Classroom/templates
 */

$teacher = get_query_var( 'teacher' );

?>
<?php get_header(); ?>

<?php

if (! defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}

$template = get_option( 'template' );

switch ($template) {
    case 'twentyeleven':
        echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
        break;
    case 'twentytwelve':
        echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
        break;
    case 'twentythirteen':
        echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
        break;
    case 'twentyfourteen':
        echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwc">';
        break;
    case 'twentyfifteen':
        echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
        break;
    case 'twentysixteen':
        echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
        break;
    default:
        echo '<div id="container"><div id="content" role="main">';
        break;
}

?>
    <div class="wrap">
        <?php if ($teacher) : ?>
        <article <?php post_class(); ?>>
            <header>
                <h1 class="entry-title"><?php _e('Teacher', 'wp-classroom') ?>: <?php echo $teacher->display_name ?></h1>
								<h2><?php echo $teacher->getMeta('title') ?></h2>
								<?php echo get_the_author_meta( 'description', $teacher->ID ); ?>
            </header>

            <div class="entry-content">
                <h2><?php _e('Classes', 'wp-classroom') ?></h2>
                <?php foreach ($teacher->getClasses() as $class) : ?>
                <h3><a href="<?php echo get_permalink($class->ID) ?>"><?php echo $class->post_title ?></a></h3>
                <?php endforeach; ?>
            </div>
        </article>
        <?php else : ?>
        <h1><?php sprintf('User, %s, not a teacher.', ucfirst(get_query_var('teacher_username'))) ?></h1>
        <?php endif; ?>
    </div>

<?php
switch ($template) {
    case 'twentyeleven':
        echo '</div>';
        get_sidebar();
        echo '</div>';
        break;
    case 'twentytwelve':
        echo '</div></div>';
        break;
    case 'twentythirteen':
        echo '</div></div>';
        break;
    case 'twentyfourteen':
        echo '</div></div></div>';
        get_sidebar();
        break;
    case 'twentyfifteen':
        echo '</div></div>';
        break;
    case 'twentysixteen':
        echo '</main></div>';
        break;
    default:
        echo '</div></div>';
        break;
}
?>

<?php get_footer(); ?>
