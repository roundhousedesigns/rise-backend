<?php
/**
 * Network Partner Page Template
 *
 * @package rise
 */
?>

<?php get_header(); ?>

<?php $term = get_queried_object(); ?>

<main id="primary" class="site-main archive-network_partner">
	<div class="chevron-banner <?php rise_term_background_color_class( $term->term_id ); ?>">
		<?php get_template_part( 'template-parts/content', 'network_partner_tag' ); ?>
	</div>

	<div class="chevron-divider"></div>

	<div class="chevron-content">
		<?php if ( have_posts() ): ?>
			<ul class="network-partner-grid post-grid">
				<?php while ( have_posts() ): the_post();
						get_template_part( 'template-parts/grid', 'network_partner' );
				endwhile; ?>
			</ul>
		<?php else:
				get_template_part( 'template-parts/content', 'none' );
		endif; ?>
	</div>
</main>

<?php get_footer();
