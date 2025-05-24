<?php
/**
 * Template part for displaying posts
 *
 * @package rise
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
			if ( is_singular() ):
				the_title( '<h1 class="entry-title">', '</h1>' );
			else:
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
			endif;
		?>
	</header>

	<div class="entry-content">
		<?php
			the_content();
			wp_link_pages();
		?>
	</div>
</article>
