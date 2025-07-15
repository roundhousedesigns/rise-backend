<?php
	/**
	 * Footer
	 *
	 * @package rise
	 */
?>

			<?php $footer_content = get_option( 'rise_settings_footer_content' ); ?>

			<footer id="colophon" class="site-footer">
				<?php if ( has_custom_logo() ): ?>
					<?php the_custom_logo(); ?>
				<?php endif; ?>

				<div class="social-links">
					<?php get_template_part( 'template-parts/snippet', 'social-links' ); ?>
				</div>
				<div class="site-info">
					<?php echo apply_filters( 'the_content', $footer_content ); ?>
				</div>
			</footer>

		</div><!-- #page -->

		<?php wp_footer(); ?>

	</body>
</html>
