<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<?php
$pod = pods( 'page', get_the_ID() );
$page_color = 'bg-color-' . ( $pod->field( 'background_color' ) ? $pod->field( 'background_color' ) : 'bg-color-dark' );
?>

<body <?php body_class( $page_color ); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<header id="masthead" class="site-header">
			<div class="site-branding">
				<?php
					if ( has_custom_logo() ):
						the_custom_logo();
					else:
				?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
					<?php
						endif;
					?>
			</div>

			<div class="spacer"></div>

			<div class="directory-link wp-block-buttons">
				<div class="wp-block-button">
					<a href="<?php echo esc_url( home_url( '/directory' ) ); ?>" class="wp-block-button__link">Directory</a>
				</div>
			</div>

			<div class="menu-container">
				<?php echo do_blocks( '<!-- wp:navigation {"ref":15617,"overlayMenu":"always","icon":"menu","overlayBackgroundColor":"bg-dark","overlayTextColor":"white","fontSize":"medium","layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right","flexWrap":"nowrap","orientation":"vertical"}} /-->' ); ?>
			</div>
		</header>
