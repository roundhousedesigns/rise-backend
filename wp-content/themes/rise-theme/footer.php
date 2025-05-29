<?php
	/**
	 * Footer
	 *
	 * @package rise
	 */
?>

			<?php $footer_content = get_option( 'rise_settings_footer_content' ); ?>

			<footer id="colophon" class="site-footer">
				<div class="social-links">
					<?php echo do_blocks( '<!-- wp:block {"ref":15749} /-->' ); ?>
				</div>
				<div class="site-info">
					<?php echo apply_filters( 'the_content', $footer_content ); ?>
				</div>
			</footer>

		</div><!-- #page -->

		<?php wp_footer(); ?>

	</body>
</html>
