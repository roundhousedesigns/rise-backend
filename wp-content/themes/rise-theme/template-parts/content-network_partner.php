<?php
/**
 * Template part for displaying single Network Partner pages
 *
 * @package rise
 */

$pod = pods( 'network_partner', get_the_ID() );
$cover_bg_attachment = $pod->field( 'cover_bg' );
$cover_bg = wp_get_attachment_image_url( $cover_bg_attachment['ID'], 'full' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'network-partner-container'); ?>>
	<header class="entry-header" style="background-image: url(<?php echo esc_url( $cover_bg ); ?>);">
		<div class="entry-title-overlay">
			<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
		</div>
	</header>

	<div class="network-partner-content-container">
		<div class="entry-thumbnail">
			<?php the_post_thumbnail( 'partner-logo', array( 'class' => 'entry-thumbnail' ) ); ?>
		</div>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</div>
</article>
