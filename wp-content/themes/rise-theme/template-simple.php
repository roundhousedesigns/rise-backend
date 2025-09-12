<?php
/**
 * Template Name: Simple one-column page
 *
 * @package rise
 */
?>

<?php get_header(); ?>

<main id="primary" class="site-main">
	<?php
		if ( have_posts() ):
			while ( have_posts() ):
				the_post();

				get_template_part( 'template-parts/content', 'page-simple' );
			endwhile;
		endif;
	?>
</main>

<?php get_footer();
