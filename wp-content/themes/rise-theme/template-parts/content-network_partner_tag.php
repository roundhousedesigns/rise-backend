<?php
/**
 * Template part for displaying pages
 *
 * @package rise
 */

$network_partners_page = get_page_by_path( 'network-partners' );
?>

<article <?php post_class( 'page-container archive-network_partner_tag' ); ?>>
	<div class="back-to-network-partners">
		<a href="<?php echo get_permalink( $network_partners_page->ID ); ?>" rel="bookmark" title="Back to Network Partners">&larr; Back to Network Partners</a>
	</div>

	<div class="archive-header-container">
		<header class="entry-header">
			<?php the_archive_title( '<h2 class="entry-title archive-title">', '</h2>' ); ?>
		</header>
	
		<div class="entry-content">
			<?php
			$term = get_queried_object();
			if ( $term && !empty( $term->description ) ) {
				echo '<div class="term-description">' . wp_kses_post( $term->description ) . '</div>';
			}
			?>
		</div>
	</div>
</article>
