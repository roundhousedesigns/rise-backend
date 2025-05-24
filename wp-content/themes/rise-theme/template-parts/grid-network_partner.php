<?php
/**
 * Network Partner Grid Template
 *
 * @package rise
 */
?>

<li id="post-<?php the_ID(); ?>" <?php post_class( 'network-partner post-grid-item' ); ?> tabindex="0" role="button" aria-label="<?php echo esc_attr__( 'View details for', 'rise' ) . ' ' . esc_attr( get_the_title() ); ?>">
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ): ?>
			<div class="entry-thumbnail">
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="entry-thumbnail-link" aria-label="<?php echo esc_attr__( 'Read more about', 'rise' ) . ' ' . esc_attr( get_the_title() ); ?>">
					<?php the_post_thumbnail( 'medium' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php the_title( '<h3 class="entry-title screen-reader-text"><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr__( 'Read more about', 'rise' ) . ' ' . esc_attr( get_the_title() ) . '" class="entry-title-link">', '</a></h3>' ); ?>
	</header>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
</li>
