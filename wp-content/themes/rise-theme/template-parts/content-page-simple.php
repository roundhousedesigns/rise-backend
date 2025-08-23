<?php
/**
 * Template part for displaying pages
 *
 * @package rise
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-container template-simple' ); ?>>
	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
	</header>

	<div class="entry-content">
		<?php the_content(); ?>
	</div>
</article>
