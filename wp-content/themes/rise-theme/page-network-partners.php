<?php
/**
 * Network Partner Page Template
 *
 * @package rise
 */
?>

<?php get_header(); ?>

<main id="primary" class="site-main archive-network_partner">
	<div class="chevron-banner">

		<?php if ( have_posts() ):
			while ( have_posts() ): the_post();
					get_template_part( 'template-parts/content', 'page' );
				endwhile;
			endif;

			$args = [
				'post_type'      => 'network_partner',
				'posts_per_page' => -1,
			];

			$query = new WP_Query( $args );

			$tags = get_terms( [
				'taxonomy'   => 'network_partner_tag',
				'hide_empty' => true,
			] );

		if ( !empty( $tags ) && !is_wp_error( $tags ) ): ?>
			<div class="network-partner-tags">
				<ul class="tag-list">
					<?php foreach ( $tags as $tag ): ?>
						<li class="wp-block-button">
							<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="wp-block-button__link">
								<?php echo esc_html( $tag->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
	<div class="chevron-divider"></div>
	<div class="chevron-content">

		<?php if ( $query->have_posts() ): ?>
			<ul class="network-partner-grid post-grid">
				<?php while ( $query->have_posts() ): $query->the_post();
						get_template_part( 'template-parts/grid', 'network_partner' );
				endwhile; ?>
			</ul>
		<?php else:
				get_template_part( 'template-parts/content', 'none' );
			endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>
</main>

<?php get_footer();
